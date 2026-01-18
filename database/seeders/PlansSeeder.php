<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('plans')->insert([
            [
				'id' => '1',
                'title' => 'Starter',
                'price' => 10,
				'symbol' => '$',
                'is_recommended' => 0,
                'is_trial' => 0,
                'status' => 1,
                'days' => 30,
                'trial_days' => 0,
                'data' => '{"messages_limit":1000,"device_limit":2,"ai_message":false,"schedule_message":false,"bulk_message":false,"autoreply":true,"send_message":true,"send_media":false,"send_list":false,"send_template":true,"send_button":true,"send_location":true,"send_poll":true,"send_sticker":true,"send_vcard":true,"webhook":false,"api":false}',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
				'id' => '2',
                'title' => 'Enterprise',
                'price' => 50,
				'symbol' => '$',
                'is_recommended' => 1,
                'is_trial' => 1,
                'status' => 1,
                'days' => 30,
                'trial_days' => 10,
                'data' => '{"messages_limit":10000,"device_limit":10,"ai_message":true,"schedule_message":true,"bulk_message":true,"autoreply":true,"send_message":true,"send_media":true,"send_list":true,"send_template":true,"send_button":true,"send_location":true,"send_poll":true,"send_sticker":true,"send_vcard":true,"webhook":true,"api":true}',
                'created_at' => now(),
                'updated_at' => now(),
            ],
			[
				'id' => '3',
                'title' => 'Basic',
                'price' => 20,
				'symbol' => '$',
                'is_recommended' => 0,
                'is_trial' => 0,
                'status' => 1,
                'days' => 30,
                'trial_days' => 0,
                'data' => '{"messages_limit":5000,"device_limit":5,"ai_message":true,"schedule_message":false,"bulk_message":true,"autoreply":true,"send_message":true,"send_media":true,"send_list":true,"send_template":true,"send_button":true,"send_location":true,"send_poll":true,"send_sticker":true,"send_vcard":true,"webhook":true,"api":false}',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
