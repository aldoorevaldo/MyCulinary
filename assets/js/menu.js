
    let items = [];

    async function loadMenu() {
      try {
        const res = await fetch('backend/index.php?action=get_menu');
        const data = await res.json();
        items = data.map(menu => ({ ...menu, jumlah: 0, catatan: '' }));

        const kategori = ['makanan', 'minuman', 'dessert'];
        kategori.forEach(kat => {
          const section = document.getElementById('menu_' + kat);
          const menus = items.filter(i => i.kategori === kat);

          if (menus.length === 0) {
            section.innerHTML = '<p><em>Belum ada menu.</em></p>';
            return;
          }

          section.innerHTML = menus.map(menu => `
            <div class="menu-container">
              <div class="menu-card">
                <img src="uploads/menu/${menu.gambar}" alt="${menu.nama_menu}">
                <h3>${menu.nama_menu}</h3>
                <p>Rp ${menu.harga}</p>
                <p>Stok: ${menu.stok}</p>
                ${menu.stok == 0 
                ? `<p class="text-red-500 font-bold" style="color:red;">Menu Habis</p>` 
                : `
                  <div class="flex items-center justify-center gap-2 mb-2">
                    <button onclick="ubahJumlah(${menu.id_menu}, -1)" class="btn-minus">-</button>
                    <span id="jumlah-${menu.id_menu}">0</span>
                    <button onclick="ubahJumlah(${menu.id_menu}, 1)" class="btn-plus">+</button>
                  </div>
                  <input type="text" id="catatan-${menu.id_menu}" class="catatan-input" placeholder="Catatan..." oninput="ubahCatatan(${menu.id_menu}, this.value)">
                `
              }
              </div>
            </div>
          `).join('');
        });

      } catch (err) {
        console.error(err);
        alert('Gagal memuat menu.');
      }
    }

    function ubahJumlah(id_menu, delta) {
      const item = items.find(i => i.id_menu == id_menu);
      if (!item) return;

      const newJumlah = item.jumlah + delta;
      if (newJumlah > item.stok) {
        alert('Stok tidak mencukupi untuk ' + item.nama_menu);
        return;
      }

      item.jumlah = Math.max(0, newJumlah);
      document.getElementById('jumlah-' + id_menu).innerText = item.jumlah;
    }

    function ubahCatatan(id_menu, val) {
      const item = items.find(i => i.id_menu == id_menu);
      item.catatan = val;
    }

    async function kirimPesanan() {
      const id_meja = document.getElementById('id_meja').value;
      const pesanan = items.filter(i => i.jumlah > 0).map(i => ({
        id_menu: i.id_menu,
        jumlah: i.jumlah,
        catatan: i.catatan
      }));

      if (pesanan.length === 0 || !id_meja) {
        alert('Masukkan nomor meja dan pilih menu.');
        return;
      }

      try {
        const res = await fetch('backend/index.php?action=buat_pesanan', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id_meja, items: pesanan })
        });

        const result = await res.json();
        if (result.status === 'success') {
          alert('Pesanan berhasil dikirim!');
          window.location.href = window.location.pathname + '?meja=' + id_meja;
        } else {
          alert('Gagal mengirim pesanan: ' + (result.message || ''));
        }
      } catch (e) {
        console.error(e);
        alert('Terjadi kesalahan saat mengirim pesanan.');
      }
    }

    window.onload = loadMenu;