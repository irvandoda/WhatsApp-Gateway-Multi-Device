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
class Autoreply extends Model
{
    use HasFactory;
    protected $guarded = ['id'];


    public function user(){
        return $this->belongsTo(User::class);
    }

    public function device (){
        return $this->belongsTo(Device::class);
    }

    public static function boot(){
        parent::boot();
        
        static::updated(function($autoreply){
            clearCacheNode();
        });

        static::created(function($autoreply){
            clearCacheNode();
        });
    }
  
}
