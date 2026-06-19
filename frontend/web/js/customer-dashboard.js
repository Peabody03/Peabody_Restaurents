/**
 * Customer dashboard — live polling & UI interactions (frontend only)
 */
(function () {
    'use strict';

    const POLL_INTERVAL = 10000;
    const liveUrl = document.body.dataset.liveUrl;

    // Sidebar toggle (mobile)
    const sidebar = document.getElementById('customerSidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    const toggle = document.getElementById('sidebarToggle');

    function closeSidebar() {
        sidebar?.classList.remove('open');
        backdrop?.classList.remove('visible');
    }

    if (toggle && sidebar) {
        toggle.addEventListener('click', () => {
            if (sidebar.classList.contains('open')) {
                closeSidebar();
            } else {
                sidebar.classList.add('open');
                backdrop?.classList.add('visible');
            }
        });
    }

    backdrop?.addEventListener('click', closeSidebar);

    document.querySelectorAll('.customer-nav-link').forEach((link) => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 768) closeSidebar();
        });
    });

    // Animated counter helper
    function animateValue(el, target, prefix, suffix) {
        const start = parseFloat(el.dataset.current || '0');
        const duration = 800;
        const startTime = performance.now();

        function tick(now) {
            const progress = Math.min((now - startTime) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            const current = start + (target - start) * eased;
            el.textContent = prefix + formatNumber(current) + suffix;
            if (progress < 1) requestAnimationFrame(tick);
            else el.dataset.current = String(target);
        }

        requestAnimationFrame(tick);
    }

    function formatNumber(n) {
        if (Number.isInteger(n) || n % 1 === 0) {
            return Math.round(n).toLocaleString();
        }
        return n.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    }

    function statusClass(status) {
        const map = {
            pending: 'pending',
            confirmed: 'confirmed',
            preparing: 'preparing',
            ready: 'ready',
            delivered: 'delivered',
            cancelled: 'cancelled',
        };
        return map[status] || 'pending';
    }

    function renderOrders(orders) {
        const tbody = document.getElementById('recentOrdersBody');
        if (!tbody) return;

        if (!orders.length) {
            tbody.innerHTML = '<tr><td colspan="5" class="cd-empty-state">No orders yet. <a href="' + (document.body.dataset.menuUrl || '#') + '">Browse the menu</a></td></tr>';
            return;
        }

        const prevIds = tbody.dataset.orderIds ? JSON.parse(tbody.dataset.orderIds) : [];
        const html = orders.map((o) => {
            const isNew = !prevIds.includes(o.id);
            return '<tr class="' + (isNew && prevIds.length ? 'new-row' : '') + '">' +
                '<td><strong>' + escapeHtml(o.order_number) + '</strong></td>' +
                '<td>' + escapeHtml(o.items_summary) + '</td>' +
                '<td>' + escapeHtml(o.total) + '</td>' +
                '<td><span class="cd-status-badge ' + statusClass(o.status) + '">' + escapeHtml(o.status_label) + '</span></td>' +
                '<td class="text-muted">' + escapeHtml(o.date) + '</td>' +
                '</tr>';
        }).join('');

        tbody.innerHTML = html;
        tbody.dataset.orderIds = JSON.stringify(orders.map((o) => o.id));
    }

    function renderTopItems(items) {
        const container = document.getElementById('topItemsList');
        if (!container) return;

        if (!items.length) {
            container.innerHTML = '<div class="cd-empty-state">No purchase history yet.</div>';
            return;
        }

        const maxQty = Math.max(...items.map((i) => i.qty), 1);
        container.innerHTML = items.map((item, idx) =>
            '<div class="cd-top-item">' +
            '<span class="cd-top-rank">' + (idx + 1) + '</span>' +
            '<div class="cd-top-info">' +
            '<span class="cd-top-name">' + escapeHtml(item.name) + '</span>' +
            '<span class="cd-top-meta">' + item.qty + ' purchased · ' + escapeHtml(item.revenue) + '</span>' +
            '</div>' +
            '<div class="cd-top-bar-wrap"><div class="cd-top-bar" style="width:' + Math.round((item.qty / maxQty) * 100) + '%"></div></div>' +
            '</div>'
        ).join('');
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function updateKpis(data) {
        const avgEl = document.getElementById('kpiAvgOrder');
        const totalEl = document.getElementById('kpiTotalOrders');
        const cards = document.querySelectorAll('.cd-kpi-card');

        if (avgEl) {
            animateValue(avgEl, data.avg_order_value, 'TZS ', '');
            cards[0]?.classList.add('updating');
        }
        if (totalEl) {
            animateValue(totalEl, data.total_orders, '', '');
            cards[1]?.classList.add('updating');
        }

        setTimeout(() => cards.forEach((c) => c.classList.remove('updating')), 600);

        const updatedAt = document.getElementById('lastUpdated');
        if (updatedAt) {
            updatedAt.textContent = '· Updated ' + new Date().toLocaleTimeString();
        }

        document.querySelectorAll('.cd-live-badge').forEach((badge) => {
            badge.classList.add('cd-live-pulse');
            setTimeout(() => badge.classList.remove('cd-live-pulse'), 600);
        });
    }

    function fetchLiveData() {
        if (!liveUrl) return;

        const range = document.getElementById('dateFilter')?.value || 'today';
        const url = liveUrl + (liveUrl.includes('?') ? '&' : '?') + 'range=' + encodeURIComponent(range);

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        })
            .then((res) => res.json())
            .then((data) => {
                if (!data.success) return;
                updateKpis(data);
                renderOrders(data.recent_orders || []);
                renderTopItems(data.top_items || []);
            })
            .catch(() => {
                const badge = document.querySelector('.cd-live-badge');
                if (badge) {
                    badge.style.background = 'var(--cd-warning-soft)';
                    badge.style.color = '#92400E';
                    badge.innerHTML = '<span class="cd-live-dot" style="background:var(--cd-warning)"></span> Reconnecting…';
                }
            });
    }

    // Date filter UI (client-side label only for demo)
    const dateFilter = document.getElementById('dateFilter');
    if (dateFilter) {
        dateFilter.addEventListener('change', () => fetchLiveData());
    }

    const branchFilter = document.getElementById('branchFilter');
    if (branchFilter) {
        branchFilter.addEventListener('change', () => fetchLiveData());
    }

    if (liveUrl) {
        fetchLiveData();
        setInterval(fetchLiveData, POLL_INTERVAL);
    }
})();
