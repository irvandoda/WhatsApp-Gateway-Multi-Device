<div class="tab-pane fade" id="sendmedia" role="tabpanel">
  <div class="d-flex mb-3 gap-3">
    <div>
      <span class="badge bg-label-primary rounded-2 p-2">
        <i class="ti tabler-photo icon-32px"></i>
      </span>
    </div>
    <div>
      <h4 class="mb-0 lh-sm">
        <span class="align-middle">Send Media API</span>
      </h4>
      <small>Api Docs Sending Media Messages</small>
    </div>
  </div>
  <div id="accordionPayment" class="accordion">
    <div class="card">
      <div class="card-body">
        <p>Method : <code class="text-success">POST</code> | <code class="text-primary">GET</code></p>
        <p>Endpoint: <code>{{ url('/') }}/send-media</code></p>

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
              <td>media_type</td>
              <td>string</td>
              <td>Yes</td>
              <td>Allow: image, video, audio, document</td>
            </tr>
            <tr>
              <td>caption</td>
              <td>string</td>
              <td>No</td>
              <td>Caption/message</td>
            </tr>
            <tr>
              <td>footer</td>
              <td>string</td>
              <td>No</td>
              <td>Footer under message</td>
            </tr>
            <tr>
              <td>url</td>
              <td>string</td>
              <td>Yes</td>
              <td>Direct URL of media (not Google Drive, Dropbox, etc.)</td>
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

        <p>Note: Make sure the url is a direct link, not a shared link from cloud storage.</p>
        <br>

        <h6>Example Without <code>full</code></h6>
        <p>JSON Request</p>
<pre class="bg-dark rounded text-white"><code>{
    "api_key": "1234567890",
    "sender": "62888xxxx",
    "number": "62888xxxx",
    "media_type": "image",
    "caption": "Hello World",
    "footer": "Sent via mpwa",
    "url": "https://example.com/image.jpg"
}</code></pre>

        <p>URL Request</p>
<pre class="bg-dark rounded text-white"><code>{{ url('/') }}/send-media?api_key=1234567890&amp;sender=62888xxxx&amp;number=62888xxxx&amp;media_type=image&amp;caption=Hello World&amp;footer=Sent via mpwa&amp;url=https://example.com/image.jpg</code></pre>

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
    "media_type": "image",
    "caption": "Hello World",
    "footer": "Sent via mpwa",
    "url": "https://example.com/image.jpg",
    "msgid": "3EB031F83D74BF480052B9"
}</code></pre>

        <p>URL Request</p>
<pre class="bg-dark rounded text-white"><code>{{ url('/') }}/send-media?api_key=1234567890&amp;sender=62888xxxx&amp;number=62888xxxx&amp;media_type=image&amp;caption=Hello World&amp;footer=Sent via mpwa&amp;url=https://example.com/image.jpg&amp;msgid=3EB031F83D74BF480052B9</code></pre>

        <hr class="my-4">

        <h6>Example With <code>full</code></h6>
        <p>JSON Request</p>
<pre class="bg-dark rounded text-white"><code>{
    "api_key": "1234567890",
    "sender": "62888xxxx",
    "number": "62888xxxx",
    "media_type": "image",
    "caption": "Hello World",
    "footer": "Sent via mpwa",
    "url": "https://example.com/image.jpg",
    "full": 1
}</code></pre>

        <p>URL Request</p>
<pre class="bg-dark rounded text-white"><code>{{ url('/') }}/send-media?api_key=1234567890&amp;sender=62888xxxx&amp;number=62888xxxx&amp;media_type=image&amp;caption=Hello World&amp;footer=Sent via mpwa&amp;url=https://example.com/image.jpg&amp;full=1</code></pre>

        <p>JSON Response</p>
<pre class="bg-dark rounded text-white"><code>{
  "status": true,
  "data": {
    "key": {
      "remoteJid": "62888xxxx@c.us",
      "fromMe": true,
      "id": "3EB0C373B04BXXXXXXXXXXX"
    },
    "message": {
      "imageMessage": {
        "url": "https://mmg.whatsapp.net/o1/v/t24/f2/m269/AQOKz_WB7dPWFfryJk1K8Cg09KW81xBjZq-eCQkPli773uWKhUfXiQMvyAEoJupyQ6_1FZ2bdm8Bf9Fye3OhSo9Gfh5XXXXXXXXXXX...",
        "mimetype": "image/jpeg",
        "caption": "Hello World",
        "viewOnce": false
      }
    },
    "messageTimestamp": "1755628853"
  }
}</code></pre>
      </div>
    </div>
  </div>
</div>
