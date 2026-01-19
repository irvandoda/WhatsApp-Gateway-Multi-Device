<?php
/*
Copyright © Magd Almuntaser, OneXGen Technology. All rights reserved.
Project: MPWA Whatsapp Gateway | Multi Device
Licensed under the CC BY-NC-ND 4.0 License.
For details, visit https://creativecommons.org/licenses/by-nc-nd/4.0/.
*/

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MessageHistory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use App\Models\Device;
use App\Repositories\DeviceRepository;
use App\Services\WhatsappService;
use App\Utils\CacheKey;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ApiController extends Controller
{
    protected WhatsappService $wa;
    protected DeviceRepository $deviceRepository;
    protected $extendedDataNeeded = [
        'text' => ['message', 'number'],
		'textchannel' => ['url', 'message'],
        'media' => ['number', 'media_type', 'url'],
		'sticker' => ['number', 'url'],
        'button' => ['number', 'button', 'message'],
		'product' => ['number', 'url', 'message'],
        'list' => ['number', 'name', 'title', 'buttontext', 'message', 'sections'],
        'poll' => ['number', 'name', 'option', 'countable'],
		'location' => ['number', 'latitude', 'longitude'],
		'vcard' => ['number', 'name', 'phone'],
    ];

    protected $allowedMediaType = ['image', 'video', 'audio', 'document'];
    public function __construct(WhatsappService $wa, DeviceRepository $deviceRepository)
    {
		$this->RESPON_SUCCESS = __("Message sent successfully!");
		$this->RESPON_FAILED = __("Failed to send message!, Check your connection!");
		$this->RESPON_INVALID_PARAMS = __("Invalid parameters, please check your input!");
        $this->wa = $wa;
        $this->deviceRepository = $deviceRepository;
    }

    private function getUniqueReceivers($request)
    {
        return array_unique(explode('|', $request->number));
    }

    private function throwInvalidParams()
    {
        return response()->json([
            'status' => false,
            'msg' => __("Invalid parameters!")
        ], 400);
    }

    private function isValidParams($request)
    {
        $type = $request->type;
        if (!in_array($type, array_keys($this->extendedDataNeeded))) return false;
        foreach ($this->extendedDataNeeded[$type] as $key) {
            if (!$request->has($key)) return false;
        }
        return true;
    }


    private function createDataForBatchInput($request, $number, $messageSent)
    {
        return [
            'user_id' => $request->user->id,
            'device_id' => $request->device->id,
            'number' => $number,
            'message' => $request->message ? $request->message : ($request->caption ? $request->caption : ''),
            'payload' => json_encode($request->all()),
            'status' => $messageSent->status ? 'success' : 'failed',
            'type' => $request->type,
            'send_by' => 'api',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function insertAndIncrement($prepareHistoryMessage, $success)
    {
        $device = request()->device;
        MessageHistory::insert($prepareHistoryMessage);
        $this->deviceRepository->incrementMessageSent($device->id, $success);
    }

    public function messageText(Request $request)
	{
		$user = $request->user;
		$planData = $user->plan_data;

		if ($user->level != 'admin' && env("ENABLE_INDEX") == 'yes' && empty($planData['send_message'])) {
			return $this->sendFailResponse(__('You do not have permission to use this feature, Please purchase/upgrade your plan'));
		}
		
		if($user->level != 'admin' && env("ENABLE_INDEX") == 'yes' && $planData['messages_limit'] == 0){
			return $this->sendFailResponse(__('You no longer have enough messages, recharge your message counter or upgrade your plan.'));
		}

		$request->merge(['type' => 'text']);
		if (!$this->isValidParams($request)) return $this->throwInvalidParams();

		$receivers = $this->getUniqueReceivers($request);

		if (isset($planData['messages_limit']) && $planData['messages_limit'] > 0) {
			$messagesLimit = $planData['messages_limit'];
			if (count($receivers) > $messagesLimit) {
				$receivers = array_slice($receivers, 0, $messagesLimit);
			}
		}

		$success = 0;
		$prepareHistoryMessage = [];

		try {
			foreach ($receivers as $number) {
				$sendMessage = $this->wa->sendText($request, $number);
				$prepareHistoryMessage[] = $this->createDataForBatchInput($request, $number, $sendMessage);
				$success = $sendMessage->status ? $success + 1 : $success;
			}

			if (isset($planData['messages_limit'])) {
				$planData['messages_limit'] = max(0, $planData['messages_limit'] - $success);
				$user->plan_data = $planData;
				$user->save();
			}

			$this->insertAndIncrement($prepareHistoryMessage, $success);
			if ($request->full) {
				if (is_array($sendMessage)) {
					unset($sendMessage['data']['status']);
				} elseif (is_object($sendMessage) && isset($sendMessage->data->status)) {
					unset($sendMessage->data->status);
				}
				return response()->json($sendMessage);
			}
			return $this->handleResponse($success);
		} catch (\Throwable $th) {
			Log::error($th);
			return $this->sendFailResponse($this->RESPON_FAILED);
		}
	}
	
	public function messageTextChannel(Request $request)
	{
		$user = $request->user;
		$planData = $user->plan_data;

		if ($user->level != 'admin' && env("ENABLE_INDEX") == 'yes' && empty($planData['send_message_channel'])) {
			return $this->sendFailResponse(__('You do not have permission to use this feature, Please purchase/upgrade your plan'));
		}
		
		if($user->level != 'admin' && env("ENABLE_INDEX") == 'yes' && $planData['messages_limit'] == 0){
			return $this->sendFailResponse(__('You no longer have enough messages, recharge your message counter or upgrade your plan.'));
		}

		$request->merge(['type' => 'textchannel']);
		if (!$this->isValidParams($request)) return $this->throwInvalidParams();

		$receivers = array_unique(explode('|', $request->url));

		if (isset($planData['messages_limit']) && $planData['messages_limit'] > 0) {
			$messagesLimit = $planData['messages_limit'];
			if (count($receivers) > $messagesLimit) {
				$receivers = array_slice($receivers, 0, $messagesLimit);
			}
		}

		$success = 0;
		$prepareHistoryMessage = [];

		try {
			foreach ($receivers as $number) {
				$removeurl = str_replace("@newsletter", "", $this->fetchChannel($request)->data->id);
				$sendMessage = $this->wa->sendTextChannel($request, $removeurl);
				$prepareHistoryMessage[] = $this->createDataForBatchInput($request, $removeurl, $sendMessage);
				$success = $sendMessage->status ? $success + 1 : $success;
			}

			if (isset($planData['messages_limit'])) {
				$planData['messages_limit'] = max(0, $planData['messages_limit'] - $success);
				$user->plan_data = $planData;
				$user->save();
			}

			$this->insertAndIncrement($prepareHistoryMessage, $success);
			if ($request->full) {
				if (is_array($sendMessage)) {
					unset($sendMessage['data']['status']);
				} elseif (is_object($sendMessage) && isset($sendMessage->data->status)) {
					unset($sendMessage->data->status);
				}
				return response()->json($sendMessage);
			}
			return $this->handleResponse($success);
		} catch (\Throwable $th) {
			Log::error($th);
			return $this->sendFailResponse($this->RESPON_FAILED);
		}
	}
	
	public function messageLocation(Request $request)
	{
		$user = $request->user;
		$planData = $user->plan_data;

		if ($user->level != 'admin' && env("ENABLE_INDEX") == 'yes' && empty($planData['send_location'])) {
			return $this->sendFailResponse(__('You do not have permission to use this feature, Please purchase/upgrade your plan'));
		}
		
		if($user->level != 'admin' && env("ENABLE_INDEX") == 'yes' && $planData['messages_limit'] == 0){
			return $this->sendFailResponse(__('You no longer have enough messages, recharge your message counter or upgrade your plan.'));
		}

		$request->merge(['type' => 'location']);
		if (!$this->isValidParams($request)) return $this->throwInvalidParams();

		$receivers = $this->getUniqueReceivers($request);

		if (isset($planData['messages_limit']) && $planData['messages_limit'] > 0) {
			$messagesLimit = $planData['messages_limit'];
			if (count($receivers) > $messagesLimit) {
				$receivers = array_slice($receivers, 0, $messagesLimit);
			}
		}

		$success = 0;
		$prepareHistoryMessage = [];

		try {
			foreach ($receivers as $number) {
				$sendMessage = $this->wa->sendLocation($request, $number);
				$prepareHistoryMessage[] = $this->createDataForBatchInput($request, $number, $sendMessage);
				$success = $sendMessage->status ? $success + 1 : $success;
			}

			if (isset($planData['messages_limit'])) {
				$planData['messages_limit'] = max(0, $planData['messages_limit'] - $success);
				$user->plan_data = $planData;
				$user->save();
			}

			$this->insertAndIncrement($prepareHistoryMessage, $success);
			if ($request->full) {
				if (is_array($sendMessage)) {
					unset($sendMessage['data']['status']);
				} elseif (is_object($sendMessage) && isset($sendMessage->data->status)) {
					unset($sendMessage->data->status);
				}
				return response()->json($sendMessage);
			}
			return $this->handleResponse($success);
		} catch (\Throwable $th) {
			Log::error($th);
			return $this->sendFailResponse($this->RESPON_FAILED);
		}
	}
	
	public function messageProduct(Request $request)
	{
		$user = $request->user;
		$planData = $user->plan_data;

		if ($user->level != 'admin' && env("ENABLE_INDEX") == 'yes' && empty($planData['send_product'])) {
			return $this->sendFailResponse(__('You do not have permission to use this feature, Please purchase/upgrade your plan'));
		}

		if ($user->level != 'admin' && env("ENABLE_INDEX") == 'yes' && $planData['messages_limit'] == 0) {
			return $this->sendFailResponse(__('You no longer have enough messages, recharge your message counter or upgrade your plan.'));
		}

		if (!$request->filled('url')) {
			return $this->sendFailResponse(__('Product URL is required.'));
		}

		$productData = $this->fetchProductFromUrl($request->url);
		if (!$productData || !isset($productData['product_id'])) {
			return $this->sendFailResponse(__('Failed to fetch product data from URL.'));
		}

		$request->merge([
			'type' => 'product',
			'product_id' => $productData['product_id'],
			'phone' => $productData['phone'],
			'product_title' => $productData['title'] ?? '',
			'company_name' => $productData['company_name'] ?? '',
			'description' => $productData['description'] ?? '',
			'price' => $productData['price'] ?? '',
			'old_price' => $productData['old_price'] ?? '',
			'currency' => $productData['currency'] ?? 'IDR',
			'image' => $productData['image'] ?? '',
			'message' => $request->message ?? '',
		]);

		if (!$this->isValidParams($request)) {
			return $this->throwInvalidParams();
		}

		$receivers = $this->getUniqueReceivers($request);

		if (isset($planData['messages_limit']) && $planData['messages_limit'] > 0) {
			$messagesLimit = $planData['messages_limit'];
			if (count($receivers) > $messagesLimit) {
				$receivers = array_slice($receivers, 0, $messagesLimit);
			}
		}

		$success = 0;
		$prepareHistoryMessage = [];

		try {
			foreach ($receivers as $number) {
				$sendMessage = $this->wa->sendProduct($request, $number);
				$prepareHistoryMessage[] = $this->createDataForBatchInput($request, $number, $sendMessage);
				$success = $sendMessage->status ? $success + 1 : $success;
			}

			if (isset($planData['messages_limit'])) {
				$planData['messages_limit'] = max(0, $planData['messages_limit'] - $success);
				$user->plan_data = $planData;
				$user->save();
			}

			$this->insertAndIncrement($prepareHistoryMessage, $success);
			if ($request->full) {
				if (is_array($sendMessage)) {
					unset($sendMessage['data']['status']);
				} elseif (is_object($sendMessage) && isset($sendMessage->data->status)) {
					unset($sendMessage->data->status);
				}
				return response()->json($sendMessage);
			}
			return $this->handleResponse($success);
		} catch (\Throwable $th) {
			Log::error($th);
			return $this->sendFailResponse($this->RESPON_FAILED);
		}
	}
	
	public function messageVcard(Request $request)
	{
		$user = $request->user;
		$planData = $user->plan_data;

		if ($user->level != 'admin' && env("ENABLE_INDEX") == 'yes' && empty($planData['send_vcard'])) {
			return $this->sendFailResponse(__('You do not have permission to use this feature, Please purchase/upgrade your plan'));
		}
		
		if($user->level != 'admin' && env("ENABLE_INDEX") == 'yes' && $planData['messages_limit'] == 0){
			return $this->sendFailResponse(__('You no longer have enough messages, recharge your message counter or upgrade your plan.'));
		}

		$request->merge(['type' => 'vcard']);
		if (!$this->isValidParams($request)) return $this->throwInvalidParams();

		$receivers = $this->getUniqueReceivers($request);

		if (isset($planData['messages_limit']) && $planData['messages_limit'] > 0) {
			$messagesLimit = $planData['messages_limit'];
			if (count($receivers) > $messagesLimit) {
				$receivers = array_slice($receivers, 0, $messagesLimit);
			}
		}

		$success = 0;
		$prepareHistoryMessage = [];

		try {
			foreach ($receivers as $number) {
				$sendMessage = $this->wa->sendVcard($request, $number);
				$prepareHistoryMessage[] = $this->createDataForBatchInput($request, $number, $sendMessage);
				$success = $sendMessage->status ? $success + 1 : $success;
			}

			if (isset($planData['messages_limit'])) {
				$planData['messages_limit'] = max(0, $planData['messages_limit'] - $success);
				$user->plan_data = $planData;
				$user->save();
			}

			$this->insertAndIncrement($prepareHistoryMessage, $success);
			if ($request->full) {
				if (is_array($sendMessage)) {
					unset($sendMessage['data']['status']);
				} elseif (is_object($sendMessage) && isset($sendMessage->data->status)) {
					unset($sendMessage->data->status);
				}
				return response()->json($sendMessage);
			}
			return $this->handleResponse($success);
		} catch (\Throwable $th) {
			Log::error($th);
			return $this->sendFailResponse($this->RESPON_FAILED);
		}
	}

    public function messageMedia(Request $request)
	{
		$user = $request->user;
		$planData = $user->plan_data;

		if ($user->level != 'admin' && env("ENABLE_INDEX") == 'yes' && empty($planData['send_media'])) {
			return $this->sendFailResponse(__('You do not have permission to use this feature, Please purchase/upgrade your plan'));
		}
		
		if($user->level != 'admin' && env("ENABLE_INDEX") == 'yes' && $planData['messages_limit'] == 0){
			return $this->sendFailResponse(__('You no longer have enough messages, recharge your message counter or upgrade your plan.'));
		}

		$request->merge(['type' => 'media']);
		if (!$this->isValidParams($request)) return $this->sendFailResponse($this->RESPON_INVALID_PARAMS);
		if (!in_array($request->media_type, $this->allowedMediaType)) return $this->sendFailResponse(__('Invalid media type! Allowed types: :types', ['types' => implode(', ', $this->allowedMediaType)]));

		$receivers = $this->getUniqueReceivers($request);

		if (isset($planData['messages_limit']) && $planData['messages_limit'] > 0) {
			$messagesLimit = $planData['messages_limit'];
			if (count($receivers) > $messagesLimit) {
				$receivers = array_slice($receivers, 0, $messagesLimit);
			}
		}

		$success = 0;
		$prepareHistoryMessage = [];

		try {
			foreach ($receivers as $number) {
				$sendMessage = $this->wa->sendMedia($request, $number);
				$prepareHistoryMessage[] = $this->createDataForBatchInput($request, $number, $sendMessage);
				$success = $sendMessage->status ? $success + 1 : $success;
			}

			if (isset($planData['messages_limit'])) {
				$planData['messages_limit'] = max(0, $planData['messages_limit'] - $success);
				$user->plan_data = $planData;
				$user->save();
			}

			$this->insertAndIncrement($prepareHistoryMessage, $success);
			if ($request->full) {
				if (is_array($sendMessage)) {
					unset($sendMessage['data']['status']);
				} elseif (is_object($sendMessage) && isset($sendMessage->data->status)) {
					unset($sendMessage->data->status);
				}
				return response()->json($sendMessage);
			}
			return $this->handleResponse($success);
		} catch (\Throwable $th) {
			return $this->sendFailResponse($this->RESPON_FAILED);
		}
	}

	public function messageSticker(Request $request)
	{
		$user = $request->user;
		$planData = $user->plan_data;

		if ($user->level != 'admin' && env("ENABLE_INDEX") == 'yes' && empty($planData['send_sticker'])) {
			return $this->sendFailResponse(__('You do not have permission to use this feature, Please purchase/upgrade your plan'));
		}
		
		if($user->level != 'admin' && env("ENABLE_INDEX") == 'yes' && $planData['messages_limit'] == 0){
			return $this->sendFailResponse(__('You no longer have enough messages, recharge your message counter or upgrade your plan.'));
		}

		$request->merge(['type' => 'sticker']);
		if (!$this->isValidParams($request)) return $this->sendFailResponse($this->RESPON_INVALID_PARAMS);

		$receivers = $this->getUniqueReceivers($request);

		if (isset($planData['messages_limit']) && $planData['messages_limit'] > 0) {
			$messagesLimit = $planData['messages_limit'];
			if (count($receivers) > $messagesLimit) {
				$receivers = array_slice($receivers, 0, $messagesLimit);
			}
		}

		$success = 0;
		$prepareHistoryMessage = [];

		try {
			foreach ($receivers as $number) {
				$sendMessage = $this->wa->sendSticker($request, $number);
				$prepareHistoryMessage[] = $this->createDataForBatchInput($request, $number, $sendMessage);
				$success = $sendMessage->status ? $success + 1 : $success;
			}

			if (isset($planData['messages_limit'])) {
				$planData['messages_limit'] = max(0, $planData['messages_limit'] - $success);
				$user->plan_data = $planData;
				$user->save();
			}

			$this->insertAndIncrement($prepareHistoryMessage, $success);
			if ($request->full) {
				if (is_array($sendMessage)) {
					unset($sendMessage['data']['status']);
				} elseif (is_object($sendMessage) && isset($sendMessage->data->status)) {
					unset($sendMessage->data->status);
				}
				return response()->json($sendMessage);
			}
			return $this->handleResponse($success);
		} catch (\Throwable $th) {
			return $this->sendFailResponse($this->RESPON_FAILED);
		}
	}

    public function messageButton(Request $request)
    {
        $request->merge(["type" => "button"]);
        if (!$this->isValidParams($request)) {
            return $this->sendFailResponse($this->RESPON_INVALID_PARAMS);
        }
        // Validate 'button' field
        if (!is_array($request->button) || count($request->button) < 1 || count($request->button) > 5) {
            return $this->sendFailResponse("Invalid button format! The button field must be an array with 1 to 5 items.");
        }
        foreach ($request->button as $index => $button) {
            if (!isset($button["type"]) || !in_array($button["type"], ["reply", "call", "url", "copy"])) {
                return $this->sendFailResponse("Button at index $index must have a valid 'type' (reply, call, url, copy).");
            }
            if (!isset($button["displayText"]) || !is_string($button["displayText"])) {
                return $this->sendFailResponse("Button at index $index must have a 'displayText' of type string.");
            }
            if ($button["type"] === "call" && (!isset($button["phoneNumber"]) || !is_string($button["phoneNumber"]))) {
                return $this->sendFailResponse("Button at index $index with type 'call' must have a 'phoneNumber' as a string.");
            }
            if ($button["type"] === "url" && (!isset($button["url"]) || !filter_var($button["url"], FILTER_VALIDATE_URL))) {
                return $this->sendFailResponse("Button at index $index with type 'url' must have a valid 'url'.");
            }
            if ($button["type"] === "copy" && (!isset($button["copyText"]) || !is_string($button["copyText"]))) {
                return $this->sendFailResponse("Button at index $index with type 'copy' must have 'copyText' as a string.");
            }
        }
        $receivers = $this->getUniqueReceivers($request);
        $success = 0;
        $prepareHistoryMessage = [];
        try {
            foreach ($receivers as $number) {
                $sendMessage = $this->wa->sendButton($request, $number);
                $success = $sendMessage->status ? $success + 1 : $success;
                $prepareHistoryMessage[] = $this->createDataForBatchInput($request, $number, $sendMessage);
            }
            $this->insertAndIncrement($prepareHistoryMessage, $success);
			if ($request->full) {
				if (is_array($sendMessage)) {
					unset($sendMessage['data']['status']);
				} elseif (is_object($sendMessage) && isset($sendMessage->data->status)) {
					unset($sendMessage->data->status);
				}
				return response()->json($sendMessage);
			}
            return $this->handleResponse($success);
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->sendFailResponse($this->RESPON_FAILED);
        }
    }

    public function messageList(Request $request)
	{
		$user = $request->user;
		$planData = $user->plan_data;

		if ($user->level != 'admin' && env("ENABLE_INDEX") == 'yes' && empty($planData['send_list'])) {
			return $this->sendFailResponse(__('You do not have permission to use this feature, Please purchase/upgrade your plan'));
		}
		
		if($user->level != 'admin' && env("ENABLE_INDEX") == 'yes' && $planData['messages_limit'] == 0){
			return $this->sendFailResponse(__('You no longer have enough messages, recharge your message counter or upgrade your plan.'));
		}

		$request->merge(['type' => 'list']);
		if (!$this->isValidParams($request)) return $this->sendFailResponse($this->RESPON_INVALID_PARAMS);
		if ($request->isMethod('get')){
			$request->merge(['sections' => $this->parseSections($request)]);
		}
		if (!is_array($request->sections)) return $this->sendFailResponse(__('Invalid list format!'));

		$receivers = $this->getUniqueReceivers($request);

		if (isset($planData['messages_limit']) && $planData['messages_limit'] > 0) {
			$messagesLimit = $planData['messages_limit'];
			if (count($receivers) > $messagesLimit) {
				$receivers = array_slice($receivers, 0, $messagesLimit);
			}
		}

		$success = 0;
		$prepareHistoryMessage = [];

		//try {
			foreach ($receivers as $number) {

				$sendMessage = $this->wa->sendList($request, $number);
				$success = $sendMessage->status ? $success + 1 : $success;
				$prepareHistoryMessage[] = $this->createDataForBatchInput($request, $number, $sendMessage);
			}

			if (isset($planData['messages_limit'])) {
				$planData['messages_limit'] = max(0, $planData['messages_limit'] - $success);
				$user->plan_data = $planData;
				$user->save();
			}

			$this->insertAndIncrement($prepareHistoryMessage, $success);
			if ($request->full) {
				if (is_array($sendMessage)) {
					unset($sendMessage['data']['status']);
				} elseif (is_object($sendMessage) && isset($sendMessage->data->status)) {
					unset($sendMessage->data->status);
				}
				return response()->json($sendMessage);
			}
			return $this->handleResponse($success);
		//} catch (\Throwable $th) {
		//	return $this->sendFailResponse($this->RESPON_FAILED);
		//}
	}

    public function messagePoll(Request $request)
	{
		$user = $request->user;
		$planData = $user->plan_data;

		if ($user->level != 'admin' && env("ENABLE_INDEX") == 'yes' && empty($planData['send_poll'])) {
			return $this->sendFailResponse(__('You do not have permission to use this feature, Please purchase/upgrade your plan'));
		}
		
		if($user->level != 'admin' && env("ENABLE_INDEX") == 'yes' && $planData['messages_limit'] == 0){
			return $this->sendFailResponse(__('You no longer have enough messages, recharge your message counter or upgrade your plan.'));
		}

		$request->merge(['type' => 'poll']);
		if (!$this->isValidParams($request)) return $this->sendFailResponse($this->RESPON_INVALID_PARAMS);
		if ($request->isMethod('get')) $request->merge(['option' => explode(',', $request->option)]);
		if (!is_array($request->option)) return $this->sendFailResponse(__('Invalid option format!'));

		$receivers = $this->getUniqueReceivers($request);

		if (isset($planData['messages_limit']) && $planData['messages_limit'] > 0) {
			$messagesLimit = $planData['messages_limit'];
			if (count($receivers) > $messagesLimit) {
				$receivers = array_slice($receivers, 0, $messagesLimit);
			}
		}

		$success = 0;
		$prepareHistoryMessage = [];

		try {
			foreach ($receivers as $number) {
				$sendMessage = $this->wa->sendPoll($request, $number);
				$success = $sendMessage->status ? $success + 1 : $success;
				$prepareHistoryMessage[] = $this->createDataForBatchInput($request, $number, $sendMessage);
			}

			if (isset($planData['messages_limit'])) {
				$planData['messages_limit'] = max(0, $planData['messages_limit'] - $success);
				$user->plan_data = $planData;
				$user->save();
			}

			$this->insertAndIncrement($prepareHistoryMessage, $success);
			if ($request->full) {
				if (is_array($sendMessage)) {
					unset($sendMessage['data']['status']);
				} elseif (is_object($sendMessage) && isset($sendMessage->data->status)) {
					unset($sendMessage->data->status);
				}
				return response()->json($sendMessage);
			}
			return $this->handleResponse($success);
		} catch (\Throwable $th) {
			return $this->sendFailResponse($this->RESPON_FAILED);
		}
	}


    private function handleResponse($success)
    {
        if ($success > 0) return response()->json(['status' => true, 'msg' => __('Message sent successfully!')], Response::HTTP_OK);
        return response()->json(['status' => false, 'msg' => __('Failed to send message!')], Response::HTTP_BAD_REQUEST);
    }

    private function sendFailResponse($message)
    {
        return response()->json(['status' => false, 'msg' => $message], Response::HTTP_BAD_REQUEST);
    }

	public function parseSections($request)
	{
		if ($request->isMethod('get') && $request->sections) {
			$sections = $request->sections;
			
			$sections = trim($sections);
			
			$sectionParts = [];
			$currentPart = '';
			$bracketCount = 0;
			
			for ($i = 0; $i < strlen($sections); $i++) {
				$char = $sections[$i];
				
				if ($char === '{') {
					$bracketCount++;
				} elseif ($char === '}') {
					$bracketCount--;
					if ($bracketCount === 0) {
						$currentPart .= $char;
						$sectionParts[] = trim($currentPart);
						$currentPart = '';
						continue;
					}
				}
				
				$currentPart .= $char;
			}
			
			$result = [];
			foreach ($sectionParts as $section) {
				$section = trim($section, '{}');
				
				preg_match('/title:\s*(.*?),/', $section, $titleMatch);
				preg_match('/description:\s*(.*?),/', $section, $descMatch);
				
				$sectionData = [
					'title' => trim($titleMatch[1] ?? ''),
					'description' => trim($descMatch[1] ?? ''),
				];
				
				if (preg_match('/rows:\s*\[(.*?)\]/', $section, $rowsMatch)) {
					$rowsString = $rowsMatch[1];
					$rows = [];
					
					preg_match_all('/\{(.*?)\}/', $rowsString, $rowMatches);
					
					foreach ($rowMatches[1] as $row) {
						$rowData = [];
						
						preg_match('/title:\s*(.*?),/', $row, $rTitleMatch);
						preg_match('/rowId:\s*(.*?),/', $row, $rIdMatch);
						preg_match('/description:\s*(.*?)$/', $row, $rDescMatch);
						
						$rowData = [
							'title' => trim($rTitleMatch[1] ?? ''),
							'rowId' => trim($rIdMatch[1] ?? ''),
							'description' => trim($rDescMatch[1] ?? ''),
						];
						
						$rows[] = $rowData;
					}
					
					$sectionData['rows'] = $rows;
				}
				
				$result[] = $sectionData;
			}
			
			return $result;
		}
		
		return null;
	}

    public function generateQr(Request $request)
    {
        if (!$request->has('api_key') || !$request->has('device')) return $this->sendFailResponse('Invalid parameters!');
        $user = User::whereApiKey($request->api_key)->first();
        if (!$user) return $this->sendFailResponse('Invalid api key!');
        $device = $this->deviceRepository->byBody($request->device)->single();
        if (!$device) {
            if (!$request->has('force') || !$request->force) return $this->sendFailResponse(__('Device not found!'));
            $device = $this->deviceRepository->create(['body' => $request->device, 'user_id' => $user->id]);
        }
        if ($device->status == 'Connected')  return $this->sendFailResponse(__('Device already connected!'));
        try {
            $post = Http::withOptions(['verify' => false])->asForm()->post(env('WA_URL_SERVER') . '/backend-generate-qr', ['token' => $request->device,]);
        } catch (\Throwable $th) {
            return $this->sendFailResponse($this->RESPON_FAILED);
        }
        return response()->json(json_decode($post->body()), Response::HTTP_OK);
    }
	
	public function createUser(Request $request)
    {
        if (!$request->has('api_key')) return $this->sendFailResponse(__('Invalid parameters!'));
		try {
			$user = Cache::remember(CacheKey::USER_BY_API_KEY . $request->api_key, 60 * 60 * 12, fn () => User::where('api_key', $request->api_key)->first());
			if ($user->level != 'admin') {
                return response()->json(
                    [
                        'status' => false, 'msg' => __('You do not have permission to create a user'),
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }
			try {
				$request->validate([
                    'username' => 'unique:users|min:4|required',
                    'email' => 'unique:users|email|required',
                    'password'  => 'required|min:6'
                ]);
		
                if ($request->has('username') ||
				    $request->has('email') ||
				    $request->has('password') ||
				    $request->has('expire')) {
			        User::create(
                        [
                            'username' => $request->username,
                            'email' => $request->email,
                            'password' => bcrypt($request->password),
                            'api_key' =>  Str::random(30),
                            'chunk_blast' => 0,
                            'subscription_expired' => Carbon::now()->addDays($request->expire),
                            'active_subscription' => 'active',
                            'limit_device' => ($request->limit_device ? $request->limit_device : 10),
                        ]
                    );
					return response()->json(
                        [
                            'status' => true,
                            'message' => __('User :username successfully created', ['username' => $request->username]),
                        ],
                        Response::HTTP_OK
                    );
                }
			} catch (\Throwable $th) {
				return response()->json(
                    [
                        'status' => false,
                        'msg' => __('There is an error in the variables, please check all inputs'),
                    ],
                    Response::HTTP_BAD_REQUEST
                );
			}
		} catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'msg' => __('Invali api_key or sender,please check again (3)'),
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
        return response()->json(json_decode($post->body()), Response::HTTP_OK);
    }
	
	public function infoUser(Request $request)
    {
        if (!$request->has('api_key')) return $this->sendFailResponse(__('Invalid parameters!'));
		try {
			$user = Cache::remember(CacheKey::USER_BY_API_KEY . $request->api_key, 60 * 60 * 12, fn () => User::where('api_key', $request->api_key)->first());
			if ($user->level != 'admin') {
                return response()->json(
                    [
                        'status' => false, 'msg' => __('You do not have permission to show a user'),
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }

			try {
				$UserInfo = User::where('username', $request->username)->first();
                if ($request->has('username') && $UserInfo) {
					return response()->json(
                        [
                            'status' => true,
                            'info' => $UserInfo,
                        ],
                        Response::HTTP_OK
                    );
                }else{
					return response()->json(
						[
							'status' => false,
							'msg' => __('There is no user with this username'),
						],
						Response::HTTP_BAD_REQUEST
					);
				}
			} catch (\Throwable $th) {
				return response()->json(
                    [
                        'status' => false,
                        'msg' => __('There is an error in the variables, please check all inputs'),
                    ],
                    Response::HTTP_BAD_REQUEST
                );
			}
		} catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'msg' => __('Invali api_key or sender,please check again (3)'),
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
        return response()->json(json_decode($post->body()), Response::HTTP_OK);
    }
	
	public function infoDevices(Request $request)
	{
		if (!$request->has('api_key')) {
			return $this->sendFailResponse(__('Invalid parameters!'));
		}

		try {
			$user = Cache::remember(CacheKey::USER_BY_API_KEY . $request->api_key, 60 * 60 * 12, fn () => User::where('api_key', $request->api_key)->first());

			$CheckUserDevice = $this->getDevices($request, $user);

			if ($CheckUserDevice->isEmpty()) {
				return response()->json([
					'status' => false,
					'msg' => __('The number you are trying to reach does not exist, or you do not have permission.'),
				], Response::HTTP_BAD_REQUEST);
			}

			return response()->json([
				'status' => true,
				'info' => $CheckUserDevice,
			], Response::HTTP_OK);

		} catch (\Throwable $th) {
			return response()->json([
				'status' => false,
				'msg' => __('Invalid api_key or sender, please check again (3)'),
			], Response::HTTP_BAD_REQUEST);
		}
	}

	private function getDevices(Request $request, $user)
	{
		if ($user->level == 'admin') {
			if ($request->has('number')) {
				return Device::where('body', $request->number)->get();
			}
			return Device::all();
		}

		if ($request->has('number')) {
			return Device::where('user_id', $user->id)->where('body', $request->number)->get();
		}

		return Device::where('user_id', $user->id)->get();
	}


    public function checkNumber(Request $request)
    {
        if (!$request->has('number')) return $this->sendFailResponse(__('Invalid parameters!'));
        try {
            $req = $this->wa->checkNumber($request->device->body, $request->number);
            return response()->json(['status' => true, 'msg' => $req->active], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->sendFailResponse(__("Failed to check number!,check your connection!"));
        }
    }
	
	protected function fetchChannel(Request $request)
	{
		$url = $request->url;
		if (!$url || !str_contains($url, 'whatsapp.com/channel/')) {
			return response()->json(['error' => 'Invalid URL'], 400);
		}
		$urlRemoved = preg_replace('/^(https?:\/\/)?(www\.)?whatsapp\.com\/channel\//i', '', $url);
		$number = $request->sender;
		$device = Device::where('body', $number)->first();
		return $this->wa->fetchChannel($device, $urlRemoved);
	}
	
	protected function fetchProductFromUrl($url)
	{
		try {
			if (!$url || !str_contains($url, 'wa.me/p/')) {
				return null;
			}

			$html = @file_get_contents($url);
			$data = [
				'productTitle'  => '',
				'companyName'   => '',
				'description'   => '',
				'price'         => '',
				'oldPrice'      => '',
				'currency'      => '',
				'image'         => '',
				'productId'     => '',
				'phoneNumber'   => '',
			];

			if ($html) {
				preg_match('/<title.*?>(.*?)<\/title>/si', $html, $titleMatch);
				$fullTitle = trim(html_entity_decode($titleMatch[1] ?? '', ENT_QUOTES | ENT_HTML5));
				if (preg_match('/^(.*?)\s+from\s+(.*?)\s+on WhatsApp\.?$/i', $fullTitle, $matches)) {
					$data['productTitle'] = trim($matches[1]);
					$data['companyName']  = trim($matches[2]);
				}

				preg_match('/<meta name="description" content="([^"]+)"/si', $html, $descMatch);
				$descriptionRaw = trim(html_entity_decode($descMatch[1] ?? '', ENT_QUOTES | ENT_HTML5));

				if (preg_match('/·\s*(IDR)\s?([\d.,]+)/ui', $descriptionRaw, $currentPriceMatch)) {
					$data['currency'] = trim($currentPriceMatch[1]);
					$data['price']    = trim($currentPriceMatch[2]);
					$descriptionRaw = preg_replace('/·\s*IDR\s?[\d.,]+/ui', '', $descriptionRaw);
				}

				if (preg_match('/\(was\s+(IDR)\s?([\d.,]+)\)/ui', $descriptionRaw, $oldPriceMatch)) {
					$data['oldPrice'] = trim($oldPriceMatch[2]);
					$descriptionRaw = preg_replace('/\(was\s+IDR\s?[\d.,]+\)/ui', '', $descriptionRaw);
				}

				$data['description'] = trim($descriptionRaw);

				preg_match('/<meta property="og:image" content="([^"]+)"/si', $html, $imageMatch);
				$data['image'] = html_entity_decode($imageMatch[1] ?? '', ENT_QUOTES | ENT_HTML5);

				preg_match('#wa\.me/p/(\d+)/(\d+)#', $url, $linkMatch);
				$data['productId']   = $linkMatch[1] ?? '';
				$data['phoneNumber'] = $linkMatch[2] ?? '';
			}

			return [
				'product_id'  => $data['productId'],
				'phone'       => $data['phoneNumber'],
				'title'       => $data['productTitle'],
				'company_name' => $data['companyName'],
				'description' => $data['description'],
				'price'       => $data['price'],
				'old_price'   => $data['oldPrice'],
				'currency'    => $data['currency'],
				'image'       => $data['image'],
			];
		} catch (\Throwable $e) {
			Log::error('fetchProductFromUrl failed: ' . $e->getMessage());
			return null;
		}
	}
}
