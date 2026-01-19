<?php
/*
Copyright © Magd Almuntaser, OneXGen Technology. All rights reserved.
Project: MPWA Whatsapp Gateway | Multi Device
Licensed under the CC BY-NC-ND 4.0 License.
For details, visit https://creativecommons.org/licenses/by-nc-nd/4.0/.
*/

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\User;
use App\Models\Order;
use App\Models\MessageHistory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class CheckSubscription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:check';

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
    public function __construct()
    {
        parent::__construct();
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
		File::put(storage_path('app/cronjob/userandhistory.txt'), time());
		
		$orders = Order::where('status', 'pending')->where('payment_gateway', '!=', 'custom')->get();
		
		foreach ($orders as $order) {
			if (Carbon::parse($order->created_at)->addDay()->lt(now())) {
				$order->status = 'failed';
				$order->save();
			}
		}
		
		$users = User::where('delete_history', '!=', 0)->get();

		foreach ($users as $user) {
			$messageHistory = MessageHistory::where('user_id', '=', $user->id)->get();
			
			foreach ($messageHistory as $history) {
				$historyDelTime = Carbon::parse($history->created_at)->addDays($user->delete_history)->timestamp;

				if ($historyDelTime < time()) {
					MessageHistory::where('created_at', '<', Carbon::now()->subDays($user->delete_history))
								  ->where('user_id', $user->id)
								  ->delete();
				}
			}
		}
        $user_with_active_subscription = User::whereActiveSubscription('active')
										->where('subscription_expired', '<', date('Y-m-d'))
										->where('level', '!=', 'admin')
										->get();
        Log::info('Checking subscription');
        foreach ($user_with_active_subscription as $user) {
                $user->active_subscription = 'inactive';
                $user->subscription_expired = null;
                $user->save();
        }
    }
}
