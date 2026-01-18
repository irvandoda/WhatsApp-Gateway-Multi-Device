<div class="tab-pane fade " id="userinfo" role="tabpanel">
	<div class="d-flex mb-3 gap-3">
		<div>
			<span class="badge bg-label-primary rounded-2 p-2">
			<i class="ti tabler-user icon-32px"></i>
			</span>
		</div>
		<div>
			<h4 class="mb-0 lh-sm">
				<span class="align-middle">User Info API</span>
			</h4>
			<small>Api Docs Geting User Information</small>
		</div>
	</div>
	<div id="accordionPayment" class="accordion">
		<div class="card">
			<div class="card-body">
    <p>Method : <code class="text-success">POST</code> | <code class="text-primary">GET</code></p>
    <p>Endpoint: <code>{{ url('/') }}/info-user</code></p>

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
                <td>username</td>
                <td>string</td>
                <td>Yes</td>
                <td>The username must not contain symbols</td>
            </tr>
        </tbody>
    </table>
    <br>
    <p>Example JSON Request</p>
<pre class="bg-dark rounded text-white"><code>{
    "api_key": "123456789",
    "username": "magd"
}</code></pre>
    <p>Example URL Request</p>
    <pre class="bg-dark rounded text-white"><code>{{ url('/') }}/info-user?api_key=1234567890&username=magd</code></pre>
    <p>Example JSON Response</p>
<pre class="bg-dark rounded text-white"><code>{
    "status":true,
    "info":{
        "id":1,
        "username":"magd",
        "email":"ttmttxx@xx.com",
        "email_verified_at":null,
        "api_key":"ORtg0LwKMZoadhXuwDYg40frCkfaul",
        "chunk_blast":0,
        "level":"admin",
        "status":"active",
        "limit_device":10,
        "active_subscription":"active",
        "subscription_expired":"2024-09-14 23:11:31",
        "created_at":"2024-08-15T16:11:31.000000Z",
        "updated_at":"2024-08-15T16:11:31.000000Z",
        "two_factor_enabled":0,
        "two_factor_secret":null,
        "recovery_codes":null
    }
}</code></pre>

</div>
</div>
</div>
</div>