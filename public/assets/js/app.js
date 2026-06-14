const summaryList = document.getElementById("summaryList");
const btnKonfirmasi = document.getElementById("btnKonfirmasi");
const btnBayar = document.getElementById("btnBayar");
const btnBatal = document.getElementById("btnBatal");
const estimasiTotal = document.getElementById("estimasiTotal");
const buktiReservasi = document.getElementById("buktiReservasi");
const buktiReservasiInfo = document.getElementById("buktiReservasiInfo");
const summaryListFinal = document.getElementById("summaryListFinal");
const paketMember = document.getElementById("paketMember");
const estimasiMember = document.getElementById("estimasiMember");
const buktiMembership = document.getElementById("buktiMembership");
const buktiMembershipInfo = document.getElementById("buktiMembershipInfo");
const btnDaftarMembership = document.getElementById("btnDaftarMembership");
const namaPIC = document.getElementById("namaPIC");
const kontakPIC = document.getElementById("kontakbtnToConfirmPIC");
const stepItems = document.querySelectorAll(".step-item[data-step]");
const stepPanels = document.querySelectorAll(".step-panel[data-step]");
const btnToConfirm = document.getElementById("btnToConfirm");
const btnBackToStep1 = document.getElementById("btnBackToStep1");
const btnBackToStep2 = document.getElementById("btnBackToStep2");
const btnResetBooking = document.getElementById("btnResetBooking");
const catatanPreview = document.getElementById("catatanPreview");
const bookingStatusText = document.getElementById("bookingStatusText");
const bookingStatusBadge = document.getElementById("bookingStatusBadge");

// Harga ambil dari variabel global (yang akan diset oleh backend dari blade),
// kalau belum didefinisikan pakai default 100000
const hargaFlat =
    typeof window.HARGA_SEWA !== "undefined" ? window.HARGA_SEWA : 100000;

function hitungEstimasi() {
    const jamMain = document.getElementById("jamMain")?.value;
    const jamSelesaiMain = document.getElementById("jamSelesaiMain")?.value;
    let durasi = 1;

    if (jamMain && jamSelesaiMain) {
        const hMulai = parseInt(jamMain.split(":")[0]);
        const hSelesai = parseInt(jamSelesaiMain.split(":")[0]);
        durasi = hSelesai - hMulai;
        if (durasi < 1) durasi = 1;
    }

    return durasi * hargaFlat;
}

function formatRupiah(nominal) {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        maximumFractionDigits: 0,
    }).format(nominal);
}

function tampilkanNamaFile(inputFile, targetInfo) {
    if (!targetInfo) {
        return;
    }

    const fileName = inputFile?.files?.[0]?.name;
    targetInfo.textContent = fileName
        ? `File dipilih: ${fileName}`
        : "Belum ada file dipilih.";
}

function setSummary(statusText) {
    const tanggal = document.getElementById("tanggalMain")?.value || "-";
    const jamMulai = document.getElementById("jamMain")?.value || "-";
    const jamSelesai = document.getElementById("jamSelesaiMain")?.value || "-";

    let durasi = "-";
    if (jamMulai !== "-" && jamSelesai !== "-") {
        durasi =
            parseInt(jamSelesai.split(":")[0]) -
            parseInt(jamMulai.split(":")[0]);
    }

    const jamText = jamMulai !== "-" ? `${jamMulai} s/d ${jamSelesai}` : "-";
    const estimasi = hitungEstimasi();

    if (!summaryList) {
        return;
    }

    summaryList.innerHTML = [
        `<li class="mb-2">Tanggal: <span class="badge text-bg-success ms-1">${tanggal}</span></li>`,
        `<li class="mb-2">Jam: <span class="badge text-bg-primary ms-1">${jamText}</span></li>`,
        `<li class="mb-2">Durasi: <span class="badge text-bg-info ms-1">${durasi} jam</span></li>`,
        `<li class="mb-2">Harga: <span class="badge text-bg-dark ms-1">${formatRupiah(estimasi)}</span></li>`,
        `<li>Status: <span class="badge text-bg-warning ms-1">${statusText}</span></li>`,
    ].join("");

    if (estimasiTotal) {
        estimasiTotal.textContent = formatRupiah(estimasi);
    }

    if (summaryListFinal) {
        summaryListFinal.innerHTML = [
            `<li class="mb-2">Tanggal: <span class="badge text-bg-success ms-1">${tanggal}</span></li>`,
            `<li class="mb-2">Jam: <span class="badge text-bg-primary ms-1">${jamText}</span></li>`,
            `<li class="mb-2">Durasi: <span class="badge text-bg-info ms-1">${durasi} jam</span></li>`,
            `<li class="mb-2">Total Harga: <span class="badge text-bg-dark ms-1">${formatRupiah(estimasi)}</span></li>`,
            `<li>Status: <span class="badge text-bg-warning ms-1">${statusText}</span></li>`,
        ].join("");
    }

    // Update Ringkasan Pembayaran di step 3 agar sama
    const totalPembayaranElems = document.querySelectorAll(
        '.step-panel[data-step="3"] .estimated-total strong',
    );
    totalPembayaranElems.forEach((el) => {
        el.textContent = formatRupiah(estimasi);
    });
}

function updateCatatanPreview() {
    if (!catatanPreview) {
        return;
    }

    const catatanValue = document.getElementById("catatan")?.value?.trim();
    catatanPreview.textContent = catatanValue || "-";
}

const stepState = {
    current: 1,
    max: 1,
};

function setStep(step) {
    if (!stepPanels.length || !stepItems.length) {
        return;
    }

    const nextStep = Number(step);
    if (Number.isNaN(nextStep) || nextStep < 1 || nextStep > 4) {
        return;
    }

    if (nextStep > stepState.max) {
        return;
    }

    stepState.current = nextStep;
    stepPanels.forEach((panel) => {
        const panelStep = Number(panel.dataset.step || 0);
        panel.classList.toggle("is-active", panelStep === nextStep);
    });

    stepItems.forEach((item) => {
        const itemStep = Number(item.dataset.step || 0);
        item.classList.toggle("active", itemStep === nextStep);
        item.disabled = itemStep > stepState.max;
    });
}

function setMaxStep(step) {
    const nextMax = Math.max(stepState.max, Number(step));
    stepState.max = Math.min(nextMax, 4);
    setStep(stepState.current);
}

function setStatus(text, badgeClass) {
    if (bookingStatusText) {
        bookingStatusText.textContent = text;
    }
    if (bookingStatusBadge) {
        bookingStatusBadge.className = `badge ${badgeClass}`;
    }
}

["tanggalMain", "jamMain", "durasiMain"].forEach((id) => {
    const element = document.getElementById(id);
    element?.addEventListener("change", () => {
        setSummary("Menunggu konfirmasi");
    });
});

document
    .getElementById("catatan")
    ?.addEventListener("input", updateCatatanPreview);

setSummary("Menunggu konfirmasi");
updateCatatanPreview();
setStep(1);

paketMember?.addEventListener("change", () => {
    if (estimasiMember) {
        estimasiMember.textContent = formatRupiah(
            Number(paketMember.value || 0),
        );
    }
});

buktiReservasi?.addEventListener("change", () => {
    tampilkanNamaFile(buktiReservasi, buktiReservasiInfo);
    document.getElementById("buktiReservasiError").textContent = "";
    document.getElementById("buktiReservasi").classList.remove("is-invalid");
});

buktiMembership?.addEventListener("change", () => {
    tampilkanNamaFile(buktiMembership, buktiMembershipInfo);
});

if (estimasiMember && paketMember) {
    estimasiMember.textContent = formatRupiah(Number(paketMember.value || 0));
}

// Di-comment atau dimatikan, karena kontrol ganti step sekarang ditangani dari respons backend (di reservasi.blade.php)
// btnKonfirmasi?.addEventListener("click", () => {
//    setSummary("Pesanan terkonfirmasi");
//    setMaxStep(3);
//    setStep(3);
//});

btnBayar?.addEventListener("click", () => {
    if (!buktiReservasi?.files?.length) {
        alert("Silakan upload bukti pembayaran reservasi terlebih dahulu.");
        return;
    }

    const reservasiId = document.getElementById("reservasiId")?.value;
    if (!reservasiId) {
        // Jangan teruskan setStatus ke tahap berikutnya jika submitnya gagal karena ID kosong
        return;
    }

    // Transisi step 4 di-handle oleh fetch response di reservasi.blade.php
    // agar tidak pindah step saat backend menolak (misalnya expired)
});

btnBatal?.addEventListener("click", () => {
    setSummary("Pesanan dibatalkan");
    setStatus(
        "Pesanan dibatalkan. Silakan pilih jadwal baru.",
        "text-bg-danger",
    );
    setMaxStep(4);
    setStep(4);
});

btnToConfirm?.addEventListener("click", () => {
    const tanggal = document.getElementById("tanggalMain")?.value;
    if (!tanggal) {
        alert("Silakan pilih tanggal main terlebih dahulu.");
        return;
    }

    updateCatatanPreview();
    setSummary("Menunggu konfirmasi");
    setMaxStep(2);
    setStep(2);
});

btnBackToStep1?.addEventListener("click", () => {
    setStep(1);
});

btnBackToStep2?.addEventListener("click", () => {
    setStep(2);
});

btnResetBooking?.addEventListener("click", () => {
    setStatus("Pembayaran berhasil. Booking kamu aktif.", "text-bg-success");
    setSummary("Menunggu konfirmasi");
    stepState.max = 1;
    setStep(1);
});

stepItems.forEach((item) => {
    item.addEventListener("click", () => {
        setStep(item.dataset.step);
    });
});


