document.addEventListener('DOMContentLoaded', async function () {

    // Ambil CSRF
    const csrfRes = await fetch('/csrf-token');
    const csrfData = await csrfRes.json();
    const csrfToken = csrfData.token;

    // Star rating pakai radio
    const radios = document.querySelectorAll('input[name="rating"]');

    radios.forEach(radio => {
        radio.addEventListener('change', () => {
            const selected = parseInt(radio.value);
            updateStars(selected);
        });
    });

    function updateStars(value) {
        for (let i = 1; i <= 5; i++) {
            const img = document.getElementById('img' + i);
            if (img) {
                img.src = i <= value ? '/image/yellow_star.png' : '/image/star.png';
            }
        }
    }

    window.kirimUlasan = async function () {
        const selected = document.querySelector('input[name="rating"]:checked');

        if (!selected) {
            alert('⚠️ Pilih rating bintang dulu!');
            return;
        }

        const selectedRating = parseInt(selected.value);
        const ulasan = document.getElementById('ulasan').value;
        const orderId = document.getElementById('orderId')?.innerText.replace('#', '').trim();

        try {
            const res = await fetch('/rating', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    order_id: orderId,
                    rating: selectedRating,
                    ulasan: ulasan
                })
            });

            const data = await res.json();

            if (data.status) {
                document.getElementById('success').style.display = 'block';
                setTimeout(() => window.location.href = '/', 2000);
            } else {
                alert('❌ ' + (data.message || 'Gagal mengirim ulasan'));
            }

        } catch (err) {
            console.error(err);
            alert('❌ Server tidak bisa diakses.');
        }
    };

});