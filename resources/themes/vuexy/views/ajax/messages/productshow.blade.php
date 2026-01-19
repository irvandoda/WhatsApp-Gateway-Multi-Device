<link href="{{ asset('css/custom.css') }}" rel="stylesheet" type="text/css" />
<div class="page">
    <div class="marvel-device nexus5">
        <div class="top-bar"></div>
        <div class="sleep"></div>
        <div class="volume"></div>
        <div class="camera"></div>
        <div class="screen">
            <div class="screen-container">
                <div class="status-bar">
                    <div class="time"></div>
                    <div class="battery">
                        <i class="ti tabler-battery"></i>
                    </div>
                    <div class="network">
                        <i class="ti tabler-signal-4g"></i>
                    </div>
                    <div class="wifi">
                        <i class="ti tabler-wifi-2"></i>
                    </div>
                    <div class="star">
                        <i class="ti tabler-star-filled"></i>
                    </div>
                </div>
                <div class="chat">
                    <div class="chat-container">
                        <div class="user-bar">
                            <div class="back">
                                <i class="ti tabler-arrow-left"></i>
                            </div>
                            <div class="avatar">
                                <img src="{{ asset('img/avatars/1.png') }}" alt="Avatar">
                            </div>
                            <div class="name">
                                <span>{{ __('MPWA') }}</span>
                                <span class="status">{{ __('online') }}</span>
                            </div>
                            <div class="actions more">
                                <i class="ti tabler-dots-vertical"></i>
                            </div>
                            <div class="actions attachment">
                                <i class="ti tabler-paperclip"></i>
                            </div>
                            <div class="actions">
                                <i class="ti tabler-phone-filled"></i>
                            </div>
                        </div>

                        <div class="conversation" dir="ltr">
                            <div class="conversation-container">
                                <div class="message sent" style="max-width: 85%; padding: 0; border-radius: 14px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                                    @if(!empty($message->product->productImage->url))
                                        <img src="{{ $message->product->productImage->url }}" style="max-height: 180px; width: 100%; object-fit: cover;">
                                    @endif
                                    <div class="p-2">
                                        <div style="font-weight: bold; font-size: 14px; margin-bottom: 2px;">{{ $message->product->title ?? '' }}</div>
                                        <div style="color: #555; font-size: 13px; margin-bottom: 4px;">
                                            {{ $message->product->currencyCode ?? '' }} 
                                            {{ number_format($message->product->priceAmount1000 ?? 0, 0, ',', '.') }}
                                        </div>
                                        @if(!empty($message->footer))
                                            <div style="color: #555; font-size: 12px;">{{ $message->footer }}</div>
                                        @endif
                                    </div>
                                    <div style="background-color: #e6f2ea; text-align: center; padding: 6px; font-size: 14px; color: #1d8c3f;">
                                        {{ __('View') }}
                                    </div>
                                    <span class="metadata" style="bottom: 2px;padding: 0 8px 0 7px;">
                                        <span class="time"></span>
                                        <span class="tick">
                                            <i class="ti tabler-checks text-primary" style="font-size: 16px;"></i>
                                        </span>
                                    </span>
                                </div>
                            </div>

                            <form class="conversation-compose">
                                <div class="emoji">
                                    <i class="ti tabler-mood-smile" style="font-size: 26px;"></i>
                                </div>
                                <input class="input-msg" name="input" placeholder="{{ __('Type a message') }}" autocomplete="off" autofocus disabled>
                                <div class="photo">
                                    <i class="ti tabler-camera"></i>
                                </div>
                                <button class="send">
                                    <div class="circle">
                                        <i class="ti tabler-send"></i>
                                    </div>
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
