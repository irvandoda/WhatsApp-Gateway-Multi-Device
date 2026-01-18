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
                        <div class="conversation">
                            <div class="conversation-container">
                                <div class="message received">
                                    {{ $keyword }}
                                    <span class="metadata"><span class="time"></span></span>
                                </div>
                                <div class="message sent">
                                    {!! nl2br($text) !!}
                                    <span class="metadata">
                                        <span class="time"></span>
                                        <span class="tick">
                                            <i class="ti tabler-checks text-primary" style="font-size: 16px; color: #4c94f1 !important;"></i>
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
