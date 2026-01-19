<div class="tab-pane fade  " id="sendbutton" role="tabpanel">
	<div class="d-flex mb-3 gap-3">
		<div>
			<span class="badge bg-label-primary rounded-2 p-2">
			<i class="ti tabler-square-plus icon-32px"></i>
			</span>
		</div>
		<div>
			<h4 class="mb-0 lh-sm">
				<span class="align-middle">Send Button API</span>
			</h4>
			<small>Api Docs Sending Button Messages</small>
		</div>
	</div>
	<div id="accordionPayment" class="accordion">
		<div class="card">
			<div class="card-body">
     <p>Method : <code class="text-success">POST</code> | <code class="text-primary">GET</code></p>
     <p>Endpoint: <code>{{ url('/') }}/send-button</code></p>

     <p>Request Body : (JSON If POST)
     <table class="table">
		<thead>
			<tr>
				<th>Parameter</th>
				<th>Type</th>
				<th>Required</th>
				<th>Description</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>api_key</td>
				<td>string</td>
				<td>Yes</td>
				<td>API Key</td>
			</tr>
			<tr>
				<td>sender</td>
				<td>string</td>
				<td>Yes</td>
				<td>Number of your device</td>
			</tr>
			<tr>
				<td>number</td>
				<td>string</td>
				<td>Yes</td>
				<td>Recipient number (e.g. 72888xxxx or 62888xxxx)</td>
			</tr>
			<tr>
				<td>message</td>
				<td>string</td>
				<td>Yes</td>
				<td>Text of the message</td>
			</tr>
			<tr>
				<td>button</td>
				<td>array</td>
				<td>Yes</td>
				<td>
					Array of buttons (max 5). Each item must include:<br>
					<strong>type</strong>: <code>reply</code>, <code>call</code>, <code>url</code>, or <code>copy</code><br>
					<strong>displayText</strong>: Button label<br>
					<strong>phoneNumber</strong>: Required if type is <code>call</code><br>
					<strong>url</strong>: Required if type is <code>url</code><br>
					<strong>copyCode</strong>: Required if type is <code>copy</code>
				</td>
			</tr>
			<tr>
				<td>footer</td>
				<td>string</td>
				<td>No</td>
				<td>Footer text of the message</td>
			</tr>
			<tr>
				<td>url</td>
				<td>string</td>
				<td>Yes</td>
				<td>Image or video URL (must media attachment)</td>
			</tr>
		</tbody>
	</table>
	<br>
     <p>Example json</p>
<pre class="bg-dark rounded text-white"><code>{
    "sender" : "6281222xxxxxx",
    "api_key" : "yourapikey",
    "number" : "201111xxxxxx",
    "url" : "https://imageurl.com",
    "footer" : "optional",
    "message" : "Hello magd, this is a button message",
    "button" : [
        {
            "type": "reply",
            "displayText": "Reply Button"
        },
        {
            "type": "call",
            "displayText": "Call Button",
            "phoneNumber" : "6281222xxxxxx"
        },
        {
            "type": "url",
            "displayText": "URL Button",
            "url" : "https://google.com"
        },
        {
            "type": "copy",
            "displayText": "Copy Button",
            "copyText" : "123123"
        }
    ]
}</code></pre>
     <p> Example URL</p>
<pre class="bg-dark rounded text-white"><code>{{ url('/') }}/send-button?sender=6281222xxxxxx&api_key=yourapikey&number=201111xxxxxx&url=https://imageurl.com&footer=optional&message=Hello magd, this is a button message&button[0][type]=reply&button[0][displayText]=Reply Button&button[1][type]=call&button[1][displayText]=Call Button&button[1][phoneNumber]=6281222xxxxxx&button[2][type]=url&button[2][displayText]=URL Button&button[2][url]=https://google.com&button[3][type]=copy&button[3][displayText]=Copy Button&button[3][copyText]=123123</code></pre>
    <p>Example JSON Response</p>
<pre class="bg-dark rounded text-white"><code>{
    "status":true,
    "msg":"Message sent successfully!"
}</code></pre>

 </div>
</div>
</div>
</div>
