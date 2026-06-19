/**
 * Food detail modal on menu category pages.
 */
(function () {
    'use strict';

    const modalEl = document.getElementById('foodViewModal');
    if (!modalEl) {
        return;
    }

    // Keep modal at document root so it is not clipped by dashboard layout.
    if (modalEl.parentElement !== document.body) {
        document.body.appendChild(modalEl);
    }

    const img = document.getElementById('foodViewModalImage');
    const title = document.getElementById('foodViewModalLabel');
    const price = document.getElementById('foodViewModalPrice');
    const desc = document.getElementById('foodViewModalDesc');
    const category = document.getElementById('foodViewModalCategory');
    const foodIdInput = document.getElementById('foodViewModalFoodId');

    document.querySelectorAll('.js-food-view').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const card = btn.closest('.menu-food-item');
            if (!card) {
                return;
            }

            if (title) {
                title.textContent = card.dataset.foodName || '';
            }
            if (price) {
                price.textContent = card.dataset.foodPrice || '';
            }
            if (desc) {
                desc.textContent = card.dataset.foodDesc || '';
            }
            if (category) {
                category.textContent = card.dataset.foodCategory || 'Menu';
            }
            if (img) {
                img.alt = card.dataset.foodName || '';
                img.style.opacity = '0';
                img.src = card.dataset.foodImage || '';
                img.onload = function () {
                    img.style.opacity = '1';
                };
            }
            if (foodIdInput) {
                foodIdInput.value = card.dataset.foodId || '';
            }

            if (typeof bootstrap !== 'undefined') {
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            }
        });
    });
})();
