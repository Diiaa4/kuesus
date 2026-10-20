(function () {
  var box = document.getElementById('chat');
  if (!box) return;

  var cfg = JSON.parse(box.dataset.config);
  var list = box.querySelector('.chat-list');
  var form = box.querySelector('form');
  var input = form.querySelector('input[name=body]');
  var seen = {};
  var last = 0;

  function add(m) {
    if (seen[m.id]) return; // dedupe: pesan bisa datang dari WebSocket dan polling
    seen[m.id] = true;
    last = Math.max(last, m.id);

    var bubble = document.createElement('div');
    bubble.className = 'bubble ' + (m.sender_id === cfg.me ? 'me' : 'them');
    var p = document.createElement('p');
    p.textContent = m.body;
    var t = document.createElement('time');
    t.textContent = m.time;
    bubble.appendChild(p);
    bubble.appendChild(t);
    list.appendChild(bubble);
    list.scrollTop = list.scrollHeight;
  }

  function poll() {
    fetch(cfg.messagesUrl + '?after=' + last, { headers: { Accept: 'application/json' } })
      .then(function (r) { return r.ok ? r.json() : []; })
      .then(function (rows) { rows.forEach(add); })
      .catch(function () {});
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    var body = input.value.trim();
    if (!body) return;
    input.value = '';
    fetch(cfg.sendUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': cfg.csrf },
      body: JSON.stringify({ body: body })
    })
      .then(function (r) { if (!r.ok) throw new Error(); return r.json(); })
      .then(add)
      .catch(function () { input.value = body; });
  });

  poll();
  setInterval(poll, 4000); // cadangan bila WebSocket tidak tersambung

  if (window.chatRealtime && window.Echo) {
    window.Echo.private('chat.' + cfg.customerId).listen('.message.sent', add);
  }
})();
