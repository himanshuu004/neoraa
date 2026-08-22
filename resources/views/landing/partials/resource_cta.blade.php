<aside class="nd-wiki-book" id="book" aria-label="Book a session">
    <div class="nd-wiki-book-card">
        <h3>Book a Session</h3>
        <form class="nd-wiki-book-form" id="ndResourceBookForm">
            @csrf
            <label class="hero-form-label" for="ndResourceName">Your Name</label>
            <input type="text" name="name" id="ndResourceName" class="hero-form-input" required placeholder="Enter your name" autocomplete="name">

            <label class="hero-form-label" for="ndResourcePhone">Phone / WhatsApp</label>
            <input type="tel" name="phone" id="ndResourcePhone" class="hero-form-input" required placeholder="+91 XXXXX XXXXX" inputmode="tel" autocomplete="tel">

            <label class="hero-form-label" for="ndResourceService">Service Needed</label>
            <select name="service" id="ndResourceService" class="hero-form-input" style="appearance:auto;cursor:pointer;">
                <option value="">Select a service</option>
                <option>Speech Therapy</option>
                <option>Audiology</option>
                <option>Language Therapy</option>
                <option>Occupational Therapy</option>
                <option>Special Education</option>
                <option>Early Intervention</option>
                <option>Consult</option>
            </select>

            <div class="nd-book-glow-msg" id="ndResourceBookMsg" hidden></div>

            <button type="submit" class="nd-btn nd-btn-orange nd-wiki-book-btn" id="ndResourceBookBtn">
                Request Appointment
            </button>
        </form>
    </div>
</aside>
<script>
(function () {
    var form = document.getElementById('ndResourceBookForm');
    if (!form || form.dataset.bound === '1') return;
    form.dataset.bound = '1';
    var msg = document.getElementById('ndResourceBookMsg');
    var btn = document.getElementById('ndResourceBookBtn');
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var name = (form.querySelector('[name="name"]').value || '').trim();
        var phone = (form.querySelector('[name="phone"]').value || '').trim();
        if (!name || !phone) {
            msg.hidden = false;
            msg.textContent = 'Please enter your name and phone number.';
            msg.className = 'nd-book-glow-msg is-err';
            return;
        }
        btn.disabled = true;
        btn.textContent = 'Sending…';
        var fd = new FormData(form);
        fetch(@json(route('api.book-session')), { method: 'POST', body: fd, headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (res) {
                if (res.success) {
                    form.reset();
                    if (typeof ndShowBookingPopup === 'function') ndShowBookingPopup('success', name);
                    else {
                        msg.hidden = false;
                        msg.textContent = 'Request sent. We will call you soon.';
                        msg.className = 'nd-book-glow-msg is-ok';
                    }
                } else if (res.duplicate && typeof ndShowBookingPopup === 'function') {
                    ndShowBookingPopup('duplicate', name);
                } else if (typeof ndShowBookingPopup === 'function') {
                    ndShowBookingPopup('error', name);
                } else {
                    msg.hidden = false;
                    msg.textContent = res.message || 'Could not send. Please call 9634579408.';
                    msg.className = 'nd-book-glow-msg is-err';
                }
            })
            .catch(function () {
                if (typeof ndShowBookingPopup === 'function') ndShowBookingPopup('error', name);
                else {
                    msg.hidden = false;
                    msg.textContent = 'Could not send. Please call 9634579408.';
                    msg.className = 'nd-book-glow-msg is-err';
                }
            })
            .finally(function () {
                btn.disabled = false;
                btn.textContent = 'Request Appointment';
            });
    });
})();
</script>
