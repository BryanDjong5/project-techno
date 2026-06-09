// ===== CATEGORY PILLS FILTER =====
const pills = document.querySelectorAll('.cat-pill');
const cards = document.querySelectorAll('.product-card');

pills.forEach(pill => {
  pill.addEventListener('click', () => {
    pills.forEach(p => p.classList.remove('active'));
    pill.classList.add('active');

    const filter = pill.dataset.filter;
    cards.forEach(card => {
      if (filter === 'all' || card.dataset.category === filter) {
        card.style.display = 'block';
      } else {
        card.style.display = 'none';
      }
    });
  });
});

// ===== SIDEBAR CATEGORY FILTER =====
const sidebarItems = document.querySelectorAll('.sidebar-item[data-filter]');
sidebarItems.forEach(item => {
  item.addEventListener('click', () => {
    sidebarItems.forEach(s => s.classList.remove('active'));
    item.classList.add('active');
  });
});

// ===== SEARCH BAR =====
const searchInput = document.querySelector('.nav-search input');
searchInput.addEventListener('input', () => {
  const query = searchInput.value.toLowerCase();
  cards.forEach(card => {
    const name = card.querySelector('.product-name').textContent.toLowerCase();
    const game = card.querySelector('.product-game').textContent.toLowerCase();
    card.style.display = (name.includes(query) || game.includes(query)) ? 'block' : 'none';
  });
});

// ===== CART =====
let cart = [];
const cartBtn = document.querySelector('.nav-btn');

document.querySelectorAll('.product-card').forEach(card => {
  card.addEventListener('click', () => {
    const name = card.querySelector('.product-name').textContent;
    const price = card.querySelector('.product-price').textContent.replace(/[^0-9.]/g, '').trim();
    cart.push({ name, price });
    cartBtn.textContent = `🛒 Cart (${cart.length})`;
  });
});

// ===== HOT DEAL BUTTON =====
const hotDealBtn = document.querySelector('.featured-banner .btn-primary');

if (hotDealBtn) {
  hotDealBtn.addEventListener('click', () => {
    localStorage.setItem('selectedGame', 'Genshin Impact');
    localStorage.setItem('selectedProduct', 'Genesis Crystals 1980');
    localStorage.setItem('selectedPrice', 'Rp 185.000');

    window.location.href = 'buy.html';
  });
}
// ===== PRODUCT CLICK =====

document.querySelectorAll('.product-card')
.forEach(card => {

  card.style.cursor = 'pointer';

  card.addEventListener('click', () => {

    const game =
      card.querySelector('.product-game').textContent;

    const product =
      card.querySelector('.product-name').textContent;

    const priceElement =
      card.querySelector('.product-price');

    const oldPrice =
      priceElement.querySelector('.old');

    let price =
      priceElement.innerText;

    // Hapus harga lama jika ada diskon
    if (oldPrice) {
      price = price.replace(oldPrice.innerText, '').trim();
    }

    localStorage.setItem('selectedGame', game);
    localStorage.setItem('selectedProduct', product);
    localStorage.setItem('selectedPrice', price);

    window.location.href = 'buy.html';

  });

});
// ===== TOP GAMES CLICK =====

document.querySelectorAll('.top-game-card')
.forEach(card => {

  card.style.cursor = 'pointer';

  card.addEventListener('click', () => {

    const game =
      card.querySelector('.top-game-name').textContent;

    const price =
      card.querySelector('.top-game-price').textContent;

    localStorage.setItem('selectedGame', game);
    localStorage.setItem('selectedProduct', game + ' Top Up');
    localStorage.setItem('selectedPrice', price);

    window.location.href = 'buy.html';

  });

  document.querySelectorAll('.product-card').forEach(card => {
    card.style.cursor = 'pointer';
    card.addEventListener('click', async () => {
        const game    = card.querySelector('.product-game').textContent;
        const name    = card.querySelector('.product-name').textContent;
        const priceEl = card.querySelector('.product-price');
        const oldEl   = priceEl.querySelector('.old');
        let price     = priceEl.innerText;
        if (oldEl) price = price.replace(oldEl.innerText, '').trim();

        const res = await fetch('/cart/add', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ game, product: name, price })
        });

        const data = await res.json();

        if (res.status === 401) {
            alert('⚠️ Login dulu untuk menambah ke keranjang!');
            window.location.href = '/login';
            return;
        }

        if (data.status) {
            alert(`✅ ${name} ditambahkan ke keranjang!`);
        } else {
            alert('❌ ' + data.message);
        }
    });
});

// ===== CEK LOGIN STATUS =====
async function cekLogin() {
    try {
        const res  = await fetch('/user-info', { credentials: 'include' });
        const data = await res.json();

        const navBtn = document.querySelector('.nav-btn');

        if (data.status) {
            // Sudah login — ganti tombol jadi avatar + nama
            navBtn.outerHTML = `
                <div class="nav-user" onclick="window.location.href='/profile'" style="cursor:pointer">
                    <div class="nav-avatar">${data.user.name[0].toUpperCase()}</div>
                    <span class="nav-username">${data.user.name}</span>
                </div>
            `;
        }
    } catch (err) {
        console.error('Cek login error:', err);
    }
}

cekLogin();

});