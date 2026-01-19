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

class MessageHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'message',
        'user_id',
        'message',
        'number',
        'send_by',
        'payload',
        'status',
        'type',
        'note',
    ];

    public function device(){
        return $this->belongsTo(Device::class);
    }


}
