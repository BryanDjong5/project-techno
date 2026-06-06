(function () {
    console.log("searchGame.js loaded ✅");

    const searchInput = document.getElementById('search');
    const resultsBox = document.getElementById('results');

    console.log("searchInput:", searchInput);
    console.log("resultsBox:", resultsBox);

    if (!searchInput || !resultsBox) {
        console.error("❌ Element #search atau #results tidak ditemukan!");
        return;
    }

    let debounceTimer;

    searchInput.addEventListener('keyup', function () {
        clearTimeout(debounceTimer);
        const keyword = this.value.trim();
        console.log("Keyword:", keyword);

        if (keyword.length === 0) {
            resultsBox.innerHTML = '';
            resultsBox.style.display = 'none';
            return;
        }

        debounceTimer = setTimeout(() => {
            const url = `/search-game?keyword=${encodeURIComponent(keyword)}`;
            console.log("Fetching:", url);

            fetch(url)
                .then(res => {
                    console.log("Status:", res.status);
                    return res.json();
                })
                .then(games => {
                    console.log("Response:", games);
                    resultsBox.innerHTML = '';

                    if (!games.length) {
                        resultsBox.innerHTML = `<div class="result-empty">Game tidak ditemukan</div>`;
                        resultsBox.style.display = 'block';
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
                            searchInput.value = game.name;
                            resultsBox.innerHTML = '';
                            resultsBox.style.display = 'none';
                        });
                        resultsBox.appendChild(item);
                    });

                    resultsBox.style.display = 'block';
                })
                .catch(err => {
                    console.error("❌ Fetch error:", err);
                });
        }, 300);
    });

    document.addEventListener('click', function (e) {
        if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
            resultsBox.innerHTML = '';
            resultsBox.style.display = 'none';
        }
    });

})();