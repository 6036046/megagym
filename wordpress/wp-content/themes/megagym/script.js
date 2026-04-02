document.addEventListener('DOMContentLoaded', function() {
  const nav = document.querySelector('.primary-navigation');
  if (!nav) return;
  const links = nav.querySelectorAll('a');
  links.forEach(function(link) {
    link.addEventListener('click', function() {
      if (!link.hash) return;
      const target = document.querySelector(link.hash);
      if (target) {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });
});
