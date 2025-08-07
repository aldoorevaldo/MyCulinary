async function loadPesanan() {
      const container = document.getElementById('daftarPesanan');
      container.innerHTML = '<p class="loading">Memuat pesanan...</p>';

      try {
        const res = await fetch('../backend/index.php?action=pesanan_siap_dimasak');
        const data = await res.json();

        if (!Array.isArray(data) || data.length === 0) {
          container.innerHTML = '<p class="no-data">Belum ada pesanan.</p>';
          return;
        }

        const grouped = {};
        data.forEach(item => {
          if (!grouped[item.id_pesanan]) grouped[item.id_pesanan] = [];
          grouped[item.id_pesanan].push(item);
        });

        container.innerHTML = '';
        for (const id in grouped) {
          const pesanan = grouped[id];
          const kategori = { makanan: [], minuman: [], dessert: [] };
          pesanan.forEach(i => kategori[i.kategori]?.push(i));

          const div = document.createElement('div');
          div.className = 'card';

          div.innerHTML = `
            <h3>Meja ${pesanan[0].id_meja}</h3>
            ${Object.keys(kategori).map(k => kategori[k].length ? `
              <h4 class="kategori-title">${k}</h4>
              <ul>${kategori[k].map(i => `<li>${i.nama_menu} x ${i.jumlah}</li>`).join('')}</ul>
            ` : '').join('')}
            ${pesanan[0].status === 'siap'
            ? `<button class="btn-antar" onclick="antarPesanan(${id})">Antar</button>`
            : `<p class="status-dimasak">Sedang dimasak...</p>`}
          `;
          container.appendChild(div);
        }
      } catch (err) {
        console.error('Gagal memuat pesanan:', err);
        container.innerHTML = '<p class="error">Gagal memuat pesanan.</p>';
      }
    }

    async function antarPesanan(id_pesanan) {
      if (!confirm('Tandai pesanan ini sebagai DIANTAR?')) return;
      await fetch('../backend/index.php?action=antar_makanan', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id_pesanan })
      });
      loadPesanan();
    }

    async function loadSemuaMeja() {
      const container = document.getElementById('mejaKosong');
      container.innerHTML = '<p>Memuat data meja...</p>';

      try {
        const res = await fetch('../backend/index.php?action=semua_meja');
        const data = await res.json();

        if (data.length === 0) {
          container.innerHTML = '<p class="no-data">Tidak ada data meja.</p>';
          return;
        }

        container.innerHTML = '';
        data.forEach(meja => {
          const div = document.createElement('div');
          div.className = 'status-box';
          div.innerHTML = `
            <p class="meja-nomor">Meja ${meja.id_meja}</p>
            <p class="status-${meja.status}">Status: ${meja.status}</p>
          `;
          container.appendChild(div);
        });
      } catch (err) {
        console.error('Gagal mengambil data semua meja:', err);
        container.innerHTML = '<p class="error">Gagal memuat data meja.</p>';
      }
    }

    window.onload = function () {
      loadSemuaMeja();
      loadPesanan();
    };