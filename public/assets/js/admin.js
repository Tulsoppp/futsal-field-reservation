// const ADMIN_KEYS = {
//   jadwal: "jf_admin_jadwal",
//   reservasi: "jf_admin_reservasi",
//   membership: "jf_admin_membership",
//   pelanggan: "jf_admin_pelanggan",
// };

// const seedData = {
//   jadwal: [
//     {
//       id: "jdw-1",
//       tanggal: "2026-04-22",
//       jamMulai: "18:00",
//       jamSelesai: "20:00",
//       lapangan: "Lapangan 1 - Vinyl",
//       harga: 200000,
//       status: "Tersedia",
//     },
//     {
//       id: "jdw-2",
//       tanggal: "2026-04-22",
//       jamMulai: "20:00",
//       jamSelesai: "22:00",
//       lapangan: "Lapangan 2 - Rumput Sintetis",
//       harga: 240000,
//       status: "Penuh",
//     },
//     {
//       id: "jdw-3",
//       tanggal: "2026-04-23",
//       jamMulai: "14:00",
//       jamSelesai: "16:00",
//       lapangan: "Lapangan 4 - Hybrid",
//       harga: 150000,
//       status: "Tersedia",
//     },
//   ],
//   reservasi: [
//     {
//       id: "rsv-1",
//       namaTim: "Jaya Squad",
//       tanggal: "2026-04-20",
//       lapangan: "Lapangan 2 - Rumput Sintetis",
//       jam: "19:00",
//       durasi: 2,
//       total: 360000,
//       statusPembayaran: "Menunggu Validasi",
//       statusPembatalan: "-",
//       statusReservasi: "Pending",
//       createdAt: "2026-04-18T10:30:00",
//     },
//     {
//       id: "rsv-2",
//       namaTim: "Gili FC",
//       tanggal: "2026-04-19",
//       lapangan: "Lapangan 1 - Vinyl",
//       jam: "20:00",
//       durasi: 1,
//       total: 200000,
//       statusPembayaran: "Lunas",
//       statusPembatalan: "-",
//       statusReservasi: "Selesai",
//       createdAt: "2026-04-17T09:00:00",
//     },
//     {
//       id: "rsv-3",
//       namaTim: "Sahabat Bola",
//       tanggal: "2026-04-21",
//       lapangan: "Lapangan 3 - Vinyl",
//       jam: "18:00",
//       durasi: 2,
//       total: 400000,
//       statusPembayaran: "Lunas",
//       statusPembatalan: "Menunggu Validasi",
//       statusReservasi: "Disetujui",
//       createdAt: "2026-04-18T13:45:00",
//     },
//   ],
//   membership: [
//     {
//       id: "mbs-1",
//       namaTim: "Jaya Squad",
//       paket: "Pro Team",
//       masaAktif: "3 Bulan",
//       status: "Aktif",
//     },
//     {
//       id: "mbs-2",
//       namaTim: "Gili FC",
//       paket: "Basic",
//       masaAktif: "1 Bulan",
//       status: "Menunggu Aktivasi",
//     },
//   ],
//   pelanggan: [
//     {
//       id: "plg-1",
//       nama: "Ari Wibowo",
//       telepon: "081234567890",
//       email: "ari@jayafutsal.id",
//       statusMember: "Member",
//     },
//     {
//       id: "plg-2",
//       nama: "Fahmi Rizal",
//       telepon: "089912345678",
//       email: "fahmi@gmail.com",
//       statusMember: "Non Member",
//     },
//   ],
// };

// function formatRupiah(value) {
//   return new Intl.NumberFormat("id-ID", {
//     style: "currency",
//     currency: "IDR",
//     maximumFractionDigits: 0,
//   }).format(Number(value || 0));
// }

// function formatTanggal(value) {
//   if (!value) {
//     return "-";
//   }

//   const date = new Date(value);
//   if (Number.isNaN(date.getTime())) {
//     return value;
//   }

//   return new Intl.DateTimeFormat("id-ID", {
//     day: "2-digit",
//     month: "short",
//     year: "numeric",
//   }).format(date);
// }

// function getMonthKey(dateString) {
//   const date = new Date(dateString);
//   if (Number.isNaN(date.getTime())) {
//     return "Tanpa Bulan";
//   }

//   return new Intl.DateTimeFormat("id-ID", {
//     month: "long",
//     year: "numeric",
//   }).format(date);
// }

// function getData(key) {
//   const raw = localStorage.getItem(key);
//   if (!raw) {
//     return [];
//   }

//   try {
//     return JSON.parse(raw);
//   } catch (error) {
//     return [];
//   }
// }

// function setData(key, value) {
//   localStorage.setItem(key, JSON.stringify(value));
// }

// function createId(prefix) {
//   return `${prefix}-${Date.now()}-${Math.floor(Math.random() * 1000)}`;
// }

// function getQueryId() {
//   return new URLSearchParams(window.location.search).get("id") || "";
// }

// function bootstrapAdminData() {
//   Object.entries(ADMIN_KEYS).forEach(([name, key]) => {
//     if (!localStorage.getItem(key)) {
//       setData(key, seedData[name]);
//     }
//   });
// }

// function initAdminDashboard() {
//   const totalJadwalEl = document.getElementById("statTotalJadwal");
//   if (!totalJadwalEl) {
//     return;
//   }

//   const reservasi = getData(ADMIN_KEYS.reservasi);
//   const jadwal = getData(ADMIN_KEYS.jadwal);
//   const membership = getData(ADMIN_KEYS.membership);

//   const menungguBayar = reservasi.filter(
//     (item) => item.statusPembayaran === "Menunggu Validasi",
//   ).length;
//   const menungguBatal = reservasi.filter(
//     (item) => item.statusPembatalan === "Menunggu Validasi",
//   ).length;
//   const totalPendapatan = reservasi
//     .filter((item) => item.statusPembayaran === "Lunas")
//     .reduce((sum, item) => sum + Number(item.total || 0), 0);
//   const membershipAktif = membership.filter(
//     (item) => item.status === "Aktif",
//   ).length;

//   totalJadwalEl.textContent = String(jadwal.length);
//   document.getElementById("statMenungguBayar").textContent =
//     String(menungguBayar);
//   document.getElementById("statMenungguBatal").textContent =
//     String(menungguBatal);
//   document.getElementById("statPendapatan").textContent =
//     formatRupiah(totalPendapatan);
//   document.getElementById("statMembershipAktif").textContent =
//     String(membershipAktif);

//   const activityList = document.getElementById("recentActivityList");
//   const latest = [...reservasi]
//     .sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt))
//     .slice(0, 6);

//   activityList.innerHTML = latest
//     .map(
//       (item) => `
//       <li class="admin-activity-item">
//         <strong>${item.namaTim}</strong>
//         <span>${formatTanggal(item.tanggal)} | ${item.lapangan}</span>
//         <span class="badge text-bg-light border">${item.statusReservasi}</span>
//       </li>`,
//     )
//     .join("");
// }

// function initJadwalListPage() {
//   const tableBody = document.getElementById("jadwalTableBody");
//   if (!tableBody) {
//     return;
//   }

//   function render() {
//     const list = getData(ADMIN_KEYS.jadwal).sort((a, b) => {
//       const left = `${a.tanggal} ${a.jamMulai}`;
//       const right = `${b.tanggal} ${b.jamMulai}`;
//       return left.localeCompare(right);
//     });

//     tableBody.innerHTML = list
//       .map(
//         (item, index) => `
//         <tr>
//           <td>${index + 1}</td>
//           <td>${formatTanggal(item.tanggal)}</td>
//           <td>${item.jamMulai} - ${item.jamSelesai}</td>
//           <td>${item.lapangan}</td>
//           <td>${formatRupiah(item.harga)}</td>
//           <td><span class="badge ${
//             item.status === "Tersedia" ? "text-bg-success" : "text-bg-secondary"
//           }">${item.status}</span></td>
//           <td class="text-nowrap">
//             <a class="btn btn-sm btn-outline-dark" href="admin-jadwal-form.html?id=${item.id}">Edit</a>
//             <button class="btn btn-sm btn-outline-danger" data-delete-jadwal="${item.id}" type="button">Hapus</button>
//           </td>
//         </tr>`,
//       )
//       .join("");
//   }

//   tableBody.addEventListener("click", (event) => {
//     const deleteId = event.target.getAttribute("data-delete-jadwal");
//     if (!deleteId) {
//       return;
//     }

//     const list = getData(ADMIN_KEYS.jadwal);
//     setData(
//       ADMIN_KEYS.jadwal,
//       list.filter((item) => item.id !== deleteId),
//     );
//     render();
//   });

//   render();
// }

// function initReservasiAdmin() {
//   const tableBody = document.getElementById("reservasiTableBody");
//   if (!tableBody) {
//     return;
//   }

//   const riwayatBody = document.getElementById("riwayatSewaBody");

//   function updateSummary(list) {
//     document.getElementById("rvMenungguBayar").textContent = String(
//       list.filter((item) => item.statusPembayaran === "Menunggu Validasi")
//         .length,
//     );
//     document.getElementById("rvMenungguBatal").textContent = String(
//       list.filter((item) => item.statusPembatalan === "Menunggu Validasi")
//         .length,
//     );
//     document.getElementById("rvDisetujui").textContent = String(
//       list.filter((item) => item.statusReservasi === "Disetujui").length,
//     );
//     document.getElementById("rvSelesai").textContent = String(
//       list.filter((item) => item.statusReservasi === "Selesai").length,
//     );
//   }

//   function render() {
//     const list = getData(ADMIN_KEYS.reservasi).sort(
//       (a, b) => new Date(b.tanggal) - new Date(a.tanggal),
//     );

//     tableBody.innerHTML = list
//       .map(
//         (item) => `
//         <tr>
//           <td>${item.namaTim}</td>
//           <td>${formatTanggal(item.tanggal)}</td>
//           <td>${item.lapangan}</td>
//           <td>${item.jam}</td>
//           <td>${item.durasi} Jam</td>
//           <td>${formatRupiah(item.total)}</td>
//           <td>${item.statusPembayaran}</td>
//           <td>${item.statusPembatalan}</td>
//           <td><span class="badge text-bg-dark">${item.statusReservasi}</span></td>
//           <td class="text-nowrap">
//             <button class="btn btn-sm btn-outline-success" data-pay="${item.id}" type="button">Validasi Bayar</button>
//             <button class="btn btn-sm btn-outline-warning" data-cancel="${item.id}" type="button">Validasi Batal</button>
//             <button class="btn btn-sm btn-outline-primary" data-done="${item.id}" type="button">Tandai Selesai</button>
//           </td>
//         </tr>`,
//       )
//       .join("");

//     if (riwayatBody) {
//       riwayatBody.innerHTML = list
//         .map(
//           (item) => `
//           <tr>
//             <td>${formatTanggal(item.tanggal)}</td>
//             <td>${item.namaTim}</td>
//             <td>${item.lapangan}</td>
//             <td>${formatRupiah(item.total)}</td>
//             <td>${item.statusReservasi}</td>
//           </tr>`,
//         )
//         .join("");
//     }

//     updateSummary(list);
//   }

//   tableBody.addEventListener("click", (event) => {
//     const payId = event.target.getAttribute("data-pay");
//     const cancelId = event.target.getAttribute("data-cancel");
//     const doneId = event.target.getAttribute("data-done");

//     const list = getData(ADMIN_KEYS.reservasi);
//     const index = list.findIndex(
//       (row) => row.id === (payId || cancelId || doneId),
//     );

//     if (index < 0) {
//       return;
//     }

//     if (payId) {
//       list[index].statusPembayaran = "Lunas";
//       if (list[index].statusReservasi === "Pending") {
//         list[index].statusReservasi = "Disetujui";
//       }
//     }

//     if (cancelId) {
//       list[index].statusPembatalan = "Disetujui";
//       list[index].statusReservasi = "Dibatalkan";
//     }

//     if (doneId && list[index].statusReservasi === "Disetujui") {
//       list[index].statusReservasi = "Selesai";
//     }

//     setData(ADMIN_KEYS.reservasi, list);
//     render();
//   });

//   render();
// }

// function initLaporanAdmin() {
//   const totalPendapatanEl = document.getElementById("lapTotalPendapatan");
//   if (!totalPendapatanEl) {
//     return;
//   }

//   const list = getData(ADMIN_KEYS.reservasi);
//   const laporanSewaBody = document.getElementById("laporanSewaBody");
//   const laporanKeuanganBody = document.getElementById("laporanKeuanganBody");

//   const transaksiLunas = list.filter(
//     (item) => item.statusPembayaran === "Lunas",
//   );
//   const totalPendapatan = transaksiLunas.reduce(
//     (sum, item) => sum + Number(item.total || 0),
//     0,
//   );
//   const totalBatal = list.filter(
//     (item) => item.statusReservasi === "Dibatalkan",
//   ).length;
//   const rata = transaksiLunas.length
//     ? Math.round(totalPendapatan / transaksiLunas.length)
//     : 0;

//   totalPendapatanEl.textContent = formatRupiah(totalPendapatan);
//   document.getElementById("lapTotalTransaksi").textContent = String(
//     transaksiLunas.length,
//   );
//   document.getElementById("lapTotalBatal").textContent = String(totalBatal);
//   document.getElementById("lapRataRata").textContent = formatRupiah(rata);

//   laporanSewaBody.innerHTML = list
//     .map(
//       (item, index) => `
//       <tr>
//         <td>${index + 1}</td>
//         <td>${formatTanggal(item.tanggal)}</td>
//         <td>${item.namaTim}</td>
//         <td>${item.lapangan}</td>
//         <td>${item.durasi} Jam</td>
//         <td>${item.statusReservasi}</td>
//       </tr>`,
//     )
//     .join("");

//   const grouped = list.reduce((acc, item) => {
//     const key = getMonthKey(item.tanggal);
//     if (!acc[key]) {
//       acc[key] = { pendapatan: 0, transaksi: 0, batal: 0 };
//     }

//     if (item.statusPembayaran === "Lunas") {
//       acc[key].pendapatan += Number(item.total || 0);
//       acc[key].transaksi += 1;
//     }

//     if (item.statusReservasi === "Dibatalkan") {
//       acc[key].batal += 1;
//     }

//     return acc;
//   }, {});

//   laporanKeuanganBody.innerHTML = Object.entries(grouped)
//     .map(
//       ([bulan, value]) => `
//       <tr>
//         <td>${bulan}</td>
//         <td>${value.transaksi}</td>
//         <td>${value.batal}</td>
//         <td>${formatRupiah(value.pendapatan)}</td>
//       </tr>`,
//     )
//     .join("");
// }

// function initMembershipListPage() {
//   const tableBody = document.getElementById("membershipTableBody");
//   if (!tableBody) {
//     return;
//   }

//   function render() {
//     const list = getData(ADMIN_KEYS.membership);
//     tableBody.innerHTML = list
//       .map(
//         (item, index) => `
//         <tr>
//           <td>${index + 1}</td>
//           <td>${item.namaTim}</td>
//           <td>${item.paket}</td>
//           <td>${item.masaAktif}</td>
//           <td>${item.status}</td>
//           <td class="text-nowrap">
//             <a class="btn btn-sm btn-outline-dark" href="admin-membership-form.html?id=${item.id}">Edit</a>
//             <button class="btn btn-sm btn-outline-danger" type="button" data-delete-membership="${item.id}">Hapus</button>
//           </td>
//         </tr>`,
//       )
//       .join("");
//   }

//   tableBody.addEventListener("click", (event) => {
//     const deleteId = event.target.getAttribute("data-delete-membership");
//     if (!deleteId) {
//       return;
//     }

//     const list = getData(ADMIN_KEYS.membership);
//     setData(
//       ADMIN_KEYS.membership,
//       list.filter((item) => item.id !== deleteId),
//     );
//     render();
//   });

//   render();
// }

// // function initMembershipFormPage() {
// //   const form = document.getElementById("membershipFormAdmin");
// //   if (!form || !document.getElementById("membershipFormMode")) {
// //     return;
// //   }

// //   const idInput = document.getElementById("membershipId");
// //   const modeText = document.getElementById("membershipFormMode");
// //   const existingId = getQueryId();
// //   const list = getData(ADMIN_KEYS.membership);

// //   if (existingId) {
// //     const item = list.find((row) => row.id === existingId);
// //     if (item) {
// //       idInput.value = item.id;
// //       document.getElementById("membershipNamaTim").value = item.namaTim;
// //       document.getElementById("membershipPaket").value = item.paket;
// //       document.getElementById("membershipMasaAktif").value = item.masaAktif;
// //       document.getElementById("membershipStatus").value = item.status;
// //       modeText.textContent = "Mode: Edit Membership";
// //     }
// //   }

// //   form.addEventListener("submit", (event) => {
// //     event.preventDefault();

// //     const payload = {
// //       id: idInput.value || createId("mbs"),
// //       namaTim: document.getElementById("membershipNamaTim").value,
// //       paket: document.getElementById("membershipPaket").value,
// //       masaAktif: document.getElementById("membershipMasaAktif").value,
// //       status: document.getElementById("membershipStatus").value,
// //     };

// //     if (!payload.namaTim.trim()) {
// //       alert("Nama tim wajib diisi.");
// //       return;
// //     }

// //     const latest = getData(ADMIN_KEYS.membership);
// //     const found = latest.findIndex((item) => item.id === payload.id);

// //     if (found >= 0) {
// //       latest[found] = payload;
// //     } else {
// //       latest.push(payload);
// //     }

// //     setData(ADMIN_KEYS.membership, latest);
// //     window.location.href = "admin-membership.html";
// //   });

// //   document.getElementById("membershipCancel")?.addEventListener("click", () => {
// //     window.location.href = "admin-membership.html";
// //   });
// // }

// function initPelangganListPage() {
//   const tableBody = document.getElementById("pelangganTableBody");
//   if (!tableBody) {
//     return;
//   }

//   function render() {
//     const list = getData(ADMIN_KEYS.pelanggan);
//     tableBody.innerHTML = list
//       .map(
//         (item, index) => `
//         <tr>
//           <td>${index + 1}</td>
//           <td>${item.nama}</td>
//           <td>${item.telepon}</td>
//           <td>${item.email}</td>
//           <td>${item.statusMember}</td>
//           <td class="text-nowrap">
//             <a class="btn btn-sm btn-outline-dark" href="admin-pelanggan-form.html?id=${item.id}">Edit</a>
//             <button class="btn btn-sm btn-outline-danger" type="button" data-delete-pelanggan="${item.id}">Hapus</button>
//           </td>
//         </tr>`,
//       )
//       .join("");
//   }

//   tableBody.addEventListener("click", (event) => {
//     const deleteId = event.target.getAttribute("data-delete-pelanggan");
//     if (!deleteId) {
//       return;
//     }

//     const list = getData(ADMIN_KEYS.pelanggan);
//     setData(
//       ADMIN_KEYS.pelanggan,
//       list.filter((item) => item.id !== deleteId),
//     );
//     render();
//   });

//   render();
// }

// function initPelangganFormPage() {
//   const form = document.getElementById("pelangganFormAdmin");
//   if (!form || !document.getElementById("pelangganFormMode")) {
//     return;
//   }

//   const idInput = document.getElementById("pelangganId");
//   const modeText = document.getElementById("pelangganFormMode");
//   const existingId = getQueryId();
//   const list = getData(ADMIN_KEYS.pelanggan);

//   if (existingId) {
//     const item = list.find((row) => row.id === existingId);
//     if (item) {
//       idInput.value = item.id;
//       document.getElementById("pelangganNama").value = item.nama;
//       document.getElementById("pelangganTelepon").value = item.telepon;
//       document.getElementById("pelangganEmail").value = item.email;
//       document.getElementById("pelangganStatusMember").value =
//         item.statusMember;
//       modeText.textContent = "Mode: Edit Pelanggan";
//     }
//   }

//   form.addEventListener("submit", (event) => {
//     event.preventDefault();

//     const payload = {
//       id: idInput.value || createId("plg"),
//       nama: document.getElementById("pelangganNama").value,
//       telepon: document.getElementById("pelangganTelepon").value,
//       email: document.getElementById("pelangganEmail").value,
//       statusMember: document.getElementById("pelangganStatusMember").value,
//     };

//     if (!payload.nama.trim() || !payload.email.trim()) {
//       alert("Nama dan email pelanggan wajib diisi.");
//       return;
//     }

//     const latest = getData(ADMIN_KEYS.pelanggan);
//     const found = latest.findIndex((item) => item.id === payload.id);

//     if (found >= 0) {
//       latest[found] = payload;
//     } else {
//       latest.push(payload);
//     }

//     setData(ADMIN_KEYS.pelanggan, latest);
//     window.location.href = "admin-pelanggan.html";
//   });

//   document.getElementById("pelangganCancel")?.addEventListener("click", () => {
//     window.location.href = "admin-pelanggan.html";
//   });
// }

// document.addEventListener("DOMContentLoaded", () => {
//   bootstrapAdminData();
//   initAdminDashboard();
//   initJadwalListPage();
//   initJadwalFormPage();
//   initReservasiAdmin();
//   initLaporanAdmin();
//   initMembershipListPage();
//   initMembershipFormPage();
//   initPelangganListPage();
//   initPelangganFormPage();
// });
