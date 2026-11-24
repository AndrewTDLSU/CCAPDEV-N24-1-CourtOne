const useFallback = image => {
  if (!image.hasAttribute('data-using-fallback')) {
    image.setAttribute('data-using-fallback', '');
    image.src = image.dataset.imageFallback;
  }
};

document.addEventListener('error', event => {
  if (event.target.matches?.('img[data-image-fallback]')) useFallback(event.target);
}, true);

document.querySelectorAll('img[data-image-fallback]').forEach(image => {
  if (image.getAttribute('src') === image.dataset.imageFallback) {
    image.setAttribute('data-using-fallback', '');
  } else if (image.complete && image.naturalWidth === 0) {
    useFallback(image);
  }
});

const conversationSearch = document.querySelector('[data-court-conversation-search]');
if (conversationSearch) {
  const entries = document.querySelectorAll('[data-court-searchable]');
  const noResults = document.querySelector('[data-court-no-conversations]');
  conversationSearch.addEventListener('input', () => {
    const query = conversationSearch.value.toLocaleLowerCase().trim();
    let found = false;
    entries.forEach(entry => {
      const contact = Array.from(entry.children).slice(0, -1).map(label => label.textContent).join(' ');
      entry.hidden = !contact.toLocaleLowerCase().includes(query);
      found ||= !entry.hidden;
    });
    noResults.hidden = found;
  });
}

const thread = document.querySelector('[data-court-messages]');
if (thread) thread.scrollTop = thread.scrollHeight;

function updateSectionLinks() {
  document.querySelectorAll('[data-section-nav]').forEach(nav => {
    const links = Array.from(nav.querySelectorAll('a[href^="#"]'));
    const active = links.find(link => link.hash === location.hash) ?? links[0];
    links.forEach(link => {
      link.classList.toggle('active', link === active);
      if (link === active) link.setAttribute('aria-current', 'location');
      else link.removeAttribute('aria-current');
    });
  });
}
updateSectionLinks();
window.addEventListener('hashchange', updateSectionLinks);
