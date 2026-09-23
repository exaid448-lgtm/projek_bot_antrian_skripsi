document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formAkun');
    const loketSelect = document.getElementById('sel_loket');
    const layananSelect = document.getElementById('sel_layanan');
    
    // Elemen untuk Search
    const searchInput = document.getElementById('searchInput');
    const btnSearch = document.getElementById('btnSearch');
    const tableBody = document.getElementById('akunTableBody');

    // ==========================================
    // 1. FITUR SEARCH (MANUAL KLIK TOMBOL)
    // ==========================================
    function filterTable() {
        const filter = searchInput.value.toLowerCase();
        const rows = tableBody.getElementsByTagName('tr');

        for (let i = 0; i < rows.length; i++) {
            // Kolom: Nama (index 1), Email (index 2), Username (index 3)
            let nameCol = rows[i].getElementsByTagName('td')[1];
            let emailCol = rows[i].getElementsByTagName('td')[2];
            let userCol = rows[i].getElementsByTagName('td')[3];

            if (nameCol || emailCol || userCol) {
                const txtName = nameCol.textContent || nameCol.innerText;
                const txtEmail = emailCol.textContent || emailCol.innerText;
                const txtUser = userCol.textContent || userCol.innerText;

                if (
                    txtName.toLowerCase().indexOf(filter) > -1 || 
                    txtEmail.toLowerCase().indexOf(filter) > -1 ||
                    txtUser.toLowerCase().indexOf(filter) > -1
                ) {
                    rows[i].style.display = "";
                } else {
                    rows[i].style.display = "none";
                }
            }
        }
    }

    // Jalankan search HANYA saat tombol diklik
    if(btnSearch) {
        btnSearch.addEventListener('click', function(e) {
            e.preventDefault(); // Mencegah reload jika berada dalam form
            filterTable();
        });
    }

    // Opsi tambahan: Jika user menekan tombol 'Enter' di keyboard saat di input search
    if(searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                filterTable();
            }
        });
    }


    // ==========================================
    // 2. HANDLE DROPDOWN DINAMIS (EDIT MODAL)
    // ==========================================
    loketSelect.addEventListener('change', function () {
        const selectedLoket = this.value;
        layananSelect.innerHTML = '<option value="">-- Pilih Layanan --</option>';
        if (selectedLoket) {
            layananSelect.disabled = false;
            const filtered = window.allLoketData.filter(item => item.nama_loket === selectedLoket);
            filtered.forEach(item => {
                let opt = new Option(item.nama_pelayanan, item.id_loket);
                layananSelect.add(opt);
            });
        } else {
            layananSelect.disabled = true;
        }
    });

    // ==========================================
    // 2.5 HANDLE DROPDOWN DINAMIS (UNDANG MODAL)
    // ==========================================
    const undangLoketSelect = document.getElementById('undang_sel_loket');
    const undangLayananSelect = document.getElementById('undang_sel_layanan');
    if(undangLoketSelect) {
        undangLoketSelect.addEventListener('change', function () {
            const selectedLoket = this.value;
            undangLayananSelect.innerHTML = '<option value="">-- Pilih Layanan --</option>';
            if (selectedLoket) {
                undangLayananSelect.disabled = false;
                const filtered = window.allLoketData.filter(item => item.nama_loket === selectedLoket);
                filtered.forEach(item => {
                    let opt = new Option(item.nama_pelayanan, item.id_loket);
                    undangLayananSelect.add(opt);
                });
            } else {
                undangLayananSelect.disabled = true;
            }
        });
    }

    // ==========================================
    // 4. MODAL CONTROL
    // ==========================================
    window.showUndangModal = function () {
        document.getElementById('formUndang').reset();
        undangLayananSelect.innerHTML = '<option value="">-- Pilih Layanan --</option>';
        undangLayananSelect.disabled = true;
        openModal('modalUndang');
    };

    window.showEditModal = function (data) {
        document.getElementById('modalTitle').innerText = "Edit Data Karyawan";
        form.action = "/super-admin/data-akun/update/" + data.id;
        document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="POST">';
        
        document.getElementById('in_name').value = data.name;
        document.getElementById('in_email').value = data.email;
        document.getElementById('in_jenis_kelamin').value = data.jk;
        document.getElementById('in_tanggal_lahir').value = data.tgl;

        if(data.loket) {
            const found = window.allLoketData.find(l => l.id_loket == data.loket);
            if(found) {
                loketSelect.value = found.nama_loket;
                loketSelect.dispatchEvent(new Event('change'));
                setTimeout(() => {
                    layananSelect.value = data.loket;
                }, 150);
            }
        }

        window.currentEditUserId = data.id;

        openModal('modalAkun');
    };

    window.kirimResetAkun = function() {
        if(!window.currentEditUserId) return;
        
        Swal.fire({
            title: 'Kirim Email Reset?',
            text: "Karyawan ini akan menerima email berisi tautan untuk mengatur ulang akun mereka.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Kirim Email!'
        }).then((result) => {
            if (result.isConfirmed) {
                const formReset = document.createElement('form');
                formReset.method = 'POST';
                formReset.action = `/super-admin/data-akun/reset-password/${window.currentEditUserId}`;
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = document.querySelector('input[name="_token"]').value;
                
                formReset.appendChild(csrfInput);
                document.body.appendChild(formReset);
                formReset.submit();
            }
        });
    };

    window.showDeleteModal = function (id) {
        const formDelete = document.getElementById('formDelete');
        formDelete.action = "/super-admin/data-akun/delete/" + id;
        openModal('modalDelete');
    };

    window.openModal = (id) => document.getElementById(id).classList.add('show');
    window.closeModal = (id) => document.getElementById(id).classList.remove('show');
});