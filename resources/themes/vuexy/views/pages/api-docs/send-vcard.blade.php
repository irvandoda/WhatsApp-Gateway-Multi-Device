<div class="tab-pane fade" id="sendvcard" role="tabpanel">
  <div class="d-flex mb-3 gap-3">
    <div>
      <span class="badge bg-label-primary rounded-2 p-2">
        <i class="ti tabler-id icon-32px"></i>
      </span>
    </div>
    <div>
      <h4 class="mb-0 lh-sm">
        <span class="align-middle">Send VCard API</span>
      </h4>
      <small>Api Docs Sending VCard Messages</small>
    </div>
  </div>
  <div id="accordionPayment" class="accordion">
    <div class="card">
      <div class="card-body">
        <p>Method : <code class="text-success">POST</code> | <code class="text-primary">GET</code></p>
        <p>Endpoint: <code>{{ url('/') }}/send-vcard</code></p>

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
              <td>name</td>
              <td>string</td>
              <td>Yes</td>
              <td>Contact name ex Magd Almuntaser</td>
            </tr>
            <tr>
              <td>phone</td>
              <td>string</td>
              <td>Yes</td>
              <td>Contact phone number ex 6281222xxxxxx</td>
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
    "sender": "6281222xxxxxx",
    "number": "201111xxxxxx",
    "name": "magd",
    "phone": "6281222xxxxxx"
}</code></pre>

        <p>URL Request</p>
<pre class="bg-dark rounded text-white"><code>{{ url('/') }}/send-vcard?api_key=1234567890&amp;sender=6281222xxxxxx&amp;number=201111xxxxxx&amp;name=magd&amp;phone=6281222xxxxxx</code></pre>

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
    "sender": "6281222xxxxxx",
    "number": "201111xxxxxx",
    "name": "magd",
    "phone": "6281222xxxxxx",
    "msgid": "3EB031F83D74BF480052B9"
}</code></pre>

        <p>URL Request</p>
<pre class="bg-dark rounded text-white"><code>{{ url('/') }}/send-vcard?api_key=1234567890&amp;sender=6281222xxxxxx&amp;number=201111xxxxxx&amp;name=magd&amp;phone=6281222xxxxxx&amp;msgid=3EB031F83D74BF480052B9</code></pre>

        <hr class="my-4">

        <h6>Example With <code>full</code></h6>
        <p>JSON Request</p>
<pre class="bg-dark rounded text-white"><code>{
    "api_key": "1234567890",
    "sender": "6281222xxxxxx",
    "number": "201111xxxxxx",
    "name": "magd",
    "phone": "6281222xxxxxx",
    "full": 1
}</code></pre>

        <p>URL Request</p>
<pre class="bg-dark rounded text-white"><code>{{ url('/') }}/send-vcard?api_key=1234567890&amp;sender=6281222xxxxxx&amp;number=201111xxxxxx&amp;name=magd&amp;phone=6281222xxxxxx&amp;full=1</code></pre>

        <p>JSON Response</p>
<pre class="bg-dark rounded text-white"><code>{
  "status": true,
  "data": {
    "key": {
      "remoteJid": "201111xxxxxx@c.us",
      "fromMe": true,
      "id": "3EB0E8D243166XXXXXXX"
    },
    "message": {
      "contactMessage": {
        "vcard": "BEGIN:VCARD\nVERSION:3.0\nFN:magd\nTEL;type=CELL;type=VOICE;waid=6281222xxxxxx:+6281222xxxxxx\nEND:VCARD"
      }
    },
    "messageTimestamp": "1755630412"
  }
}</code></pre>
      </div>
    </div>
  </div>
</div>
