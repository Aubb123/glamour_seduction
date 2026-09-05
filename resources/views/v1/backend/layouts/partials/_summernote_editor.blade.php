@push('styles')
    <script src="{{ asset(getEnvFolder() . 'others/backend/summernote/jquery-3.5.1.min.js')}}"></script>
    <script src="{{ asset(getEnvFolder() . 'others/backend/summernote/popper.min.js')}}"></script>
    <script src="{{ asset(getEnvFolder() . 'others/backend/summernote/bootstrap.min.js')}}"></script>
    <link href="{{ asset(getEnvFolder() . 'others/backend/summernote/summernote-bs4.min.css')}}" rel="stylesheet">
    <script src="{{ asset(getEnvFolder() . 'others/backend/summernote/summernote-bs4.min.js')}}"></script>
@endpush

@push('summernote-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(app()->getLocale() == 'fr')
                $.getScript("{{ asset(getEnvFolder() . 'others/backend/summernote/lang/summernote-fr-FR.js') }}");
            @endif
            
            document.querySelectorAll('[id^="summernote-"]').forEach(textarea => {
                $(textarea).summernote({
                    tabsize: 2,
                    height: 200,
                    lang: 'fr-FR',
                    toolbar: [
                        ['history', ['undo', 'redo']],
                        ['style', ['style']],
                        ['font', ['bold', 'italic', 'underline', 'clear', 'strikethrough', 'superscript', 'subscript']],
                        ['fontname', ['fontname']],
                        ['fontsize', ['fontsize']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['height', ['height']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture', 'video']],
                        ['view', ['fullscreen', 'codeview', 'help']],
                    ]
                });
            });
        });
    </script>
@endpush





