<?php
/*
Copyright © Magd Almuntaser, OneXGen Technology. All rights reserved.
Project: MPWA Whatsapp Gateway | Multi Device
Licensed under the CC BY-NC-ND 4.0 License.
For details, visit https://creativecommons.org/licenses/by-nc-nd/4.0/.
*/

namespace App\Console\Commands;

use App\Models\Blast;
use App\Models\Campaign;
use App\Services\WhatsappService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class StartBlast extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'start:blast';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    protected $wa;
    public function __construct(WhatsappService $wa)
    {
        parent::__construct();
        $this->wa = $wa;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function handle()
    {
		if (!File::exists(storage_path('app/cronjob'))) {
			File::makeDirectory(storage_path('app/cronjob'), 0755, true);
		}
		File::put(storage_path('app/cronjob/blast.txt'), time());
		
        Campaign::where('schedule', '<=', now())
            ->whereIn('status', ['waiting', 'processing'])
            ->with('phonebook', 'device')
            ->chunk(20, function ($waitingCampaigns) {
                $this->info("Processing " . $waitingCampaigns->count() . " campaigns");
                foreach ($waitingCampaigns as $campaign) {

                    try {
                        if ($campaign->device->status != 'Connected') {
                            $campaign->update(['status' => 'paused']);
                            continue;
                        }

                        $campaign->update(['status' => 'processing']);
                        $pendingBlasts = $this->getPendingBlasts($campaign);


                        if ($pendingBlasts->isEmpty()) {
                            $campaign->update(['status' => 'completed']);
                            Log::info("Campaign {$campaign->name} completed");
                            continue;
                        }
                        $blastdata = $pendingBlasts->map(function ($blast) {
                            return [
                                'receiver' => $blast->receiver,
                                'message' => $blast->message,
                            ];
                        })->toArray();


                        $data = [
                            'data' => $blastdata,
                            'type' => $campaign->type,
                            'delay' => $campaign->delay ?? 0,
							'delay_max' => $campaign->delay_max ?? 0,
                            'campaign_id' => $campaign->id,
                            'sender' => $campaign->device->body,
                        ];
						
						$user = $campaign->device->user;


                        try {
                            $res = $this->wa->startBlast($data);
                          
                            if (isset($res->status) && $res->status === false && $res->message === 'Unauthorized') {
                                $campaign->update(['status' => 'failed']);

								if($user->level != "admin"){
									$planData = $user->plan_data;
									$planData['messages_limit'] = max(0, $planData['messages_limit'] + count($blastdata));
									$user->plan_data = $planData;
									$user->save();
								}
                                Log::error("Campaign {$campaign->id} failed: Unauthorized");
                                continue;
                            }
                        } catch (\Exception $e) {
                            Log::error("Failed to start blast for campaign {$campaign->id}: " . $e->getMessage());
                            for ($i = 0; $i < 3; $i++) {
                                try {
                                    $res = $this->wa->startBlast($data);
                                    if (isset($res->status) && $res->status === false && $res->message === 'Unauthorized') {
                                        $campaign->update(['status' => 'failed']);
										if($user->level != "admin"){
											$planData = $user->plan_data;
											$planData['messages_limit'] = max(0, $planData['messages_limit'] + count($blastdata));
											$user->plan_data = $planData;
											$user->save();
										}
                                        Log::error("Campaign {$campaign->id} failed: Unauthorized");
                                        break;
                                    }
                                    break;
                                } catch (\Exception $e) {
                                    Log::error("Retry $i failed for campaign {$campaign->id}: " . $e->getMessage());
                                    sleep(5);
                                }
                            }
                        }
                    } catch (\Exception $e) {

                        Log::error("Failed to update campaign status or fetch pending blasts: " . $e->getMessage());
                        continue;
                    }
                }
            });
    }

    public function getPendingBlasts($campaign)
    {
        return $campaign
            ->blasts()
            ->where('status', 'pending')
            ->limit(15)
            ->get();
    }
}
