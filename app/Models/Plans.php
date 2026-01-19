<?php
/*
Copyright © Magd Almuntaser, OneXGen Technology. All rights reserved.
Project: MPWA Whatsapp Gateway | Multi Device
Licensed under the CC BY-NC-ND 4.0 License.
For details, visit https://creativecommons.org/licenses/by-nc-nd/4.0/.
*/

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plans extends Model
{
    use HasFactory;

    protected $table = 'plans';

    protected $fillable = [
        'title',
        'price',
		'symbol',
        'is_recommended',
        'is_trial',
        'status',
        'days',
        'trial_days',
        'data',
    ];
	
	protected $casts = [
        'data' => 'json',
		'trial_days' => 'integer',
    ];

    public $timestamps = true;
}
