<?php

use App\Http\Controllers\Admin\ManageUsersController;
use App\Http\Controllers\Admin\UpdateController;
use App\Http\Controllers\Admin\TroubleshootController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\AutoreplyController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\AibotController;
use App\Http\Controllers\BlastController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DevicesController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MessagesHistoryController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\RestapiController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ShowMessageController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect;
use Alexusmai\LaravelFileManager\Controllers\FileManagerController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ThemesController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PickindexController;
use App\Http\Controllers\PlansController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Payments\StripeController;
use App\Http\Controllers\Payments\PaypalController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Payments\PaymobController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\User\UserPlansController;
use App\Http\Controllers\MessageTemplateController;

use App\Http\Controllers\Payments;
use App\Http\Controllers\Admin\PaymentGatewayController;

use App\Http\Controllers\User\TicketController as UserTicketController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\User\NotificationController as UserNotificationController;


require_once 'custom-route.php';

Route::group(['prefix' => LaravelLocalization::setLocale()], function() {
	if(env("ENABLE_INDEX") == 'no'){
		Route::get('/', function()
		{
			return Redirect::to( '/login');
		});
	}else{
		Route::get('/',[IndexController::class,'index'])->name('index');
	}
	
	Route::middleware('2fa')->group(function (){
		Route::get('/2fa', [TwoFactorController::class, 'showVerify'])->name('2fa.show');
		Route::post('/2fa', [TwoFactorController::class, 'verifyLogin'])->name('2fa.verify');
	});
	Route::middleware('auth', '2fa')->group(function (){

		Route::group(['prefix' => 'file-manager'], function () {
			Route::get('/', function(){return view('theme::pages.file-manager');})->name('file-manager');
            Route::get('initialize', [FileManagerController::class, 'initialize'])->name('fm.initialize');
			Route::get('content', [FileManagerController::class, 'content'])->name('fm.content');
			Route::get('tree', [FileManagerController::class, 'tree'])->name('fm.tree');
			Route::get('select-disk', [FileManagerController::class, 'selectDisk'])->name('fm.select-disk');
			Route::post('upload', [FileManagerController::class, 'upload'])->name('fm.upload');
			Route::post('delete', [FileManagerController::class, 'delete'])->name('fm.delete');
			Route::post('paste', [FileManagerController::class, 'paste'])->name('fm.paste');
			Route::post('rename', [FileManagerController::class, 'rename'])->name('fm.rename');
			Route::get('download', [FileManagerController::class, 'download'])->name('fm.download');
			Route::get('thumbnails', [FileManagerController::class, 'thumbnails'])->name('fm.thumbnails');
			Route::get('preview', [FileManagerController::class, 'preview'])->name('fm.preview');
			Route::get('url', [FileManagerController::class, 'url'])->name('fm.url');
			Route::post('create-directory', [FileManagerController::class, 'createDirectory'])->name('fm.create-directory');
			Route::post('create-file', [FileManagerController::class, 'createFile'])->name('fm.create-file');
			Route::post('update-file', [FileManagerController::class, 'updateFile'])->name('fm.update-file');
			Route::get('stream-file', [FileManagerController::class, 'streamFile'])->name('fm.stream-file');
			Route::post('zip', fn() => abort(403))->name('fm.zip');
			Route::post('unzip', fn() => abort(403))->name('fm.unzip');
			Route::get('ckeditor', [FileManagerController::class, 'ckeditor'])->name('fm.ckeditor');
			Route::get('tinymce', [FileManagerController::class, 'tinymce'])->name('fm.tinymce');
			Route::get('tinymce5', [FileManagerController::class, 'tinymce5'])->name('fm.tinymce5');
			Route::get('summernote', [FileManagerController::class, 'summernote'])->name('fm.summernote');
			Route::get('fm-button', [FileManagerController::class, 'fmButton'])->name('fm.fm-button');
        });
		
		Route::get('/home',[HomeController::class,'index'])->name('home');
		
		Route::get('/devices',[DevicesController::class,'index'])->name('devices');
		Route::post('/devices/setSessionSelectedDevice',[DevicesController::class,'setSelectedDeviceSession'])->name('setSessionSelectedDevice');
		Route::post('/devices/sethook',[DevicesController::class,'setHook'])->name('setHook');
		Route::post('/devices/setavailable',[DevicesController::class,'setAvailable'])->name('setAvailable');
		Route::post('/devices/setdelay',[DevicesController::class,'setDelay'])->name('setDelay');
		Route::post('/devices/sethookread',[DevicesController::class,'setHookRead'])->name('setHookRead');
		Route::post('/devices/sethookfull',[DevicesController::class,'setHookFull'])->name('setHookFull');
		Route::post('/devices/sethookreject',[DevicesController::class,'setHookReject'])->name('setHookReject');
		Route::post('/devices/sethooktyping',[DevicesController::class,'setHookTyping'])->name('setHookTyping');
		Route::post('/devices/setGPT',[DevicesController::class,'setGPT'])->name('setGPT');
		Route::post('/devices',[DevicesController::class,'store'])->name('addDevice');
		Route::delete('/devices',[DevicesController::class,'destroy'])->name('deleteDevice');

		Route::get('/scan/{number:body}',[ScanController::class,'scan'])->name('scan');
		Route::get('/code/{number:body}',[ScanController::class,'code'])->name('connect-via-code');

		Route::get('/autoreply',[AutoreplyController::class,'index'])->name('autoreply')->middleware('permissions');
		Route::post('/autoreply',[AutoreplyController::class,'store'])->name('autoreply')->middleware('permissions');
		Route::get('/autoreply-edit/{id}', [AutoreplyController::class, 'edit'])->name('autoreply.edit')->middleware('permissions');
		Route::post('/autoreply-edit', [AutoreplyController::class, 'editUpdate'])->name('autoreply.edit.update')->middleware('permissions');
		Route::delete('/autoreply',[AutoreplyController::class,'destroy'])->name('autoreply.delete')->middleware('permissions');
		Route::post('auto-reply/update/{autoreply:id}',[AutoreplyController::class,'update'])->name('autoreply.update')->middleware('permissions');

		Route::get('/aibot',[AibotController::class,'index'])->name('aibot')->middleware('permissions');
		Route::post('/aibot',[AibotController::class,'store'])->name('aibot')->middleware('permissions');
		
		Route::get('/phonebook',[TagController::class,'index'])->name('phonebook');
		Route::get('/get-phonebook',[TagController::class,'getPhonebook'])->name('getPhonebook');
		Route::delete('/clear-phonebook',[TagController::class,'clearPhonebook'])->name('clearPhonebook');
		Route::get('get-contact/{id}',[ContactController::class,'getContactByTagId']);
		Route::post('/contact/store',[ContactController::class,'store'])->name('contact.store');
		Route::delete('/contact/delete/{contact:id}',[ContactController::class,'destroy'])->name('contact.delete');
		Route::delete('/contact/delete-all/{id}',[ContactController::class,'DestroyAll'])->name('deleteAll');
		Route::post('/contact/import',[ContactController::class,'import'])->name('import');
		Route::get('/contact/export/{id}',[ContactController::class,'export'])->name('exportContact')->withoutMiddleware([LocaleCookieRedirect::class]);

	  Route::post('/tags',[TagController::class,'store'])->name('tag.store');
	  Route::delete('/tags',[TagController::class,'destroy'])->name('tag.delete');
	  Route::post('fetch-groups',[TagController::class ,'fetchGroups'])->name('fetch.groups');

	  Route::get('/campaigns',[CampaignController::class,'index'])->name('campaigns')->middleware('permissions');
	  Route::get('/campaign/create',[CampaignController::class,'create'])->name('campaign.create')->middleware('permissions');
	  Route::post('/campaign/store',[CampaignController::class,'store'])->name('campaign.store')->middleware('permissions');
	  Route::post('/campaign/pause/{id}',[CampaignController::class,'pause'])->name('campaign.pause')->middleware('permissions');
	  Route::post('/campaign/resume/{id}',[CampaignController::class,'resume'])->name('campaign.resume')->middleware('permissions');
	  Route::delete('/campaign/delete/{id}',[CampaignController::class,'destroy'])->name('campaign.delete')->middleware('permissions');
	  Route::get('/campaign/show/{id}',[CampaignController::class,'show'])->name('campaign.show')->middleware('permissions');
	  Route::delete('/campaign/clear',[CampaignController::class,'destroyAll'])->name('campaigns.delete.all')->middleware('permissions');
	  Route::get('/campaign/blast/{campaign:id}',[BlastController::class,'index'])->name('campaign.blasts')->middleware('permissions');

	  Route::get('/templates',[MessageTemplateController::class,'index'])->name('templates.index')->middleware('permissions');
	  Route::get('/templates/create',[MessageTemplateController::class,'create'])->name('templates.create')->middleware('permissions');
	  Route::post('/templates',[MessageTemplateController::class,'store'])->name('templates.store')->middleware('permissions');
	  Route::post('/templates/{id}',[MessageTemplateController::class,'show'])->name('templates.show')->middleware('permissions');
	  Route::get('/templates/{id}/edit',[MessageTemplateController::class,'edit'])->name('templates.edit')->middleware('permissions');
	  Route::put('/templates/{id}',[MessageTemplateController::class,'update'])->name('templates.update')->middleware('permissions');
	  Route::delete('/templates/{id}',[MessageTemplateController::class,'destroy'])->name('templates.destroy')->middleware('permissions');
	  Route::post('/templates/{id}/preview',[MessageTemplateController::class,'preview'])->name('templates.preview')->middleware('permissions');
	  Route::post('/templates/{id}/toggle-status',[MessageTemplateController::class,'toggleStatus'])->name('templates.toggle-status')->middleware('permissions');
	  Route::post('/templates-for-campaign',[MessageTemplateController::class,'getTemplatesForCampaign'])->name('templates.for-campaign')->middleware('permissions');

	  Route::post('/preview-message',[ShowMessageController::class,'index'])->name('previewMessage');
	  Route::get('/form-message/{type}',[ShowMessageController::class,'getFormByType'])->name('formMessage');
	  Route::get('/form-message-edit/{type}',[ShowMessageController::class,'showEdit'])->name('formMessageEdit');

	  Route::get('/message/test',[MessagesController::class,'index'])->name('messagetest');
	  Route::post('/message/test',[MessagesController::class,'store'])->name('messagetest')->middleware('permissions');
	  Route::get('/fetch-whatsapp-product', [MessagesController::class, 'fetchWhatsAppProduct'])->name('fetch.whatsapp.product');
	  Route::post('fetch-channel',[MessagesController::class ,'fetchChannel'])->name('fetch.channel');
	  
	  Route::get('/plans',[UserPlansController::class,'index'])->name('user.plans.index');

	  Route::get('/api-docs',RestapiController::class)->name('rest-api')->middleware('permissions');
	  
	  Route::get('/chat', [ChatController::class, 'index'])->name('chat.index')->middleware('permissions');
	  Route::get('/chat/messages/{session}', [ChatController::class, 'messages'])->name('chat.messages')->middleware('permissions');
	  Route::post('/chat/session/name', [ChatController::class, 'setSessionName'])->name('chat.session.setName')->middleware('permissions');

	  Route::get('/user/settings',[UserController::class,'settings'])->name('user.settings');
	  Route::post('/user/change-password',[UserController::class,'changePasswordPost'])->name('changePassword');
	  Route::post('/user/setting/apikey',[UserController::class,'generateNewApiKey'])->name('generateNewApiKey');
	  Route::post('/user/setting/deletehistory',[UserController::class,'deleteHistory'])->name('deleteHistory');
	  
	  Route::post('/settings/timezone', [UserController::class, 'updateTimezone'])->name('user.settings.timezone');
	  
	  Route::post('/user/settings/2fa', [UserController::class, 'toggleTwoFactor'])->name('user.settings.2fa');
	  Route::get('/user/2fa_setup', [TwoFactorController::class, 'showSetup'])->name('user.2fa_setup');
	  Route::post('/user/2fa/verify', [TwoFactorController::class, 'verify'])->name('user.2fa.verify');
	  
	  Route::post('/user/notification-seen', [UserNotificationController::class, 'markAsSeen'])->name('user.notification.seen');
	  
	  Route::get('/admin/settings',[SettingController::class,'index'])->name('admin.settings')->middleware('admin');
	  Route::post('/settings/server',[SettingController::class,'setServer'])->name('setServer')->middleware('admin');
	  Route::post('/settings/generate-ssl', [SettingController::class, 'generateSslCertificate'])->name('generateSsl')->middleware('admin');
	  Route::post('/settings/setenvall', [SettingController::class, 'setEnvAll'])->name('setEnvAll')->middleware('admin');
	  Route::post('/settings/registration', [SettingController::class, 'setRegistration'])->name('settings.registration')->middleware('admin');
	  
	  Route::get('/admin/cronjob',[SettingController::class,'cronJob'])->name('cronjob')->middleware('admin');

	  Route::get('tickets', [UserTicketController::class, 'index'])->name('user.tickets.index');
	  Route::post('tickets/{ticket}/reply', [UserTicketController::class, 'reply'])->name('user.tickets.reply');
	  Route::post('tickets/store', [UserTicketController::class, 'store'])->name('user.tickets.store');
	  Route::get('tickets/create', [UserTicketController::class, 'create'])->name('user.tickets.create');
	  Route::get('tickets/{ticket}', [UserTicketController::class, 'show'])->name('user.tickets.show');

	  Route::get('/admin/tickets', [AdminTicketController::class, 'index'])->name('admin.tickets.index')->middleware('admin');
	  Route::get('/admin/tickets/{ticket}', [AdminTicketController::class, 'show'])->name('admin.tickets.show')->middleware('admin');
	  Route::post('/admin/tickets/{ticket}/reply', [AdminTicketController::class, 'reply'])->name('admin.tickets.reply')->middleware('admin');
	  Route::post('/admin/tickets/{ticket}/close', [AdminTicketController::class, 'close'])->name('admin.tickets.close')->middleware('admin');
	  Route::post('/admin/tickets/{ticket}/reopen', [AdminTicketController::class, 'reopen'])->name('admin.tickets.reopen')->middleware('admin');
	  
	  Route::get('/admin/troubleshoot', [TroubleshootController::class, 'index'])->name('admin.troubleshoot')->middleware('admin');
	  Route::post('/admin/troubleshoot/check', [TroubleshootController::class, 'check'])->name('admin.troubleshoot.check')->middleware('admin');
	  Route::post('/admin/troubleshoot/upload-report', [TroubleshootController::class, 'uploadReport'])->name('admin.troubleshoot.upload')->middleware('admin');

	  Route::get('/admin/plans', [PlansController::class, 'index'])->name('admin.plans.index')->middleware('admin');
	  Route::get('/admin/plans/create', [PlansController::class, 'create'])->name('admin.plans.create')->middleware('admin');
	  Route::post('/admin/plans', [PlansController::class, 'store'])->name('admin.plans.store')->middleware('admin');
	  Route::get('/admin/plans/{plan}/edit', [PlansController::class, 'edit'])->name('admin.plans.edit')->middleware('admin');
	  Route::put('/admin/plans/{plan}', [PlansController::class, 'update'])->name('admin.plans.update')->middleware('admin');
	  Route::delete('/admin/plans/{plan}', [PlansController::class, 'destroy'])->name('admin.plans.destroy')->middleware('admin');
	  
	  Route::get('/admin/notifications', [NotificationController::class, 'index'])->name('admin.notifications.index')->middleware('admin');
	  Route::post('/admin/notifications', [NotificationController::class, 'send'])->name('admin.notifications.send')->middleware('admin');
	  
	  Route::get('/admin/pickindex', [PickindexController::class, 'editSettings'])->name('admin.index.edit')->middleware('admin');
	  Route::post('/admin/pickindex', [PickindexController::class, 'updateSettings'])->name('admin.index.update')->middleware('admin');
	  Route::post('/admin/pickindexcolor', [PickindexController::class, 'updateColor'])->name('admin.index.color')->middleware('admin');
	  Route::post('/admin/pickindexenable', [PickindexController::class, 'enableIndex'])->name('admin.index.enable')->middleware('admin');
	  Route::post('/admin/pickindexconfig', [PickindexController::class, 'updateConfigOptions'])->name('admin.index.config.update')->middleware('admin');
	  
	  Route::get('/admin/languages', [LanguageController::class, 'index'])->name('languages.index')->middleware('admin');
	  Route::get('/admin/languages/{lang}/edit', [LanguageController::class, 'edit'])->name('languages.edit')->middleware('admin');
	  Route::post('/admin/languages/{lang}', [LanguageController::class, 'update'])->name('languages.update')->middleware('admin');
	  Route::post('/admin/languages/add/new', [LanguageController::class, 'add'])->name('languages.add')->middleware('admin');
	  Route::delete('/admin/languages/{lang}', [LanguageController::class, 'destroy'])->name('languages.destroy')->middleware('admin');
	  
	  Route::get('/admin/update',[UpdateController::class,'checkUpdate'])->name('update')->middleware('admin');
	  Route::post('/admin/update/install',[UpdateController::class,'installUpdate'])->name('update.install')->middleware('admin');
	  
	  Route::get('/admin/manage-themes',[ThemesController::class,'index'])->name('admin.manage-themes')->middleware('admin');
	  Route::get('/admin/active-themes/{name}',[ThemesController::class,'activeTheme'])->name('themes.active')->middleware('admin');
	  Route::get('/admin/active-index-themes/{name}',[ThemesController::class,'activeIndexTheme'])->name('themes.activeIndex')->middleware('admin');
	  Route::post('/admin/download-themes',[ThemesController::class,'downloadTheme'])->name('themes.download')->middleware('admin');
	  Route::post('/admin/delete-themes',[ThemesController::class,'deleteTheme'])->name('themes.delete')->middleware('admin');
	  Route::post('/admin/delete-index-themes',[ThemesController::class,'deleteIndexTheme'])->name('themes.deleteIndex')->middleware('admin');

	  Route::get('/admin/manage-users',[ManageUsersController::class,'index'])->name('admin.manage-users')->middleware('admin');
	  Route::post('/admin/user/store',[ManageUsersController::class,'store'])->name('user.store')->middleware('admin');
	  Route::post('/admin/user/updatePlan/{id}',[ManageUsersController::class,'updatePlan'])->name('admin.users.updatePlan')->middleware('admin');
	  Route::delete('/admin/user/delete/{id}',[ManageUsersController::class,'delete'])->name('user.delete')->middleware('admin');
	  Route::get('admin/user/edit',[ManageUsersController::class,'edit'])->name('user.edit')->middleware('admin');
	  Route::post('admin/user/update',[ManageUsersController::class,'update'])->name('user.update')->middleware('admin');
	  
	  Route::get('/login-as-user/{id}', [ManageUsersController::class, 'loginAsUser'])->name('admin.loginAsUser')->middleware('admin');
	  Route::get('/back-to-admin', [ManageUsersController::class, 'backToAdmin'])->name('admin.backToAdmin')->middleware('admin');
	  
	  Route::get('/checkout/{planId}', [PaymentController::class, 'checkout'])->name('payments.checkout');
	  Route::post('/checkout/{planId}', [PaymentController::class, 'process'])->name('payments.process');
	  Route::get('/trial/{planId}', [PaymentController::class, 'trial'])->name('payments.trial');
	  Route::post('/trial/{planId}', [PaymentController::class, 'trialProcess'])->name('payments.process.trial');
	  Route::get('/payment/callback', [PaymentController::class, 'callback'])->name('payments.callback');
	  Route::post('/stripe/webhook', [StripeController::class, 'handleWebhook'])->name('stripe.webhook');
	  Route::post('/paypal/ipn', [PayPalController::class, 'handleIpn'])->name('paypal.ipn');
	  Route::post('/payments/paymob/process', [PaymobController::class, 'process'])->name('payments.paymob.process');
	  Route::post('/payments/paymob/callback', [PaymobController::class, 'callback'])->name('payments.paymob.callback');
	  
	  Route::get('/admin/orders', [OrderController::class, 'index'])->name('admin.orders.index')->middleware('admin');
	  Route::post('/admin/orders', [OrderController::class, 'changeStatus'])->name('admin.orders.status')->middleware('admin');

	  Route::get('/messages-history',[MessagesHistoryController::class,'index'])->name('messages.history');
	  Route::post('/resend-message',[MessagesHistoryController::class,'resend'])->name('resend.message');
	  Route::post('/delete-messages',[MessagesHistoryController::class,'deleteAll'])->name('delete.messages');
	  
	  Route::get('/admin/payments', [PaymentGatewayController::class, 'index'])->name('admin.payments.index')->middleware('admin');
	  Route::post('/admin/payments/update', [PaymentGatewayController::class, 'update'])->name('admin.payments.update')->middleware('admin');
	  
	  Route::get('/permission-denied', function () { return view('theme::pages.permission'); })->name('permission.denied');

	});

	Route::middleware('guest')->group(function(){

		Route::get('/login',[LoginController::class,'index'])->name('login');
		Route::get('/register',[RegisterController::class,'index'])->name('register');
		Route::post('/register',[RegisterController::class,'store'])->name('register');
		Route::post('/login',[LoginController::class,'store'])->name('login')->middleware('throttle:5,1');
		Route::get('password/reset', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
		Route::post('password/email', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
		Route::get('password/reset/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
		Route::post('password/reset', [PasswordResetController::class, 'reset'])->name('password.update');
	});
	Route::match(['get', 'post'], '/logout', LogoutController::class)->name('logout');
	Route::get('/install', [SettingController::class,'install'])->name('setting.install_app');
	Route::post('/install', [SettingController::class,'install'])->name('settings.install_app');
	Route::post('/payment/callback', [PaymentController::class, 'callback'])->name('payments.callback');

	Route::post('/settings/check_database_connection',[SettingController::class,'test_database_connection'])->name('connectDB');
	Route::post('/settings/activate_license',[SettingController::class,'activate_license'])->name('activateLicense');
});

?>