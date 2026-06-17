(function () {

    const searchInput = document.getElementById('search');
    const resultsBox  = document.getElementById('results');

    if (!searchInput || !resultsBox) return;

    let debounceTimer;

    searchInput.addEventListener('keyup', function () {
        clearTimeout(debounceTimer);

        const keyword = this.value.trim();

        if (keyword.length === 0) {
            resultsBox.innerHTML     = '';
            resultsBox.style.display = 'none';

            document.querySelectorAll('.product-card').forEach(card => {
                card.style.display = 'block';
            });
            return;
        }

        // Filter card DOM dulu (instant)
        document.querySelectorAll('.product-card').forEach(card => {
            const gameName = card.querySelector('.product-game')?.innerText.toLowerCase() || '';
            const prodName = card.querySelector('.product-name')?.innerText.toLowerCase() || '';
            card.style.display = (
                gameName.includes(keyword.toLowerCase()) ||
                prodName.includes(keyword.toLowerCase())
            ) ? 'block' : 'none';
        });

        // Fetch API untuk dropdown
        debounceTimer = setTimeout(() => {
            fetch(`/search-game?keyword=${encodeURIComponent(keyword)}`)
                .then(res => res.json())
                .then(games => {
                    resultsBox.innerHTML = '';

                    if (!games.length) {
                        resultsBox.style.display = 'none';
                        return;
                    }

                    games.forEach(game => {
                        const item = document.createElement('div');
                        item.classList.add('result-item');
                        item.innerHTML = `
                            <span class="result-name">${game.name}</span>
                            <span class="result-cat">${game.category ?? ''}</span>
                        `;
                        item.addEventListener('click', () => {
                            searchInput.value        = game.name;
                            resultsBox.innerHTML     = '';
                            resultsBox.style.display = 'none';

                            localStorage.setItem('selectedGame',    game.name);
                            localStorage.setItem('selectedProduct', game.name + ' Top Up');
                            localStorage.setItem('selectedPrice',   '');

                            window.location.href = '/buy';
                        });
                        resultsBox.appendChild(item);
                    });

                    resultsBox.style.display = 'block';
                })
                .catch(() => {
                    resultsBox.style.display = 'none';
                });
        }, 300);
    });

    document.addEventListener('click', function (e) {
        if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
            resultsBox.innerHTML     = '';
            resultsBox.style.display = 'none';
        }
    });

})();