<div class="tab-pane fade" id="createdevice" role="tabpanel">
    <h3>Create Device API</h3>
    <p>Method : <code class="text-success">POST</code> | <code class="text-primary">GET</code></p>
    <p>Endpoint: <code>{{ env('APP_URL') }}/create-device</code></p>

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
                <td>User API Key</td>
            </tr>
            <tr>
                <td>sender</td>
                <td>string</td>
                <td>Yes</td>
                <td>Sender ID (must be unique and at least 8 characters)</td>
            </tr>
            <tr>
                <td>urlwebhook</td>
                <td>string</td>
                <td>No</td>
                <td>Webhook URL for incoming message callbacks</td>
            </tr>
        </tbody>
    </table>
    <br>
    <p>Example JSON Request</p>
    <pre class="bg-dark text-white">
<code>
{
  "api_key": "1234567890",
  "sender": "6285747476308",
  "urlwebhook": "https://yourdomain.com/webhook"
}
</code>
    </pre>
    <p>Example URL Request</p>
    <pre class="bg-dark text-white">
<code class="json">
{{ env('APP_URL') }}/create-device?api_key=1234567890&sender=6281234567890&urlwebhook=https://yourdomain.com/webhook
</code>
    </pre>
</div>
