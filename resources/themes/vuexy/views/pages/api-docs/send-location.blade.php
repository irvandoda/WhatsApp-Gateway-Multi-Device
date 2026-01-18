<div class="tab-pane fade" id="sendlocation" role="tabpanel">
  <div class="d-flex mb-3 gap-3">
    <div>
      <span class="badge bg-label-primary rounded-2 p-2">
        <i class="ti tabler-map-pin icon-32px"></i>
      </span>
    </div>
    <div>
      <h4 class="mb-0 lh-sm">
        <span class="align-middle">Send Location API</span>
      </h4>
      <small>Api Docs Sending Location Messages</small>
    </div>
  </div>
  <div id="accordionPayment" class="accordion">
    <div class="card">
      <div class="card-body">
        <p>Method : <code class="text-success">POST</code> | <code class="text-primary">GET</code></p>
        <p>Endpoint: <code>{{ url('/') }}/send-location</code></p>

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
              <td>number</td>
              <td>string</td>
              <td>Yes</td>
              <td>Recipient number ex 72888xxxx | 62888xxxx</td>
            </tr>
            <tr>
              <td>latitude</td>
              <td>string</td>
              <td>Yes</td>
              <td>Latitude ex 24.121231</td>
            </tr>
            <tr>
              <td>longitude</td>
              <td>string</td>
              <td>Yes</td>
              <td>Longitude ex 55.1121221</td>
            </tr>
            <tr>
              <td>msgid</td>
              <td>string</td>
              <td>No</td>
              <td>Quoted message ID to reply to</td>
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
    "sender": "62888xxxx",
    "number": "62888xxxx",
    "latitude": "24.121231",
    "longitude": "55.1121221"
}</code></pre>

        <p>URL Request</p>
<pre class="bg-dark rounded text-white"><code>{{ url('/') }}/send-location?api_key=1234567890&amp;sender=62888xxxx&amp;number=62888xxxx&amp;latitude=24.121231&amp;longitude=55.1121221</code></pre>

        <p>JSON Response</p>
<pre class="bg-dark rounded text-white"><code>{
    "status": true,
    "msg": "Message sent successfully!"
}</code></pre>

        <hr class="my-4">

        <h6>Example With <code>msgid</code> (reply)</h6>
        <p>JSON Request</p>
<pre class="bg-dark rounded text-white"><code>{
    "api_key": "1234567890",
    "sender": "62888xxxx",
    "number": "62888xxxx",
    "latitude": "24.121231",
    "longitude": "55.1121221",
    "msgid": "3EB031F83D74BF480052B9"
}</code></pre>

        <p>URL Request</p>
<pre class="bg-dark rounded text-white"><code>{{ url('/') }}/send-location?api_key=1234567890&amp;sender=62888xxxx&amp;number=62888xxxx&amp;latitude=24.121231&amp;longitude=55.1121221&amp;msgid=3EB031F83D74BF480052B9</code></pre>

        <hr class="my-4">

        <h6>Example With <code>full</code></h6>
        <p>JSON Request</p>
<pre class="bg-dark rounded text-white"><code>{
    "api_key": "1234567890",
    "sender": "62888xxxx",
    "number": "62888xxxx",
    "latitude": "24.121231",
    "longitude": "55.1121221",
    "full": 1
}</code></pre>

        <p>URL Request</p>
<pre class="bg-dark rounded text-white"><code>{{ url('/') }}/send-location?api_key=1234567890&amp;sender=62888xxxx&amp;number=62888xxxx&amp;latitude=24.121231&amp;longitude=55.1121221&amp;full=1</code></pre>

        <p>JSON Response</p>
<pre class="bg-dark rounded text-white"><code>{
  "status": true,
  "data": {
    "key": {
      "remoteJid": "62888xxxx@c.us",
      "fromMe": true,
      "id": "3EB03AC0A048XXXXXXXXX"
    },
    "message": {
      "locationMessage": {
        "degreesLatitude": 24.121231,
        "degreesLongitude": 55.1121221
      }
    },
    "messageTimestamp": "1755630180"
  }
}</code></pre>
      </div>
    </div>
  </div>
</div>
