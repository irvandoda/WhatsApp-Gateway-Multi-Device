<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class FixChatSessionUserIds extends Command
{
    protected $signature = 'chat:fix-user-ids {--dry-run}';
    protected $description = 'Fix chat_sessions.user_id from devices and keep one session per (body, phone_number)';

    public function handle()
    {
        $dry = $this->option('dry-run');

        $devices = DB::table('devices')->select('user_id','body','updated_at','created_at')->get();
        $owner = [];
        foreach ($devices as $d) {
            $b = preg_replace('/\D+/', '', (string)$d->body);
            if ($b === '') continue;
            $ts = $d->updated_at ?? $d->created_at ?? Carbon::create(1970,1,1);
            $cur = $owner[$b]['ts'] ?? null;
            if (!$cur || $ts >= $cur) {
                $owner[$b] = ['user_id' => (int)$d->user_id, 'ts' => $ts];
            }
        }

        $groups = 0;
        $updated = 0;
        $deleted = 0;
        $skipped = 0;
        $log = [];

        $rows = DB::table('chat_sessions')
            ->select('id','user_id','body','phone_number','created_at','updated_at')
            ->orderBy('body')->orderBy('phone_number')->orderByDesc('updated_at')->orderByDesc('id')
            ->cursor();

        $current = null;
        $bucket = [];

        $flush = function () use (&$bucket, &$current, &$groups, &$updated, &$deleted, &$skipped, &$log, $owner, $dry) {
            if (!$bucket) return;
            $groups++;
            usort($bucket, function($a,$b){
                $at = $a->updated_at ?? $a->created_at;
                $bt = $b->updated_at ?? $b->created_at;
                return strtotime((string)$bt) <=> strtotime((string)$at);
            });
            $bodyRaw = (string)$bucket[0]->body;
            $phone = (string)$bucket[0]->phone_number;
            $bodyKey = preg_replace('/\D+/', '', $bodyRaw);
            $own = $owner[$bodyKey]['user_id'] ?? null;
            if (!$own) {
                $skipped += count($bucket);
                $bucket = [];
                $current = null;
                return;
            }

            $ownerRows = array_values(array_filter($bucket, fn($r) => (int)$r->user_id === (int)$own));
            $canonical = $ownerRows ? $ownerRows[0] : $bucket[0];

            DB::beginTransaction();
            try {
                if ((int)$canonical->user_id !== (int)$own) {
                    if (!$dry) DB::table('chat_sessions')->where('id',$canonical->id)->update(['user_id'=>$own,'updated_at'=>now()]);
                    $updated++;
                    $log[] = ['update'=>$canonical->id,'from'=>(int)$canonical->user_id,'to'=>$own,'body'=>$bodyRaw,'phone'=>$phone];
                }
                foreach ($bucket as $r) {
                    if ($r->id === $canonical->id) continue;
                    if (!$dry) DB::table('chat_sessions')->where('id',$r->id)->delete();
                    $deleted++;
                    $log[] = ['delete'=>$r->id,'user_id'=>(int)$r->user_id,'body'=>$r->body,'phone'=>$r->phone_number,'kept'=>$canonical->id];
                }
                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                $log[] = ['error'=>get_class($e),'message'=>$e->getMessage(),'body'=>$bodyRaw,'phone'=>$phone];
            }

            $bucket = [];
            $current = null;
        };

        foreach ($rows as $r) {
            $key = preg_replace('/\D+/', '', (string)$r->body) . '|' . (string)$r->phone_number;
            if ($current === null) {
                $current = $key;
                $bucket[] = $r;
                continue;
            }
            if ($key === $current) {
                $bucket[] = $r;
            } else {
                $flush();
                $current = $key;
                $bucket[] = $r;
            }
        }
        $flush();

        $summary = ['groups'=>$groups,'updated'=>$updated,'deleted'=>$deleted,'skipped_no_owner'=>$skipped,'dry_run'=>$dry ? true : false,'log'=>$log];
        $this->line(json_encode($summary));
        return 0;
    }
}
