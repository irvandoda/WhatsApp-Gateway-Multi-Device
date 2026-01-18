<div class="tab-pane fade" id="sendpoll" role="tabpanel">
  <div class="d-flex mb-3 gap-3">
    <div>
      <span class="badge bg-label-primary rounded-2 p-2">
        <i class="ti tabler-chart-bar-popular icon-32px"></i>
      </span>
    </div>
    <div>
      <h4 class="mb-0 lh-sm">
        <span class="align-middle">Send Poll API</span>
      </h4>
      <small>Api Docs Sending Poll Messages</small>
    </div>
  </div>
  <div id="accordionPayment" class="accordion">
    <div class="card">
      <div class="card-body">
        <p>Method : <code class="text-success">POST</code> | <code class="text-primary">GET</code></p>
        <p>Endpoint: <code>{{ url('/') }}/send-poll</code></p>

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
              <td>Name or poll question</td>
            </tr>
            <tr>
              <td>option</td>
              <td>array</td>
              <td>Yes</td>
              <td>Values of poll message</td>
            </tr>
            <tr>
              <td>msgid</td>
              <td>string</td>
              <td>No</td>
              <td>Quoted message ID to reply to</td>
            </tr>
            <tr>
              <td>countable</td>
              <td>string (1 or 0)</td>
              <td>Yes</td>
              <td>1 = allow one option only, 0 = allow multiple</td>
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
    "sender": "081222xxxxxx",
    "api_key": "123456789",
    "number": "201111xxxxxx",
    "countable": "1",
    "name": "what color do you like?",
    "option": ["red","blue","yellow"]
}</code></pre>

        <p>URL Request</p>
<pre class="bg-dark rounded text-white"><code>{{ url('/') }}/send-poll?sender=081222xxxxxx&amp;api_key=123456789&amp;number=201111xxxxxx&amp;name=what color do you like&amp;option=red,blue,yellow&amp;countable=1</code></pre>

        <p>JSON Response</p>
<pre class="bg-dark rounded text-white"><code>{
    "status": true,
    "msg": "Message sent successfully!"
}</code></pre>

        <hr class="my-4">

        <h6>Example With <code>msgid</code> (reply)</h6>
        <p>JSON Request</p>
<pre class="bg-dark rounded text-white"><code>{
    "sender": "081222xxxxxx",
    "api_key": "123456789",
    "number": "201111xxxxxx",
    "countable": "1",
    "name": "what color do you like?",
    "option": ["red","blue","yellow"],
    "msgid": "3EB031F83D74BF480052B9"
}</code></pre>

        <p>URL Request</p>
<pre class="bg-dark rounded text-white"><code>{{ url('/') }}/send-poll?sender=081222xxxxxx&amp;api_key=123456789&amp;number=201111xxxxxx&amp;name=what color do you like&amp;option=red,blue,yellow&amp;countable=1&amp;msgid=3EB031F83D74BF480052B9</code></pre>

        <hr class="my-4">

        <h6>Example With <code>full</code></h6>
        <p>JSON Request</p>
<pre class="bg-dark rounded text-white"><code>{
    "sender": "081222xxxxxx",
    "api_key": "123456789",
    "number": "201111xxxxxx",
    "countable": "1",
    "name": "what color do you like?",
    "option": ["red","blue","yellow"],
    "full": 1
}</code></pre>

        <p>URL Request</p>
<pre class="bg-dark rounded text-white"><code>{{ url('/') }}/send-poll?sender=081222xxxxxx&amp;api_key=123456789&amp;number=201111xxxxxx&amp;name=what color do you like&amp;option=red,blue,yellow&amp;countable=1&amp;full=1</code></pre>

        <p>JSON Response</p>
<pre class="bg-dark rounded text-white"><code>{
  "status": true,
  "data": {
    "key": {
      "remoteJid": "201111xxxxxx@c.us",
      "fromMe": true,
      "id": "3EB0416D89587XXXXXXXX"
    },
    "message": {
      "messageContextInfo": {
        "messageSecret": "9q9Co188HnKBbsz8JrZBNzYlVXXGXXXXXXXXXXXXXqq4="
      },
      "pollCreationMessageV3": {
        "name": "what color do you like",
        "options": [
          { "optionName": "red" },
          { "optionName": "blue" },
          { "optionName": "yellow" }
        ],
        "selectableOptionsCount": 1
      }
    },
    "messageTimestamp": "1755628143"
  }
}</code></pre>

      </div>
    </div>
  </div>
</div>
