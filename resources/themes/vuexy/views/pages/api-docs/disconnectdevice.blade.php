<div class="tab-pane fade " id="disconnectdevice" role="tabpanel">
	<div class="d-flex mb-3 gap-3">
		<div>
			<span class="badge bg-label-primary rounded-2 p-2">
			<i class="ti tabler-plug-off icon-32px"></i>
			</span>
		</div>
		<div>
			<h4 class="mb-0 lh-sm">
				<span class="align-middle">Disconnect device</span>
			</h4>
			<small>Api Docs Disconnecting device</small>
		</div>
	</div>
	<div id="accordionPayment" class="accordion">
		<div class="card">
			<div class="card-body">
    <p>Method : <code class="text-success">POST</code>
    <p>Endpoint: <code>{{ url('/') }}/logout-device</code></p>

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
                <td>sender</td>
                <td>string</td>
                <td>Yes</td>
                <td>Device you want log out</td>

            </tr>
            <tr>
                <td>api_key</td>
                <td>string</td>
                <td>Yes</td>
                <td>API Key</td>
            </tr>

        </tbody>
    </table>
    <br>
    <p>Example JSON Request</p>
<pre class="bg-dark rounded text-white"><code>{
    "api_key": "123456789",
    "sender": "6281222xxxxxx"
}</code></pre>
    <p>Example JSON Response</p>
<pre class="bg-dark rounded text-white"><code>{
    "status": true,
    "message": "device disconnected "
}</code></pre>


</div>
</div>
</div>
</div>
