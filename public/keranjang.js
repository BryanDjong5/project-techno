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
    return parseInt(str.replace(/[^0-9]/g, '')) || 0;
}

function formatHarga(num) {
    return 'Rp ' + num.toLocaleString('id-ID');
}

function loadCart() {
    return JSON.parse(localStorage.getItem('cart') || '[]');
}

function saveCart(cart) {
    localStorage.setItem('cart', JSON.stringify(cart));
}

function renderCart() {
    const cart = loadCart();
    const list = document.getElementById('daftar-belanja');
    const empty = document.getElementById('emptyState');
    const summary = document.getElementById('summary');

    list.innerHTML = '';

    if (cart.length === 0) {
        empty.style.display = 'block';
        summary.style.display = 'none';
        return;
    }

    empty.style.display = 'none';
    summary.style.display = 'block';

    let subtotal = 0;

    cart.forEach((item, index) => {
        const harga = parseHarga(item.price);
        const total = harga * item.qty;
        subtotal += total;

        const div = document.createElement('div');
        div.classList.add('cart-item');
        div.innerHTML = `
            <div class="cart-icon">${getEmoji(item.game)}</div>
            <div class="cart-info">
                <div class="cart-game">${item.game}</div>
                <div class="cart-name">${item.name}</div>
                <div class="cart-price">${formatHarga(total)}</div>
            </div>
            <div class="cart-qty">
                <button class="qty-btn" onclick="changeQty(${index}, -1)">−</button>
                <span class="qty-val">${item.qty}</span>
                <button class="qty-btn" onclick="changeQty(${index}, 1)">+</button>
            </div>
            <button class="cart-delete" onclick="removeItem(${index})">🗑️</button>
        `;
        list.appendChild(div);
    });

    document.getElementById('subtotal').innerText = formatHarga(subtotal);
    document.getElementById('total').innerText = formatHarga(subtotal);
}

function changeQty(index, delta) {
    const cart = loadCart();
    cart[index].qty += delta;
    if (cart[index].qty <= 0) {
        cart.splice(index, 1);
    }
    saveCart(cart);
    renderCart();
}

function removeItem(index) {
    const cart = loadCart();
    cart.splice(index, 1);
    saveCart(cart);
    renderCart();
}

function clearCart() {
    if (confirm('Kosongkan semua keranjang?')) {
        localStorage.removeItem('cart');
        renderCart();
    }
}

function checkout() {
    const cart = loadCart();
    if (cart.length === 0) return;

    // Ambil item pertama untuk di-checkout
    const item = cart[0];
    localStorage.setItem('selectedGame', item.game);
    localStorage.setItem('selectedProduct', item.name);
    localStorage.setItem('selectedPrice', item.price);

    window.location.href = '/buy';
}

renderCart();