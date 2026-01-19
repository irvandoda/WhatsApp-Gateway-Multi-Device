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
                    <div class="battery"><i class="ti tabler-battery"></i></div>
                    <div class="network"><i class="ti tabler-signal-4g"></i></div>
                    <div class="wifi"><i class="ti tabler-wifi-2"></i></div>
                    <div class="star"><i class="ti tabler-star-filled"></i></div>
                </div>
                <div class="chat">
                    <div class="chat-container">
                        <div class="user-bar">
                            <div class="back"><i class="ti tabler-arrow-left"></i></div>
                            <div class="avatar"><img src="{{ asset('img/avatars/1.png') }}" alt="Avatar"></div>
                            <div class="name"><span>{{ __('MPWA') }}</span><span class="status">{{ __('online') }}</span></div>
                            <div class="actions more"><i class="ti tabler-dots-vertical"></i></div>
                            <div class="actions attachment"><i class="ti tabler-paperclip"></i></div>
                            <div class="actions"><i class="ti tabler-phone-filled"></i></div>
                        </div>

                        <div class="conversation" dir="ltr">
                            <div class="conversation-container">

                                <div class="message sent p-0" style="max-width: 90%; border-radius: 10px; overflow: hidden;">
                                    @if (!empty($image))
                                        <img src="{{ $image }}" alt="image" style="width: 100%; max-height: 200px; object-fit: contain;">
                                    @endif

                                    <div class="p-2 bg-white">
                                        <div style="font-size: 15px; color: #000;">{{ $message }}</div>
                                        @if(!empty($footer))
                                            <div style="font-size: 13px; color: gray;">{{ $footer }}</div>
                                        @endif
                                    </div>

                                    @foreach ($buttons as $btn)
                                        @php
                                            $display = $btn->buttonText->displayText ?? $btn->buttonText;
                                            $type = $display->type ?? 'reply';
                                            $text = $display->displayText ?? '';
                                            $icon = match($type) {
                                                'call' => 'phone-call',
                                                'copy' => 'clipboard-copy',
                                                default => 'arrow-right',
                                            };
                                        @endphp
                                        <div class="d-flex align-items-center justify-content-between border-top px-3 py-2" style="background: #fff;">
                                            <span style="color: #075e54; font-size: 15px;">{{ $text }}</span>
                                            <i class="ti tabler-{{ $icon }}" style="color: #075e54;"></i>
                                        </div>
                                    @endforeach

                                    <span class="metadata px-2 pb-1">
                                        <span class="time"></span>
                                        <span class="tick">
                                            <i class="ti tabler-checks text-primary" style="font-size: 16px;"></i>
                                        </span>
                                    </span>
                                </div>

                            </div>
                            <form class="conversation-compose">
                                <div class="emoji"><i class="ti tabler-mood-smile" style="font-size: 26px;"></i></div>
                                <input class="input-msg" placeholder="{{ __('Type a message') }}" readonly>
                                <div class="photo"><i class="ti tabler-camera"></i></div>
                                <button class="send">
                                    <div class="circle"><i class="ti tabler-send"></i></div>
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
