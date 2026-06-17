const gameEmoji = {
    'mobile legends': '⚡',
    'free fire': '🔥',
    'roblox': '🧊',
    'genshin impact': '✨',
    'valorant': '🎯',
    'steam': '🎲',
    'pubg mobile': '🪖',
    'minecraft': '⛏️',
};

function getEmoji(game) {
    return gameEmoji[game.toLowerCase()] || '🎮';
}

function parseHarga(str) {
    return parseInt(str.toString().replace(/[^0-9]/g, '')) || 0;
}

function formatHarga(num) {
    return 'Rp ' + num.toLocaleString('id-ID');
}

let cartData = [];

async function renderCart() {
    const list    = document.getElementById('daftar-belanja');
    const empty   = document.getElementById('emptyState');
    const summary = document.getElementById('summary');

    try {
        // Cek login
        const loginRes  = await fetch('/user-info', { credentials: 'include' });
        const loginData = await loginRes.json();

        if (!loginData.status) {
            empty.style.display   = 'block';
            summary.style.display = 'none';
            list.innerHTML        = '';
            empty.innerHTML       = `
                <div class="empty-icon">🛒</div>
                <div class="empty-text">Wah, keranjang belanja kamu masih kosong!</div>
                <div style="display:flex;flex-direction:column;align-items:center;gap:10px;margin-top:4px;">
                    <button class="btn-shop"  onclick="window.location.href='/'">🛍️ Belanja Sekarang</button>
                    <button class="btn-login" onclick="window.location.href='/login'">🔑 Login Sekarang</button>
                </div>
            `;
            return;
        }

        // Ambil cart dari Firebase via Laravel
        const cartRes  = await fetch('/cart/data', { credentials: 'include' });
        const cartJson = await cartRes.json();

        cartData       = cartJson.data || [];
        list.innerHTML = '';

        if (cartData.length === 0) {
            empty.style.display   = 'block';
            summary.style.display = 'none';
            empty.innerHTML       = `
                <div class="empty-icon">🛒</div>
                <div class="empty-text">Wah, keranjang belanja kamu masih kosong!</div>
                <button class="btn-shop" onclick="window.location.href='/'">🛍️ Mulai Belanja</button>
            `;
            return;
        }

        empty.style.display   = 'none';
        summary.style.display = 'block';

        let subtotal = 0;

        cartData.forEach((item, index) => {
            const harga = parseHarga(item.price);
            const total = harga * (item.qty || 1);
            subtotal   += total;

            const div = document.createElement('div');
            div.classList.add('cart-item');
            div.innerHTML = `
                <div class="cart-icon">${getEmoji(item.game)}</div>
                <div class="cart-info">
                    <div class="cart-game">${item.game}</div>
                    <div class="cart-name">${item.product}</div>
                    <div class="cart-price">${formatHarga(total)}</div>
                </div>
                <div class="cart-qty">
                    <button class="qty-btn" onclick="changeQty(${index}, -1)">−</button>
                    <span class="qty-val">${item.qty || 1}</span>
                    <button class="qty-btn" onclick="changeQty(${index}, 1)">+</button>
                </div>
                <button class="cart-delete" onclick="removeItem(${index})">🗑️</button>
            `;
            list.appendChild(div);
        });

        document.getElementById('subtotal').innerText = formatHarga(subtotal);
        document.getElementById('total').innerText    = formatHarga(subtotal);

    } catch (err) {
        console.error(err);
        empty.style.display = 'block';
        empty.innerHTML     = `
            <div class="empty-icon">⚠️</div>
            <div class="empty-text">Gagal memuat keranjang. Coba refresh halaman.</div>
        `;
    }
}

async function getCsrf() {
    const res  = await fetch('/csrf-token');
    const data = await res.json();
    return data.token;
}

async function changeQty(index, delta) {
    const item   = cartData[index];
    const newQty = (item.qty || 1) + delta;
    const token  = await getCsrf();

    await fetch('/cart/update-qty', {
        method:      'POST',
        headers:     { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
        credentials: 'include',
        body:        JSON.stringify({ key: item._key, qty: newQty })
    });

    renderCart();
}

async function removeItem(index) {
    const item  = cartData[index];
    const token = await getCsrf();

    await fetch('/cart/remove', {
        method:      'POST',
        headers:     { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
        credentials: 'include',
        body:        JSON.stringify({ key: item._key })
    });

    renderCart();
}

async function clearCart() {
    if (!confirm('Kosongkan semua keranjang?')) return;

    const token = await getCsrf();

    await fetch('/cart/clear', {
        method:      'POST',
        headers:     { 'X-CSRF-TOKEN': token },
        credentials: 'include'
    });

    renderCart();
}

async function checkout() {
    const token = await getCsrf();

    const res  = await fetch('/cart/checkout', {
        method:      'POST',
        headers:     { 'X-CSRF-TOKEN': token },
        credentials: 'include'
    });

    const data = await res.json();

    if (data.status) {
        alert('✅ Checkout berhasil! Sisa saldo: Rp ' + data.balance.toLocaleString('id-ID'));
        window.location.href = '/';
    } else {
        alert('❌ ' + data.message);
    }
}

renderCart();
