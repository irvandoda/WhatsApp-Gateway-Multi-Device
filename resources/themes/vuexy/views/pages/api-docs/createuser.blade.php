<div class="tab-pane fade " id="createuser" role="tabpanel">
	<div class="d-flex mb-3 gap-3">
		<div>
			<span class="badge bg-label-primary rounded-2 p-2">
			<i class="ti tabler-user-plus icon-32px"></i>
			</span>
		</div>
		<div>
			<h4 class="mb-0 lh-sm">
				<span class="align-middle">Create User API</span>
			</h4>
			<small>Api Docs Creating User</small>
		</div>
	</div>
	<div id="accordionPayment" class="accordion">
		<div class="card">
			<div class="card-body">
    <p>Method : <code class="text-success">POST</code> | <code class="text-primary">GET</code></p>
    <p>Endpoint: <code>{{ url('/') }}/create-user</code></p>

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
            <tr>
                <td>password</td>
                <td>string</td>
                <td>Yes</td>
                <td>User Password</td>
            </tr>
            <tr>
                <td>email</td>
                <td>string</td>
                <td>Yes</td>
                <td>Email</td>
            </tr>
			<tr>
                <td>expire</td>
                <td>number</td>
                <td>Yes</td>
                <td>Subscription expiry time in days ex 30</td>
            </tr>
			<tr>
                <td>limit_device</td>
                <td>number</td>
                <td>No</td>
                <td>User Allowed Numbers ex 10</td>
            </tr>
        </tbody>
    </table>
    <br>
    <p>Example JSON Request</p>
<pre class="bg-dark rounded text-white"><code>{
    "api_key": "1234567890",
    "username": "magd",
    "password": "12345678",
    "email": "ttmttxx@xx.com",
    "expire": "30",
    "limit_device": "10"
}</code></pre>
    <p>Example URL Request</p>
<pre class="bg-dark rounded text-white"><code class="json">{{ url('/') }}/create-user?api_key=1234567890&username=magd&password=12345678&email=ttmttxx@xx.com&expire=30&limit_device=10</code></pre>


</div>
</div>
</div>
</div>
