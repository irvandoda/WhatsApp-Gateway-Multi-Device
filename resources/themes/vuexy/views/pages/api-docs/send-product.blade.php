<div class="tab-pane fade" id="sendproduct" role="tabpanel">
  <div class="d-flex mb-3 gap-3">
    <div>
      <span class="badge bg-label-primary rounded-2 p-2">
        <i class="ti tabler-apps icon-32px"></i>
      </span>
    </div>
    <div>
      <h4 class="mb-0 lh-sm">
        <span class="align-middle">Send Product API</span>
      </h4>
      <small>Api Docs Sending Product Messages</small>
    </div>
  </div>
  <div id="accordionPayment" class="accordion">
    <div class="card">
      <div class="card-body">
        <p>Method : <code class="text-success">POST</code> | <code class="text-primary">GET</code></p>
        <p>Endpoint: <code>{{ url('/') }}/send-product</code></p>

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
              <td>url</td>
              <td>string</td>
              <td>Yes</td>
              <td>WhatsApp Product URL (e.g. https://wa.me/p/123456789/628xxxxxxxxxx)</td>
            </tr>
            <tr>
              <td>message</td>
              <td>string</td>
              <td>No</td>
              <td>Optional caption or footer to include with the product</td>
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
    "url": "https://wa.me/p/12345678901234567/6281222xxxxxx",
    "message": "Check out this item!"
}</code></pre>

        <p>URL Request</p>
<pre class="bg-dark rounded text-white"><code>{{ url('/') }}/send-product?api_key=1234567890&amp;sender=6281222xxxxxx&amp;number=201111xxxxxx&amp;url=https://wa.me/p/12345678901234567/6281222xxxxxx&amp;message=Check%20out%20this%20item!</code></pre>

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
    "url": "https://wa.me/p/12345678901234567/6281222xxxxxx",
    "message": "Check out this item!",
    "msgid": "3EB031F83D74BF480052B9"
}</code></pre>

        <p>URL Request</p>
<pre class="bg-dark rounded text-white"><code>{{ url('/') }}/send-product?api_key=1234567890&amp;sender=6281222xxxxxx&amp;number=201111xxxxxx&amp;url=https://wa.me/p/12345678901234567/6281222xxxxxx&amp;message=Check%20out%20this%20item!&amp;msgid=3EB031F83D74BF480052B9</code></pre>

        <hr class="my-4">

        <h6>Example With <code>full</code></h6>
        <p>JSON Request</p>
<pre class="bg-dark rounded text-white"><code>{
    "api_key": "1234567890",
    "sender": "6281222xxxxxx",
    "number": "201111xxxxxx",
    "url": "https://wa.me/p/12345678901234567/6281222xxxxxx",
    "message": "Check out this item!",
    "full": 1
}</code></pre>

        <p>URL Request</p>
<pre class="bg-dark rounded text-white"><code>{{ url('/') }}/send-product?api_key=1234567890&amp;sender=6281222xxxxxx&amp;number=201111xxxxxx&amp;url=https://wa.me/p/12345678901234567/6281222xxxxxx&amp;message=Check%20out%20this%20item!&amp;full=1</code></pre>

        <p>JSON Response</p>
<pre class="bg-dark rounded text-white"><code>{
  "status": true,
  "data": {
    "key": {
      "remoteJid": "201111xxxxxx@c.us",
      "fromMe": true,
      "id": "3EB09CF18B98FBXXXXXXX"
    },
    "message": {
      "productMessage": {
        "product": {
          "productImage": {
            "url": "https://mmg.whatsapp.net/o1/v/t24/f2/m234/AQPHFLaqOvE1JImcYjd16J3v1BJfJ6B6mQ7d4IFCJkkxuNfpPeQ4kpeBsXinof1zeUv9M1hxI2xQkrJ80K6XXXXXXXXXXXXXXX..."
          },
          "productId": "2462531XXXXXXXXXX",
          "title": "Field Delivery",
          "description": "XXXXXXXXXXXXXXX XXXXXXXXXXXXXXX",
          "currencyCode": "IDR",
          "priceAmount1000": "350000000",
          "retailerId": "OneXGen Technology",
          "url": "",
          "productImageCount": 1,
          "salePriceAmount1000": "25000000",
          "signedUrl": ""
        },
        "businessOwnerJid": "6283XXXXXXXX@s.whatsapp.net",
        "footer": "Check out this item!"
      }
    },
    "messageTimestamp": "1755630823"
  }
}</code></pre>
      </div>
    </div>
  </div>
</div>
