/* Low-dependency progressive enhancement for the Phase 3 UI. */
document.addEventListener('DOMContentLoaded', function () {
  const search = document.querySelector('[data-court-conversation-search]');
  if (search) {
    const entries = Array.from(document.querySelectorAll('[data-court-searchable]'));
    search.addEventListener('input', function () {
      const query = search.value.toLocaleLowerCase().trim();
      entries.forEach(function (entry) {
        entry.hidden = !entry.textContent.toLocaleLowerCase().includes(query);
      });
    });
  }
  const thread = document.querySelector('[data-court-messages]');
  if (thread) thread.scrollTop = thread.scrollHeight;
});
