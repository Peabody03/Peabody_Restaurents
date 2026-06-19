(function () {
    'use strict';

    const root = document.getElementById('dashboard-live');
    if (!root) return;

    const apiUrl = root.dataset.api;
    const date = root.dataset.date;
    const pollMs = 10000;

    function fmtMoney(n) {
        return 'TZS ' + Math.round(Number(n)).toLocaleString();
    }

    function fmtTrend(v) {
        const n = Number(v);
        return (n >= 0 ? '+' : '') + n + '%';
    }

    function pulseKpis() {
        document.querySelectorAll('.kpi-value').forEach(function (el) {
            el.classList.add('kpi-flash');
            setTimeout(function () { el.classList.remove('kpi-flash'); }, 600);
        });
    }

    function renderOrders(orders) {
        const tbody = document.getElementById('recent-orders-body');
        if (!tbody) return;
        if (!orders.length) {
            tbody.innerHTML = '<tr class="empty-row"><td colspan="5" class="text-center text-muted py-5">No orders for this date yet.</td></tr>';
            return;
        }
        tbody.innerHTML = orders.map(function (o) {
            return '<tr>' +
                '<td><a href="' + o.viewUrl + '" class="order-link">' + escapeHtml(o.orderNumber) + '</a></td>' +
                '<td>' + escapeHtml(o.customer) + '</td>' +
                '<td><span class="status-badge status-' + o.status + '">' + escapeHtml(o.statusLabel) + '</span></td>' +
                '<td class="fw-semibold">' + escapeHtml(o.total) + '</td>' +
                '<td class="text-muted small">' + escapeHtml(o.time) + '</td>' +
                '</tr>';
        }).join('');
    }

    function renderTopItems(items) {
        const list = document.getElementById('top-items-list');
        if (!list) return;
        if (!items.length) {
            list.innerHTML = '<li class="top-item-empty text-muted text-center py-5">No sales data yet.</li>';
            return;
        }
        list.innerHTML = items.map(function (item, i) {
            return '<li class="top-item-row">' +
                '<div class="top-item-rank">' + (i + 1) + '</div>' +
                '<div class="top-item-info">' +
                '<span class="top-item-name">' + escapeHtml(item.name) + '</span>' +
                '<span class="top-item-sales">TZS ' + Math.round(item.sales).toLocaleString() + '</span>' +
                '</div>' +
                '<span class="top-item-badge">' + item.qty + ' sold</span>' +
                '</li>';
        }).join('');
    }

    function escapeHtml(str) {
        const d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    function refresh() {
        fetch(apiUrl + '?date=' + encodeURIComponent(date))
            .then(function (r) { return r.json(); })
            .then(function (data) {
                const s = data.stats;
                document.querySelector('[data-stat="totalRevenue"]').textContent = fmtMoney(s.totalRevenue);
                document.querySelector('[data-stat="avgOrder"]').textContent = fmtMoney(s.avgOrder);
                document.querySelector('[data-stat="totalOrders"]').textContent = s.totalOrders;
                document.querySelector('[data-stat="itemsSold"]').textContent = s.itemsSold;

                ['revenueGrowth', 'avgGrowth', 'ordersGrowth', 'itemsGrowth'].forEach(function (key) {
                    const el = document.querySelector('[data-trend="' + key + '"]');
                    if (el) {
                        el.textContent = fmtTrend(s[key]);
                        el.classList.toggle('text-success', s[key] >= 0);
                        el.classList.toggle('text-danger', s[key] < 0);
                    }
                });

                renderOrders(data.recentOrders || []);
                renderTopItems(data.topItems || []);

                const updated = document.getElementById('last-updated');
                if (updated) updated.textContent = 'Updated ' + (data.updatedAt || 'just now');

                pulseKpis();
            })
            .catch(function () {});
    }

    refresh();
    setInterval(refresh, pollMs);
})();
