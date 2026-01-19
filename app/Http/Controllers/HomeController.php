<?php
/*
Copyright © Magd Almuntaser, OneXGen Technology. All rights reserved.
Project: MPWA Whatsapp Gateway | Multi Device
Licensed under the CC BY-NC-ND 4.0 License.
For details, visit https://creativecommons.org/licenses/by-nc-nd/4.0/.
*/

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Services\Impl\WhatsappServiceImpl;
use App\Utils\CacheKey;

class HomeController extends Controller
{
    

    public function index(Request $request)
	{

        $user = $request->user()->withCount(['devices','campaigns'])->
        withCount(['blasts as blasts_pending' => function($q){
            return $q->where('status', 'pending');
        }])->withCount(['blasts as blasts_success' => function($q){
            return $q->where('status', 'success');
        }])->withCount(['blasts as blasts_failed' => function($q){
            return $q->where('status', 'failed');
        }])->withCount('messageHistories')->find($request->user()->id);
		
		if($request->user()->level == 'admin'){
			$currentVersion = config('app.version');
			$checkUrl = "https://mpwa.onexgen.com/tools/check.php?v=$currentVersion&lang=".config('app.locale');
			try {
				$response = Http::timeout(5)->get($checkUrl);
				$data = $response->json();
				if (isset($data['update_available']) && $data['update_available']) {
					$newVersion = $data['new_version'];
				}else{
					$newVersion = false;
				}
			} catch (\Exception $e) {
				$newVersion = false;
			}
			$user['subscription_status'] = __('Admin');
		}else{
			$newVersion = false;
			$user['subscription_status'] = $user->isExpiredSubscription ? 'Expired' : $user->active_subscription;
		}

        $uid = $user->id;
$today = now()->toDateString();

$base = \App\Models\ChatMessage::query()
  ->join('chat_sessions', 'chat_sessions.id', '=', 'chat_messages.session_id')
  ->where('chat_sessions.user_id', $uid);

$messagesToday = (clone $base)->whereDate('chat_messages.created_at', $today)->count();
$incomingToday = (clone $base)->whereDate('chat_messages.created_at', $today)->where('direction','incoming')->count();
$outgoingToday = (clone $base)->whereDate('chat_messages.created_at', $today)->where('direction','outgoing')->count();
$activeSessionsToday = \App\Models\ChatSession::where('user_id',$uid)->whereDate('updated_at',$today)->count();

$typesRaw = (clone $base)->whereDate('chat_messages.created_at', $today)
  ->select('chat_messages.type', DB::raw('COUNT(*) as c'))
  ->groupBy('chat_messages.type')
  ->orderByDesc('c')
  ->get();

$typesLabels = $typesRaw->pluck('type')->toArray();
$typesData   = $typesRaw->pluck('c')->toArray();

$topContacts = (clone $base)->whereDate('chat_messages.created_at', $today)
  ->select('chat_sessions.id','chat_sessions.push_name','chat_sessions.phone_number', DB::raw('COUNT(chat_messages.id) as cnt'))
  ->groupBy('chat_sessions.id','chat_sessions.push_name','chat_sessions.phone_number')
  ->orderByDesc('cnt')
  ->limit(5)
  ->get();

$mediaToday = (clone $base)->whereDate('chat_messages.created_at', $today)->where('chat_messages.type','!=','text')->count();

$chatUi = [
  'messages_today' => $messagesToday,
  'incoming_today' => $incomingToday,
  'outgoing_today' => $outgoingToday,
  'active_sessions_today' => $activeSessionsToday,
  'media_today' => $mediaToday,
  'types_labels' => $typesLabels,
  'types_data' => $typesData,
  'top_contacts' => $topContacts,
];

        $user['expired_subscription_status'] = $user->expiredSubscription;
        return view('theme::home', compact('user','newVersion','chatUi'));
    }

}
?>