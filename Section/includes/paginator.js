/**
 * Tupi Municipal Hospital Information Management System
 * Client-Side 10-Record Table Paginator Component
 */

class TablePaginator {
    constructor(options) {
        this.containerId = options.containerId;
        this.items = options.items || [];
        this.pageSize = options.pageSize || 8;
        this.currentPage = options.initialPage || 1;
        this.recordLabel = options.recordLabel || 'records';
        this.onPageChange = options.onPageChange || function() {};
        this.paginationContainerId = options.paginationContainerId;
    }

    setItems(items) {
        this.items = items;
        this.currentPage = 1;
        this.render();
    }

    getTotalPages() {
        return Math.max(1, Math.ceil(this.items.length / this.pageSize));
    }

    getCurrentPageItems() {
        const start = (this.currentPage - 1) * this.pageSize;
        return this.items.slice(start, start + this.pageSize);
    }

    goToPage(page) {
        const total = this.getTotalPages();
        if (page < 1) page = 1;
        if (page > total) page = total;
        this.currentPage = page;
        this.render();
    }

    render() {
        const totalItems = this.items.length;
        const totalPages = this.getTotalPages();
        const startRecord = totalItems === 0 ? 0 : (this.currentPage - 1) * this.pageSize + 1;
        const endRecord = Math.min(startRecord + this.pageSize - 1, totalItems);

        // Render data items
        const pageItems = this.getCurrentPageItems();
        this.onPageChange(pageItems, this.currentPage, totalPages);

        // Render pagination controls
        if (this.paginationContainerId) {
            const container = document.getElementById(this.paginationContainerId);
            if (!container) return;

            let html = `
            <div class="tmhis-pagination-bar flex flex-col sm:flex-row items-center justify-between gap-4 py-3 px-4 bg-white border-t border-slate-200 text-xs font-medium text-slate-600 rounded-b-2xl select-none">
              <div class="text-slate-500 font-medium">
                ${totalItems === 0 
                  ? 'Showing <span class="font-bold text-slate-800">0</span> records' 
                  : `Showing <span class="font-bold text-slate-900">${startRecord}–${endRecord}</span> of <span class="font-bold text-slate-900">${totalItems}</span> ${this.recordLabel}`}
              </div>
              <div class="flex items-center gap-1.5">
            `;

            // Previous button
            if (this.currentPage <= 1) {
                html += `<span class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-300 font-bold cursor-not-allowed bg-slate-50 flex items-center gap-1"><i data-lucide="chevron-left" class="w-3.5 h-3.5"></i> Previous</span>`;
            } else {
                html += `<button type="button" onclick="window['${this.containerId}_paginator'].goToPage(${this.currentPage - 1})" class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold transition flex items-center gap-1"><i data-lucide="chevron-left" class="w-3.5 h-3.5"></i> Previous</button>`;
            }

            // Numeric page buttons
            const startP = Math.max(1, this.currentPage - 2);
            const endP = Math.min(totalPages, this.currentPage + 2);

            if (startP > 1) {
                html += `<button type="button" onclick="window['${this.containerId}_paginator'].goToPage(1)" class="w-8 h-8 rounded-lg flex items-center justify-center font-bold border border-slate-200 text-slate-700 hover:bg-slate-50 transition">1</button>`;
                if (startP > 2) html += `<span class="px-1 text-slate-400">...</span>`;
            }

            for (let p = startP; p <= endP; p++) {
                if (p === this.currentPage) {
                    html += `<span class="w-8 h-8 rounded-lg flex items-center justify-center font-extrabold bg-blue-600 text-white shadow-xs">${p}</span>`;
                } else {
                    html += `<button type="button" onclick="window['${this.containerId}_paginator'].goToPage(${p})" class="w-8 h-8 rounded-lg flex items-center justify-center font-bold border border-slate-200 text-slate-700 hover:bg-slate-50 transition">${p}</button>`;
                }
            }

            if (endP < totalPages) {
                if (endP < totalPages - 1) html += `<span class="px-1 text-slate-400">...</span>`;
                html += `<button type="button" onclick="window['${this.containerId}_paginator'].goToPage(${totalPages})" class="w-8 h-8 rounded-lg flex items-center justify-center font-bold border border-slate-200 text-slate-700 hover:bg-slate-50 transition">${totalPages}</button>`;
            }

            // Next button
            if (this.currentPage >= totalPages) {
                html += `<span class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-300 font-bold cursor-not-allowed bg-slate-50 flex items-center gap-1">Next <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i></span>`;
            } else {
                html += `<button type="button" onclick="window['${this.containerId}_paginator'].goToPage(${this.currentPage + 1})" class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold transition flex items-center gap-1">Next <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i></button>`;
            }

            html += `</div></div>`;
            container.innerHTML = html;
            if (window.lucide && typeof window.lucide.createIcons === 'function') {
                window.lucide.createIcons();
            }
        }
    }

    static paginateHtmlTable(tableSelectorOrEl, options = {}) {
        const table = typeof tableSelectorOrEl === 'string' ? document.querySelector(tableSelectorOrEl) : tableSelectorOrEl;
        if (!table) return null;
        const tbody = table.querySelector('tbody');
        if (!tbody) return null;
        
        const pageSize = options.pageSize || 8;
        const recordLabel = options.recordLabel || 'records';
        let currentPage = options.initialPage || 1;
        
        let paginationBar = table.nextElementSibling;
        if (!paginationBar || !paginationBar.classList.contains('tmhis-table-pagination-wrapper')) {
            paginationBar = document.createElement('div');
            paginationBar.className = 'tmhis-table-pagination-wrapper';
            table.parentNode.insertBefore(paginationBar, table.nextSibling);
        }
        
        function update() {
            const allRows = Array.from(tbody.querySelectorAll('tr')).filter(r => !r.classList.contains('empty-placeholder-row'));
            const visibleRows = allRows.filter(r => r.style.display !== 'none' || r.dataset.paginatedHidden === 'true');
            const total = visibleRows.length;
            const totalPages = Math.max(1, Math.ceil(total / pageSize));
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;
            
            const start = (currentPage - 1) * pageSize;
            const end = start + pageSize;
            
            visibleRows.forEach((row, idx) => {
                if (idx >= start && idx < end) {
                    row.style.display = '';
                    row.dataset.paginatedHidden = 'false';
                } else {
                    row.style.display = 'none';
                    row.dataset.paginatedHidden = 'true';
                }
            });
            
            const startRecord = total === 0 ? 0 : start + 1;
            const endRecord = Math.min(end, total);
            
            let html = `
            <div class="tmhis-pagination-bar flex flex-col sm:flex-row items-center justify-between gap-4 py-3 px-4 bg-white border-t border-slate-200 text-xs font-medium text-slate-600 rounded-b-2xl select-none">
              <div class="text-slate-500 font-medium">
                ${total === 0 
                  ? 'Showing <span class="font-bold text-slate-800">0</span> records' 
                  : `Showing <span class="font-bold text-slate-900">${startRecord}–${endRecord}</span> of <span class="font-bold text-slate-900">${total}</span> ${recordLabel}`}
              </div>
              <div class="flex items-center gap-1.5">
            `;
            
            if (currentPage <= 1) {
                html += `<span class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-300 font-bold cursor-not-allowed bg-slate-50 flex items-center gap-1"><i data-lucide="chevron-left" class="w-3.5 h-3.5"></i> Previous</span>`;
            } else {
                html += `<button type="button" class="btn-prev-page px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold transition flex items-center gap-1"><i data-lucide="chevron-left" class="w-3.5 h-3.5"></i> Previous</button>`;
            }
            
            const startP = Math.max(1, currentPage - 2);
            const endP = Math.min(totalPages, currentPage + 2);
            
            if (startP > 1) {
                html += `<button type="button" data-page="1" class="btn-num-page w-8 h-8 rounded-lg flex items-center justify-center font-bold border border-slate-200 text-slate-700 hover:bg-slate-50 transition">1</button>`;
                if (startP > 2) html += `<span class="px-1 text-slate-400">...</span>`;
            }
            
            for (let p = startP; p <= endP; p++) {
                if (p === currentPage) {
                    html += `<span class="w-8 h-8 rounded-lg flex items-center justify-center font-extrabold bg-blue-600 text-white shadow-xs">${p}</span>`;
                } else {
                    html += `<button type="button" data-page="${p}" class="btn-num-page w-8 h-8 rounded-lg flex items-center justify-center font-bold border border-slate-200 text-slate-700 hover:bg-slate-50 transition">${p}</button>`;
                }
            }
            
            if (endP < totalPages) {
                if (endP < totalPages - 1) html += `<span class="px-1 text-slate-400">...</span>`;
                html += `<button type="button" data-page="${totalPages}" class="btn-num-page w-8 h-8 rounded-lg flex items-center justify-center font-bold border border-slate-200 text-slate-700 hover:bg-slate-50 transition">${totalPages}</button>`;
            }
            
            if (currentPage >= totalPages) {
                html += `<span class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-300 font-bold cursor-not-allowed bg-slate-50 flex items-center gap-1">Next <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i></span>`;
            } else {
                html += `<button type="button" class="btn-next-page px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold transition flex items-center gap-1">Next <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i></button>`;
            }
            
            html += `</div></div>`;
            paginationBar.innerHTML = html;
            
            const prevBtn = paginationBar.querySelector('.btn-prev-page');
            if (prevBtn) prevBtn.addEventListener('click', () => { currentPage--; update(); });
            
            const nextBtn = paginationBar.querySelector('.btn-next-page');
            if (nextBtn) nextBtn.addEventListener('click', () => { currentPage++; update(); });
            
            paginationBar.querySelectorAll('.btn-num-page').forEach(btn => {
                btn.addEventListener('click', () => {
                    currentPage = parseInt(btn.dataset.page);
                    update();
                });
            });
            
            if (window.lucide && typeof window.lucide.createIcons === 'function') {
                window.lucide.createIcons();
            }
        }
        
        update();
        return {
            refresh: update,
            goToPage: (p) => { currentPage = p; update(); }
        };
    }
}

window.TablePaginator = TablePaginator;
