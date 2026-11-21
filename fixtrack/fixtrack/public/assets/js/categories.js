
const categoryModal = document.getElementById('categoryModal');

function openCategoryModal() {

    document.getElementById('categoryModalTitle').textContent =
        'Tambah Kategori';

    document.querySelector('#categoryModal form').reset();

    document.getElementById('categoryId').value = '';

    categoryModal.classList.add('show');
}

function closeCategoryModal() {

    categoryModal.classList.remove('show');
}

function editCategory(category) {

    document.getElementById('categoryModalTitle').textContent =
        'Edit Kategori';

    document.getElementById('categoryId').value =
        category.id;

    document.getElementById('categoryName').value =
        category.name;

    document.getElementById('categoryDescription').value =
        category.description || '';

    categoryModal.classList.add('show');
}

// Tutup modal jika klik bagian luar
categoryModal.addEventListener('click', function(event) {

    if (event.target === categoryModal) {
        closeCategoryModal();
    }

});

// PENCARIAN KATEGORI

const searchCategory =
    document.getElementById('searchCategory');

searchCategory.addEventListener('input', function() {

    const keyword = this.value.toLowerCase();

    const rows = document.querySelectorAll(
        '#categoryTableBody tr'
    );

    let visibleCount = 0;

    rows.forEach(row => {

        const name = row.dataset.name;
        const description = row.dataset.description;

        const visible =
            name.includes(keyword) ||
            description.includes(keyword);

        row.style.display = visible ? '' : 'none';

        if (visible) {
            visibleCount++;
        }

    });

    document.getElementById('categoryEmpty').hidden =
        visibleCount !== 0;

});
