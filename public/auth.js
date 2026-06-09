async function registerPengguna() {
    event.preventDefault();

    const email    = document.getElementById('signup-email').value.trim();
    const password = document.getElementById('signup-password').value;
    const msgBox   = document.getElementById('auth-message');

    msgBox.innerText = '';

    if (!email || !password) {
        msgBox.style.color = 'red';
        msgBox.innerText   = '⚠️ Email dan password wajib diisi!';
        return;
    }

    try {
        const csrfRes  = await fetch('/csrf-token');
        const csrfData = await csrfRes.json();

        const res = await fetch('/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept':       'application/json',
                'X-CSRF-TOKEN': csrfData.token
            },
            credentials: 'include',
            body: JSON.stringify({ email, password })
        });

        const data = await res.json();

        if (data.status) {
            msgBox.style.color = 'green';
            msgBox.innerText   = '✅ ' + data.message;
            setTimeout(() => window.location.href = '/login', 1000);
        } else {
            msgBox.style.color = 'red';
            msgBox.innerText   = '❌ ' + (data.message || 'Registrasi gagal');
        }

    } catch (err) {
        console.error(err);
        msgBox.style.color = 'red';
        msgBox.innerText   = '❌ Server tidak bisa diakses.';
    }
}


async function loginPengguna() {
    event.preventDefault();

    const email    = document.getElementById('login-email').value.trim();
    const password = document.getElementById('login-password').value;
    const msgBox   = document.getElementById('auth-message');

    msgBox.innerText = '';

    if (!email || !password) {
        msgBox.style.color = 'red';
        msgBox.innerText = 'Email dan password wajib diisi!';
        return;
    }

    try {
        const csrfRes  = await fetch('/csrf-token');
        const csrfData = await csrfRes.json();

        const res  = await fetch('/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept':       'application/json',
                'X-CSRF-TOKEN': csrfData.token
            },
            credentials: 'include',
            body: JSON.stringify({ email, password })
        });

        const data = await res.json();

        if (data.status) {
            msgBox.style.color = 'green';
            msgBox.innerText   = '✅ ' + data.message;
            setTimeout(() => window.location.href = '/', 1000);
        } else {
            msgBox.style.color = 'red';
            msgBox.innerText   = '❌ ' + data.message;
        }

    } catch (err) {
        console.error(err);
        msgBox.style.color = 'red';
        msgBox.innerText   = '❌ Server tidak bisa diakses.';
    }
}