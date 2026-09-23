function gantiStatusAntrian(idAntrian, statusBaru) {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    // Ambil URL dinamis dari meta tag
    const urlEndpoint = document.querySelector('meta[name="update-status-url"]').getAttribute('content');

    fetch(urlEndpoint, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": token
        },
        body: JSON.stringify({
            id_antrian: idAntrian,
            status_antrian: statusBaru
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // 1. Update text waktu panggil & selesai secara real-time di baris tabel tanpa reload
                document.getElementById(`panggil-${idAntrian}`).innerText = data.waktu_panggil ?? '-';
                document.getElementById(`selesai-${idAntrian}`).innerText = data.waktu_selesai ?? '-';

                // 2. Update style warna label statusnya
                const labelStatus = document.getElementById(`label-${idAntrian}`);
                labelStatus.innerText = statusBaru.charAt(0).toUpperCase() + statusBaru.slice(1);
                labelStatus.className = `status ${statusBaru}`;
            } else {
                alert("Gagal memperbarui status: " + data.message);
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Terjadi kesalahan sistem jaringan.");
        });
}