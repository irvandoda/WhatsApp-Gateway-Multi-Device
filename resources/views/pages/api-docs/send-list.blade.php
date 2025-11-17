<div class="tab-pane fade" id="sendlist" role="tabpanel">
    <div class="bg-slate-900/80 backdrop-blur-xl rounded-2xl border border-slate-700/50 p-8 shadow-2xl">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-4 mb-4">
                <div class="h-10 w-1 bg-gradient-to-b from-brand-neon to-transparent rounded-full"></div>
                <div>
                    <h2 class="text-3xl font-bold text-slate-100 mb-2">Send List Message API</h2>
                    <div class="flex items-center space-x-3 mt-3">
                        <span class="method-badge method-post">POST</span>
                        <span class="method-badge method-get">GET</span>
                    </div>
                </div>
            </div>
            <div class="mt-6">
                <p class="text-slate-400 mb-2 text-sm uppercase tracking-wider">Endpoint</p>
                <code class="endpoint-code">{{ env('APP_URL') }}/send-LIST</code>
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
                            <td><code class="text-brand-neon">footer</code></td>
                            <td><span class="text-slate-400">string</span></td>
                            <td><span class="px-2 py-1 rounded bg-slate-600/20 text-slate-400 text-xs">No</span></td>
                            <td>Footer of your message</td>
                        </tr>
                        <tr>
                            <td><code class="text-brand-neon">title</code></td>
                            <td><span class="text-slate-400">string</span></td>
                            <td><span class="px-2 py-1 rounded bg-emerald-500/20 text-emerald-400 text-xs">Yes</span></td>
                            <td>Title of your list</td>
                        </tr>
                        <tr>
                            <td><code class="text-brand-neon">buttontext</code></td>
                            <td><span class="text-slate-400">string</span></td>
                            <td><span class="px-2 py-1 rounded bg-emerald-500/20 text-emerald-400 text-xs">Yes</span></td>
                            <td>Text of your button list</td>
                        </tr>
                        <tr>
                            <td><code class="text-brand-neon">message</code></td>
                            <td><span class="text-slate-400">string</span></td>
                            <td><span class="px-2 py-1 rounded bg-emerald-500/20 text-emerald-400 text-xs">Yes</span></td>
                            <td>Text of your message</td>
                        </tr>
                        <tr>
                            <td><code class="text-brand-neon">list</code></td>
                            <td><span class="text-slate-400">array</span></td>
                            <td><span class="px-2 py-1 rounded bg-emerald-500/20 text-emerald-400 text-xs">Yes</span></td>
                            <td>List of your message (min 1, max 10)</td>
                        </tr>
                        <tr>
                            <td><code class="text-brand-neon">image/url</code></td>
                            <td><span class="text-slate-400">url</span></td>
                            <td><span class="px-2 py-1 rounded bg-slate-600/20 text-slate-400 text-xs">No</span></td>
                            <td>Fill this if you want to add an image</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Example JSON Request -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold text-brand-neon mb-4 flex items-center">
                <span class="w-2 h-2 rounded-full bg-brand-neon mr-3"></span>
                Example JSON Request
            </h3>
            <div class="code-block p-6">
                <pre class="text-slate-200 font-mono text-sm overflow-x-auto"><code>{
  "sender": "6285747476308",
  "api_key": "ndUJR38JkvyCfLZ",
  "number": "082298859671",
  "name": "pesan list",
  "footer": "optional",
  "title": "title list",
  "buttontext": "ey",
  "message": "Halo, ini pesan list button",
  "list": ["sdf", "list2", "list3"]
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
                <pre class="text-slate-200 font-mono text-sm overflow-x-auto break-all"><code>{{ env('APP_URL') }}/send-list?sender=6285747476308&api_key=ndUJR38JkvyCfLZ&number=082298859671&name=pesan list&footer=optional&title=title list&buttontext=ey&message=Halo,ini pesan list&list=sdf,list2,list3</code></pre>
            </div>
        </div>
    </div>
</div>