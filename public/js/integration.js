// Presentation only. All saved records and authentication use PHP forms.
document.querySelectorAll('[data-court]').forEach(button => {
  button.addEventListener('click', () => {
    document.querySelectorAll('[data-court]').forEach(other => {
      const selected = other.dataset.court === button.dataset.court && other.dataset.time === button.dataset.time;
      other.classList.toggle('selected', selected);
      other.setAttribute('aria-pressed', String(selected));
    });
    document.getElementById('selected-court').value = button.dataset.court;
    document.getElementById('selected-time').value = button.dataset.time;
    const summary = document.getElementById('booking-summary');
    summary.textContent = button.dataset.label;
    summary.hidden = false;
    document.getElementById('reserve-button').disabled = false;
  });
});
const signup = document.getElementById('signup-form');
if (signup) {
  const password = document.getElementById('password');
  const confirm = document.getElementById('confirm-password');
  const validate = () => confirm.setCustomValidity(password.value === confirm.value ? '' : 'Passwords must match.');
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
