<?php
/*
Copyright © Magd Almuntaser, OneXGen Technology. All rights reserved.
Project: MPWA Whatsapp Gateway | Multi Device
Licensed under the CC BY-NC-ND 4.0 License.
For details, visit https://creativecommons.org/licenses/by-nc-nd/4.0/.
*/

namespace App\Services;

use App\Services\Impl\WhatsappServiceImpl;

interface WhatsappService
{
    public function fetchGroups($device): object;
	
	public function fetchChannel($device, $data): object;

    public function startBlast($data): object;

    public function sendText($request, $receiver): object | bool;
	
	public function sendLocation($request, $receiver): object | bool;
	
	public function sendVcard($request, $receiver): object | bool;

    public function sendMedia($request, $receiver): object | bool;
	
	public function sendProduct($request, $receiver): object | bool;
	
	public function sendTextChannel($request, $receiver): object | bool;
	
	public function sendSticker($request, $receiver): object | bool;

    public function sendButton($request, $receiver): object | bool;

    public function sendList($request, $receiver): object | bool;

    public function sendPoll($request, $receiver): object | bool;

    public function logoutDevice($device): object | bool;

    public function checkNumber($device, $number): object | bool;
}
  
?>