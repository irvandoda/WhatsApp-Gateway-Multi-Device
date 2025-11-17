<div class="tab-pane fade" id="generateqr" role="tabpanel">
    <div class="bg-slate-900/80 backdrop-blur-xl rounded-2xl border border-slate-700/50 p-8 shadow-2xl">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-4 mb-4">
                <div class="h-10 w-1 bg-gradient-to-b from-brand-neon to-transparent rounded-full"></div>
                <div>
                    <h2 class="text-3xl font-bold text-slate-100 mb-2">Generate QR API</h2>
                    <div class="flex items-center space-x-3 mt-3">
                        <span class="method-badge method-post">POST</span>
                    </div>
                </div>
            </div>
            <div class="mt-6">
                <p class="text-slate-400 mb-2 text-sm uppercase tracking-wider">Endpoint</p>
                <code class="endpoint-code">{{ env('APP_URL') }}/generate-qr</code>
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
                            <td><code class="text-brand-neon">device</code></td>
                            <td><span class="text-slate-400">string</span></td>
                            <td><span class="px-2 py-1 rounded bg-emerald-500/20 text-emerald-400 text-xs">Yes</span></td>
                            <td>Number of your device</td>
                        </tr>
                        <tr>
                            <td><code class="text-brand-neon">api_key</code></td>
                            <td><span class="text-slate-400">string</span></td>
                            <td><span class="px-2 py-1 rounded bg-emerald-500/20 text-emerald-400 text-xs">Yes</span></td>
                            <td>API Key</td>
                        </tr>
                        <tr>
                            <td><code class="text-brand-neon">force</code></td>
                            <td><span class="text-slate-400">boolean</span></td>
                            <td><span class="px-2 py-1 rounded bg-slate-600/20 text-slate-400 text-xs">No</span></td>
                            <td>If true, when device is not exist, it will be created</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Normal Response -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold text-brand-neon mb-4 flex items-center">
                <span class="w-2 h-2 rounded-full bg-brand-neon mr-3"></span>
                Normal Response
            </h3>
            <div class="code-block p-6 mb-4">
                <pre class="text-slate-200 font-mono text-sm overflow-x-auto"><code>{
  "status": "processing",
  "message": "Processing"
}</code></pre>
            </div>
            <div class="p-4 rounded-lg bg-blue-500/10 border border-blue-500/30 mb-4">
                <p class="text-blue-400 text-sm">
                    <strong>Note:</strong> If processing like above, you need to hit the endpoint again to get the result.
                </p>
            </div>
            <div class="code-block p-6">
                <pre class="text-slate-200 font-mono text-sm overflow-x-auto"><code>{
  "status": false,
  "qrcode": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAARQAAAEUCAYAAADqcMl5...",
  "message": "Please scan qrcode"
}</code></pre>
            </div>
            <div class="p-4 rounded-lg bg-blue-500/10 border border-blue-500/30 mt-4">
                <p class="text-blue-400 text-sm">
                    <strong>Note:</strong> The qrcode is base64 encoded. After scanned the qrcode, you need to hit the endpoint again to get last response.
                </p>
            </div>
            <div class="code-block p-6 mt-4">
                <pre class="text-slate-200 font-mono text-sm overflow-x-auto"><code>{
  "status": false,
  "msg": "Device already connected!"
}</code></pre>
            </div>
        </div>

        <!-- Failed Response -->
        <div>
            <h3 class="text-xl font-semibold text-brand-neon mb-4 flex items-center">
                <span class="w-2 h-2 rounded-full bg-brand-neon mr-3"></span>
                Failed Response
            </h3>
            <div class="code-block p-6">
                <pre class="text-slate-200 font-mono text-sm overflow-x-auto"><code>{
  "status": false,
  "msg": "Invalid data!",
  "errors": {} // list of errors
}</code></pre>
            </div>
        </div>
    </div>
</div>