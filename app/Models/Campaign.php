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

class Campaign extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'sender', 'name', 'phonebook_id', 'type', 'status', 'message', 'schedule','delay','delay_max'];

    public function blasts(){
        return $this->hasMany(Blast::class);
    }

    public function phonebook(){
        return $this->belongsTo(Tag::class);
    }

    public function device(){
        return $this->belongsTo(Device::class);
    }

    public function scopeFilter ($query, $request)
    {
        return $query->when($request->device , function($q) use ($request){
            return $q->whereHas('device', function($q) use ($request){
                return $q->where('body','=', $request->device);
            });
        })->when($request->status , function($q) use ($request){
            if ($request->status == 'all') {
                return $q;
            } else {
                return $q->where('status','=', $request->status);
            }
        });
    }


    public function getScheduleAttribute($value){
        return $value ? date('d M y H:i', strtotime($value)) : null;
    }

    
}
