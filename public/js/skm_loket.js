// public/js/skm_loket.js

$(document).ready(function() {
    // Jalankan pengecekan setiap 5 detik
    setInterval(function() {
        // Hanya cek jika modal SKM sedang tidak terbuka
        if (!$('#modalSKM').is(':visible')) {
            $.get("/cek-status-skm", function(data) {
                console.log("Response SKM:", data); // Ditambahkan untuk membantu debug
                if (data.perlu_skm) {
                    // Isi hidden input untuk identitas antrian & loket
                    $('#id_antrian_modal').val(data.id_antrian);
                    $('#id_loket_modal').val(data.id_loket);
                    
                    // Render daftar soal ke dalam modal
                    let htmlSoal = '';
                    data.soal.forEach((s, index) => {
                        htmlSoal += `
                            <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-100">
                                <p class="font-bold text-gray-700 mb-3">${index + 1}. ${s.pertanyaan}</p>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="flex items-center gap-2 text-xs bg-white p-2 rounded border border-gray-200 cursor-pointer hover:bg-blue-50">
                                        <input type="radio" name="jawaban[${s.id_soal}]" value="sangat bagus" required> Sangat Bagus
                                    </label>
                                    <label class="flex items-center gap-2 text-xs bg-white p-2 rounded border border-gray-200 cursor-pointer hover:bg-blue-50">
                                        <input type="radio" name="jawaban[${s.id_soal}]" value="bagus"> Bagus
                                    </label>
                                    <label class="flex items-center gap-2 text-xs bg-white p-2 rounded border border-gray-200 cursor-pointer hover:bg-blue-50">
                                        <input type="radio" name="jawaban[${s.id_soal}]" value="kurang"> Kurang
                                    </label>
                                    <label class="flex items-center gap-2 text-xs bg-white p-2 rounded border border-gray-200 cursor-pointer hover:bg-blue-50">
                                        <input type="radio" name="jawaban[${s.id_soal}]" value="sangat kurang"> Sangat Kurang
                                    </label>
                                </div>
                            </div>`;
                    });
                    
                    $('#isi-soal-skm').html(htmlSoal);
                    
                    // Tampilkan modal (tidak bisa ditutup kecuali isi)
                    window.dispatchEvent(new CustomEvent('open-modal-skm'));
                }
            });
        }
    }, 5000);

    // Proses Simpan SKM via AJAX
// Proses Simpan SKM via AJAX
    $('#form-skm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        
        $.ajax({
            url: "/simpan-skm",
            type: "POST",
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                if (res.success) {
                    // PERBAIKAN: Gunakan event Alpine untuk menutup modal
                    window.dispatchEvent(new CustomEvent('close-modal-skm'));
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Terima kasih! Penilaian Anda sangat berarti.',
                        confirmButtonColor: '#3085d6',
                    });
                    
                    // Opsional: Reset form agar tidak bertumpuk jika ada antrean lain
                    $('#form-skm')[0].reset();
                    $('#isi-soal-skm').html('');
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Gagal menyimpan survei. Silakan coba lagi.',
                    confirmButtonColor: '#d33',
                });
            }
        });
    });
});