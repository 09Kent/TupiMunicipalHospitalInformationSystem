/**
 * Tupi Municipal Hospital Information Management System
 * Client-side Interactivity Script
 */

document.addEventListener('DOMContentLoaded', () => {
  // 1. Initialize AOS Animations
  if (typeof AOS !== 'undefined') {
    AOS.init({
      duration: 800,
      easing: 'ease-out-cubic',
      once: true,
      offset: 80
    });
  }

  // 2. Initialize Lucide Icons
  if (typeof lucide !== 'undefined') {
    lucide.createIcons();
  }

  // 3. Sticky Navigation Scroll Effect
  const navbar = document.getElementById('navbar');
  const handleScroll = () => {
    if (window.scrollY > 20) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  };
  window.addEventListener('scroll', handleScroll);
  handleScroll();

  // 4. Mobile Menu Drawer Toggle
  const menuButton = document.getElementById('menuButton');
  const mobileMenu = document.getElementById('mobileMenu');
  const mobileMenuLinks = document.querySelectorAll('.mobile-menu-link');

  if (menuButton && mobileMenu) {
    menuButton.addEventListener('click', () => {
      mobileMenu.classList.toggle('hidden');
      const isExpanded = !mobileMenu.classList.contains('hidden');
      menuButton.setAttribute('aria-expanded', isExpanded);
    });

    mobileMenuLinks.forEach(link => {
      link.addEventListener('click', () => {
        mobileMenu.classList.add('hidden');
      });
    });
  }

  // 5. Hospital Facilities Search Filter
  const facilities = [
    { name: "Emergency Room", type: "24/7 Critical Care", pop: "24/7", captain: "Dr. Maria Santos", detail: "Fully equipped emergency department for trauma, cardiac events, respiratory emergencies, and acute medical conditions." },
    { name: "General Ward", type: "Inpatient Facility", pop: "20 beds", captain: "Head Nurse: Rosa Cruz", detail: "Multi-bed ward for admitted patients requiring observation, IV therapy, and continuous nursing care." },
    { name: "Private Rooms", type: "Inpatient Facility", pop: "10 rooms", captain: "Head Nurse: Ana Reyes", detail: "Individual patient rooms with private bathroom, air conditioning, and dedicated nursing attention." },
    { name: "Operating Room", type: "Surgical Suite", pop: "2 ORs", captain: "Dr. Juan dela Cruz", detail: "Sterile surgical theaters for major and minor operations including cesarean sections and general surgery." },
    { name: "Delivery Room", type: "Obstetric Unit", pop: "4 beds", captain: "Dr. Elena Magsaysay", detail: "Labor and delivery suite for normal and assisted deliveries with newborn resuscitation capability." },
    { name: "NICU / Nursery", type: "Neonatal Care", pop: "8 bassinets", captain: "Dr. Sofia Aquino", detail: "Neonatal intensive care and well-baby nursery with incubators and phototherapy equipment." },
    { name: "Clinical Laboratory", type: "Diagnostics", pop: "Full-service", captain: "Med Tech: Carlo Luna", detail: "Complete laboratory services for CBC, blood chemistry, urinalysis, serology, and specialized tests." },
    { name: "Radiology Unit", type: "Imaging Center", pop: "X-ray & Ultrasound", captain: "Rad Tech: Marco Silang", detail: "Digital X-ray, ultrasound, and ECG services for comprehensive diagnostic imaging assessments." },
    { name: "Pharmacy", type: "Pharmaceutical Services", pop: "In-house", captain: "RPh: Liza Bonifacio", detail: "Hospital pharmacy dispensing prescription medications, generic drugs, and pharmaceutical counseling." },
    { name: "Dental Clinic", type: "Oral Health", pop: "2 chairs", captain: "Dr. Miguel Rizal", detail: "Dental clinic offering tooth extraction, oral prophylaxis, dental checkups, and oral surgery." },
    { name: "Physical Therapy", type: "Rehabilitation", pop: "Rehab gym", captain: "PT: Gabriel Mabini", detail: "Rehabilitation services with therapy equipment for post-surgical recovery and mobility restoration." },
    { name: "OPD Clinic", type: "Outpatient Services", pop: "6 rooms", captain: "Dr. Carmen Aguinaldo", detail: "Outpatient consultation rooms for general medicine, pediatrics, OB-GYN, and specialist referrals." },
    { name: "Medical Records", type: "Health Information", pop: "Archive", captain: "Chief: Teresa del Pilar", detail: "Patient record management, medical certificate issuance, clinical abstracts, and health data archiving." },
    { name: "Ambulance Bay", type: "Emergency Transport", pop: "2 units", captain: "EMT: Roberto Quezon", detail: "Ambulance garage with 24/7 dispatch for emergency response and inter-facility patient transfers." },
    { name: "Admin & Billing", type: "Hospital Administration", pop: "Front desk", captain: "Admin: Patricia Corpuz", detail: "Hospital administration, patient billing, PhilHealth processing, and medical social services office." }
  ];

  const barangayGrid = document.getElementById('barangayGrid');
  const searchInput = document.getElementById('barangaySearch');
  const searchCount = document.getElementById('searchCount');

  function renderBarangays(query = '') {
    if (!barangayGrid) return;
    const filtered = facilities.filter(b => 
      b.name.toLowerCase().includes(query.toLowerCase()) || 
      b.type.toLowerCase().includes(query.toLowerCase()) ||
      b.detail.toLowerCase().includes(query.toLowerCase())
    );

    if (searchCount) {
      searchCount.textContent = `Showing ${filtered.length} of ${facilities.length} Facilities`;
    }

    if (filtered.length === 0) {
      barangayGrid.innerHTML = `
        <div class="col-span-full text-center py-12 bg-slate-50 rounded-2xl border border-slate-200">
          <i data-lucide="search-x" class="w-12 h-12 text-slate-400 mx-auto mb-3"></i>
          <h3 class="text-lg font-semibold text-slate-700">No Facilities Found</h3>
          <p class="text-sm text-slate-500 mt-1">Try searching with a different keyword or facility name.</p>
        </div>
      `;
    } else {
      barangayGrid.innerHTML = filtered.map(b => `
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all hover:border-blue-300 group">
          <div class="flex items-start justify-between mb-3">
            <span class="inline-flex items-center justify-center p-2.5 bg-blue-50 text-blue-600 rounded-xl group-hover:bg-blue-600 group-hover:text-white transition-colors">
              <i data-lucide="building-2" class="w-5 h-5"></i>
            </span>
            <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-600 rounded-full">
              ${b.pop}
            </span>
          </div>
          <h3 class="text-xl font-bold text-slate-800 mb-1 group-hover:text-blue-600 transition-colors">
            ${b.name}
          </h3>
          <p class="text-xs font-medium text-blue-600 mb-3">${b.type}</p>
          <p class="text-sm text-slate-600 mb-4 line-clamp-2">${b.detail}</p>
          <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>Head / In-Charge:</span>
            <span class="font-semibold text-slate-700">${b.captain}</span>
          </div>
        </div>
      `).join('');
    }

    if (typeof lucide !== 'undefined') {
      lucide.createIcons();
    }
  }

  if (searchInput) {
    searchInput.addEventListener('input', (e) => {
      renderBarangays(e.target.value.trim());
    });
  }

  // Initial Render
  renderBarangays();

  // 6. Interactive Modal Handling for Services & Announcements
  const modal = document.getElementById('infoModal');
  const modalTitle = document.getElementById('modalTitle');
  const modalCategory = document.getElementById('modalCategory');
  const modalBody = document.getElementById('modalBody');
  const closeModalBtn = document.getElementById('closeModalBtn');

  window.openServiceModal = function(title, category, description, requirements) {
    if (!modal) return;
    modalTitle.textContent = title;
    modalCategory.textContent = category;
    modalBody.innerHTML = `
      <div class="space-y-4 text-slate-600">
        <p class="text-base text-slate-700 leading-relaxed">${description}</p>
        <div class="bg-blue-50/70 p-4 rounded-xl border border-blue-100">
          <h4 class="font-semibold text-blue-900 mb-2 flex items-center gap-2">
            <i data-lucide="file-check" class="w-4 h-4 text-blue-600"></i>
            Requirements & Details
          </h4>
          <ul class="list-disc list-inside text-sm text-blue-800 space-y-1">
            ${requirements ? requirements.map(req => `<li>${req}</li>`).join('') : '<li>Valid Government-Issued ID</li><li>PhilHealth MDR (if applicable)</li><li>Referral letter from physician</li>'}
          </ul>
        </div>
        <div class="flex items-center justify-between pt-4 border-t border-slate-100 text-sm text-slate-500">
          <span class="flex items-center gap-1.5"><i data-lucide="building-2" class="w-4 h-4 text-slate-400"></i> Tupi Municipal Hospital</span>
          <span class="flex items-center gap-1.5"><i data-lucide="clock" class="w-4 h-4 text-slate-400"></i> OPD: Mon-Fri 8AM-5PM | ER: 24/7</span>
        </div>
      </div>
    `;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    if (typeof lucide !== 'undefined') lucide.createIcons();
  };

  if (closeModalBtn) {
    closeModalBtn.addEventListener('click', () => {
      if (!modal) return;
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    });
  }

  if (modal) {
    modal.addEventListener('click', (e) => {
      if (e.target === modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
      }
    });
  }

  // 7. Contact Form Handling with Toast Notification
  const contactForm = document.getElementById('contactForm');
  const toast = document.getElementById('toast');
  const toastMessage = document.getElementById('toastMessage');

  function showToast(msg) {
    if (!toast) return;
    if (toastMessage) toastMessage.textContent = msg;
    toast.classList.remove('translate-y-20', 'opacity-0');
    toast.classList.add('translate-y-0', 'opacity-100');
    setTimeout(() => {
      toast.classList.remove('translate-y-0', 'opacity-100');
      toast.classList.add('translate-y-20', 'opacity-0');
    }, 4500);
  }

  if (contactForm) {
    contactForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const submitBtn = contactForm.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      
      submitBtn.disabled = true;
      submitBtn.innerHTML = `
        <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
        <span>Submitting...</span>
      `;
      if (typeof lucide !== 'undefined') lucide.createIcons();

      setTimeout(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        if (typeof lucide !== 'undefined') lucide.createIcons();
        contactForm.reset();
        showToast("Success! Your message has been submitted to Tupi Municipal Hospital.");
      }, 1000);
    });
  }

  // 8. Dynamic Scroll Indicator for Active Nav
  const sections = document.querySelectorAll('section[id]');
  const navLinks = document.querySelectorAll('.nav-link');

  function highlightNavOnScroll() {
    let scrollPosition = window.scrollY + 120;

    sections.forEach(section => {
      const sectionTop = section.offsetTop;
      const sectionHeight = section.offsetHeight;
      const sectionId = section.getAttribute('id');

      if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
        navLinks.forEach(link => {
          link.classList.remove('active', 'text-blue-600');
          if (link.getAttribute('href') === `#${sectionId}`) {
            link.classList.add('active', 'text-blue-600');
          }
        });
      }
    });
  }
});
