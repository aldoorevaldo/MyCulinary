<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'cashier') {
  header('Location: ../index.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kasir | MyCulinary</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../assets/css/cashierss.css">
  <link rel="shortcut icon" href="../assets/img/logo-brand.png">
</head>
<body>
  <div class="container">
    <header>
      <h1>Selamat Datang Kasir</h1>
    </header>

    <div class="input-group">
      <label>Masukkan Nomor Meja :</label>
      <input type="number" id="id_meja" placeholder="Contoh : 3" />
      <button onclick="cekTagihan()">Cek Tagihan</button>
    </div>

    <div id="detailTagihan" class="tagihan-box"></div>
    <div class="logout-container">
      <a href="../logout.php" class="btn logout">Logout</a>
    </div>
  </div>

  <script>
    let nonTunaiSudahScan = false;

    async function cekTagihan() {
      const id_meja = document.getElementById('id_meja').value;
      if (!id_meja) return alert('Masukkan nomor meja');

      try {
        const res = await fetch(`../backend/index.php?action=cek_tagihan&id_meja=${id_meja}`);
        const data = await res.json();

        const detail = document.getElementById('detailTagihan');
        if (!data.items || data.items.length === 0) {
          detail.innerHTML = '<p class="no-data">Tidak ada tagihan untuk meja ini.</p>';
          return;
        }

        const grouped = { makanan: [], minuman: [], dessert: [] };
        data.items.forEach(item => grouped[item.kategori]?.push(item));

        detail.innerHTML = `
          <div id="strukContainer">
            ${Object.entries(grouped).map(([kategori, items]) => {
              if (items.length === 0) return '';
              const label = kategori.charAt(0).toUpperCase() + kategori.slice(1);
              return `
                <h3>${label}</h3>
                <ul>${items.map(i => `<li>${i.nama_menu} x ${i.jumlah} = Rp ${i.total}</li>`).join('')}</ul>
              `;
            }).join('')}
            <p style="font-weight:bold;">Total: Rp ${data.grand_total}</p>
          </div>

          <label>Metode Pembayaran</label>
          <select id="metode">
            <option value="tunai">Tunai</option>
            <option value="non-tunai">Non Tunai</option>
          </select>

          <div id="qrContainer" class="qr"></div>
          <button id="btnBayar">Bayar</button>
        `;

        setTimeout(setupMetodeListener, 10); // Delay agar DOM render dulu
        setTimeout(() => {
          const btn = document.getElementById('btnBayar');
          if (btn) btn.addEventListener('click', () => bayar(id_meja, data.grand_total));
        }, 10);

      } catch (err) {
        console.error(err);
        alert('Gagal ambil data tagihan.');
      }
    }

    async function bayar(id_meja, total) {
      const metode = document.getElementById('metode')?.value || 'tunai';

      if (metode === 'non-tunai' && !nonTunaiSudahScan) {
        alert('Silakan scan kode QR terlebih dahulu.');
        nonTunaiSudahScan = true;
        return;
      }

      if (!confirm(`Bayar dengan metode: ${metode}?`)) return;

      try {
        const res = await fetch('../backend/index.php?action=bayar', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id_meja, total, metode })
        });

        const text = await res.text();
        let data;
        try {
          data = JSON.parse(text);
        } catch {
          throw new Error(text);
        }

        if (!res.ok || data.status !== 'success') {
          throw new Error(data.message || 'Pembayaran gagal');
        }

        alert('Pembayaran berhasil!');
        cetakStruk(); // ✅ Cetak struk
        document.getElementById('detailTagihan').innerHTML = '';
        document.getElementById('id_meja').value = '';
        nonTunaiSudahScan = false;
      } catch (err) {
        console.error(err);
        alert('Error: ' + err.message);
      }
    }

    function setupMetodeListener() {
      const metodeEl = document.getElementById('metode');
      const qr = document.getElementById('qrContainer');
      nonTunaiSudahScan = false;

      if (!metodeEl) return;

      metodeEl.addEventListener('change', function () {
        nonTunaiSudahScan = false;
        if (this.value === 'non-tunai') {
          qr.innerHTML = `
            <p class="qr-label">Silakan scan kode QR di bawah ini:</p>
            <img src="https://api.qrserver.com/v1/create-qr-code/?data=RPX1234567890&size=200x200" alt="QR Code" />
          `;
        } else {
          qr.innerHTML = '';
        }
      });
    }

    function cetakStruk() {
      const isi = document.getElementById('strukContainer')?.innerHTML;
      if (!isi) return alert('Struk tidak tersedia.');

      const win = window.open('', '', 'width=600,height=400');
      win.document.write(`
        <html>
          <head><title>Struk Pembayaran</title></head>
          <body onload="window.print(); window.close();">
            <h2>Struk Pembayaran</h2>
            ${isi}
          </body>
        </html>
      `);
      win.document.close();
    }
  </script>
</body>
</html>
