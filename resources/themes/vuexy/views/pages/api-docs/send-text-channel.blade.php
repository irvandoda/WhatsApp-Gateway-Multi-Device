<div class="tab-pane fade show" id="textchannel" role="tabpanel">
	<div class="d-flex mb-3 gap-3">
		<div>
			<span class="badge bg-label-primary rounded-2 p-2">
				<i class="ti tabler-speakerphone icon-32px"></i>
			</span>
		</div>
		<div>
			<h4 class="mb-0 lh-sm">
				<span class="align-middle">Send Text To Channel API</span>
			</h4>
			<small>Api Docs Sending Text Messages To Channel</small>
		</div>
	</div>
	<div id="accordionPayment" class="accordion">
		<div class="card">
			<div class="card-body">
				<p>Method : <code class="text-success">POST</code> | <code class="text-primary">GET</code></p>
				<p>Endpoint: <code>{{ url('/') }}/send-text-channel</code></p>
				<p>Request Body : (JSON If POST)</p>
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
							<td>url</td>
							<td>string</td>
							<td>Yes</td>
							<td>Channel URL ex https://whatsapp.com/channel/ABCDEF123456</td>
						</tr>
						<tr>
							<td>message</td>
							<td>string</td>
							<td>Yes</td>
							<td>Message to be sent</td>
						</tr>
						<tr>
							<td>footer</td>
							<td>string</td>
							<td>No</td>
							<td>Footer under message</td>
						</tr>
						<tr>
							<td>full</td>
							<td>number</td>
							<td>No</td>
							<td>Show full response from WhatsApp</td>
						</tr>
					</tbody>
				</table>
				<br>

				<h6>Example Without <code>full</code></h6>
				<p>JSON Request</p>
<pre class="bg-dark rounded text-white"><code>{
    "api_key": "1234567890",
    "sender": "6281222xxxxxx",
    "url": "https://whatsapp.com/channel/ABCDEF123456",
    "message": "Hello World",
    "footer": "Sent via mpwa"
}</code></pre>

				<p>URL Request</p>
<pre class="bg-dark rounded text-white"><code>{{ url('/') }}/send-text-channel?api_key=1234567890&sender=201111xxxxxx&url=https://whatsapp.com/channel/ABCDEF123456&message=Hello%20World&footer=Sent%20via%20mpwa</code></pre>

				<p>JSON Response</p>
<pre class="bg-dark rounded text-white"><code>{
    "status": true,
    "msg": "Message sent successfully!"
}</code></pre>

				<hr class="my-4">

				<h6>Example With <code>full</code></h6>
				<p>JSON Request</p>
<pre class="bg-dark rounded text-white"><code>{
    "api_key": "1234567890",
    "sender": "6281222xxxxxx",
    "url": "https://whatsapp.com/channel/ABCDEF123456",
    "message": "Hello World",
    "footer": "Sent via mpwa",
    "full": 1
}</code></pre>

				<p>URL Request</p>
<pre class="bg-dark rounded text-white"><code>{{ url('/') }}/send-text-channel?api_key=1234567890&sender=201111xxxxxx&url=https://whatsapp.com/channel/ABCDEF123456&message=Hello%20World&footer=Sent%20via%20mpwa&full=1</code></pre>

				<p>JSON Response</p>
<pre class="bg-dark rounded text-white"><code>{
  "status": true,
  "data": {
    "tag": "ack",
    "attrs": {
      "from": "120363148xxxxxxxx@newsletter",
      "class": "message",
      "id": "182xx.7666-xxx",
      "server_id": "107",
      "t": "1755628624"
    }
  }
}</code></pre>
			</div>
		</div>
	</div>
</div>
