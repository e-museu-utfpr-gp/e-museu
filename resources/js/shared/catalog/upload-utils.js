(function attachCatalogUploadUtils() {
    if (window.__catalogUploadUtils) {
        return;
    }

    window.__catalogUploadUtils = {
        /** MIME check only; Laravel validates uploads (source of truth). */
        isImage(file) {
            return file && file.type && file.type.indexOf('image/') === 0;
        },
        /**
         * Optional UX when `file.type` is empty or wrong; does not replace server validation.
         * @param {File} file
         * @returns {Promise<boolean>}
         */
        async isImageByMagic(file) {
            if (!file || typeof file.slice !== 'function') {
                return false;
            }
            try {
                const buf = await file.slice(0, 16).arrayBuffer();
                const u = new Uint8Array(buf);
                if (u.length >= 3 && u[0] === 0xff && u[1] === 0xd8 && u[2] === 0xff) {
                    return true;
                }
                if (u.length >= 8 && u[0] === 0x89 && u[1] === 0x50 && u[2] === 0x4e && u[3] === 0x47) {
                    return true;
                }
                if (u.length >= 6 && u[0] === 0x47 && u[1] === 0x49 && u[2] === 0x46 && u[3] === 0x38) {
                    return true;
                }
                if (
                    u.length >= 12 &&
                    u[0] === 0x52 &&
                    u[1] === 0x49 &&
                    u[2] === 0x46 &&
                    u[3] === 0x46 &&
                    u[8] === 0x57 &&
                    u[9] === 0x45 &&
                    u[10] === 0x42 &&
                    u[11] === 0x50
                ) {
                    return true;
                }
                return false;
            } catch {
                return false;
            }
        },
        attachDropZoneState(zone) {
            if (!zone) {
                return;
            }
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
        setFileInputs(form, inputName, files) {
            if (!form) {
                return;
            }
            form.querySelectorAll('input').forEach(function (inp) {
                if (inp.name === inputName) {
                    inp.remove();
                }
            });
            files.forEach(function (file) {
                if (typeof DataTransfer === 'undefined') {
                    return;
                }
                var inp = document.createElement('input');
                inp.type = 'file';
                inp.name = inputName;
                inp.className = 'd-none';
                var dt = new DataTransfer();
                dt.items.add(file);
                inp.files = dt.files;
                form.appendChild(inp);
            });
        },
    };
})();
