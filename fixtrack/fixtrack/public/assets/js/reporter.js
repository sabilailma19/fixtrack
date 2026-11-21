
const photoInput = document.getElementById('photoInput');

const choosePhoto = document.getElementById('choosePhoto');

const photoPreview = document.getElementById('photoPreview');

const previewContainer =
    document.getElementById('photoPreviewContainer');

const removePhoto = document.getElementById('removePhoto');


// MEMILIH FOTO

choosePhoto.addEventListener('click', function() {
    photoInput.click();
});


// PREVIEW FOTO

photoInput.addEventListener('change', function() {

    const file = this.files[0];

    if (!file) return;

    const allowedTypes = [
        'image/jpeg',
        'image/png',
        'image/webp'
    ];

    if (!allowedTypes.includes(file.type)) {

        alert('Format foto harus JPG, PNG, atau WEBP.');

        this.value = '';

        return;
    }

    if (file.size > 3 * 1024 * 1024) {

        alert('Ukuran foto maksimal 3 MB.');

        this.value = '';

        return;
    }

    const reader = new FileReader();

    reader.onload = function(event) {

        photoPreview.src = event.target.result;

        previewContainer.hidden = false;

    };

    reader.readAsDataURL(file);

});


// HAPUS FOTO

removePhoto.addEventListener('click', function() {

    photoInput.value = '';

    photoPreview.src = '';

    previewContainer.hidden = true;

});
