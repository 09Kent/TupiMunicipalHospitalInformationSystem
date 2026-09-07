<?php
// includes/footer.php
?>
<!-- CLINICAL FOOTER (No Print) -->
<footer class="no-print mt-14 bg-white border-t border-slate-200 py-8 px-4 sm:px-6 lg:px-8 text-slate-500 text-xs">
  <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
    
    <div class="flex items-center gap-3">
      <div class="w-7 h-7 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-xs shadow-xs">
        +
      </div>
      <div>
        <span class="font-bold text-slate-900">Tupi Municipal Hospital Information Management System</span>
        <p class="text-[11px] text-slate-400">Accredited Clinical Registrator & Pre-Consultation System (PHP OOP & MySQL)</p>
      </div>
    </div>

    <!-- Regulatory & Safety Notice -->
    <div class="text-center md:text-right text-[11px] text-slate-400 max-w-md">
      <p>This digital clinical platform is intended for patient registration, queue triage, and consultation preparation. Final clinical diagnoses are conducted by licensed healthcare professionals.</p>
      <p class="mt-1 font-medium text-slate-500">© 2026 Tupi Municipal Hospital. Database: <code>MedicalRegistrationDB</code>.</p>
    </div>

  </div>
</footer>

<!-- Toast Notifications Container -->
<div id="toast-container" class="fixed top-20 right-5 z-50 flex flex-col gap-2 pointer-events-none"></div>

<!-- Initialize Lucide Icons -->
<script>
  document.addEventListener('DOMContentLoaded', () => {
    if (window.lucide) {
      window.lucide.createIcons();
    }
  });

  // Global Toast Helper
  window.showToast = function(message, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    const isSuccess = type === 'success';
    toast.className = `pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-2xl shadow-xl border text-xs font-bold transition-all duration-300 transform translate-x-10 opacity-0 ${
      isSuccess 
        ? 'bg-white text-slate-800 border-emerald-200 ring-2 ring-emerald-500/20' 
        : 'bg-white text-slate-800 border-rose-200 ring-2 ring-rose-500/20'
    }`;

    toast.innerHTML = `
      <div class="w-7 h-7 rounded-xl flex items-center justify-center shrink-0 ${isSuccess ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'}">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
      </div>
      <div>
        <div class="${isSuccess ? 'text-emerald-950 font-extrabold' : 'text-rose-950 font-extrabold'}">${isSuccess ? 'Success' : 'Notice'}</div>
        <div class="text-[11px] text-slate-600 font-medium">${message}</div>
      </div>
    `;

    container.appendChild(toast);
    setTimeout(() => {
      toast.classList.remove('translate-x-10', 'opacity-0');
    }, 20);

    setTimeout(() => {
      toast.classList.add('translate-x-10', 'opacity-0');
      setTimeout(() => toast.remove(), 300);
    }, 3500);
  };
</script>
</body>
</html>
