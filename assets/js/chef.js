async function loadPesanan() {
      const container = document.getElementById('listPesanan');
      container.innerHTML = '<p class="info">Memuat pesanan...</p>';
      try {
        const res = await fetch('../backend/index.php?action=pesanan_chef');
        const data = await res.json();
        container.innerHTML = '';

        if (!Array.isArray(data) || data.length === 0) {
          container.innerHTML = '<p class="info">Belum ada pesanan.</p>';
          return;
        }

        const grouped = {};
        data.forEach(item => {
          if (!grouped[item.id_pesanan]) grouped[item.id_pesanan] = [];
          grouped[item.id_pesanan].push(item);
        });

        Object.keys(grouped).forEach(id => {
          const pesanan = grouped[id];
          const meja = pesanan[0].id_meja;

          const kategoriMap = { makanan: [], minuman: [], dessert: [] };
          pesanan.forEach(item => {
            if (kategoriMap[item.kategori]) {
              kategoriMap[item.kategori].push(item);
            }
          });

          const div = document.createElement('div');
          div.className = 'pesanan-box';
          div.innerHTML = `<h3>Pesanan Meja ${meja} (ID: ${id})</h3>`;

          for (const kategori in kategoriMap) {
            if (kategoriMap[kategori].length > 0) {
              div.innerHTML += `<h4>${kategori}</h4><ul>`;
              kategoriMap[kategori].forEach(i => {
                div.innerHTML += `<li>${i.nama_menu} x ${i.jumlah} <em>(${i.catatan || '-'})</em></li>`;
              });
              div.innerHTML += `</ul>`;
            }
          }

          div.innerHTML += `
            <div class="button-group">
              <button onclick="tandaiDimasak(${id})" class="btn yellow">Tandai Dimasak</button>
              <button onclick="tandaiSiap(${id})" class="btn green">Tandai Siap</button>
            </div>
          `;

          container.appendChild(div);
        });
      } catch (err) {
        console.error('Gagal mengambil data pesanan:', err);
        container.innerHTML = '<p class="error">Gagal memuat pesanan.</p>';
      }
    }

    async function tandaiDimasak(id) {
      if (!confirm("Tandai pesanan ini sebagai sedang dimasak?")) return;
      try {
        const res = await fetch('../backend/index.php?action=pesanan_dimasak', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id_pesanan: id })
        });
        const data = await res.json();
        if (data.status === 'success') {
          alert('Pesanan ditandai sebagai DIMASAK');
          loadPesanan();
        } else {
          alert('Gagal: ' + (data.message || 'Unknown error'));
        }
      } catch (err) {
        alert('Terjadi kesalahan.');
        console.error(err);
      }
    }

    async function tandaiSiap(id) {
      if (!confirm("Tandai pesanan ini sebagai SIAP?")) return;
      try {
        const res = await fetch('../backend/index.php?action=makanan_siap', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id_pesanan: id })
        });
        const data = await res.json();
        if (data.status === 'success') {
          alert('Pesanan ditandai sebagai SIAP');
          loadPesanan();
        } else {
          alert('Gagal: ' + (data.message || 'Unknown error'));
        }
      } catch (err) {
        alert('Terjadi kesalahan.');
        console.error(err);
      }
    }

    window.onload = loadPesanan;