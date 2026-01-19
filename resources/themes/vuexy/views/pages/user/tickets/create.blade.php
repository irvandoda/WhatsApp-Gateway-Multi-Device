<x-layout-dashboard title="{{__('Create Ticket')}}">
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">{{ __('Create New Ticket') }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('user.tickets.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="title" class="form-label">{{ __('Title') }}</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="priority" class="form-label">{{ __('Priority') }}</label>
                        <select class="form-select @error('priority') is-invalid @enderror" 
                                id="priority" name="priority" required>
                            <option value="">{{ __('Select Priority') }}</option>
                            <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>{{ __('Low') }}</option>
                            <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>{{ __('Medium') }}</option>
                            <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>{{ __('High') }}</option>
                        </select>
                        @error('priority')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="message" class="form-label">{{ __('Message') }}</label>
                        <div id="editor-container" style="height: 200px; background: white;">{{ old('message') }}</div>
							<input type="hidden" id="message" name="message">
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('user.tickets.index') }}" class="btn btn-sm btn-outline-secondary">
                            {{ __('Back') }}
                        </a>
                        <button type="submit" class="btn btn-sm btn-outline-primary">
                            {{ __('Create Ticket') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
	<script>
        document.addEventListener('DOMContentLoaded', function () {
            var quill = new Quill('#editor-container', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike'],
                        ['blockquote', 'code-block'],
                        [{ 'header': 1 }, { 'header': 2 }],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'indent': '-1'}, { 'indent': '+1' }],
                        [{ 'direction': 'rtl' }],
                        [{ 'size': ['small', false, 'large', 'huge'] }],
                        [{ 'color': [] }, { 'background': [] }],
                        [{ 'align': [] }],
                        ['link'],
                        ['clean']
                    ]
                }
            });

            document.querySelector('form[action="{{ route('user.tickets.store') }}"]').addEventListener('submit', function () {
                document.getElementById('message').value = quill.root.innerHTML;
            });
        });
    </script>
    </div>
</x-layout-dashboard>