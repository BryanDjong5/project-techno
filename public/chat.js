const firebaseConfig = {
  apiKey: "AIzaSyDrN7IQffZ9myo9_ignJhs3gwCYjiAExY4",
  authDomain: "gemstore-90db2.firebaseapp.com",
  databaseURL: "https://gemstore-90db2-default-rtdb.asia-southeast1.firebasedatabase.app",
  projectId: "gemstore-90db2",
  storageBucket: "gemstore-90db2.firebasestorage.app",
  messagingSenderId: "268517643179",
  appId: "1:268517643179:web:38d622ccd493b9baaff8ee",
  measurementId: "G-3TDRX2VM26"
};

firebase.initializeApp(firebaseConfig);
const db = firebase.database();

let currentUser  = null;
let roomId       = null;

async function initChat() {
    try {
        const res  = await fetch('/user-info', { credentials: 'include' });
        const data = await res.json();

        if (!data.status) {
            document.getElementById('loginWarning').style.display = 'block';
            document.querySelector('.chat-input').disabled = true;
            document.querySelector('.btn-send').disabled   = true;
            return;
        }

        currentUser = data.user;

        roomId = 'room_' + currentUser.id;

        document.getElementById('sellerName').innerText   = 'Gem Station Support';
        document.getElementById('sellerAvatar').innerText = 'GS';

        listenMessages();

    } catch (err) {
        console.error('Init chat error:', err);
    }
}

function listenMessages() {
    const ref = db.ref('chats/' + roomId + '/messages');

    ref.on('value', snapshot => {
        const container = document.getElementById('chatContainer');
        const empty     = document.getElementById('chatEmpty');
        const typing    = document.getElementById('typing');

        const oldBubbles = container.querySelectorAll('.bubble-row, .date-divider');
        oldBubbles.forEach(b => b.remove());

        const messages = snapshot.val();

        if (!messages) {
            empty.style.display = 'flex';
            return;
        }

        empty.style.display = 'none';

        let lastDate = '';

        Object.values(messages).forEach(msg => {
            const date = new Date(msg.timestamp).toLocaleDateString('id-ID', {
                day: 'numeric', month: 'long', year: 'numeric'
            });

            if (date !== lastDate) {
                lastDate = date;
                const divider = document.createElement('div');
                divider.classList.add('date-divider');
                divider.innerText = date;
                container.insertBefore(divider, typing);
            }

            const isMine = msg.sender_id == currentUser.id;
            const row    = document.createElement('div');
            row.classList.add('bubble-row', isMine ? 'mine' : 'theirs');

            const time = new Date(msg.timestamp).toLocaleTimeString('id-ID', {
                hour: '2-digit', minute: '2-digit'
            });

            row.innerHTML = `
                ${!isMine ? `<div class="bubble-avatar">GS</div>` : ''}
                <div class="bubble-wrapper">
                    <div class="bubble">${escapeHtml(msg.text)}</div>
                    <div class="bubble-time">${time}</div>
                </div>
                ${isMine ? `<div class="bubble-avatar">${currentUser.name[0].toUpperCase()}</div>` : ''}
            `;

            container.insertBefore(row, typing);
        });

        container.scrollTop = container.scrollHeight;
    });
}

async function kirimPesan() {
    if (!currentUser) {
        alert('Maaf anda belum login, silahkan login dahulu!');
        return;
    }

    const input = document.getElementById('chatInput');
    const text  = input.value.trim();

    if (!text) return;

    input.value = '';

    const ref = db.ref('chats/' + roomId + '/messages');

    await ref.push({
        text:      text,
        sender_id: currentUser.id,
        sender:    currentUser.name,
        role:      'user',
        timestamp: Date.now()
    });

    await fetch('/chat/send', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({ room_id: roomId, text })
    });
}

function escapeHtml(text) {
    return text
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

initChat();