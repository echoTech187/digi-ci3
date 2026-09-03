/**
 * Topbar Global Search, Theme Switcher & Maintenance Controls
 */
document.addEventListener('DOMContentLoaded', () => {
    'use strict';

    // ── Theme Switcher ──
    const themeDropdownItems = document.querySelectorAll('[data-bs-theme-value]');
    const activeThemeIcon = document.getElementById('theme-icon-active');

    function updateThemeIcon(theme) {
        if (!activeThemeIcon) return;
        activeThemeIcon.className = 'fas fa-fw ';
        if (theme === 'dark') {
            activeThemeIcon.classList.add('fa-moon');
        } else if (theme === 'light') {
            activeThemeIcon.classList.add('fa-sun');
        } else {
            activeThemeIcon.classList.add('fa-circle-half-stroke');
        }
    }

    const currentTheme = localStorage.getItem('theme') || 'auto';
    updateThemeIcon(currentTheme);

    themeDropdownItems.forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            const theme = item.getAttribute('data-bs-theme-value');
            localStorage.setItem('theme', theme);

            if (theme === 'auto') {
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                document.documentElement.setAttribute('data-bs-theme', prefersDark ? 'dark' : 'light');
            } else {
                document.documentElement.setAttribute('data-bs-theme', theme);
            }

            themeDropdownItems.forEach(el => el.classList.remove('active'));
            item.classList.add('active');
            updateThemeIcon(theme);
        });
    });

    // ── Global Search System ──
    const searchInput = document.getElementById('globalSearchInput');
    const searchLoader = document.getElementById('searchLoader');
    const searchIcon = document.getElementById('searchIcon');
    const resultsDropdown = document.getElementById('searchResultsDropdown');
    const searchMerchantId = window.SEARCH_MERCHANT_ID || '';
    let debounceTimer;
    let abortController = null;

    let activeCategory = 'All';
    let recentTransactionsData = null;
    let isFetchingRecent = false;

    function performSearch(query) {
        clearTimeout(debounceTimer);
        if (abortController) {
            abortController.abort();
        }

        if (!query || query.trim().length < 2) {
            if (resultsDropdown) {
                resultsDropdown.style.display = 'none';
                resultsDropdown.textContent = '';
            }
            if (searchLoader) searchLoader.classList.add('d-none');
            if (searchIcon) searchIcon.classList.remove('d-none');
            return;
        }

        if (searchLoader) searchLoader.classList.remove('d-none');
        if (searchIcon) searchIcon.classList.add('d-none');

        debounceTimer = setTimeout(async () => {
            abortController = new AbortController();
            const currentSignal = abortController.signal;

            try {
                let url = `${window.BASE_URL}dashboard/global-search?q=${encodeURIComponent(query)}`;
                if (searchMerchantId) {
                    url += `&merchant_id=${encodeURIComponent(searchMerchantId)}`;
                }

                const response = await fetch(url, {
                    signal: currentSignal,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (!response.ok) throw new Error('Search failed');

                const data = await response.json();
                if (currentSignal.aborted) return;

                if (data.status === 'success' && data.data && data.data.length > 0) {
                    resultsDropdown.textContent = '';
                    resultsDropdown.style.display = 'block';

                    const grouped = {};
                    const categories = ['All'];

                    data.data.forEach(item => {
                        const cat = item.category || 'Other';
                        if (!grouped[cat]) {
                            grouped[cat] = [];
                            categories.push(cat);
                        }
                        grouped[cat].push(item);
                    });

                    if (searchMerchantId) {
                        categories.push('Recent Transactions');
                    }

                    if (!categories.includes(activeCategory)) {
                        activeCategory = 'All';
                    }

                    const renderResults = () => {
                        resultsDropdown.textContent = '';

                        // Render Tabs Container
                        const tabsContainer = document.createElement('div');
                        tabsContainer.className = 'search-tabs-container';

                        categories.forEach(cat => {
                            const tab = document.createElement('button');
                            tab.type = 'button';
                            tab.className = `search-tab ${cat === activeCategory ? 'active' : ''}`;
                            tab.setAttribute('data-category', cat);

                            let count = '';
                            if (cat === 'All') count = ` (${data.data.length})`;
                            else if (cat === 'Recent Transactions') count = recentTransactionsData ? ` (${recentTransactionsData.length})` : '';
                            else count = ` (${grouped[cat].length})`;

                            tab.textContent = cat + count;
                            tabsContainer.appendChild(tab);
                        });

                        resultsDropdown.appendChild(tabsContainer);

                        // Render List Wrapper
                        const listWrapper = document.createElement('div');
                        listWrapper.className = 'search-list-wrapper';

                        if (activeCategory === 'All') {
                            Object.keys(grouped).forEach(cat => {
                                const catHeader = document.createElement('div');
                                catHeader.className = 'search-category-header';
                                catHeader.textContent = cat;
                                listWrapper.appendChild(catHeader);

                                grouped[cat].forEach(res => {
                                    const itemEl = createResultItem(res);
                                    listWrapper.appendChild(itemEl);
                                });
                            });
                        } else if (activeCategory === 'Recent Transactions') {
                            if (recentTransactionsData) {
                                if (recentTransactionsData.length > 0) {
                                    recentTransactionsData.forEach(rt => {
                                        const rtItem = createRecentTxItem(rt);
                                        listWrapper.appendChild(rtItem);
                                    });

                                    const linkWrapper = document.createElement('div');
                                    linkWrapper.className = 'p-2 mt-1';
                                    const linkA = document.createElement('a');
                                    linkA.href = `${window.BASE_URL}merchant/manage/detail/${encodeURIComponent(searchMerchantId)}#nav-history`;
                                    linkA.className = 'btn btn-sm btn-block text-primary font-weight-bold';
                                    linkA.innerHTML = 'View All Transactions <i class="fas fa-arrow-right ml-1"></i>';
                                    linkWrapper.appendChild(linkA);
                                    listWrapper.appendChild(linkWrapper);
                                } else {
                                    const emptyDiv = document.createElement('div');
                                    emptyDiv.className = 'p-3 text-center text-muted small';
                                    emptyDiv.innerHTML = '<i class="fas fa-inbox mb-2 d-block"></i>No recent transactions';
                                    listWrapper.appendChild(emptyDiv);
                                }
                            } else {
                                const loadDiv = document.createElement('div');
                                loadDiv.className = 'p-4 text-center text-muted small';
                                loadDiv.innerHTML = '<i class="fas fa-spinner fa-spin mb-2 d-block text-primary" style="font-size:20px;"></i>Loading transactions...';
                                listWrapper.appendChild(loadDiv);

                                if (!isFetchingRecent && searchMerchantId) {
                                    isFetchingRecent = true;
                                    fetch(`${window.BASE_URL}dashboard/recent-search?merchant_id=${encodeURIComponent(searchMerchantId)}`, {
                                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                                    })
                                        .then(res => res.json())
                                        .then(d => {
                                            if (d.status === 'success') {
                                                recentTransactionsData = d.data;
                                                if (activeCategory === 'Recent Transactions') renderResults();
                                            }
                                        })
                                        .catch((err) => {
                                            console.warn('Could not load recent transactions:', err);
                                            recentTransactionsData = [];
                                            if (activeCategory === 'Recent Transactions') renderResults();
                                        });
                                }
                            }
                        } else {
                            if (grouped[activeCategory]) {
                                grouped[activeCategory].forEach(res => {
                                    const itemEl = createResultItem(res);
                                    listWrapper.appendChild(itemEl);
                                });
                            }
                        }

                        resultsDropdown.appendChild(listWrapper);

                        // Tab event bindings
                        const tabs = resultsDropdown.querySelectorAll('.search-tab');
                        let isDragging = false;
                        tabs.forEach(tab => {
                            tab.addEventListener('click', (e) => {
                                if (isDragging) return;
                                e.stopPropagation();
                                activeCategory = tab.getAttribute('data-category');
                                renderResults();
                                document.getElementById('globalSearchInput').focus();
                            });
                        });
                    };

                    renderResults();
                } else {
                    resultsDropdown.textContent = '';
                    const emptyDiv = document.createElement('div');
                    emptyDiv.className = 'p-3 text-center text-muted small';
                    emptyDiv.innerHTML = '<i class="fas fa-search mb-2 d-block"></i>No matching results found';
                    resultsDropdown.appendChild(emptyDiv);
                    resultsDropdown.style.display = 'block';
                }
            } catch (error) {
                if (error.name !== 'AbortError') console.error('Search error:', error);
            } finally {
                if (!currentSignal.aborted) {
                    if (searchLoader) searchLoader.classList.add('d-none');
                    if (searchIcon) searchIcon.classList.remove('d-none');
                }
            }
        }, 300);
    }

    function createResultItem(res) {
        const itemEl = document.createElement('div');
        itemEl.className = 'search-result-item';
        itemEl.onclick = function() { window.location.href = res.url || '#'; };

        const iconDiv = document.createElement('div');
        iconDiv.className = 'result-icon';
        const iconEl = document.createElement('i');
        iconEl.className = res.icon || 'fas fa-search';
        iconDiv.appendChild(iconEl);

        const infoDiv = document.createElement('div');
        infoDiv.className = 'result-info';

        const titleDiv = document.createElement('div');
        titleDiv.className = 'result-title';
        titleDiv.textContent = res.title || '';

        const catDiv = document.createElement('div');
        catDiv.className = 'result-category';
        catDiv.textContent = res.category || '';

        infoDiv.appendChild(titleDiv);
        infoDiv.appendChild(catDiv);
        itemEl.appendChild(iconDiv);
        itemEl.appendChild(infoDiv);
        return itemEl;
    }

    function createRecentTxItem(rt) {
        let statusBadge = 'badge-secondary';
        if (rt.status === 'SUCCESS' || rt.status === 'Success' || rt.status === 'PAID') statusBadge = 'badge-success';
        else if (rt.status === 'PENDING' || rt.status === 'Pending' || rt.status === 'PROCESS') statusBadge = 'badge-warning';
        else if (rt.status === 'FAILED' || rt.status === 'Failed') statusBadge = 'badge-danger';

        const rtItem = document.createElement('div');
        rtItem.className = 'search-result-item recent-tx-item';

        const iconDiv = document.createElement('div');
        iconDiv.className = 'result-icon';
        const iconEl = document.createElement('i');
        iconEl.className = 'fas fa-receipt text-primary';
        iconDiv.appendChild(iconEl);

        const detailsDiv = document.createElement('div');
        detailsDiv.className = 'rt-details';
        detailsDiv.innerHTML = `<div class="rt-invoice">${rt.invoice || 'INV-UNKNOWN'}</div><div class="rt-meta">${rt.channel || ''} &bull; ${rt.date || ''}</div>`;

        const statusDiv = document.createElement('div');
        statusDiv.className = 'rt-status';
        statusDiv.innerHTML = `<span class="rt-amount">${rt.amount_formatted || ''}</span><span class="badge ${statusBadge} px-2 py-0" style="font-size:0.6rem; border-radius:4px;">${rt.status || ''}</span>`;

        rtItem.appendChild(iconDiv);
        rtItem.appendChild(detailsDiv);
        rtItem.appendChild(statusDiv);
        return rtItem;
    }

    if (searchInput) {
        searchInput.addEventListener('keydown', (e) => { if (e.key === 'Enter') e.preventDefault(); });
        searchInput.addEventListener('input', (e) => performSearch(e.target.value));
        searchInput.addEventListener('focus', (e) => { if(e.target.value.length > 1) resultsDropdown.style.display = 'block'; });
    }

    document.addEventListener('click', (e) => {
        if (searchInput && !searchInput.contains(e.target) && !resultsDropdown.contains(e.target)) {
            resultsDropdown.style.display = 'none';
        }
    });

    // ── Maintenance Toggle Logic ──
    const toggle = document.getElementById('toggleMaintenanceButton');
    if (toggle) {
        fetch(window.BASE_URL + 'dashboard/maintenance-status')
            .then(response => response.json())
            .then(data => { toggle.checked = (data.status === 'Not Active'); })
            .catch(err => console.error('Error fetching maintenance status:', err));

        toggle.addEventListener('change', function () {
            const status = toggle.checked ? 'Not Active' : 'Active';
            const displayStatus = toggle.checked ? 'ON' : 'OFF';
            const originalState = !toggle.checked;
            const isEnabling = toggle.checked;

            toggle.checked = originalState;

            Swal.fire({
                html: `
                    <div style="display:flex; flex-direction:column; align-items:center; gap:12px; padding: 8px 0;">
                        <div style="width:52px; height:52px; border-radius:50%; background:rgba(239,68,68,0.1); display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-network-wired" style="font-size:22px; color:#ef4444;"></i>
                        </div>
                        <h5 style="font-weight:700; margin:0; font-size:1.1rem;">Change Maintenance Mode?</h5>
                        <p style="color:var(--gray-500, #94a3b8); font-size:0.92rem; margin:0; line-height:1.6;">
                            Are you sure you want to turn Maintenance Mode <strong>${displayStatus}</strong>?<br>
                            <span style="font-size:0.82rem; opacity:0.7;">${isEnabling ? 'This will disable all merchant API access.' : 'This will re-enable all merchant API access.'}</span>
                        </p>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: `<i class="fas fa-check mr-1"></i> Yes, turn ${displayStatus}`,
                cancelButtonText: 'Cancel',
                customClass: {
                    popup: 'swal2-premium-popup',
                    confirmButton: 'swal2-premium-confirm',
                    cancelButton: 'swal2-premium-cancel',
                    actions: 'swal2-premium-actions'
                },
                buttonsStyling: false,
                focusConfirm: false
            }).then((result) => {
                if (result.isConfirmed) {
                    toggle.checked = !originalState;

                    const csrfName = document.querySelector('meta[name="csrf-token-name"]')?.getAttribute('content');
                    const csrfHash = document.querySelector('meta[name="csrf-token-hash"]')?.getAttribute('content');
                    const formData = new FormData();
                    formData.append('status', status);
                    if (csrfName && csrfHash) formData.append(csrfName, csrfHash);

                    fetch(window.BASE_URL + "dashboard/toggle-openapi", {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        Swal.fire({
                            html: `
                                <div style="display:flex; flex-direction:column; align-items:center; gap:10px; padding:8px 0;">
                                    <div style="width:52px; height:52px; border-radius:50%; background:rgba(28,200,138,0.1); display:flex; align-items:center; justify-content:center;">
                                        <i class="fas fa-check-circle" style="font-size:24px; color:#1cc88a;"></i>
                                    </div>
                                    <h5 style="font-weight:700; margin:0;">Updated!</h5>
                                    <p style="color:var(--gray-500,#94a3b8); font-size:0.9rem; margin:0;">${data.message || 'Maintenance Mode has been updated.'}</p>
                                </div>
                            `,
                            showConfirmButton: false,
                            timer: 2500,
                            customClass: { popup: 'swal2-premium-popup' },
                            buttonsStyling: false
                        });
                    })
                    .catch(() => {
                        toggle.checked = originalState;
                        Swal.fire({
                            html: `
                                <div style="display:flex; flex-direction:column; align-items:center; gap:10px; padding:8px 0;">
                                    <div style="width:52px; height:52px; border-radius:50%; background:rgba(239,68,68,0.1); display:flex; align-items:center; justify-content:center;">
                                        <i class="fas fa-exclamation-triangle" style="font-size:24px; color:#ef4444;"></i>
                                    </div>
                                    <h5 style="font-weight:700; margin:0;">Error</h5>
                                    <p style="color:var(--gray-500,#94a3b8); font-size:0.9rem; margin:0;">An error occurred while updating Maintenance Mode.</p>
                                </div>
                            `,
                            showConfirmButton: true,
                            confirmButtonText: 'OK',
                            customClass: { popup: 'swal2-premium-popup', confirmButton: 'swal2-premium-confirm' },
                            buttonsStyling: false
                        });
                    });
                }
            });
        });
    }
});
