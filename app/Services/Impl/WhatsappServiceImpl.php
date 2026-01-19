<?php
/*
Copyright © Magd Almuntaser, OneXGen Technology. All rights reserved.
Project: MPWA Whatsapp Gateway | Multi Device
Licensed under the CC BY-NC-ND 4.0 License.
For details, visit https://creativecommons.org/licenses/by-nc-nd/4.0/.
*/

namespace App\Services\Impl;
use App\Services\WhatsappService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
class WhatsappServiceImpl implements WhatsappService
{
    private $url;
    protected const ROUTE_SEND_TEXT = "/backend-send-text";
    protected const ROUTE_SEND_MEDIA = "/backend-send-media";
	protected const ROUTE_SEND_STICKER = "/backend-send-sticker";
    protected const ROUTE_SEND_BUTTON = "/backend-send-button";
    protected const ROUTE_SEND_LIST = "/backend-send-list";
    protected const ROUTE_SEND_POLL = "/backend-send-poll";
	protected const ROUTE_SEND_LOCATION = "/backend-send-location";
	protected const ROUTE_SEND_PRODUCT = "/backend-send-product";
	protected const ROUTE_SEND_TEXT_CHANNEL = "/backend-send-text-channel";
	protected const ROUTE_SEND_VCARD = "/backend-send-vcard";
    protected const ROUTE_LOGOUT_DEVICE = "/backend-logout-device";
    protected const ROUTE_CHECK_NUMBER = "/backend-check-number";
    protected const ROUTE_GET_GROUPS = "/backend-getgroups";
	protected const ROUTE_GET_CHANNEL = "/backend-getchannel";
    protected const ROUTE_START_BLAST = "/backend-blast";
    public function __construct()
    {
        $this->url = env("WA_URL_SERVER");
    }
    private function sendRequest($route, $data): object
    {
        try {
            $results = Http::withOptions(["verify" => false])
                ->asForm()
                ->post($this->url . $route, $data);
            return json_decode($results->body());
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function fetchGroups($device): object
    {
        return $this->sendRequest(self::ROUTE_GET_GROUPS, [
            "token" => $device->body,
        ]);
    }
	public function fetchChannel($device, $data): object
    {
        return $this->sendRequest(self::ROUTE_GET_CHANNEL, [
            "token" => $device->body,
			"code" => $data,
        ]);
    }
    public function startBlast($data): object
    {
        return $this->sendRequest(self::ROUTE_START_BLAST, [
            "data" => json_encode($data)
        ]);
    }
    public function sendText($request, $receiver): object|bool
    {
		$footer = "\n\n> _".$request->footer."_";
        return $this->sendRequest(self::ROUTE_SEND_TEXT, [
            "token" => $request->sender,
            "number" => $receiver,
			"msgid" => $request->msgid ?? '',
            "text" => $request->footer ? $this->randomizeText($request->message, $receiver).$footer : $this->randomizeText($request->message, $receiver),
        ]);
    }
	public function sendTextChannel($request, $receiver): object|bool
    {
		$footer = "\n\n> _".$request->footer."_";
        return $this->sendRequest(self::ROUTE_SEND_TEXT_CHANNEL, [
            "token" => $request->sender,
            "number" => $receiver,
            "text" => $request->footer ? $this->randomizeText($request->message, $receiver).$footer : $this->randomizeText($request->message, $receiver),
        ]);
    }
	public function sendChannel($request, $receiver): object|bool
    {
		$footer = "\n\n> _".$request->footer."_";
        return $this->sendRequest(self::ROUTE_SEND_TEXT, [
            "token" => $request->sender,
            "number" => $receiver,
            "text" => $request->footer ? $this->randomizeText($request->message, $receiver).$footer : $this->randomizeText($request->message, $receiver),
        ]);
    }
	public function sendLocation($request, $receiver): object|bool
    {
        return $this->sendRequest(self::ROUTE_SEND_LOCATION, [
            "token" => $request->sender,
            "number" => $receiver,
			"msgid" => $request->msgid ?? '',
            "latitude" => $request->latitude,
			"longitude" => $request->longitude,
        ]);
    }
	public function sendProduct($request, $receiver): object|bool
	{
		return $this->sendRequest(self::ROUTE_SEND_PRODUCT, [
			"token"        => $request->sender,
			"number"       => $receiver,
			"message"       => $this->randomizeText($request->message, $receiver) ?? '',
			"product_id"   => $request->product_id,
			"phone"        => $request->phone,
			"product_title"=> $request->product_title,
			"company_name" => $request->company_name,
			"description"  => $request->description,
			"price"         => preg_replace('/[^\d]/', '', $request->price),
			"old_price"     => preg_replace('/[^\d]/', '', $request->old_price),
			"currency"     => $request->currency,
			"image"        => $request->image,
			"msgid" => $request->msgid ?? '',
		]);
	}
	public function sendVcard($request, $receiver): object|bool
    {
        return $this->sendRequest(self::ROUTE_SEND_VCARD, [
            "token" => $request->sender,
            "number" => $receiver,
            "name" => $request->name,
			"phone" => $request->phone,
			"msgid" => $request->msgid ?? '',
        ]);
    }
    public function sendMedia($request, $receiver): object|bool
    {
        $fileName = explode("/", $request->url);
        $fileName = explode(".", end($fileName));
        $fileName = implode(".", $fileName);
		$footer = "\n\n> _".$request->footer."_";
        $data = [
            "token" => $request->sender,
            "url" => $request->url,
            "number" => $receiver,
            "caption" => $request->footer ? ($this->randomizeText($request->caption, $receiver) ? $this->randomizeText($request->caption, $receiver) . $footer : $footer) : ($this->randomizeText($request->caption, $receiver) ?? ""),
            "filename" => $fileName,
            "type" => $request->media_type,
			"viewonce" => $request->viewonce,
			"msgid" => $request->msgid ?? '',
            "ptt" => $request->ptt
                ? ($request->ptt == "vn"
                    ? true
                    : false)
                : false,
        ];
        return $this->sendRequest(self::ROUTE_SEND_MEDIA, $data);
    }
	public function sendSticker($request, $receiver): object|bool
    {
        $fileName = explode("/", $request->url);
        $fileName = explode(".", end($fileName));
        $fileName = implode(".", $fileName);
        $data = [
            "token" => $request->sender,
            "url" => $request->url,
            "number" => $receiver,
			"msgid" => $request->msgid ?? '',
        ];
        return $this->sendRequest(self::ROUTE_SEND_STICKER, $data);
    }
    public function sendButton($request, $receiver): object|bool
    {
        $buttons = [];
        foreach ($request->button as $button) {
            $buttons[] = $button;
        }
        $image = $request->url
            ? $request->url
            : ($request->image
                ? $request->image
                : "");
        $data = [
            "token" => $request->sender,
            "number" => $receiver,
            "button" => json_encode($buttons),
            "message" => $this->randomizeText($request->message, $receiver),
            "footer" => $this->randomizeText($request->footer, $receiver) ?? "",
            "image" => $image
        ];
        return $this->sendRequest(self::ROUTE_SEND_BUTTON, $data);
    }
    public function sendList($request, $receiver): object|bool
	{
		$sections = [];

		foreach ($request->sections as $section) {
			$formattedRows = [];

			if (isset($section['rows'])) {
				foreach ($section['rows'] as $row) {
					$formattedRows[] = [
						"title" => $row['title'],
						"rowId" => uniqid('id'),
						"description" => $row['description'] ?? "",
					];
				}
			}

			$sections[] = [
				"title" => $section['title'],
				"rows" => $formattedRows,
			];
		}

		$data = [
			"token" => $request->sender,
			"number" => $receiver,
			"list" => json_encode($sections),
			"text" => $this->randomizeText($request->message, $receiver),
			"footer" => $this->randomizeText($request->footer, $receiver) ?? "",
			"title" => $request->name,
			"buttonText" => $request->buttontext,
			"msgid" => $request->msgid ?? '',
		];

		return $this->sendRequest(self::ROUTE_SEND_LIST, $data);
	}
    public function sendPoll($request, $receiver): object|bool
    {
        $optionss = [];
        foreach ($request->option as $opt) {
            $optionss[] = $opt;
        }
        $data = [
            "token" => $request->sender,
            "number" => $receiver,
            "name" => $request->name,
            "options" => json_encode($optionss),
            "countable" => $request->countable === "1" ? true : false,
			"msgid" => $request->msgid ?? '',
        ];
        return $this->sendRequest(self::ROUTE_SEND_POLL, $data);
    }
    public function logoutDevice($device): object|bool
    {
        return $this->sendRequest(self::ROUTE_LOGOUT_DEVICE, [
            "token" => $device,
        ]);
    }
    public function checkNumber($device, $number): object|bool
    {
        return $this->sendRequest(self::ROUTE_CHECK_NUMBER, [
            "token" => $device,
            "number" => $number,
        ]);
    }
	
	private function randomizeText($text, $receiver = "")
	{
		$text = preg_replace_callback('/{([^{}|]+(?:\|[^{}|]+)+)}/', function ($matches) {
			$options = explode('|', $matches[1]);
			return $options[array_rand($options)];
		}, $text);
		
		if($receiver != ""){
			$text = str_replace('{number}', $receiver, $text);
		}

		return $text;
	}
}
?>