<div class="tab-pane fade " id="deviceinfo" role="tabpanel">
	<div class="d-flex mb-3 gap-3">
		<div>
			<span class="badge bg-label-primary rounded-2 p-2">
			<i class="ti tabler-device-mobile icon-32px"></i>
			</span>
		</div>
		<div>
			<h4 class="mb-0 lh-sm">
				<span class="align-middle">Device Info API</span>
			</h4>
			<small>Api Docs Geting Device Information</small>
		</div>
	</div>
	<div id="accordionPayment" class="accordion">
		<div class="card">
			<div class="card-body">
    <p>Method : <code class="text-success">POST</code> | <code class="text-primary">GET</code></p>
    <p>Endpoint: <code>{{ url('/') }}/info-devices</code></p>

    <p>Request Body : (JSON If POST) </p>
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
                <td>number</td>
                <td>string</td>
                <td>Yes</td>
                <td>any number ex 62888xxxx</td>
            </tr>
        </tbody>
    </table>
    <br>
    <p>Example JSON Request</p>
<pre class="bg-dark rounded text-white"><code>{
    "api_key": "1234567890",
    "number": "201111347197"
}</code></pre>
    <p>Example URL Request</p>
<pre class="bg-dark rounded text-white"><code class="json">{{ url('/') }}/info-devices?api_key=1234567890&number=6281222xxxxx</code></pre>
    <p>Example JSON Response</p>
<pre class="bg-dark rounded text-white"><code>{
    "status":true,
    "info":[
        {
            "id":1,
            "user_id":1,
            "body":"628122xxxxxx",
            "webhook":null,
            "status":"Disconnect",
            "created_at":"2024-08-16T11:07:27.000000Z",
            "updated_at":"2024-08-16T11:07:27.000000Z",
            "message_sent":0,
            "chatgpt":null,
            "typebot":0,
            "reject_call":0,
            "reject_message":null,
            "can_read_message":0,
            "reply_when":"Personal",
            "chatgpt_name":null,
            "chatgpt_api":null,
            "gemini_name":null,
            "gemini_api":null,
            "claude_name":null,
            "claude_api":null,
            "webhook_read":0,
            "webhook_reject_call":0,
            "webhook_typing":0,
            "bot_typing":0
        }
    ]
}</code></pre>

</div>
</div>
</div>
</div>
