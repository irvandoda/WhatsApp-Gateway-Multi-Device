<div class="tab-pane fade " id="examplewebhook" role="tabpanel">
	<div class="d-flex mb-3 gap-3">
		<div>
			<span class="badge bg-label-primary rounded-2 p-2">
			<i class="ti tabler-api-app icon-32px"></i>
			</span>
		</div>
		<div>
			<h4 class="mb-0 lh-sm">
				<span class="align-middle">Webhook Example</span>
			</h4>
			<small>Make Webhook Like AutoResponder</small>
		</div>
	</div>
	<div id="accordionPayment" class="accordion">
		<div class="card">
			<div class="card-body">
    <p>Webhook is a feature that allows you to receive a callback from our server when a message is incoming to your
        device.
        You can use this feature for made a dinamic chatbot or whatever you want. </p>
    <p>We will send a POST request to your webhook url with a JSON body. Here is an example of the JSON body we will
        send:</p>
<pre class="bg-dark rounded text-white"><code>{
    "device" : "your sender/device"
    "message" : "message",
    "from" : "the number of the whatsapp sender",
    "name" : "the name of the sender",
    "participant" : "sender number if group",
    "ppUrl" : "url profile picture sender",
    "media" : [
        "caption" : "caption, equal to message",
        "fileName" : "xxxx.xx",
        "stream" : [
            "type" : "Buffer",
            "data" : "xxxx"
        ]
    ],
    "mimetype" : "image\/jpeg" // depends to media type, could be image,document,audio etc
}</code></pre>
      <br>
      <p>For example webhook you can see in <a href="https://github.com/TTMTT/webhook-mpwa-example.git" target="_blank"> <span class="badge bg-outline-primary">Here</span></a></p>
      

</div>
</div>
</div>
</div>
