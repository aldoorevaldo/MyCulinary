let currentPage = 1;
const pageLimit  = 10;

window.onload = loadDashboard;

async function loadDashboard() {
  await loadRingkasan();
  await loadLaporan();
  await loadUser();
  await loadGrafik();
  await loadMeja();

  const prevBtn = document.getElementById('prevPage');
  const nextBtn = document.getElementById('nextPage');

  if (prevBtn) prevBtn.addEventListener('click', e => {
    e.preventDefault();
    changePage(-1);
  });
  if (nextBtn) nextBtn.addEventListener('click', e => {
    e.preventDefault();
    changePage(1);
  });
}

function formatRupiah(angka) {
  const str = angka.toString();
  return str.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

async function loadRingkasan() {
  const res  = await fetch('../backend/index.php?action=ringkasan_owner');
  const data = await res.json();

  const totalPendapatanEl = document.getElementById('totalPendapatan');
  totalPendapatanEl.textContent = 'Rp ' + formatRupiah(data.total_pendapatan);

  const totalPesananEl = document.getElementById('totalPesanan');
  totalPesananEl.textContent    = data.total_pesanan + ' Pesanan';
}

async function loadLaporan(showAlert = false) {
  const tanggal = document.getElementById('filterTanggal')?.value || '';

  let url = '../backend/index.php?action=laporan_pembayaran';
  if (tanggal) {
    url += `&tanggal=${tanggal}`;
  }

  try {
    const res = await fetch(url);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const rows = await res.json();

    const tbody = document.getElementById('laporanBody');
    tbody.innerHTML = '';

    if (!Array.isArray(rows) || rows.length === 0) {
      tbody.innerHTML = '<tr><td colspan="4" class="text-center">Tidak ada data</td></tr>';
    } else {
      rows.forEach(r => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${r.id_pembayaran}</td>
          <td>Meja ${r.id_meja}</td>
          <td>Rp ${r.total}</td>
          <td>${r.waktu_bayar}</td>
        `;
        tbody.appendChild(tr);
      });
    }

    if (showAlert) {
      alert(`Laporan berhasil difilter untuk tanggal: ${tanggal}`);
    }
  } catch (e) {
    console.error('loadLaporan error:', e);
    const tbody = document.getElementById('laporanBody');
    tbody.innerHTML = '<tr><td colspan="4" class="text-center">Gagal memuat data</td></tr>';
  }
}

async function loadGrafik() {
  const res = await fetch('../backend/index.php?action=grafik_pendapatan');
  const data = await res.json();

  const ctx = document.getElementById('grafikPendapatan').getContext('2d');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: data.map(d => d.tanggal),
      datasets: [{
        label: 'Pendapatan (Rp)',
        data: data.map(d => d.total),
        backgroundColor: 'rgba(34, 197, 94, 0.7)',
        borderColor: 'rgba(34, 197, 94, 1)',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
}

async function loadUser() {
  const res = await fetch('../backend/index.php?action=daftar_user');
  const data = await res.json();
  const tbody = document.getElementById('userBody');
  tbody.innerHTML = '';
  data.forEach(row => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${row.username}</td>
      <td>${row.role}</td>
      <td>${row.nama_lengkap}</td>
      <td class="actions">
        <button onclick="editUser('${row.username}')">Edit</button>
        <button onclick="hapusUser('${row.username}')">Hapus</button>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

function editUser(username) {
  window.location.href = `edit_user_form.php?username=${username}`;
}

async function hapusUser(username) {
  if (!confirm(`Yakin ingin menghapus user ${username}?`)) return;

  const res = await fetch(`../backend/index.php?action=hapus_user`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ username })
  });

  const data = await res.json();
  if (data.status === 'success') {
    alert('User berhasil dihapus');
    location.reload();
  } else {
    alert('Gagal menghapus user: ' + (data.message || 'Unknown error'));
  }
}

function exportLaporan(format) {
  const tanggal = document.getElementById('filterTanggal').value;
  const params = new URLSearchParams();
  if (tanggal) params.append('tanggal', tanggal);
  params.append('format', format);

  window.open('../backend/export.php?' + params.toString(), '_blank');
}

async function tambahMeja(event) {
  event.preventDefault();
  const nomor = document.getElementById('nomorMejaBaru').value;
  const res = await fetch('../backend/index.php?action=tambah_meja', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ nomor })
  });
  const data = await res.json();
  const notif = document.getElementById('notifMeja');
  if (data.status === 'success') {
    notif.textContent = 'Meja berhasil ditambahkan!';
    notif.classList.remove('text-red-600');
    notif.classList.add('text-green-600');
    document.getElementById('nomorMejaBaru').value = '';
  } else {
    notif.textContent = data.message || 'Gagal menambah meja';
    notif.classList.remove('text-green-600');
    notif.classList.add('text-red-600');
  }
  notif.style.display = 'block';
}

async function loadMeja() {
  const res = await fetch('../backend/index.php?action=semua_meja');
  const data = await res.json();
  const container = document.getElementById('daftarMeja');
  container.innerHTML = '';

  if (!Array.isArray(data) || data.length === 0) {
    container.innerHTML = '<p class="text-gray-500">Belum ada meja.</p>';
    return;
  }

  data.forEach(meja => {
    const div = document.createElement('div');
    div.className = 'meja-item';
    div.innerHTML = `
      <div>
        <p class="font-semibold">Meja ${meja.id_meja}</p>
        <p class="status">Status: ${meja.status}</p>
      </div>
      <div class="meja-actions">
        <select id="status-${meja.id_meja}">
          <option value="kosong" ${meja.status === 'kosong' ? 'selected' : ''}>Kosong</option>
          <option value="terisi" ${meja.status === 'terisi' ? 'selected' : ''}>Terisi</option>
          <option value="selesai" ${meja.status === 'selesai' ? 'selected' : ''}>Selesai</option>
        </select>
        <button onclick="simpanStatus(${meja.id_meja})">Simpan</button>
      </div>
    `;
    container.appendChild(div);
  });
}

async function simpanStatus(id_meja) {
  const status = document.getElementById(`status-${id_meja}`).value;
  const res = await fetch('../backend/index.php?action=edit_meja', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id_meja, status })
  });
  const data = await res.json();
  if (data.status === 'success') {
    alert('Status meja berhasil diperbarui');
    loadMeja();
  } else {
    alert('Gagal memperbarui status meja');
  }
}

window.onload = loadDashboard;
