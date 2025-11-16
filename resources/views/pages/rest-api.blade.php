<x-layout-dashboard title="Rest Api">
    <div class="space-y-8">
        <div class="rounded-3xl border border-slate-800/60 bg-slate-900/60 p-5">
            <h2 class="text-xl font-semibold text-white">Rest API</h2>
            <div class="mt-4 rounded-2xl border border-slate-800/70 overflow-hidden">
                <table class="min-w-full divide-y divide-slate-800/80 text-sm">
                    <thead class="bg-slate-900/60 text-xs uppercase tracking-[0.25em] text-slate-500">
                        <tr>
                            <th class="px-4 py-3 text-left">Method</th>
                            <th class="px-4 py-3 text-left">POST & GET ( All support )</th>
                        </tr>
                        <tr>
                            <th class="px-4 py-3 text-left">Type</th>
                            <th class="px-4 py-3 text-left">JSON</th>
                        </tr>
                        <tr>
                            <th class="px-4 py-3 text-left">RESPONSE</th>
                            <th class="px-4 py-3 text-left">{ status : boolean , msg : 'text' } (JSON)</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-800/60 bg-slate-900/60 p-5">
            <p class="text-slate-300">Rest Api</p>
            <div class="mt-4">
                <ul class="flex flex-wrap gap-2 text-xs">
                    <li><a class="rounded-xl border border-slate-800/60 px-3 py-2 text-slate-300 hover:text-white" data-bs-toggle="pill" data-bs-target="#textMessage">Text Message</a></li>
                    <li><a class="rounded-xl border border-slate-800/60 px-3 py-2 text-slate-300 hover:text-white" data-bs-toggle="pill" data-bs-target="#imageMessage">Media Message</a></li>
                    <li><a class="rounded-xl border border-slate-800/60 px-3 py-2 text-slate-300 hover:text-white" data-bs-toggle="pill" data-bs-target="#buttonMessage">Button Message</a></li>
                    <li><a class="rounded-xl border border-slate-800/60 px-3 py-2 text-slate-300 hover:text-white" data-bs-toggle="pill" data-bs-target="#templateMessage">Template Message</a></li>
                    <li><a class="rounded-xl border border-slate-800/60 px-3 py-2 text-slate-300 hover:text-white" data-bs-toggle="pill" data-bs-target="#listMessage">List Message</a></li>
                    <li><a class="rounded-xl border border-slate-800/60 px-3 py-2 text-slate-300 hover:text-white" data-bs-toggle="pill" data-bs-target="#generateQr">Generate Qr</a></li>
                    <li><a class="rounded-xl border border-slate-800/60 bg-brand-neon/15 px-3 py-2 text-brand-neon" data-bs-toggle="pill" data-bs-target="#webhook">Webhook</a></li>
                </ul>

                <div class="tab-content mt-4">
                    <div class="tab-pane fade show" id="textMessage">
<pre class="hljs" style="display:block;overflow-x:auto;padding:0.5em;background-color:#0b1220;color:#dce3f1">&lt;?php

    $data = [
        'api_key' =&gt; '{{Auth::user()->api_key}}',
        'sender' =&gt; 'Sender',
        'number' =&gt; 'receiver',
        'message' =&gt; 'Your message'
    ];
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL =&gt; {{url('/')}}/send-message,
      CURLOPT_RETURNTRANSFER =&gt; true,
      CURLOPT_ENCODING =&gt; '',
      CURLOPT_MAXREDIRS =&gt; 10,
      CURLOPT_TIMEOUT =&gt; 0,
      CURLOPT_FOLLOWLOCATION =&gt; true,
      CURLOPT_HTTP_VERSION =&gt; CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST =&gt; 'POST',
      CURLOPT_POSTFIELDS =&gt; json_encode($data),
      CURLOPT_HTTPHEADER =&gt; array('Content-Type: application/json'),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    echo $response;
?&gt;</pre>
                    </div>

                    <div class="tab-pane fade" id="imageMessage">
<pre class="hljs" style="display:block;overflow-x:auto;padding:0.5em;background-color:#0b1220;color:#dce3f1">&lt;?php
    $data = [
        'api_key' =&gt; '{{Auth::user()->api_key}}',
        'sender' =&gt; 'Sender',
        'number' =&gt; 'receiver',
        'message' =&gt; 'Your caption',
        'url' =&gt; 'Url Media'
        'type' =&gt; 'audio / video / image / pdf / xls /xlsx /doc /docx /zip' //Choose One
    ];
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL =&gt; {{url('/')}}/send-media,
      CURLOPT_RETURNTRANSFER =&gt; true,
      CURLOPT_ENCODING =&gt; '',
      CURLOPT_MAXREDIRS =&gt; 10,
      CURLOPT_TIMEOUT =&gt; 0,
      CURLOPT_FOLLOWLOCATION =&gt; true,
      CURLOPT_HTTP_VERSION =&gt; CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST =&gt; 'POST',
      CURLOPT_POSTFIELDS =&gt; json_encode($data),
      CURLOPT_HTTPHEADER =&gt; array('Content-Type: application/json'),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    echo $response;
?&gt;</pre>
                    </div>

                    <div class="tab-pane fade" id="buttonMessage">
<pre class="hljs" style="display:block;overflow-x:auto;padding:0.5em;background-color:#0b1220;color:#dce3f1">&lt;?php
    $data = [
        'api_key' =&gt; '{{Auth::user()->api_key}}',
        'sender' =&gt; 'Sender',
        'number' =&gt; 'receiver',
        'message' =&gt; 'Your message',
        'footer' =&gt; 'Your footer message',
        'image' =&gt; 'URL image ', //OPTIONAL
        'button1' =&gt; 'Button 1 ', //REQUIRED ( Button minimal 1 )
        'button2' =&gt; 'Button 2', //OPTIONAL
        'button3' =&gt; 'Button 3', //OPTIONAL
    ];
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL =&gt; {{url('/')}}/send-button,
      CURLOPT_RETURNTRANSFER =&gt; true,
      CURLOPT_ENCODING =&gt; '',
      CURLOPT_MAXREDIRS =&gt; 10,
      CURLOPT_TIMEOUT =&gt; 0,
      CURLOPT_FOLLOWLOCATION =&gt; true,
      CURLOPT_HTTP_VERSION =&gt; CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST =&gt; 'POST',
      CURLOPT_POSTFIELDS =&gt; json_encode($data),
      CURLOPT_HTTPHEADER =&gt; array('Content-Type: application/json'),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    echo $response;
?&gt;</pre>
                    </div>

                    <div class="tab-pane fade" id="templateMessage">
<pre class="hljs" style="display:block;overflow-x:auto;padding:0.5em;background-color:#0b1220;color:#dce3f1">&lt;?php
    $data = [
        'api_key' =&gt; '{{Auth::user()->api_key}}',
        'sender' =&gt; 'Sender',
        'number' =&gt; 'receiver',
        'message' =&gt; 'Your message',
        'footer' =&gt; 'Your footer message',
        'image' =&gt; 'URL image ', //OPTIONAL
        'template1' =&gt; 'template 1 ', //REQUIRED ( template minimal 1 )
        'template2' =&gt; 'template 2', //OPTIONAL
        'template3' =&gt; 'template 3', //OPTIONAL
    ];
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL =&gt; {{url('/')}}/send-template,
      CURLOPT_RETURNTRANSFER =&gt; true,
      CURLOPT_ENCODING =&gt; '',
      CURLOPT_MAXREDIRS =&gt; 10,
      CURLOPT_TIMEOUT =&gt; 0,
      CURLOPT_FOLLOWLOCATION =&gt; true,
      CURLOPT_HTTP_VERSION =&gt; CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST =&gt; 'POST',
      CURLOPT_POSTFIELDS =&gt; json_encode($data),
      CURLOPT_HTTPHEADER =&gt; array('Content-Type: application/json'),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    echo $response;
?&gt;</pre>
                    </div>

                    <div class="tab-pane fade" id="listMessage">
<pre class="hljs" style="display:block;overflow-x:auto;padding:0.5em;background-color:#0b1220;color:#dce3f1">&lt;?php
    $data = [
        'api_key' =&gt; '{{Auth::user()->api_key}}',
        'sender' =&gt; 'Sender',
        'number' =&gt; 'receiver',
        'message' =&gt; 'Your message',
        'footer' =&gt; 'Your footer message',
        'name' =&gt; 'Name List ',
        'title' =&gt; 'Title List ',
        'list1' =&gt; 'list 1 ', //REQUIRED ( list minimal 1 )
        'list2' =&gt; 'list 2', //OPTIONAL
        'list3' =&gt; 'list 3', //OPTIONAL
        'list4' =&gt; 'list 4', //OPTIONAL
        'list5' =&gt; 'list 5', //OPTIONAL
    ];
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL =&gt; {{url('/')}}/send-list,
      CURLOPT_RETURNTRANSFER =&gt; true,
      CURLOPT_ENCODING =&gt; '',
      CURLOPT_MAXREDIRS =&gt; 10,
      CURLOPT_TIMEOUT =&gt; 0,
      CURLOPT_FOLLOWLOCATION =&gt; true,
      CURLOPT_HTTP_VERSION =&gt; CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST =&gt; 'POST',
      CURLOPT_POSTFIELDS =&gt; json_encode($data),
      CURLOPT_HTTPHEADER =&gt; array('Content-Type: application/json'),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    echo $response;
?&gt;</pre>
                    </div>

                    <div class="tab-pane fade" id="generateQr">
<pre class="hljs" style="display:block;overflow-x:auto;padding:0.5em;background-color:#0b1220;color:#dce3f1">&lt;?php
    //Type respon (json)
    // { "status" : "processing", "message" : "processing" }
    // { "status" : true, "message" : "Already Connected" }
    // { "status" : false, "qrcode" : "qr url",  "message" : "Please Scan qrcode" }

    $data = [
        'api_key' =&gt; '{{Auth::user()->api_key}}',
        'number' =&gt; 'Number', //the number to connect
    ];
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL =&gt; {{url('/')}}/generate-qr,
      CURLOPT_RETURNTRANSFER =&gt; true,
      CURLOPT_ENCODING =&gt; '',
      CURLOPT_MAXREDIRS =&gt; 10,
      CURLOPT_TIMEOUT =&gt; 0,
      CURLOPT_FOLLOWLOCATION =&gt; true,
      CURLOPT_HTTP_VERSION =&gt; CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST =&gt; 'POST',
      CURLOPT_POSTFIELDS =&gt; json_encode($data),
      CURLOPT_HTTPHEADER =&gt; array('Content-Type: application/json'),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    echo $response;
?&gt;</pre>
                    </div>

                    <div class="tab-pane fade show active" id="webhook">
<pre class="php" style="display:block;overflow-x:auto;padding:0.5em;background-color:#0b1220;color:#dce3f1">&lt;?php 

header('content-type: application/json');
$data = json_decode(file_get_contents('php://input'), true);
file_put_contents('whatsapp.txt', '[' . date('Y-m-d H:i:s') . "]\n" . json_encode($data) . "\n\n", FILE_APPEND);
$message = strtolower($data['message']);
$from = strtolower($data['from']);
$respon = false;

function sayHello(){ return ['text' => 'Halloooo!']; }
function gambar(){ return ['image' => ['url' => 'https://seeklogo.com/images/W/whatsapp-logo-A5A7F17DC1-seeklogo.com.png'], 'caption' => 'Logo whatsapp!']; }
function button(){
    $buttons = [
        ['buttonId' => 'id1', 'buttonText' => ['displayText' => 'BUTTON 1'], 'type' => 1],
        ['buttonId' => 'id2', 'buttonText' => ['displayText' => 'BUTTON 2'], 'type' => 1],
        ['buttonId' => 'id3', 'buttonText' => ['displayText' => 'BUTTON 3'], 'type' => 1],
    ];
    return ['text' => 'HOLA, INI ADALAH PESAN BUTTON', 'footer' => 'ini pesan footer', 'buttons' => $buttons, 'headerType' => 1];
}
function lists(){
    $sections = [[ 'title' => 'This is List menu', 'rows' => [ ['title'=>'List 1','description'=>'this is list one'], ['title'=>'List 2','description'=>'this is list two'] ] ]];
    return ['text' => 'This is a list', 'title' => 'Title Chat', 'buttonText' => 'Select what will you do?', 'sections' => $sections];
}
if($message === 'hai'){ $respon = sayHello(); }
else if($message === 'gambar'){ $respon = gambar(); }
else if($message === 'tes button'){ $respon = button(); }
else if($message === 'lists msg'){ $respon = lists(); }
echo json_encode($respon);
?&gt;</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout-dashboard>