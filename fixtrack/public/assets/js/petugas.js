
// ==========================================
// MENAMPILKAN DETAIL PENGADUAN
// ==========================================

function showComplaintDetail(button) {
    const modal = document.getElementById('detailModal');
    detailCompletionNotes: 'completion-notes'

    if (!modal) {
        console.error('Modal detail tidak ditemukan!');
        return;
    }

    // Mengambil data dari tombol Detail
    const fields = {
        detailCode: 'code',
        detailTitle: 'title',
        detailCategory: 'category',
        detailReporter: 'reporter',
        detailFacility: 'facility',
        detailStatus: 'status',
        detailDescription: 'description',
        detailNotes: 'notes',
        detailAdminNote: 'admin-note',
        detailCompletionNotes: 'completionNotes'
    };

    // Mengisi informasi pengaduan
    Object.entries(fields).forEach(([elementId, attribute]) => {
        const element = document.getElementById(elementId);

        if (element) {
            element.textContent = button.dataset[attribute] || '-';
        }
    });

    // Mengisi informasi lokasi
    const locationElement = document.getElementById('detailLocation');

    if (locationElement) {
        locationElement.textContent =
            (button.dataset.building || '-') + ' - ' +
            (button.dataset.room || '-');
    }

    // Mengatur foto kerusakan
    const photo = button.dataset.photo;
    const photoElement = document.getElementById('detailPhoto');
    const noPhoto = document.getElementById('noPhoto');

    if (photo && photoElement) {

        if (photo.startsWith('http')) {
            photoElement.src = photo;
        } else {
            const baseUrl = window.FIXTRACK_BASE_URL || '';

            photoElement.src =
                baseUrl + '/' + photo.replace(/^\/+/, '');
        }

        photoElement.style.display = 'block';

        if (noPhoto) {
            noPhoto.style.display = 'none';
        }

        // Jika foto gagal dimuat
        photoElement.onerror = function () {
            photoElement.style.display = 'none';

            if (noPhoto) {
                noPhoto.textContent = 'Foto gagal dimuat.';
                noPhoto.style.display = 'block';
            }
        };

    } else {

        if (photoElement) {
            photoElement.style.display = 'none';
            photoElement.src = '';
        }

        if (noPhoto) {
            noPhoto.textContent = 'Tidak ada foto yang dilampirkan.';
            noPhoto.style.display = 'block';
        }
    }

    // Menampilkan modal
    modal.classList.add('show');
}


// ==========================================
// MENUTUP DETAIL PENGADUAN
// ==========================================

function closeComplaintDetail() {
    const modal = document.getElementById('detailModal');

    if (modal) {
        modal.classList.remove('show');
    }
}


// ==========================================
// MENUTUP MODAL KETIKA AREA LUAR DIKLIK
// ==========================================

document.addEventListener('DOMContentLoaded', function () {

    const detailModal = document.getElementById('detailModal');

    if (detailModal) {
        detailModal.addEventListener('click', function (event) {
            if (event.target === this) {
                closeComplaintDetail();
            }
        });
    }

});


// ==========================================
// MODAL CATATAN PENYELESAIAN
// ==========================================

function openCompletionModal(assignmentId) {
    const modal = document.getElementById('completionModal');
    const idInput = document.getElementById('completionAssignmentId');
    const notesInput = document.getElementById('completionNotes');

    if (!modal || !idInput || !notesInput) {
        console.error('Form penyelesaian tidak ditemukan!');
        return;
    }

    idInput.value = assignmentId;
    notesInput.value = '';

    modal.classList.add('show');
}

function closeCompletionModal() {
    const modal = document.getElementById('completionModal');

    if (modal) {
        modal.classList.remove('show');
    }
}

// Menutup modal jika area luar diklik
document.addEventListener('DOMContentLoaded', function () {
    const completionModal = document.getElementById('completionModal');

    if (completionModal) {
        completionModal.addEventListener('click', function (event) {
            if (event.target === this) {
                closeCompletionModal();
            }
        });
    }
});
