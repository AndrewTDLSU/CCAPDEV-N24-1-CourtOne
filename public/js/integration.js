// Presentation only. All saved records and authentication use PHP forms.
const reserveButton = document.getElementById('reserve-button');
const bookingSummary = document.getElementById('booking-summary');
if (bookingSummary) {
  const slots = document.querySelectorAll('[data-court]:not(:disabled)');
  slots.forEach(slot => {
    slot.addEventListener('click', () => {
      slots.forEach(other => {
        const selected = other.dataset.court === slot.dataset.court && other.dataset.time === slot.dataset.time;
        other.classList.toggle('selected', selected);
        other.setAttribute('aria-pressed', String(selected));
        other.querySelector('.slot-state').textContent = selected ? 'Selected' : 'Available';
      });
      bookingSummary.textContent = slot.dataset.label;
      if (reserveButton) {
        document.getElementById('selected-court').value = slot.dataset.court;
        document.getElementById('selected-time').value = slot.dataset.time;
        reserveButton.disabled = false;
      }
    });
  });
}
const signup = document.getElementById('signup-form');
if (signup) {
  const password = document.getElementById('password');
  const confirm = document.getElementById('confirm-password');
  const feedback = document.getElementById('password-match');
  const validate = () => {
    // Match PHP trim and the four ASCII character classes in the server rules.
    const value = password.value.replace(/^[ \t\r\n\x00\x0B]+|[ \t\r\n\x00\x0B]+$/g, '');
    const valid = Array.from(value).length >= 8 && /[A-Z]/.test(value)
      && /[a-z]/.test(value) && /[0-9]/.test(value) && /[\W_]/.test(value);
    password.setCustomValidity(password.value && !valid ? 'Use the password format shown below the field.' : '');

    const mismatch = confirm.value && confirm.value !== password.value;
    feedback.textContent = mismatch ? 'Passwords do not match.' : '';
    confirm.setCustomValidity(feedback.textContent);
  };
  password.addEventListener('input', validate);
  confirm.addEventListener('input', validate);
}
const photos = document.getElementById('photos');
if (photos) photos.addEventListener('change', () => {
  const preview = document.getElementById('photo-previews');
  preview.replaceChildren();
  Array.from(photos.files).forEach(file => {
    if (!file.type.startsWith('image/')) return;
    const column = document.createElement('div'); column.className = 'col-4';
    const image = document.createElement('img'); image.className = 'thumb border rounded img-fluid'; image.alt = file.name;
    const url = URL.createObjectURL(file); image.src = url; image.onload = () => URL.revokeObjectURL(url);
    column.append(image); preview.append(column);
  });
});
const tabs = Array.from(document.querySelectorAll('.dashboard-tabs [role="tab"]'));
function selectTab(tab) {
  tabs.forEach(item => {
    const selected = item === tab;
    item.classList.toggle('is-current', selected); item.setAttribute('aria-selected', String(selected)); item.tabIndex = selected ? 0 : -1;
    document.getElementById(item.getAttribute('aria-controls')).hidden = !selected;
  });
}
tabs.forEach((tab, index) => {
  tab.addEventListener('click', e => { e.preventDefault(); selectTab(tab); });
  tab.addEventListener('keydown', e => {
    let next;
    if (e.key === 'ArrowDown') next = (index + 1) % tabs.length;
    if (e.key === 'ArrowUp') next = (index + tabs.length - 1) % tabs.length;
    if (e.key === 'Home') next = 0;
    if (e.key === 'End') next = tabs.length - 1;
    if (next !== undefined) { e.preventDefault(); selectTab(tabs[next]); tabs[next].focus(); }
  });
});

document.querySelectorAll('form[data-confirm]').forEach(form => {
  form.addEventListener('submit', event => {
    if (!window.confirm(form.dataset.confirm)) event.preventDefault();
  });
});
