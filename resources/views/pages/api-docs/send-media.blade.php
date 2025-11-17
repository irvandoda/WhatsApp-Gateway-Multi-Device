<div class="tab-pane fade" id="sendmedia" role="tabpanel">
    <div class="bg-slate-900/80 backdrop-blur-xl rounded-2xl border border-slate-700/50 p-8 shadow-2xl">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-4 mb-4">
                <div class="h-10 w-1 bg-gradient-to-b from-brand-neon to-transparent rounded-full"></div>
                <div>
                    <h2 class="text-3xl font-bold text-slate-100 mb-2">Send Media API</h2>
                    <div class="flex items-center space-x-3 mt-3">
                        <span class="method-badge method-post">POST</span>
                        <span class="method-badge method-get">GET</span>
                    </div>
                </div>
            </div>
            <div class="mt-6">
                <p class="text-slate-400 mb-2 text-sm uppercase tracking-wider">Endpoint</p>
                <code class="endpoint-code">{{ env('APP_URL') }}/send-media</code>
            </div>
        </div>

        <!-- Request Body Section -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold text-brand-neon mb-4 flex items-center">
                <span class="w-2 h-2 rounded-full bg-brand-neon mr-3"></span>
                Request Body (JSON If POST)
            </h3>
            <div class="overflow-x-auto">
                <table class="api-table">
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
                            <td><code class="text-brand-neon">api_key</code></td>
                            <td><span class="text-slate-400">string</span></td>
                            <td><span class="px-2 py-1 rounded bg-emerald-500/20 text-emerald-400 text-xs">Yes</span></td>
                            <td>API Key</td>
                        </tr>
                        <tr>
                            <td><code class="text-brand-neon">sender</code></td>
                            <td><span class="text-slate-400">string</span></td>
                            <td><span class="px-2 py-1 rounded bg-emerald-500/20 text-emerald-400 text-xs">Yes</span></td>
                            <td>Number of your device</td>
                        </tr>
                        <tr>
                            <td><code class="text-brand-neon">number</code></td>
                            <td><span class="text-slate-400">string</span></td>
                            <td><span class="px-2 py-1 rounded bg-emerald-500/20 text-emerald-400 text-xs">Yes</span></td>
                            <td>Recipient number ex 72888xxxx|62888xxxx</td>
                        </tr>
                        <tr>
                            <td><code class="text-brand-neon">media_type</code></td>
                            <td><span class="text-slate-400">string</span></td>
                            <td><span class="px-2 py-1 rounded bg-emerald-500/20 text-emerald-400 text-xs">Yes</span></td>
                            <td>Allow: image, video, audio, document</td>
                        </tr>
                        <tr>
                            <td><code class="text-brand-neon">caption</code></td>
                            <td><span class="text-slate-400">string</span></td>
                            <td><span class="px-2 py-1 rounded bg-slate-600/20 text-slate-400 text-xs">No</span></td>
                            <td>Caption/message</td>
                        </tr>
                        <tr>
                            <td><code class="text-brand-neon">url</code></td>
                            <td><span class="text-slate-400">string</span></td>
                            <td><span class="px-2 py-1 rounded bg-emerald-500/20 text-emerald-400 text-xs">Yes</span></td>
                            <td>URL of media, must direct link</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Note -->
        <div class="mb-8 p-4 rounded-lg bg-amber-500/10 border border-amber-500/30">
            <p class="text-amber-400 text-sm flex items-start">
                <span class="mr-2">⚠️</span>
                <span><strong>Note:</strong> Make sure the URL is a direct link, not a link from Google Drive or other cloud storage.</span>
            </p>
        </div>

        <!-- Example JSON Request -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold text-brand-neon mb-4 flex items-center">
                <span class="w-2 h-2 rounded-full bg-brand-neon mr-3"></span>
                Example JSON Request
            </h3>
            <div class="code-block p-6">
                <pre class="text-slate-200 font-mono text-sm overflow-x-auto"><code>{
  "api_key": "1234567890",
  "sender": "62888xxxx",
  "number": "62888xxxx",
  "media_type": "image",
  "caption": "Hello World",
  "url": "https://example.com/image.jpg"
}</code></pre>
            </div>
        </div>

        <!-- Example URL Request -->
        <div>
            <h3 class="text-xl font-semibold text-brand-neon mb-4 flex items-center">
                <span class="w-2 h-2 rounded-full bg-brand-neon mr-3"></span>
                Example URL Request
            </h3>
            <div class="code-block p-6">
                <pre class="text-slate-200 font-mono text-sm overflow-x-auto break-all"><code>{{ env('APP_URL') }}/send-media?api_key=1234567890&sender=62888xxxx&number=62888xxxx&media_type=image&caption=Hello World&url=https://example.com/image.jpg</code></pre>
            </div>
        </div>
    </div>
</div>