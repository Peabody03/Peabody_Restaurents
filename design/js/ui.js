/**
 * UI interactions only — no backend logic
 */
document.addEventListener('DOMContentLoaded', () => {
  // Header scroll shadow
  const header = document.querySelector('.site-header');
  if (header) {
    window.addEventListener('scroll', () => {
      header.classList.toggle('scrolled', window.scrollY > 20);
    });
  }

  // Mobile nav toggle
  const toggle = document.querySelector('.nav-toggle');
  const nav = document.querySelector('.nav-main');
  if (toggle && nav) {
    toggle.addEventListener('click', () => {
      nav.style.display = nav.style.display === 'flex' ? 'none' : 'flex';
      nav.style.flexDirection = 'column';
      nav.style.position = 'absolute';
      nav.style.top = '72px';
      nav.style.left = '0';
      nav.style.right = '0';
      nav.style.background = '#fff';
      nav.style.padding = '1rem';
      nav.style.boxShadow = '0 8px 24px rgba(0,0,0,0.1)';
    });
  }

  // Favorite toggle
  document.querySelectorAll('.food-card-fav').forEach(btn => {
    btn.addEventListener('click', e => {
      e.preventDefault();
      btn.classList.toggle('active');
      btn.textContent = btn.classList.contains('active') ? '❤️' : '🤍';
    });
  });

  // Animated counters
  document.querySelectorAll('[data-count]').forEach(el => {
    const target = parseInt(el.dataset.count, 10);
    const duration = 1500;
    const start = performance.now();
    const animate = now => {
      const progress = Math.min((now - start) / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3);
      el.textContent = Math.floor(eased * target).toLocaleString();
      if (progress < 1) requestAnimationFrame(animate);
    };
    requestAnimationFrame(animate);
  });

  // Qty controls (visual only)
  document.querySelectorAll('.qty-control').forEach(ctrl => {
    const minus = ctrl.querySelector('[data-qty-minus]');
    const plus = ctrl.querySelector('[data-qty-plus]');
    const display = ctrl.querySelector('[data-qty-value]');
    if (!display) return;
    minus?.addEventListener('click', () => {
      const v = Math.max(1, parseInt(display.textContent, 10) - 1);
      display.textContent = v;
    });
    plus?.addEventListener('click', () => {
      display.textContent = parseInt(display.textContent, 10) + 1;
    });
  });
});
