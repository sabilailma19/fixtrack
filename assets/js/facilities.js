
const facilityModal = document.getElementById('facilityModal');

// MODAL TAMBAH
function openFacilityModal() {

    document.getElementById('facilityModalTitle').textContent =
        'Tambah Fasilitas';

    document.querySelector('#facilityModal form').reset();

    document.getElementById('facilityId').value = '';

    facilityModal.classList.add('show');
}


// TUTUP MODAL
function closeFacilityModal() {

    facilityModal.classList.remove('show');
}


// MODAL EDIT
function editFacility(facility) {

    document.getElementById('facilityModalTitle').textContent =
        'Edit Fasilitas';

    document.getElementById('facilityId').value =
        facility.id;

    document.getElementById('buildingName').value =
        facility.building_name;

    document.getElementById('roomName').value =
        facility.room_name;

    document.getElementById('facilityName').value =
        facility.facility_name;

    document.getElementById('facilityDescription').value =
        facility.description || '';

    facilityModal.classList.add('show');
}


// TUTUP KETIKA KLIK DI LUAR MODAL
facilityModal.addEventListener('click', function(event) {

    if (event.target === facilityModal) {
        closeFacilityModal();
    }

});


// =====================================
// PENCARIAN DAN FILTER FASILITAS
// =====================================

const searchFacility =
    document.getElementById('searchFacility');

const filterBuilding =
    document.getElementById('filterBuilding');

const filterStatus =
    document.getElementById('filterFacilityStatus');


function filterFacilities() {

    const keyword = searchFacility.value.toLowerCase();

    const building = filterBuilding.value;

    const status = filterStatus.value;

    const rows = document.querySelectorAll(
        '#facilityTableBody tr'
    );

    let visibleCount = 0;

    rows.forEach(row => {

        const searchText = row.dataset.search;

        const rowBuilding = row.dataset.building;

        const rowStatus = row.dataset.status;

        const matchSearch =
            searchText.includes(keyword);

        const matchBuilding =
            building === '' || rowBuilding === building;

        const matchStatus =
            status === '' || rowStatus === status;

        const visible =
            matchSearch && matchBuilding && matchStatus;

        row.style.display = visible ? '' : 'none';

        if (visible) {
            visibleCount++;
        }

    });

    document.getElementById('facilityEmpty').hidden =
        visibleCount !== 0;

}


searchFacility.addEventListener(
    'input',
    filterFacilities
);

filterBuilding.addEventListener(
    'change',
    filterFacilities
);

filterStatus.addEventListener(
    'change',
    filterFacilities
);
