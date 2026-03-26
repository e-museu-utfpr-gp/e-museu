@once
    @push('scripts')
        <script>
            (function () {
                if (window.__catalogUploadUtils) return;

                window.__catalogUploadUtils = {
                    isImage: function (file) {
                        return file && file.type && file.type.indexOf('image/') === 0;
                    },
                    attachDropZoneState: function (zone) {
                        if (!zone) return;
                        ['dragenter', 'dragover'].forEach(function (ev) {
                            zone.addEventListener(ev, function (e) {
                                e.preventDefault();
                                zone.classList.add('upload-drop-zone--over');
                            });
                        });
                        ['dragleave', 'drop'].forEach(function (ev) {
                            zone.addEventListener(ev, function (e) {
                                e.preventDefault();
                                zone.classList.remove('upload-drop-zone--over');
                            });
                        });
                    },
                    setFileInputs: function (form, inputName, files) {
                        if (!form) return;
                        form.querySelectorAll('input[name="' + inputName + '"]').forEach(function (inp) { inp.remove(); });
                        files.forEach(function (file) {
                            if (typeof DataTransfer === 'undefined') return;
                            var inp = document.createElement('input');
                            inp.type = 'file';
                            inp.name = inputName;
                            inp.className = 'd-none';
                            var dt = new DataTransfer();
                            dt.items.add(file);
                            inp.files = dt.files;
                            form.appendChild(inp);
                        });
                    }
                };
            })();
        </script>
        <style>
            .upload-drop-zone--over { border-color: #81c784 !important; background: #c8e6c9 !important; }
            .upload-drop-zone:hover { border-color: #a5d6a7 !important; }
            .upload-drop-zone--invalid { border-color: #e57373 !important; background: #ffebee !important; }
        </style>
    @endpush
@endonce
