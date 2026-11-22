
const complaintModal = document.getElementById('complaintModal');

// Pastikan data tersedia
console.log('DATA PENGADUAN:', complaintData);

if (Array.isArray(complaintData)) {
    console.table(
        complaintData.map(item => ({
            id: item.id,
            code: item.complaint_code,
            assignment_id: item.assignment_id,
            officer_name: item.officer_name,
            assignment_status: item.assignment_status,
            completion_notes: item.completion_notes
        }))
    );
}

// ==========================================
// FORMAT TANGGAL DAN WAKTU
// ==========================================

function formatDateTime(value) {
    if (!value) {
        return '-';
    }

    const date = new Date(String(value).replace(' ', 'T'));

    if (isNaN(date.getTime())) {
        return value;
    }

    return date.toLocaleString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// ==========================================
// LABEL STATUS PENUGASAN
// ==========================================

function assignmentStatusLabel(status) {
    const labels = {
        ditugaskan: 'Ditugaskan',
        diterima: 'Diterima',
        diproses: 'Sedang Diproses',
        selesai: 'Selesai',
        dibatalkan: 'Dibatalkan'
    };

    return labels[status] || status || '-';
}

// ==========================================
// DETAIL PENGADUAN
// ==========================================

function showComplaint(id) {

    const item = complaintData.find(
        complaint => Number(complaint.id) === Number(id)
    );

    if (!item) {
        alert('Data pengaduan tidak ditemukan.');
        return;
    }

    if (!complaintModal) {
        console.error('Modal pengaduan tidak ditemukan!');
        return;
    }

    // INFORMASI DASAR

    const detailFields = {
        detailCode: item.complaint_code,
        detailTitle: item.title,
        detailReporter: item.reporter_name,
        detailFacility:
            (item.building_name || '-') + ' - ' +
            (item.room_name || '-') + ' - ' +
            (item.facility_name || '-'),
        detailCategory: item.category_name,
        detailDescription: item.description
    };

    Object.entries(detailFields).forEach(([elementId, value]) => {
        const element = document.getElementById(elementId);

        if (element) {
            element.textContent = value || '-';
        }
    });

    // ID UNTUK FORM VERIFIKASI DAN PENOLAKAN

    const verifyId = document.getElementById('verifyId');
    const rejectId = document.getElementById('rejectId');

    if (verifyId) {
        verifyId.value = item.id;
    }

    if (rejectId) {
        rejectId.value = item.id;
    }

    // ======================================
    // FOTO KERUSAKAN
    // ======================================

    const photoContainer =
        document.getElementById('detailPhotoContainer');

    const photo =
        document.getElementById('detailPhoto');

    if (photo && photoContainer) {

        if (item.photo) {

            const photoPath = String(item.photo).replace(/^\/+/, '');

            photo.src = photoPath.startsWith('http')
                ? photoPath
                : complaintBaseUrl.replace(/\/+$/, '') + '/' + photoPath;

            photoContainer.hidden = false;

        } else {

            photo.removeAttribute('src');
            photoContainer.hidden = true;

        }
    }

    // ======================================
    // CATATAN ADMIN
    // ======================================

    const noteContainer =
        document.getElementById('detailNoteContainer');

    const note =
        document.getElementById('detailNote');

    if (note && noteContainer) {

        if (item.admin_note || item.rejection_reason) {

            note.textContent =
                item.rejection_reason || item.admin_note;

            noteContainer.hidden = false;

        } else {

            note.textContent = '-';
            noteContainer.hidden = true;

        }
    }

    // ======================================
    // INFORMASI PENUGASAN PETUGAS
    // ======================================

    const assignmentContainer =
        document.getElementById('detailAssignmentContainer');

    if (assignmentContainer) {

        if (item.assignment_id) {

            assignmentContainer.hidden = false;

            const assignmentFields = {
                detailOfficer: item.officer_name,
                detailAssignmentStatus:
                    assignmentStatusLabel(item.assignment_status),
                detailAssignmentNotes: item.assignment_notes,
                detailCompletionNotes: item.completion_notes,
                detailAssignedAt: formatDateTime(item.assigned_at),
                detailAcceptedAt: formatDateTime(item.accepted_at),
                detailCompletedAt: formatDateTime(item.completed_at)
            };

            Object.entries(assignmentFields).forEach(([elementId, value]) => {
                const element = document.getElementById(elementId);

                if (element) {
                    element.textContent = value || '-';
                }
            });

        } else {

            assignmentContainer.hidden = true;

        }
    }

    // ======================================
    // TOMBOL VERIFIKASI DAN PENOLAKAN
    // ======================================

    const complaintActions =
        document.getElementById('complaintActions');

    if (complaintActions) {
        complaintActions.style.display =
            item.status === 'menunggu_verifikasi' ? 'flex' : 'none';
    }

    // ======================================
    // TAMPILKAN MODAL
    // ======================================

    complaintModal.classList.add('show');
}

// ==========================================
// MENUTUP MODAL DETAIL
// ==========================================

function closeComplaint() {

    if (complaintModal) {
        complaintModal.classList.remove('show');
    }
}

// Tutup modal ketika klik area luar modal

if (complaintModal) {

    complaintModal.addEventListener('click', function(event) {

        if (event.target === complaintModal) {
            closeComplaint();
        }

    });
}

// ==========================================
// PENCARIAN DAN FILTER STATUS
// ==========================================

const searchComplaint =
    document.getElementById('searchComplaint');

const filterStatus =
    document.getElementById('filterComplaintStatus');

function filterComplaints() {

    if (!searchComplaint || !filterStatus) {
        return;
    }

    const keyword = searchComplaint.value.toLowerCase();
    const status = filterStatus.value;

    const rows = document.querySelectorAll(
        '#complaintTableBody tr'
    );

    let visibleCount = 0;

    rows.forEach(row => {

        const searchText =
            (row.dataset.search || '').toLowerCase();

        const rowStatus =
            row.dataset.status || '';

        const matchSearch = searchText.includes(keyword);

        const matchStatus =
            status === '' || rowStatus === status;

        const visible = matchSearch && matchStatus;

        row.style.display = visible ? '' : 'none';

        if (visible) {
            visibleCount++;
        }

    });

    const emptyElement =
        document.getElementById('complaintEmpty');

    if (emptyElement) {
        emptyElement.hidden = visibleCount !== 0;
    }
}

// Event pencarian dan filter

if (searchComplaint && filterStatus) {

    searchComplaint.addEventListener(
        'input',
        filterComplaints
    );

    filterStatus.addEventListener(
        'change',
        filterComplaints
    );

}
