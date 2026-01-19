<?php
/*
Copyright © Magd Almuntaser, OneXGen Technology. All rights reserved.
Project: MPWA Whatsapp Gateway | Multi Device
Licensed under the CC BY-NC-ND 4.0 License.
For details, visit https://creativecommons.org/licenses/by-nc-nd/4.0/.
*/

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use Coderflex\LaravelTicket\Concerns\HasTickets;
use Coderflex\LaravelTicket\Contracts\CanUseTickets;

class User extends Authenticatable
{
	use HasTickets;
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'api_key',
        'chunk_blast',
        'level',
        'status',
        'limit_device',
        'active_subscription',
        'subscription_expired',
        'two_factor_enabled',
        'two_factor_secret',
        'recovery_codes',
        'delete_history',
		'plan_name',
        'plan_data',
		'timezone'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'plan_data' => 'json',
    ];



    public function devices()
    {
        return $this->hasMany(Device::class);
    }
    public function autoreplies()
    {
        return $this->hasMany(Autoreply::class);
    }
    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
    public function phonebooks()
    {
        return $this->hasMany(Tag::class);
    }
    public function blasts()
    {
        return $this->hasMany(Blast::class);
    }
    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }

    public function messageTemplates()
    {
        return $this->hasMany(MessageTemplate::class);
    }

    public function messageHistories()
    {
        return $this->hasMany(MessageHistory::class);
    }

    // get expired subscription
    public function getExpiredSubscriptionAttribute()
    {
		if($this->level != 'admin'){
			if ($this->active_subscription == 'inactive') {
				return 'No subscription';
			} else if ($this->active_subscription == 'lifetime') {
				return '-';
			} else if ($this->active_subscription == 'active') {
				$expired_date = $this->subscription_expired;
				$expired_date = strtotime($expired_date);
				$current_date = strtotime(date('Y-m-d H:i:s'));
				if ($expired_date < $current_date) {
					return Carbon::parse($this->subscription_expired)->diffForHumans();
				} else {
					// count days
					$days = $expired_date - $current_date;
					$days = $days / (60 * 60 * 24);
					$days = round($days);
					return __(':days days left', ['days' => $days]);
				}
			}
		}else{
			return '-';
		}
    }

    // get booliean expired subscription
    public function getIsExpiredSubscriptionAttribute()
    {
		if($this->level != 'admin'){
			if ($this->active_subscription == 'inactive') {
				return true;
			} else if ($this->active_subscription == 'lifetime') {
				return false;
			} else if ($this->active_subscription == 'active') {
				$expired_date = $this->subscription_expired;
				$expired_date = strtotime($expired_date);
				$current_date = strtotime(date('Y-m-d H:i:s'));
				if ($expired_date < $current_date) {
					return true;
				} else {
					return false;
				}
			}
		}else{
			return false;
		}
    }

    // get total device connect and disconnect
    public function getTotalDeviceAttribute()
    {
        $connectedDevice = Device::whereUserId($this->id)->whereStatus('Connected')->count();
        return 'Connected: ' . $connectedDevice;
    }
}
