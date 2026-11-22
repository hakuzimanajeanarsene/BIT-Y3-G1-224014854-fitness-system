document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.alert').forEach(a => {
    setTimeout(() => { a.style.transition='opacity .5s'; a.style.opacity = 0; }, 3500);
    setTimeout(() => a.remove(), 4200);
  });
  document.querySelectorAll('form[data-confirm]').forEach(f => {
    f.addEventListener('submit', e => {
      if (!confirm(f.dataset.confirm)) e.preventDefault();
    });
  });
});
