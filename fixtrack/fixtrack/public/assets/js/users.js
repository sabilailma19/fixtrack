
const modal = document.getElementById('userModal');

function openModal() {
    document.getElementById('modalTitle').textContent =
        'Tambah Pengguna';

    document.querySelector('#userModal form').reset();

    document.getElementById('userId').value = '';
    document.getElementById('userPassword').required = true;
    document.getElementById('passwordHint').textContent =
        '(Minimal 8 karakter)';

    modal.classList.add('show');
}

function closeModal() {
    modal.classList.remove('show');
}

function editUser(user) {
    document.getElementById('modalTitle').textContent =
        'Edit Pengguna';

    document.getElementById('userId').value = user.id;
    document.getElementById('userName').value = user.name;
    document.getElementById('userEmail').value = user.email;
    document.getElementById('userRole').value = user.role;
    document.getElementById('userIdentity').value =
        user.identity_number || '';
    document.getElementById('userPhone').value =
        user.phone || '';

    document.getElementById('userPassword').value = '';
    document.getElementById('userPassword').required = false;

    document.getElementById('passwordHint').textContent =
        '(Kosongkan jika tidak ingin mengganti password)';

    modal.classList.add('show');
}

// Tutup modal ketika klik area luar
modal.addEventListener('click', function(event) {
    if (event.target === modal) {
        closeModal();
    }
});

// PENCARIAN DAN FILTER
const searchInput = document.getElementById('searchUser');
const roleFilter = document.getElementById('filterRole');
const statusFilter = document.getElementById('filterStatus');

function filterUsers() {
    const search = searchInput.value.toLowerCase();
    const role = roleFilter.value;
    const status = statusFilter.value;

    const rows = document.querySelectorAll(
        '#userTableBody tr'
    );

    let visibleCount = 0;

    rows.forEach(row => {
        const name = row.dataset.name;
        const email = row.dataset.email;
        const rowRole = row.dataset.role;
        const rowStatus = row.dataset.status;

        const matchSearch =
            name.includes(search) || email.includes(search);

        const matchRole =
            role === '' || rowRole === role;

        const matchStatus =
            status === '' || rowStatus === status;

        const visible =
            matchSearch && matchRole && matchStatus;

        row.style.display = visible ? '' : 'none';

        if (visible) visibleCount++;
    });

    document.getElementById('emptyMessage').hidden =
        visibleCount !== 0;
}

searchInput.addEventListener('input', filterUsers);
roleFilter.addEventListener('change', filterUsers);
statusFilter.addEventListener('change', filterUsers);
