<x-layout-dashboard title="File manager">
    <div class="rounded-3xl border border-slate-800/60 bg-slate-950/70 p-5">
        @if (session()->has('alert'))
            <x-alert>
                @slot('type',session('alert')['type'])
                @slot('msg',session('alert')['msg'])
            </x-alert>
        @endif
        @if ($errors->any())
            <div class="rounded-3xl border border-rose-500/40 bg-rose-500/10 px-6 py-4 text-sm text-rose-100 mb-4">
                <ul class="list-disc pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-2xl border border-slate-800/70 overflow-hidden">
            <iframe src="{{url('/laravel-filemanager')}}" style="width: 100%; height: 600px; overflow: hidden; border: none;"></iframe>
        </div>
    </div>

    <script>
        $('#server').on('change',function(){
           let type = $('#server :selected').val();
            if(type === 'other'){
                $('.formUrlNode').removeClass('d-none')
            } else {
                $('.formUrlNode').addClass('d-none')
            }
        })
    </script>
</x-layout-dashboard>