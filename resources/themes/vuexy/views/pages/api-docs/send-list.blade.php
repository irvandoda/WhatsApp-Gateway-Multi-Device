<div class="tab-pane fade" id="sendlist" role="tabpanel">
  <div class="d-flex mb-3 gap-3">
    <div>
      <span class="badge bg-label-primary rounded-2 p-2">
        <i class="ti tabler-list-details icon-32px"></i>
      </span>
    </div>
    <div>
      <h4 class="mb-0 lh-sm">
        <span class="align-middle">Send List Message API</span>
      </h4>
      <small>Api Docs Sending List Messages</small>
    </div>
  </div>
  <div id="accordionPayment" class="accordion">
    <div class="card">
      <div class="card-body">
        <p>Method : <code class="text-success">POST</code> | <code class="text-primary">GET</code></p>
        <p>Endpoint: <code>{{ url('/') }}/send-list</code></p>

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
              <td>Name of your list</td>
            </tr>
            <tr>
              <td>footer</td>
              <td>string</td>
              <td>No</td>
              <td>Footer of your message</td>
            </tr>
            <tr>
              <td>title</td>
              <td>string</td>
              <td>Yes</td>
              <td>Title of your list</td>
            </tr>
            <tr>
              <td>buttontext</td>
              <td>string</td>
              <td>Yes</td>
              <td>Text of your button list</td>
            </tr>
            <tr>
              <td>message</td>
              <td>string</td>
              <td>Yes</td>
              <td>Text of your message</td>
            </tr>
            <tr>
              <td>sections</td>
              <td>array</td>
              <td>Yes</td>
              <td>List of your message (min 1, max 5)</td>
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
    "api_key" : "123456789",
    "sender" : "6281222xxxxx",
    "number" : "628222xxxxxx",
    "name" : "Message list",
    "footer" : "optional",
    "title" : "title list",
    "buttontext" : "Menu",
    "message" : "Hello, this is a list button message",
    "sections": [
        {
            "title": "Main Options",
            "description": "Select a basic option to proceed.",
            "rows": [
                {"title": "Option 1","rowId": "id1","description": "Description for option 1"},
                {"title": "Option 2","rowId": "id2","description": "Description for option 2"}
            ]
        },
        {
            "title": "Advanced Options",
            "description": "Explore advanced settings.",
            "rows": [
                {"title": "Option 3","rowId": "id3","description": "Description for option 3"}
            ]
        }
    ]
}</code></pre>

        <p>URL Request</p>
<pre class="bg-dark rounded text-white"><code>{{ url('/') }}/send-list?api_key=123456789&amp;sender=6281222xxxxxx&amp;number=201111xxxxxx&amp;name=Message list&amp;footer=optional&amp;title=title list&amp;buttontext=Menu&amp;message=Hello, this is a list button message&amp;sections=[{title:Main Options,rows:[{title:Option 1,rowId:id1},{title:Option 2,rowId:id2}]},{title:Advanced Options,rows:[{title:Option 3,rowId:id3}]}]</code></pre>

        <p>JSON Response</p>
<pre class="bg-dark rounded text-white"><code>{
    "status": true,
    "msg": "Message sent successfully!"
}</code></pre>

        <hr class="my-4">

        <h6>Example With <code>msgid</code> (reply)</h6>
        <p>JSON Request</p>
<pre class="bg-dark rounded text-white"><code>{
    "api_key" : "123456789",
    "sender" : "6281222xxxxx",
    "number" : "628222xxxxxx",
    "name" : "Message list",
    "footer" : "optional",
    "title" : "title list",
    "buttontext" : "Menu",
    "message" : "Hello, this is a list button message",
    "sections": [
        {
            "title": "Main Options",
            "rows": [
                {"title": "Option 1","rowId": "id1","description": "Description for option 1"},
                {"title": "Option 2","rowId": "id2","description": "Description for option 2"}
            ]
        },
        {
            "title": "Advanced Options",
            "rows": [
                {"title": "Option 3","rowId": "id3","description": "Description for option 3"}
            ]
        }
    ],
    "msgid": "3EB031F83D74BF480052B9"
}</code></pre>

        <p>URL Request</p>
<pre class="bg-dark rounded text-white"><code>{{ url('/') }}/send-list?api_key=123456789&amp;sender=6281222xxxxxx&amp;number=201111xxxxxx&amp;name=Message list&amp;footer=optional&amp;title=title list&amp;buttontext=Menu&amp;message=Hello, this is a list button message&amp;sections=[{title:Main Options,rows:[{title:Option 1,rowId:id1},{title:Option 2,rowId:id2}]},{title:Advanced Options,rows:[{title:Option 3,rowId:id3}]}]&amp;msgid=3EB031F83D74BF480052B9</code></pre>

        <hr class="my-4">

        <h6>Example With <code>full</code></h6>
        <p>JSON Request</p>
<pre class="bg-dark rounded text-white"><code>{
    "api_key" : "123456789",
    "sender" : "6281222xxxxx",
    "number" : "628222xxxxxx",
    "name" : "Message list",
    "footer" : "optional",
    "title" : "title list",
    "buttontext" : "Menu",
    "message" : "Hello, this is a list button message",
    "sections": [
        {
            "title": "Main Options",
            "rows": [
                {"title": "Option 1","rowId": "id1","description": "Description for option 1"},
                {"title": "Option 2","rowId": "id2","description": "Description for option 2"}
            ]
        },
        {
            "title": "Advanced Options",
            "rows": [
                {"title": "Option 3","rowId": "id3","description": "Description for option 3"}
            ]
        }
    ],
    "full": 1
}</code></pre>

        <p>URL Request</p>
<pre class="bg-dark rounded text-white"><code>{{ url('/') }}/send-list?api_key=123456789&amp;sender=6281222xxxxxx&amp;number=201111xxxxxx&amp;name=Message list&amp;footer=optional&amp;title=title list&amp;buttontext=Menu&amp;message=Hello, this is a list button message&amp;sections=[{title:Main Options,rows:[{title:Option 1,rowId:id1},{title:Option 2,rowId:id2}]},{title:Advanced Options,rows:[{title:Option 3,rowId:id3}]}]&amp;full=1</code></pre>

        <p>JSON Response</p>
<pre class="bg-dark rounded text-white"><code>{
  "status": true,
  "data": {
    "key": {
      "remoteJid": "628222xxxxxx@c.us",
      "fromMe": true,
      "id": "3EB0310FB8CEXXXXXXXXX"
    },
    "message": {
      "listMessage": {
        "title": "Message list",
        "description": "Hello, this is a list button message",
        "buttonText": "menu",
        "listType": "PRODUCT_LIST",
        "sections": [
          {
            "title": "Main Options",
            "rows": [
              {
                "title": "Option 1",
                "description": "Description for option 1",
                "rowId": "id68a4cecc12968"
              },
              {
                "title": "Option 2",
                "description": "Description for option 2",
                "rowId": "id68a4cecc12969"
              }
            ]
          },
          {
            "title": "Advanced Options",
            "rows": [
              {
                "title": "Option 3",
                "description": "Description for option 3",
                "rowId": "id68a4cecc1296b"
              }
            ]
          }
        ],
        "footerText": "optional",
        "contextInfo": {
          "expiration": 604800
        }
      }
    },
    "messageTimestamp": "1755631308"
  }
}</code></pre>
      </div>
    </div>
  </div>
</div>
