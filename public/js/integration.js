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
document.querySelectorAll('[data-new-password]').forEach(password => {
  const confirm = document.getElementById('confirm-password');
  const feedback = document.getElementById('password-match');
  const validate = () => {
    // Follow the server's password format and byte limit while preserving whitespace.
    const value = password.value;
    const valid = Array.from(value).length >= 8 && /[A-Z]/.test(value)
      && /[a-z]/.test(value) && /[0-9]/.test(value) && /[^A-Za-z0-9\p{White_Space}]/u.test(value);
    const byteLength = new TextEncoder().encode(value).length;
    let error = '';
    if (byteLength > 72) {
      error = 'Use at most 72 bytes. Some characters use more than one byte.';
    } else if (value && !valid) {
      error = 'Use the password format shown below the field.';
    }
    password.setCustomValidity(error);

    if (confirm) {
      const mismatch = confirm.value && confirm.value !== value;
      feedback.textContent = mismatch ? 'Passwords do not match.' : '';
      confirm.setCustomValidity(feedback.textContent);
      return !error && !mismatch;
    }
    return !error;
  };
  password.addEventListener('input', validate);
  confirm?.addEventListener('input', validate);
  password.form.addEventListener('submit', event => {
    if (!validate()) {
      event.preventDefault();
      password.form.reportValidity();
    }
  });
});

const openingTime = document.getElementById('venue-open-time');
const closingTime = document.getElementById('venue-close-time');
const hourlyRate = document.getElementById('price-per-session');
if (openingTime && closingTime && hourlyRate) {
  const minutes = time => Number(time.slice(0, 2)) * 60 + Number(time.slice(3, 5));
  const validateVenue = () => {
    const closingMinutes = closingTime.value === '00:00' ? 24 * 60 : minutes(closingTime.value);
    const hoursError = openingTime.value && closingTime.value && closingMinutes - minutes(openingTime.value) < 60;
    closingTime.setCustomValidity(hoursError ? 'Set the closing time at least one hour after opening. Use 00:00 for midnight.' : '');

    const rateError = hourlyRate.value && !/^(?:[0-9]+(?:\.[0-9]{1,2})?|\.[0-9]{1,2})$/.test(hourlyRate.value);
    hourlyRate.setCustomValidity(rateError ? 'Enter a number with up to 2 decimal places.' : '');
    return !hoursError && !rateError;
  };
  [openingTime, closingTime, hourlyRate].forEach(field => field.addEventListener('input', validateVenue));
  openingTime.form.addEventListener('submit', event => {
    if (!validateVenue()) {
      event.preventDefault();
      openingTime.form.reportValidity();
    }
  });
}

const photos = document.getElementById('photos');
if (photos) photos.addEventListener('change', () => {
  const preview = document.getElementById('photo-previews');
  preview.replaceChildren();
  const files = Array.from(photos.files);
  const valid = files.every(file => /\.(?:jpe?g|png|webp)$/i.test(file.name)
    && file.size > 0 && file.size <= Number(photos.dataset.maxBytes));
  photos.setCustomValidity(valid ? '' : 'Choose valid JPG, PNG or WebP photos within the size limit shown below.');
  if (!valid) return;
  files.forEach(file => {
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
