<?php
// Doctor/includes/footer.php
?>

  <!-- Toast Notification Container -->
  <div id="toastContainer" class="fixed bottom-6 right-6 z-50 flex flex-col gap-2 pointer-events-none"></div>

  <!-- Lucide & AOS Initialization Script -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Initialize Lucide Icons
      if (window.lucide) {
        lucide.createIcons();
      }

      // Initialize AOS Animate On Scroll
      if (window.AOS) {
        AOS.init({
          duration: 600,
          once: true,
          easing: 'ease-out-cubic'
        });
      }

      // Re-initialize Lucide after any dynamic DOM changes
      window.refreshIcons = () => {
        if (window.lucide) {
          lucide.createIcons();
        }
      };

      // Toast Notification Helper
      window.showToast = (message, type = 'success') => {
        const container = document.getElementById('toastContainer');
        if (!container) return;

        const toast = document.createElement('div');
        const bg = type === 'success' ? 'bg-emerald-600' : (type === 'error' ? 'bg-rose-600' : 'bg-blue-600');
        const icon = type === 'success' ? 'check-circle' : (type === 'error' ? 'alert-circle' : 'info');

        toast.className = `${bg} text-white px-4 py-3 rounded-2xl shadow-xl flex items-center gap-3 text-xs font-bold pointer-events-auto transform translate-y-4 opacity-0 transition-all duration-300`;
        toast.innerHTML = `
          <i data-lucide="${icon}" class="w-4 h-4 shrink-0"></i>
          <span>${message}</span>
        `;
        container.appendChild(toast);
        if (window.lucide) lucide.createIcons();

        setTimeout(() => {
          toast.classList.remove('translate-y-4', 'opacity-0');
        }, 10);

        setTimeout(() => {
          toast.classList.add('translate-y-4', 'opacity-0');
          setTimeout(() => toast.remove(), 300);
        }, 4000);
      };

      // Auto-display PHP Flash message if exists
      <?php if ($flash = Session::getFlash()): ?>
        window.showToast(<?= json_encode($flash['message']) ?>, <?= json_encode($flash['type']) ?>);
      <?php endif; ?>
    });
  </script>

</body>
</html>
