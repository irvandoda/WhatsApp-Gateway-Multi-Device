<?php
/*
Copyright © Magd Almuntaser, OneXGen Technology. All rights reserved.
Project: MPWA Whatsapp Gateway | Multi Device
Licensed under the CC BY-NC-ND 4.0 License.
For details, visit https://creativecommons.org/licenses/by-nc-nd/4.0/.
*/

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Services\Impl\WhatsappServiceImpl;
use App\Utils\CacheKey;

class DevicesController extends Controller
{
    

    public function index(Request $request)
	{

        $numbers = $request->user()->devices()->latest()->paginate(10);

        return view('theme::devices', compact('numbers'));
    }

    public function store(Request $request){
      
       $validate =  validator($request->all(),[
            'sender' => 'required|min:8|max:15|unique:devices,body',
        ]);

        if($request->user()->isExpiredSubscription){
			if($request->user()->level != 'admin'){
				return back()->with('alert',['type' => 'danger','msg' => __('Your subscription has expired, please renew your subscription.')]);
			}
        }
        if($validate->fails()){
            return back()->with('alert',['type' => 'danger','msg' => $validate->errors()->first()]);
        }

       if($request->user()->limit_device <= $request->user()->devices()->count() ){
		   if($request->user()->level != 'admin'){
            return back()->with('alert',['type' => 'danger','msg' => __('You have reached the limit of devices!')]);
		   }
        }
        $request->user()->devices()->create(['body' => $request->sender,'webhook' => $request->urlwebhook]);
		File::deleteDirectory(base_path('credentials/'.$request->sender));
        return back()->with('alert',['type' => 'success','msg' => __('Devices Added!')]);
    }


    public function destroy(Request $request){
        try {
            //code...
             $device = $request->user()->devices()->find($request->deviceId);
			$whatsappService = new WhatsappServiceImpl();
			try {
				Session::forget('selectedDevice');
				$whatsappService->logoutDevice($device->body);
				File::deleteDirectory(base_path('credentials/'.$device->body));
			} catch (\Throwable $th) {
				return back()->with('alert',['type' => 'danger','msg' => __('Failed to check number!,check your connection!')]);
			}

			Cache::forget(CacheKey::DEVICE_BY_BODY . $device->body);
			$device->delete();
            return back()->with('alert',['type' => 'success','msg' => __('Devices Deleted!')]);
        } catch (\Throwable $th) {
            throw $th;
            return back()->with('alert',['type' => 'danger','msg' => __('Something went wrong!')]);
        }
    }


    public function setHook(Request $request){
		if ($request->user()->level != 'admin' && env("ENABLE_INDEX") == 'yes' && empty($request->user()->plan_data['webhook'])) {
			return response()->json(['error' => true, 'msg' => __('You do not have permission to use this feature, Please purchase/upgrade your plan')], 400);
		}
        clearCacheNode();  
        return $request->user()->devices()->whereBody($request->number)->update(['webhook' => $request->webhook]);
    }
	
	public function setDelay(Request $request){
        clearCacheNode();  
        return $request->user()->devices()->whereBody($request->number)->update(['delay' => $request->delay]);
    }
	
	public function setHookRead(Request $request){
        clearCacheNode();  
        $request->user()->devices()->whereBody($request->id)->update(['webhook_read' => $request->webhook_read]);
	    return response()->json(['error' => false, 'msg' => __('Webhook read has been updated')]);
    }
	
	public function setHookFull(Request $request){
        clearCacheNode();  
        $request->user()->devices()->whereBody($request->id)->update(['webhook_full' => $request->webhook_full]);
	    return response()->json(['error' => false, 'msg' => __('Webhook read has been updated')]);
    }
	
	public function setHookReject(Request $request){
        clearCacheNode();  
        $request->user()->devices()->whereBody($request->id)->update(['webhook_reject_call' => $request->webhook_reject_call]);
	    return response()->json(['error' => false, 'msg' => __('Webhook reject call has been updated')]);
    }
	
	public function setHookTyping(Request $request){
        clearCacheNode();  
        $request->user()->devices()->whereBody($request->id)->update(['webhook_typing' => $request->webhook_typing]);
	    return response()->json(['error' => false, 'msg' => __('Webhook typing has been updated')]);
    }
	
	public function setAvailable(Request $request){
        clearCacheNode($request->id);
        $request->user()->devices()->whereBody($request->id)->update(['set_available' => $request->set_available]);
	    return response()->json(['error' => false, 'msg' => __('Available has been updated')]);
    }

    public function setSelectedDeviceSession(Request $request){
        $device = $request->user()->devices()->whereId($request->device)->first();
        if(!$device){
            return response()->json(['error' => true, 'msg' => __('Device not found!')]);
            Session::forget('selectedDevice');
            
        }
        session()->put('selectedDevice', [
            'device_id' => $device->id,
            'device_body' => $device->body,
        ]);
        return response()->json(['error' => false, 'msg' => __('Device selected!')]);
    }


    


    

}
?>