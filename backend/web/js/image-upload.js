(function () {
    'use strict';
    const zone = document.getElementById('upload-zone');
    const input = document.getElementById('image-file');
    const preview = document.getElementById('image-preview');
    const removeBtn = document.getElementById('remove-preview');
    if (!zone || !input) return;

    zone.addEventListener('click', function () { input.click(); });
    zone.addEventListener('dragover', function (e) { e.preventDefault(); zone.classList.add('dragover'); });
    zone.addEventListener('dragleave', function () { zone.classList.remove('dragover'); });
    zone.addEventListener('drop', function (e) {
        e.preventDefault();
        zone.classList.remove('dragover');
        if (e.dataTransfer.files[0]) showPreview(e.dataTransfer.files[0]);
    });
    input.addEventListener('change', function () {
        if (input.files[0]) showPreview(input.files[0]);
    });
    if (removeBtn) {
        removeBtn.addEventListener('click', function () {
            input.value = '';
            preview.src = '';
            preview.classList.add('d-none');
            removeBtn.classList.add('d-none');
        });
    }
    function showPreview(file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
            if (removeBtn) removeBtn.classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    }
})();
