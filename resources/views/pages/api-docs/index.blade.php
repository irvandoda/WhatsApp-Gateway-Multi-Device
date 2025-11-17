<x-layout-dashboard title="API Docs">
    <style>
        /* Custom futuristic styles */
        .api-nav-item {
            @apply relative px-4 py-3 mb-2 rounded-lg border border-slate-700/50 bg-slate-800/30 backdrop-blur-sm transition-all duration-300 cursor-pointer;
        }
        .api-nav-item:hover {
            @apply border-brand-neon/50 bg-slate-800/50 shadow-glow;
            transform: translateX(4px);
        }
        .api-nav-item.active {
            @apply border-brand-neon bg-brand-neon/10 shadow-glow;
        }
        .api-nav-item.active::before {
            content: '';
            @apply absolute left-0 top-0 bottom-0 w-1 bg-brand-neon rounded-l-lg;
        }
        .code-block {
            @apply relative overflow-hidden rounded-xl border border-slate-700/50 bg-slate-900/80 backdrop-blur-sm;
        }
        .code-block::before {
            content: '';
            @apply absolute inset-0 bg-gradient-to-r from-brand-neon/5 via-transparent to-brand-neon/5 opacity-50;
        }
        .api-table {
            @apply w-full border-collapse rounded-lg overflow-hidden border border-slate-700/50;
        }
        .api-table thead {
            @apply bg-gradient-to-r from-slate-800/90 to-slate-700/90 backdrop-blur-sm;
        }
        .api-table th {
            @apply px-6 py-4 text-left text-sm font-semibold text-brand-neon uppercase tracking-wider border-b border-slate-700/50;
        }
        .api-table td {
            @apply px-6 py-4 text-sm text-slate-300 border-b border-slate-800/50;
        }
        .api-table tbody tr {
            @apply transition-colors duration-200 hover:bg-slate-800/30;
        }
        .api-table tbody tr:last-child td {
            @apply border-b-0;
        }
        .method-badge {
            @apply inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold uppercase tracking-wider;
        }
        .method-post {
            @apply bg-emerald-500/20 text-emerald-400 border border-emerald-500/30;
        }
        .method-get {
            @apply bg-blue-500/20 text-blue-400 border border-blue-500/30;
        }
        .endpoint-code {
            @apply font-mono text-sm text-brand-neon bg-slate-900/50 px-3 py-2 rounded-lg border border-slate-700/50;
        }
    </style>

    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center space-x-2 text-sm text-slate-400 mb-4">
            <a href="javascript:;" class="hover:text-brand-neon transition-colors">Home</a>
            <span class="text-slate-600">/</span>
            <span class="text-brand-neon">API Documentation</span>
        </div>
        <div class="flex items-center space-x-4">
            <div class="h-12 w-1 bg-gradient-to-b from-brand-neon to-transparent rounded-full"></div>
            <div>
                <h1 class="text-4xl font-bold bg-gradient-to-r from-slate-100 via-brand-neon to-slate-100 bg-clip-text text-transparent mb-2">
                    API Documentation
                </h1>
                <p class="text-slate-400 text-lg">Comprehensive guide to integrate with WhatsApp Gateway API</p>
            </div>
        </div>
    </div>

    <!-- API Documentation Container -->
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Sidebar Navigation -->
        <div class="lg:w-80 flex-shrink-0">
            <div class="sticky top-8">
                <div class="bg-slate-900/80 backdrop-blur-xl rounded-2xl border border-slate-700/50 p-6 shadow-2xl">
                    <div class="mb-6">
                        <h2 class="text-xl font-bold text-brand-neon mb-1">Endpoints</h2>
                        <div class="h-0.5 w-20 bg-gradient-to-r from-brand-neon to-transparent"></div>
                    </div>
                    <nav class="space-y-1">
                        <a class="api-nav-item active" data-bs-toggle="tab" href="#sendmessage" role="tab" aria-selected="true">
                            <div class="flex items-center space-x-3">
                                <div class="w-2 h-2 rounded-full bg-brand-neon opacity-60"></div>
                                <span class="text-slate-200 font-medium">Send Message</span>
                            </div>
                        </a>
                        <a class="api-nav-item" data-bs-toggle="tab" href="#sendmedia" role="tab" aria-selected="false">
                            <div class="flex items-center space-x-3">
                                <div class="w-2 h-2 rounded-full bg-brand-neon opacity-60"></div>
                                <span class="text-slate-200 font-medium">Send Media</span>
                            </div>
                        </a>
                        <a class="api-nav-item" data-bs-toggle="tab" href="#sendpoll" role="tab" aria-selected="false">
                            <div class="flex items-center space-x-3">
                                <div class="w-2 h-2 rounded-full bg-brand-neon opacity-60"></div>
                                <span class="text-slate-200 font-medium">Send Poll</span>
                            </div>
                        </a>
                        <a class="api-nav-item" data-bs-toggle="tab" href="#sendsticker" role="tab" aria-selected="false">
                            <div class="flex items-center space-x-3">
                                <div class="w-2 h-2 rounded-full bg-brand-neon opacity-60"></div>
                                <span class="text-slate-200 font-medium">Send Sticker</span>
                            </div>
                        </a>
                        <a class="api-nav-item" data-bs-toggle="tab" href="#sendbutton" role="tab" aria-selected="false">
                            <div class="flex items-center space-x-3">
                                <div class="w-2 h-2 rounded-full bg-brand-neon opacity-60"></div>
                                <span class="text-slate-200 font-medium">Send Button</span>
                            </div>
                        </a>
                        <a class="api-nav-item" data-bs-toggle="tab" href="#sendlist" role="tab" aria-selected="false">
                            <div class="flex items-center space-x-3">
                                <div class="w-2 h-2 rounded-full bg-brand-neon opacity-60"></div>
                                <span class="text-slate-200 font-medium">Send List</span>
                            </div>
                        </a>
                        <a class="api-nav-item" data-bs-toggle="tab" href="#sendlocation" role="tab" aria-selected="false">
                            <div class="flex items-center space-x-3">
                                <div class="w-2 h-2 rounded-full bg-brand-neon opacity-60"></div>
                                <span class="text-slate-200 font-medium">Send Location</span>
                            </div>
                        </a>
                        <a class="api-nav-item" data-bs-toggle="tab" href="#sendvcard" role="tab" aria-selected="false">
                            <div class="flex items-center space-x-3">
                                <div class="w-2 h-2 rounded-full bg-brand-neon opacity-60"></div>
                                <span class="text-slate-200 font-medium">Send Vcard</span>
                            </div>
                        </a>
                        <div class="pt-4 mt-4 border-t border-slate-700/50">
                            <p class="text-xs text-slate-500 uppercase tracking-wider mb-3 px-4">Device Management</p>
                            <a class="api-nav-item" data-bs-toggle="tab" href="#generateqr" role="tab" aria-selected="false">
                                <div class="flex items-center space-x-3">
                                    <div class="w-2 h-2 rounded-full bg-brand-neon opacity-60"></div>
                                    <span class="text-slate-200 font-medium">Generate QR</span>
                                </div>
                            </a>
                            <a class="api-nav-item" data-bs-toggle="tab" href="#disconnectdevice" role="tab" aria-selected="false">
                                <div class="flex items-center space-x-3">
                                    <div class="w-2 h-2 rounded-full bg-brand-neon opacity-60"></div>
                                    <span class="text-slate-200 font-medium">Disconnect Device</span>
                                </div>
                            </a>
                            <a class="api-nav-item" data-bs-toggle="tab" href="#createdevice" role="tab" aria-selected="false">
                                <div class="flex items-center space-x-3">
                                    <div class="w-2 h-2 rounded-full bg-brand-neon opacity-60"></div>
                                    <span class="text-slate-200 font-medium">Create Device</span>
                                </div>
                            </a>
                            <a class="api-nav-item" data-bs-toggle="tab" href="#deviceinfo" role="tab" aria-selected="false">
                                <div class="flex items-center space-x-3">
                                    <div class="w-2 h-2 rounded-full bg-brand-neon opacity-60"></div>
                                    <span class="text-slate-200 font-medium">Device Info</span>
                                </div>
                            </a>
                        </div>
                        <div class="pt-4 mt-4 border-t border-slate-700/50">
                            <p class="text-xs text-slate-500 uppercase tracking-wider mb-3 px-4">User Management</p>
                            <a class="api-nav-item" data-bs-toggle="tab" href="#createuser" role="tab" aria-selected="false">
                                <div class="flex items-center space-x-3">
                                    <div class="w-2 h-2 rounded-full bg-brand-neon opacity-60"></div>
                                    <span class="text-slate-200 font-medium">Create User</span>
                                </div>
                            </a>
                            <a class="api-nav-item" data-bs-toggle="tab" href="#userinfo" role="tab" aria-selected="false">
                                <div class="flex items-center space-x-3">
                                    <div class="w-2 h-2 rounded-full bg-brand-neon opacity-60"></div>
                                    <span class="text-slate-200 font-medium">User Info</span>
                                </div>
                            </a>
                        </div>
                        <div class="pt-4 mt-4 border-t border-slate-700/50">
                            <a class="api-nav-item" data-bs-toggle="tab" href="#checknumber" role="tab" aria-selected="false">
                                <div class="flex items-center space-x-3">
                                    <div class="w-2 h-2 rounded-full bg-brand-neon opacity-60"></div>
                                    <span class="text-slate-200 font-medium">Check Number</span>
                                </div>
                            </a>
                            <a class="api-nav-item" data-bs-toggle="tab" href="#examplewebhook" role="tab" aria-selected="false">
                                <div class="flex items-center space-x-3">
                                    <div class="w-2 h-2 rounded-full bg-brand-neon opacity-60"></div>
                                    <span class="text-slate-200 font-medium">Example Webhook</span>
                                </div>
                            </a>
                        </div>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 min-w-0">
            <div class="tab-content">
                @include('pages.api-docs.send-message')
                @include('pages.api-docs.send-media')
                @include('pages.api-docs.send-poll')
                @include('pages.api-docs.send-sticker')
                @include('pages.api-docs.send-button')
                @include('pages.api-docs.send-list')
                @include('pages.api-docs.send-location')
                @include('pages.api-docs.send-vcard')
                @include('pages.api-docs.generateqr')
                @include('pages.api-docs.disconnectdevice')
                @include('pages.api-docs.createuser')
                @include('pages.api-docs.user-info')
                @include('pages.api-docs.create-device')
                @include('pages.api-docs.device-info')
                @include('pages.api-docs.check-number')
                @include('pages.api-docs.examplewebhook')
            </div>
        </div>
    </div>

    <!-- Bootstrap JS for tabs -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Update active tab on click
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Bootstrap tabs
            const triggerTabList = document.querySelectorAll('[data-bs-toggle="tab"]');
            triggerTabList.forEach(triggerEl => {
                triggerEl.addEventListener('shown.bs.tab', function (e) {
                    // Remove active class from all nav items
                    document.querySelectorAll('.api-nav-item').forEach(item => {
                        item.classList.remove('active');
                    });
                    // Add active class to clicked nav item
                    if (e.target.closest('.api-nav-item')) {
                        e.target.closest('.api-nav-item').classList.add('active');
                    }
                });
            });
        });
    </script>
</x-layout-dashboard>