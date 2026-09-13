<?php
/**
 * Tupi Municipal Hospital Information Management System
 * Standard Table Paginator Component (Strict 8 Records Per Page with 1, 2, 3... Navigation)
 */

class Paginator
{
    public int $totalRecords;
    public int $recordsPerPage;
    public int $currentPage;
    public int $totalPages;
    public int $startRecord;
    public int $endRecord;
    public int $offset;
    private array $queryParams;

    public function __construct(int $totalRecords, int $recordsPerPage = 8, int $currentPage = 1, array $queryParams = [])
    {
        $this->totalRecords = max(0, $totalRecords);
        $this->recordsPerPage = max(1, $recordsPerPage);
        $this->totalPages = max(1, (int)ceil($this->totalRecords / $this->recordsPerPage));
        $this->currentPage = min(max(1, $currentPage), $this->totalPages);
        $this->offset = ($this->currentPage - 1) * $this->recordsPerPage;

        if ($this->totalRecords === 0) {
            $this->startRecord = 0;
            $this->endRecord = 0;
        } else {
            $this->startRecord = $this->offset + 1;
            $this->endRecord = min($this->offset + $this->recordsPerPage, $this->totalRecords);
        }

        $this->queryParams = $queryParams;
    }

    /**
     * Build URL for a specific page preserving existing filters
     */
    public function pageUrl(int $page): string
    {
        $params = $this->queryParams;
        $params['page'] = $page;
        return '?' . http_build_query($params);
    }

    /**
     * Render the pagination UI bar conforming strictly to the FDD & prompt requirements:
     * - "Showing 1–10 of 37 records"
     * - Disabled [Previous] on page 1
     * - Page numbers with active page indicator
     * - Disabled [Next] on last page
     */
    public function render(string $recordLabel = 'records'): string
    {
        $html = '<div class="tmhis-pagination-bar flex flex-col sm:flex-row items-center justify-between gap-4 py-3 px-4 bg-white border-t border-slate-200 text-xs font-medium text-slate-600 rounded-b-2xl select-none">';
        
        // Counter text: "Showing X–Y of Z records"
        $html .= '<div class="text-slate-500 font-medium">';
        if ($this->totalRecords === 0) {
            $html .= 'Showing <span class="font-bold text-slate-800">0</span> records';
        } else {
            $html .= 'Showing <span class="font-bold text-slate-900">' . $this->startRecord . '–' . $this->endRecord . '</span> of <span class="font-bold text-slate-900">' . $this->totalRecords . '</span> ' . htmlspecialchars($recordLabel);
        }
        $html .= '</div>';

        // Navigation controls
        $html .= '<div class="flex items-center gap-1.5">';

        // Previous Button
        if ($this->currentPage <= 1) {
            $html .= '<span class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-300 font-bold cursor-not-allowed bg-slate-50 flex items-center gap-1"><i data-lucide="chevron-left" class="w-3.5 h-3.5"></i> Previous</span>';
        } else {
            $html .= '<a href="' . htmlspecialchars($this->pageUrl($this->currentPage - 1)) . '" class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold transition flex items-center gap-1"><i data-lucide="chevron-left" class="w-3.5 h-3.5"></i> Previous</a>';
        }

        // Numeric Page Links
        $startPage = max(1, $this->currentPage - 2);
        $endPage = min($this->totalPages, $this->currentPage + 2);

        if ($startPage > 1) {
            $html .= '<a href="' . htmlspecialchars($this->pageUrl(1)) . '" class="w-8 h-8 rounded-lg flex items-center justify-center font-bold border border-slate-200 text-slate-700 hover:bg-slate-50 transition">1</a>';
            if ($startPage > 2) {
                $html .= '<span class="px-1 text-slate-400">...</span>';
            }
        }

        for ($p = $startPage; $p <= $endPage; $p++) {
            if ($p === $this->currentPage) {
                $html .= '<span class="w-8 h-8 rounded-lg flex items-center justify-center font-extrabold bg-blue-600 text-white shadow-xs">' . $p . '</span>';
            } else {
                $html .= '<a href="' . htmlspecialchars($this->pageUrl($p)) . '" class="w-8 h-8 rounded-lg flex items-center justify-center font-bold border border-slate-200 text-slate-700 hover:bg-slate-50 transition">' . $p . '</a>';
            }
        }

        if ($endPage < $this->totalPages) {
            if ($endPage < $this->totalPages - 1) {
                $html .= '<span class="px-1 text-slate-400">...</span>';
            }
            $html .= '<a href="' . htmlspecialchars($this->pageUrl($this->totalPages)) . '" class="w-8 h-8 rounded-lg flex items-center justify-center font-bold border border-slate-200 text-slate-700 hover:bg-slate-50 transition">' . $this->totalPages . '</a>';
        }

        // Next Button
        if ($this->currentPage >= $this->totalPages) {
            $html .= '<span class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-300 font-bold cursor-not-allowed bg-slate-50 flex items-center gap-1">Next <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i></span>';
        } else {
            $html .= '<a href="' . htmlspecialchars($this->pageUrl($this->currentPage + 1)) . '" class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold transition flex items-center gap-1">Next <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i></a>';
        }

        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }
}
