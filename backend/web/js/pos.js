(function () {
    'use strict';
    const cart = {};
    const grid = document.getElementById('pos-product-grid');
    const cartEl = document.getElementById('pos-cart-items');
    const subtotalEl = document.getElementById('pos-subtotal');
    const taxEl = document.getElementById('pos-tax');
    const totalEl = document.getElementById('pos-total');
    const discountEl = document.getElementById('pos-discount');
    const msgEl = document.getElementById('pos-message');
    const searchEl = document.getElementById('pos-search');

    function fmt(n) {
        return 'TZS ' + Math.round(n).toLocaleString();
    }

    function renderCart() {
        cartEl.innerHTML = '';
        let subtotal = 0;
        Object.values(cart).forEach(function (item) {
            subtotal += item.price * item.qty;
            const row = document.createElement('div');
            row.className = 'pos-cart-row';
            row.innerHTML = '<div><strong>' + item.name + '</strong><br><small>' + fmt(item.price) + '</small></div>' +
                '<div class="d-flex align-items-center gap-1">' +
                '<button type="button" class="pos-qty-btn" data-action="dec" data-id="' + item.id + '">-</button>' +
                '<span>' + item.qty + '</span>' +
                '<button type="button" class="pos-qty-btn" data-action="inc" data-id="' + item.id + '">+</button>' +
                '</div>';
            cartEl.appendChild(row);
        });
        const discount = parseFloat(discountEl.value) || 0;
        const tax = subtotal * (window.posTaxRate || 0.18);
        const total = Math.max(0, subtotal + tax - discount);
        subtotalEl.textContent = fmt(subtotal);
        taxEl.textContent = fmt(tax);
        totalEl.textContent = fmt(total);
    }

    function addItem(id, name, price) {
        if (!cart[id]) cart[id] = { id: id, name: name, price: parseFloat(price), qty: 0 };
        cart[id].qty++;
        renderCart();
    }

    grid.addEventListener('click', function (e) {
        const card = e.target.closest('.pos-product-card');
        if (!card) return;
        addItem(card.dataset.id, card.dataset.name, card.dataset.price);
    });

    cartEl.addEventListener('click', function (e) {
        const btn = e.target.closest('.pos-qty-btn');
        if (!btn) return;
        const id = btn.dataset.id;
        if (btn.dataset.action === 'inc') cart[id].qty++;
        else cart[id].qty = Math.max(0, cart[id].qty - 1);
        if (cart[id].qty === 0) delete cart[id];
        renderCart();
    });

    discountEl.addEventListener('input', renderCart);

    document.getElementById('pos-clear').addEventListener('click', function () {
        Object.keys(cart).forEach(function (k) { delete cart[k]; });
        renderCart();
        msgEl.innerHTML = '';
    });

    document.getElementById('pos-checkout').addEventListener('click', function () {
        const items = Object.values(cart).map(function (i) { return { id: i.id, qty: i.qty }; });
        if (!items.length) { msgEl.innerHTML = '<div class="alert alert-warning">Cart is empty.</div>'; return; }
        const formData = new FormData();
        formData.append('items', JSON.stringify(items));
        formData.append('payment_method', document.getElementById('pos-payment').value);
        formData.append('discount', discountEl.value);
        formData.append(document.querySelector('meta[name="csrf-param"]').content, document.querySelector('meta[name="csrf-token"]').content);
        fetch(window.posCheckoutUrl, { method: 'POST', body: formData })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success) {
                    msgEl.innerHTML = '<div class="alert alert-success">Sale complete! Order ' + data.orderNumber + ' — ' + data.formattedTotal + '</div>';
                    Object.keys(cart).forEach(function (k) { delete cart[k]; });
                    renderCart();
                } else {
                    msgEl.innerHTML = '<div class="alert alert-danger">' + (data.message || 'Checkout failed') + '</div>';
                }
            });
    });

    let searchTimer;
    searchEl.addEventListener('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function () {
            fetch(window.posSearchUrl + '?q=' + encodeURIComponent(searchEl.value))
                .then(function (r) { return r.json(); })
                .then(function (items) {
                    grid.innerHTML = items.map(function (f) {
                        return '<button type="button" class="pos-product-card" data-id="' + f.id + '" data-name="' + f.name + '" data-price="' + f.price + '">' +
                            '<div class="pos-product-img" style="background-image:url(\'' + f.image + '\')"></div>' +
                            '<div class="pos-product-info"><strong>' + f.name + '</strong><span>' + f.formattedPrice + '</span></div></button>';
                    }).join('');
                });
        }, 300);
    });
})();
