// assets/js/registration.js
// Client Controller for PHP/MySQL-connected Multi-Step Intake

const regState = {
  currentStep: 1,
  totalSteps: 8,

  personal: {
    firstName: "",
    middleName: "",
    lastName: "",
    dob: "",
    age: 0,
    gender: "Male",
    civilStatus: "Single",
    phone: "",
    email: "",
    address: ""
  },

  emergency: {
    name: "",
    relationship: "Parent",
    phone: ""
  },

  medical: {
    bloodType: "Unknown",
    allergies: "",
    conditions: "",
    medications: "",
    hospitalization: ""
  },

  complaint: "",
  severity: 3,
  duration: "1–3 days ago",
  aggravating: "",
  relieving: "",
  symptoms: [],

  bodyLocation: "abdomen",
  subRegion: "Epigastric & Umbilical Quadrants",
  bodySystemId: 1,
  bodySystemData: null,
  systemRelevanceScore: 96,
  possibleConditions: [],

  selectedDoctor: null,
  selectedSlot: "02:30 PM",
  consultationType: "In-Person Consultation",
  appointmentDate: new Date().toISOString().split('T')[0],

  // Server response on completion
  createdRecord: null
};

let regBodyVisualizer = null;

document.addEventListener("DOMContentLoaded", () => {
  renderRegStep(regState.currentStep);
  updateRegStepper();
  initLucideIcons();
});

function initLucideIcons() {
  if (window.lucide) {
    window.lucide.createIcons();
  }
}

function getSystemLucideIcon(sys) {
  if (!sys) return "activity";
  const name = ((sys.SystemCode || sys.SystemName || sys.name || "") + "").toLowerCase();
  if (name.includes("nerv") || name.includes("brain")) return "brain";
  if (name.includes("cardio") || name.includes("heart")) return "heart-pulse";
  if (name.includes("digest") || name.includes("stomach") || name.includes("gastro")) return "activity";
  if (name.includes("resp") || name.includes("lung") || name.includes("breath")) return "wind";
  if (name.includes("muscul") || name.includes("bone") || name.includes("joint")) return "bone";
  if (name.includes("integ") || name.includes("skin")) return "shield";
  if (name.includes("urin") || name.includes("kidney")) return "droplets";
  if (name.includes("endo") || name.includes("hormon")) return "sparkles";
  return sys.Icon || sys.lucideIcon || "activity";
}

function calculateAgeFromDob(dobString) {
  if (!dobString) return 0;
  const today = new Date();
  const birthDate = new Date(dobString);
  let age = today.getFullYear() - birthDate.getFullYear();
  const monthDiff = today.getMonth() - birthDate.getMonth();
  if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
    age--;
  }
  return age >= 0 ? age : 0;
}

function goToRegStep(step) {
  if (step < 1 || step > regState.totalSteps) return;

  if (step > regState.currentStep) {
    if (!validateRegStep(regState.currentStep)) return;
  }

  regState.currentStep = step;
  renderRegStep(regState.currentStep);
  updateRegStepper();

  const container = document.getElementById("registration-card-container");
  if (container) {
    container.scrollIntoView({ behavior: "smooth", block: "start" });
  }

  initLucideIcons();
}

function updateRegStepper() {
  const steps = [
    { num: 1, title: "Personal Info" },
    { num: 2, title: "Verification" },
    { num: 3, title: "Complaint" },
    { num: 4, title: "Classification" },
    { num: 5, title: "Body Map" },
    { num: 6, title: "Summary Review" },
    { num: 7, title: "Doctor Match" },
    { num: 8, title: "Complete" }
  ];

  const desktopStepper = document.getElementById("registration-desktop-stepper");
  if (desktopStepper) {
    desktopStepper.innerHTML = steps.map(s => {
      const isDone = s.num < regState.currentStep;
      const isCur = s.num === regState.currentStep;

      let iconHtml = `<span>${s.num}</span>`;
      if (isDone) {
        iconHtml = `<svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>`;
      }

      let circleClass = "bg-slate-100 text-slate-400 border border-slate-200";
      let textClass = "text-slate-400 font-medium";

      if (isCur) {
        circleClass = "bg-blue-600 text-white border-2 border-blue-600 shadow-md shadow-blue-500/30 ring-4 ring-blue-100";
        textClass = "text-blue-700 font-bold";
      } else if (isDone) {
        circleClass = "bg-blue-600 text-white border border-blue-600 cursor-pointer hover:bg-blue-700";
        textClass = "text-slate-700 font-semibold cursor-pointer hover:text-blue-600";
      }

      return `
        <div class="flex items-center gap-2 group" ${isDone ? `onclick="goToRegStep(${s.num})"` : ''}>
          <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs transition-all duration-200 ${circleClass}">
            ${iconHtml}
          </div>
          <div class="hidden xl:block">
            <span class="text-xs transition-colors duration-200 ${textClass}">${s.title}</span>
          </div>
          ${s.num < steps.length ? `
            <div class="w-4 lg:w-8 h-0.5 mx-0.5 rounded ${s.num < regState.currentStep ? 'bg-blue-600' : 'bg-slate-200'} transition-all duration-300"></div>
          ` : ''}
        </div>
      `;
    }).join("");
  }

  const mobileProgress = document.getElementById("reg-mobile-progress-fill");
  const mobileText = document.getElementById("reg-mobile-step-text");
  if (mobileProgress && mobileText) {
    const pct = ((regState.currentStep - 1) / (regState.totalSteps - 1)) * 100;
    mobileProgress.style.width = `${pct}%`;
    mobileText.textContent = `Step ${regState.currentStep} of ${regState.totalSteps}: ${steps[regState.currentStep - 1].title}`;
  }
}

function validateRegStep(step) {
  hideRegErrors();

  if (step === 1) {
    const p = regState.personal;
    const emg = regState.emergency;
    let valid = true;

    if (!p.firstName.trim()) { showRegError("err-first-name", "First name is required."); valid = false; }
    if (!p.lastName.trim()) { showRegError("err-last-name", "Last name is required."); valid = false; }
    if (!p.dob) { showRegError("err-dob", "Date of birth is required."); valid = false; }
    if (!p.phone.trim() || p.phone.trim().length < 7) { showRegError("err-phone", "Valid contact phone is required."); valid = false; }
    if (!p.email.trim() || !/^\S+@\S+\.\S+$/.test(p.email.trim())) { showRegError("err-email", "Valid email is required."); valid = false; }
    if (!p.address.trim()) { showRegError("err-address", "Street address is required."); valid = false; }

    if (!emg.name.trim()) { showRegError("err-emg-name", "Emergency contact name is required."); valid = false; }
    if (!emg.phone.trim()) { showRegError("err-emg-phone", "Emergency contact phone is required."); valid = false; }

    return valid;
  }

  if (step === 3) {
    if (!regState.complaint.trim() || regState.complaint.trim().length < 8) {
      showRegError("err-complaint", "Please describe your complaint with more detail (at least 8 characters).");
      return false;
    }
    return true;
  }

  return true;
}

function showRegError(elementId, msg) {
  const el = document.getElementById(elementId);
  if (el) {
    el.textContent = msg;
    el.classList.remove("hidden");
  }
}

function hideRegErrors() {
  document.querySelectorAll(".reg-form-err").forEach(el => {
    el.textContent = "";
    el.classList.add("hidden");
  });
}

// ----------------------------------------------------
// Render Engine Router
// ----------------------------------------------------
function renderRegStep(step) {
  const mount = document.getElementById("reg-step-content-area");
  if (!mount) return;

  switch (step) {
    case 1: mount.innerHTML = renderRegStep1(); attachRegStep1Events(); break;
    case 2: mount.innerHTML = renderRegStep2(); attachRegStep2Events(); break;
    case 3: mount.innerHTML = renderRegStep3(); attachRegStep3Events(); break;
    case 4: mount.innerHTML = renderRegStep4(); attachRegStep4Events(); break;
    case 5: mount.innerHTML = renderRegStep5(); attachRegStep5Events(); break;
    case 6: mount.innerHTML = renderRegStep6(); attachRegStep6Events(); break;
    case 7: mount.innerHTML = renderRegStep7(); attachRegStep7Events(); break;
    case 8: mount.innerHTML = renderRegStep8(); attachRegStep8Events(); break;
    default: mount.innerHTML = renderRegStep1(); attachRegStep1Events();
  }

  initLucideIcons();
}

// ----------------------------------------------------
// STEP 1: PATIENT REGISTRATION FORM
// ----------------------------------------------------
function renderRegStep1() {
  const p = regState.personal;
  const emg = regState.emergency;
  const med = regState.medical;
  p.age = calculateAgeFromDob(p.dob);

  return `
    <div class="step-container space-y-8">
      
      <div class="border-b border-slate-200/80 pb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
          <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/60 mb-2">
            <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
            Step 1: Patient Information & Registration
          </div>
          <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Patient Registration</h2>
          <p class="text-xs sm:text-sm text-slate-500 mt-1">Let's start by getting to know you. Please provide your basic personal, emergency, and medical history.</p>
        </div>

        <!-- 1-Click Preset Demo Loader -->
        <div class="shrink-0 flex items-center gap-2">
          <label class="text-xs font-bold text-slate-500">Preset Case:</label>
          <select id="reg-preset-select" class="text-xs bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5 font-medium text-slate-700 focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="">-- Choose Sample --</option>
            <option value="gastritis">🩺 Gastritis / Stomach Pain (GI)</option>
            <option value="migraine">🧠 Severe Migraine (Neuro)</option>
            <option value="chest_palp">🫀 Chest Palpitations (Cardio)</option>
            <option value="knee_sprain">🦴 Knee Joint Pain (Ortho)</option>
          </select>
        </div>
      </div>

      <form id="reg-form-1" class="space-y-8" onsubmit="return false;">
        
        <!-- Personal Information Card -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-2xs space-y-5">
          <h3 class="text-sm font-bold text-slate-900 pb-3 border-b border-slate-100 flex items-center gap-2">
            <i data-lucide="user" class="w-4 h-4 text-blue-600"></i>
            <span>Personal Information</span>
          </h3>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">First Name *</label>
              <input type="text" id="reg-first-name" value="${p.firstName}" placeholder="e.g. Kent Carl" 
                     class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-blue-500 focus:bg-white" />
              <p id="err-first-name" class="reg-form-err text-rose-500 text-[11px] mt-1 hidden"></p>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Middle Name</label>
              <input type="text" id="reg-middle-name" value="${p.middleName}" placeholder="e.g. Dela" 
                     class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-blue-500 focus:bg-white" />
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Last Name *</label>
              <input type="text" id="reg-last-name" value="${p.lastName}" placeholder="e.g. Amit" 
                     class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-blue-500 focus:bg-white" />
              <p id="err-last-name" class="reg-form-err text-rose-500 text-[11px] mt-1 hidden"></p>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Date of Birth *</label>
              <input type="date" id="reg-dob" value="${p.dob}" max="${new Date().toISOString().split('T')[0]}" 
                     class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-blue-500 focus:bg-white" />
              <p id="err-dob" class="reg-form-err text-rose-500 text-[11px] mt-1 hidden"></p>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Calculated Age</label>
              <div class="flex items-center h-[38px] px-3.5 bg-blue-50/80 border border-blue-200 rounded-xl">
                <span id="reg-age-badge" class="text-xs font-bold text-blue-800">
                  ${p.age ? `${p.age} years old` : 'Enter DOB above'}
                </span>
              </div>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Civil Status</label>
              <select id="reg-civil" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800">
                <option value="Single" ${p.civilStatus === 'Single' ? 'selected' : ''}>Single</option>
                <option value="Married" ${p.civilStatus === 'Married' ? 'selected' : ''}>Married</option>
                <option value="Divorced" ${p.civilStatus === 'Divorced' ? 'selected' : ''}>Divorced</option>
                <option value="Widowed" ${p.civilStatus === 'Widowed' ? 'selected' : ''}>Widowed</option>
              </select>
            </div>
          </div>

          <!-- Gender Radio Cards -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-2">Gender *</label>
            <div class="grid grid-cols-3 gap-3">
              <label class="reg-gender-card cursor-pointer border ${p.gender === 'Male' ? 'border-blue-600 bg-blue-50 text-blue-900 ring-2 ring-blue-400/20' : 'border-slate-200 bg-slate-50/50'} p-3 rounded-xl flex items-center justify-center gap-2 transition">
                <input type="radio" name="reg-gender" value="Male" class="hidden" ${p.gender === 'Male' ? 'checked' : ''}>
                <i data-lucide="user" class="w-4 h-4 text-blue-600"></i>
                <span class="text-xs font-bold">Male</span>
              </label>
              <label class="reg-gender-card cursor-pointer border ${p.gender === 'Female' ? 'border-blue-600 bg-blue-50 text-blue-900 ring-2 ring-blue-400/20' : 'border-slate-200 bg-slate-50/50'} p-3 rounded-xl flex items-center justify-center gap-2 transition">
                <input type="radio" name="reg-gender" value="Female" class="hidden" ${p.gender === 'Female' ? 'checked' : ''}>
                <i data-lucide="user" class="w-4 h-4 text-pink-500"></i>
                <span class="text-xs font-bold">Female</span>
              </label>
              <label class="reg-gender-card cursor-pointer border ${p.gender === 'Other' ? 'border-blue-600 bg-blue-50 text-blue-900 ring-2 ring-blue-400/20' : 'border-slate-200 bg-slate-50/50'} p-3 rounded-xl flex items-center justify-center gap-2 transition">
                <input type="radio" name="reg-gender" value="Other" class="hidden" ${p.gender === 'Other' ? 'checked' : ''}>
                <i data-lucide="user" class="w-4 h-4 text-slate-500"></i>
                <span class="text-xs font-bold">Other</span>
              </label>
            </div>
          </div>

          <!-- Contact Details -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Contact Phone *</label>
              <input type="tel" id="reg-phone" value="${p.phone}" placeholder="+1 (555) 000-0000" 
                     class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800" />
              <p id="err-phone" class="reg-form-err text-rose-500 text-[11px] mt-1 hidden"></p>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Email Address *</label>
              <input type="email" id="reg-email" value="${p.email}" placeholder="patient@example.com" 
                     class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800" />
              <p id="err-email" class="reg-form-err text-rose-500 text-[11px] mt-1 hidden"></p>
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Complete Address *</label>
            <input type="text" id="reg-address" value="${p.address}" placeholder="House/Unit #, Street, City, State, Postal Code" 
                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800" />
            <p id="err-address" class="reg-form-err text-rose-500 text-[11px] mt-1 hidden"></p>
          </div>
        </div>

        <!-- Emergency Contact Card -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-2xs space-y-4">
          <h3 class="text-sm font-bold text-slate-900 pb-3 border-b border-slate-100 flex items-center gap-2">
            <i data-lucide="heart-handshake" class="w-4 h-4 text-amber-600"></i>
            <span>Emergency Contact Person</span>
          </h3>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Contact Name *</label>
              <input type="text" id="reg-emg-name" value="${emg.name}" placeholder="e.g. Carmen Amit" 
                     class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800" />
              <p id="err-emg-name" class="reg-form-err text-rose-500 text-[11px] mt-1 hidden"></p>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Relationship</label>
              <select id="reg-emg-rel" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800">
                <option value="Parent" ${emg.relationship === 'Parent' ? 'selected' : ''}>Parent</option>
                <option value="Spouse" ${emg.relationship === 'Spouse' ? 'selected' : ''}>Spouse</option>
                <option value="Sibling" ${emg.relationship === 'Sibling' ? 'selected' : ''}>Sibling</option>
                <option value="Child" ${emg.relationship === 'Child' ? 'selected' : ''}>Child</option>
                <option value="Guardian" ${emg.relationship === 'Guardian' ? 'selected' : ''}>Guardian</option>
                <option value="Friend" ${emg.relationship === 'Friend' ? 'selected' : ''}>Friend</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Emergency Phone *</label>
              <input type="tel" id="reg-emg-phone" value="${emg.phone}" placeholder="+1 (555) 000-0000" 
                     class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800" />
              <p id="err-emg-phone" class="reg-form-err text-rose-500 text-[11px] mt-1 hidden"></p>
            </div>
          </div>
        </div>

        <!-- Medical Background Card -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-2xs space-y-4">
          <h3 class="text-sm font-bold text-slate-900 pb-3 border-b border-slate-100 flex items-center gap-2">
            <i data-lucide="clipboard-list" class="w-4 h-4 text-emerald-600"></i>
            <span>Medical Information (Optional)</span>
          </h3>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Blood Type</label>
              <select id="reg-med-blood" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800">
                ${['Unknown', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'].map(bt => `
                  <option value="${bt}" ${med.bloodType === bt ? 'selected' : ''}>${bt}</option>
                `).join('')}
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Known Allergies</label>
              <input type="text" id="reg-med-allergies" value="${med.allergies}" placeholder="e.g. Penicillin, Peanuts, None" 
                     class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800" />
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Existing Conditions</label>
              <input type="text" id="reg-med-conditions" value="${med.conditions}" placeholder="e.g. Hypertension, None" 
                     class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800" />
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Current Medications</label>
              <input type="text" id="reg-med-meds" value="${med.medications}" placeholder="e.g. Daily Vitamins, Omeprazole 20mg" 
                     class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800" />
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Previous Hospitalization</label>
              <input type="text" id="reg-med-hosp" value="${med.hospitalization}" placeholder="e.g. Appendectomy in 2020, None" 
                     class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800" />
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between pt-2">
          <a href="${window.SERVER_DATA?.baseUrl || '/'}views/dashboard/index.php" class="px-5 py-2.5 text-xs font-bold text-slate-500 hover:text-slate-800 bg-slate-100 rounded-xl transition">
            Cancel & Return
          </a>

          <button type="button" id="btn-reg1-continue" class="px-7 py-3 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-blue-600/20 transition flex items-center gap-2 group">
            <span>Save & Continue to Verification</span>
            <i data-lucide="arrow-right" class="w-4 h-4 transition-transform group-hover:translate-x-1"></i>
          </button>
        </div>

      </form>
    </div>
  `;
}

function attachRegStep1Events() {
  const p = regState.personal;
  const emg = regState.emergency;
  const med = regState.medical;

  const dobInput = document.getElementById("reg-dob");
  const ageDisplay = document.getElementById("reg-age-badge");
  if (dobInput) {
    dobInput.addEventListener("input", (e) => {
      p.dob = e.target.value;
      p.age = calculateAgeFromDob(p.dob);
      if (ageDisplay) ageDisplay.textContent = p.age ? `${p.age} years old` : 'Enter DOB above';
    });
  }

  // Gender Radio selection
  const genderCards = document.querySelectorAll(".reg-gender-card");
  genderCards.forEach(c => {
    c.addEventListener("click", () => {
      const radio = c.querySelector("input[type=radio]");
      if (radio) {
        radio.checked = true;
        p.gender = radio.value;
        genderCards.forEach(card => card.className = "reg-gender-card cursor-pointer border border-slate-200 bg-slate-50/50 p-3 rounded-xl flex items-center justify-center gap-2 transition");
        c.className = "reg-gender-card cursor-pointer border border-blue-600 bg-blue-50 text-blue-900 ring-2 ring-blue-400/20 p-3 rounded-xl flex items-center justify-center gap-2 transition";
      }
    });
  });

  const bind = (id, obj, key) => {
    const el = document.getElementById(id);
    if (el) el.addEventListener("input", (e) => obj[key] = e.target.value);
  };

  bind("reg-first-name", p, "firstName");
  bind("reg-middle-name", p, "middleName");
  bind("reg-last-name", p, "lastName");
  bind("reg-civil", p, "civilStatus");
  bind("reg-phone", p, "phone");
  bind("reg-email", p, "email");
  bind("reg-address", p, "address");

  bind("reg-emg-name", emg, "name");
  bind("reg-emg-rel", emg, "relationship");
  bind("reg-emg-phone", emg, "phone");

  bind("reg-med-blood", med, "bloodType");
  bind("reg-med-allergies", med, "allergies");
  bind("reg-med-conditions", med, "conditions");
  bind("reg-med-meds", med, "medications");
  bind("reg-med-hosp", med, "hospitalization");

  // Preset demo loader
  const presetSel = document.getElementById("reg-preset-select");
  if (presetSel && window.DEMO_PRESETS) {
    presetSel.addEventListener("change", (e) => {
      const val = e.target.value;
      if (DEMO_PRESETS[val]) {
        applyRegPreset(DEMO_PRESETS[val]);
      }
    });
  }

  const btnCont = document.getElementById("btn-reg1-continue");
  if (btnCont) {
    btnCont.addEventListener("click", () => {
      if (validateRegStep(1)) {
        goToRegStep(2);
      }
    });
  }
}

function applyRegPreset(preset) {
  Object.assign(regState.personal, preset.personal);
  Object.assign(regState.emergency, preset.emergency);
  Object.assign(regState.medical, preset.medical);
  regState.complaint = preset.complaint;
  regState.bodyLocation = preset.bodyLocation;
  regState.severity = preset.severity;
  regState.duration = preset.duration;
  regState.aggravating = preset.aggravating;
  regState.relieving = preset.relieving;
  regState.symptoms = [...preset.symptoms];

  renderRegStep(regState.currentStep);
  updateRegStepper();
  initLucideIcons();
}

// ----------------------------------------------------
// STEP 2: VERIFICATION
// ----------------------------------------------------
function renderRegStep2() {
  const p = regState.personal;
  const emg = regState.emergency;
  const med = regState.medical;
  const fullName = `${p.firstName} ${p.middleName ? p.middleName + ' ' : ''}${p.lastName}`;

  return `
    <div class="step-container space-y-8">
      
      <div class="border-b border-slate-200/80 pb-5">
        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/60 mb-2">
          <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
          Step 2: Patient Verification
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Review Your Information</h2>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Please make sure the entered registration data is accurate before entering symptom complaints.</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-2xs space-y-3">
          <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider pb-2 border-b border-slate-100 flex items-center gap-1.5">
            <i data-lucide="user" class="w-3.5 h-3.5 text-blue-600"></i>
            Personal Identity
          </h3>
          <div class="space-y-2 text-xs">
            <div class="text-base font-extrabold text-slate-900">${fullName}</div>
            <div class="text-slate-600">${p.gender} • ${p.age} years old • DOB: ${p.dob}</div>
            <div class="text-slate-600">Civil Status: ${p.civilStatus}</div>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-2xs space-y-3">
          <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider pb-2 border-b border-slate-100 flex items-center gap-1.5">
            <i data-lucide="phone" class="w-3.5 h-3.5 text-indigo-600"></i>
            Contact & Residence
          </h3>
          <div class="space-y-2 text-xs">
            <div><span class="text-slate-400 font-semibold">Phone:</span> <strong class="text-slate-800 ml-1">${p.phone}</strong></div>
            <div><span class="text-slate-400 font-semibold">Email:</span> <strong class="text-slate-800 ml-1">${p.email}</strong></div>
            <div><span class="text-slate-400 font-semibold">Address:</span> <span class="text-slate-700 ml-1">${p.address}</span></div>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-2xs space-y-3">
          <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider pb-2 border-b border-slate-100 flex items-center gap-1.5">
            <i data-lucide="heart-handshake" class="w-3.5 h-3.5 text-amber-600"></i>
            Emergency Contact
          </h3>
          <div class="space-y-2 text-xs">
            <div><span class="text-slate-400 font-semibold">Contact Name:</span> <strong class="text-slate-800 ml-1">${emg.name}</strong></div>
            <div><span class="text-slate-400 font-semibold">Relationship:</span> <span class="text-slate-700 ml-1">${emg.relationship}</span></div>
            <div><span class="text-slate-400 font-semibold">Phone:</span> <strong class="text-slate-800 ml-1">${emg.phone}</strong></div>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-2xs space-y-3">
          <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider pb-2 border-b border-slate-100 flex items-center justify-between">
            <span class="flex items-center gap-1.5">
              <i data-lucide="clipboard-list" class="w-3.5 h-3.5 text-emerald-600"></i>
              Medical Background
            </span>
            <span class="text-blue-600 font-bold">Blood: ${med.bloodType}</span>
          </h3>
          <div class="space-y-2 text-xs">
            <div><span class="text-slate-400 font-semibold">Allergies:</span> <span class="text-slate-800 ml-1">${med.allergies || 'None'}</span></div>
            <div><span class="text-slate-400 font-semibold">Conditions:</span> <span class="text-slate-800 ml-1">${med.conditions || 'None'}</span></div>
            <div><span class="text-slate-400 font-semibold">Medications:</span> <span class="text-slate-800 ml-1">${med.medications || 'None'}</span></div>
          </div>
        </div>

      </div>

      <div class="flex items-center justify-between pt-2">
        <button type="button" id="btn-reg2-edit" class="px-5 py-2.5 text-xs font-bold text-slate-700 hover:text-blue-600 bg-white border border-slate-200 rounded-xl transition flex items-center gap-1.5">
          <i data-lucide="edit-3" class="w-4 h-4"></i>
          <span>Edit Information</span>
        </button>

        <button type="button" id="btn-reg2-confirm" class="px-7 py-3 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-blue-600/20 transition flex items-center gap-2 group">
          <i data-lucide="check-circle-2" class="w-4 h-4"></i>
          <span>Confirm & Continue</span>
          <i data-lucide="arrow-right" class="w-4 h-4 transition-transform group-hover:translate-x-1"></i>
        </button>
      </div>

    </div>
  `;
}

function attachRegStep2Events() {
  const btnEdit = document.getElementById("btn-reg2-edit");
  if (btnEdit) btnEdit.addEventListener("click", () => goToRegStep(1));

  const btnConfirm = document.getElementById("btn-reg2-confirm");
  if (btnConfirm) {
    btnConfirm.addEventListener("click", () => {
      showRegVerifiedAnimation(() => {
        goToRegStep(3);
      });
    });
  }
}

function showRegVerifiedAnimation(onComplete) {
  const modal = document.createElement("div");
  modal.className = "fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs transition-opacity duration-300";
  modal.innerHTML = `
    <div class="bg-white rounded-3xl p-8 max-w-sm w-full text-center shadow-2xl border border-blue-100 animate-in zoom-in-95">
      <div class="w-16 h-16 rounded-full bg-blue-50 text-blue-600 mx-auto flex items-center justify-center mb-4 ring-8 ring-blue-50/50">
        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="3" stroke-linecap="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
      </div>
      <h3 class="text-lg font-extrabold text-slate-900 mb-1">Information Verified</h3>
      <p class="text-xs text-slate-500 mb-4">Patient profile verified. Moving to clinical symptom assessment...</p>
      <div class="w-full bg-blue-100 rounded-full h-1.5 overflow-hidden">
        <div class="bg-blue-600 h-full w-full animate-pulse"></div>
      </div>
    </div>
  `;
  document.body.appendChild(modal);

  setTimeout(() => {
    modal.classList.add("opacity-0");
    setTimeout(() => {
      modal.remove();
      onComplete();
    }, 250);
  }, 1000);
}

// ----------------------------------------------------
// STEP 3: COMPLAINT FORM
// ----------------------------------------------------
function renderRegStep3() {
  const dbSymptoms = window.SERVER_DATA?.symptoms || [];
  const dbLocations = window.SERVER_DATA?.bodyLocations || [];

  return `
    <div class="step-container space-y-8">
      
      <div class="border-b border-slate-200/80 pb-5">
        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/60 mb-2">
          <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
          Step 3: Chief Complaint & Symptoms
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">What brings you in today?</h2>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Describe what the patient is currently experiencing. This information directly trains our server-side classifier.</p>
      </div>

      <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-2xs space-y-6">
        
        <div>
          <label class="block text-xs font-bold text-slate-800 uppercase tracking-wide mb-1.5">
            Describe condition or complaint *
          </label>
          <textarea id="reg-complaint-text" rows="4" 
                    placeholder="Example: My stomach hurts after eating and I sometimes feel nauseous." 
                    class="w-full p-4 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50 leading-relaxed">${regState.complaint}</textarea>
          <p id="err-complaint" class="reg-form-err text-rose-500 text-[11px] mt-1 hidden"></p>
        </div>

        <!-- Where do you feel the problem? -->
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wide">
            Where is the issue primarily located?
          </label>
          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2.5">
            ${dbLocations.map(loc => `
              <button type="button" 
                      data-loc-code="${loc.LocationCode}"
                      class="reg-loc-btn p-3 rounded-xl border text-left transition-all ${
                        regState.bodyLocation === loc.LocationCode 
                          ? 'border-blue-600 bg-blue-50 text-blue-900 font-bold ring-2 ring-blue-400/20' 
                          : 'border-slate-200 bg-white hover:bg-slate-50 text-slate-700'
                      }">
                <div class="text-xs font-bold truncate">${loc.LocationName}</div>
                <div class="text-[10px] text-slate-400 truncate">${loc.SubRegion}</div>
              </button>
            `).join('')}
          </div>
        </div>

        <!-- Severity Scale 1-5 -->
        <div>
          <div class="flex items-center justify-between mb-2">
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide">Severity Discomfort Scale (1–5)</label>
            <span class="text-xs font-bold text-blue-700">Level ${regState.severity}</span>
          </div>
          <div class="grid grid-cols-5 gap-2">
            ${[1, 2, 3, 4, 5].map(lvl => `
              <button type="button" data-sev="${lvl}" 
                      class="reg-sev-btn py-3 rounded-xl border text-center transition font-extrabold text-xs ${
                        regState.severity === lvl 
                          ? 'border-blue-600 bg-blue-600 text-white shadow-md ring-2 ring-blue-300' 
                          : 'border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700'
                      }">
                ${lvl}
              </button>
            `).join('')}
          </div>
        </div>

        <!-- Duration -->
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wide">When did it start?</label>
          <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
            ${['Today', '1–3 days ago', 'Less than a week', '1–4 weeks', 'More than a month', 'Not sure'].map(dur => `
              <button type="button" data-dur="${dur}" 
                      class="reg-dur-btn px-3 py-2 rounded-xl border text-xs text-left transition ${
                        regState.duration === dur 
                          ? 'border-blue-600 bg-blue-50 text-blue-900 font-bold' 
                          : 'border-slate-200 bg-white text-slate-700'
                      }">
                ${dur}
              </button>
            `).join('')}
          </div>
        </div>

        <!-- Additional Symptoms from MySQL -->
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wide">
            Select Reported Symptoms (Database Synced)
          </label>
          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
            ${dbSymptoms.map(sym => {
              const isChecked = regState.symptoms.includes(sym.SymptomName);
              return `
                <button type="button" data-sym="${sym.SymptomName}" 
                        class="reg-sym-btn px-3 py-2 rounded-xl border text-xs text-left transition flex items-center justify-between ${
                          isChecked 
                            ? 'border-blue-600 bg-blue-600 text-white font-bold' 
                            : 'border-slate-200 bg-white hover:bg-slate-50 text-slate-700'
                        }">
                  <span class="truncate">${sym.SymptomName}</span>
                  ${isChecked ? '<i data-lucide="check" class="w-3.5 h-3.5 shrink-0"></i>' : ''}
                </button>
              `;
            }).join('')}
          </div>
        </div>

      </div>

      <div class="flex items-center justify-between pt-2">
        <button type="button" id="btn-reg3-back" class="px-5 py-2.5 text-xs font-bold text-slate-700 hover:text-blue-600 bg-white border border-slate-200 rounded-xl transition flex items-center gap-1.5">
          <i data-lucide="arrow-left" class="w-4 h-4"></i>
          <span>Back to Verification</span>
        </button>

        <button type="button" id="btn-reg3-continue" class="px-7 py-3 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-blue-600/20 transition flex items-center gap-2 group">
          <i data-lucide="sparkles" class="w-4 h-4"></i>
          <span>Analyze Symptoms (PHP OOP Engine)</span>
          <i data-lucide="arrow-right" class="w-4 h-4 transition-transform group-hover:translate-x-1"></i>
        </button>
      </div>

    </div>
  `;
}

function attachRegStep3Events() {
  const txt = document.getElementById("reg-complaint-text");
  if (txt) txt.addEventListener("input", (e) => regState.complaint = e.target.value);

  // Location selector
  document.querySelectorAll(".reg-loc-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      regState.bodyLocation = btn.getAttribute("data-loc-code");
      renderRegStep(3);
    });
  });

  // Severity
  document.querySelectorAll(".reg-sev-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      regState.severity = parseInt(btn.getAttribute("data-sev"), 10);
      renderRegStep(3);
    });
  });

  // Duration
  document.querySelectorAll(".reg-dur-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      regState.duration = btn.getAttribute("data-dur");
      renderRegStep(3);
    });
  });

  // Symptoms
  document.querySelectorAll(".reg-sym-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      const sym = btn.getAttribute("data-sym");
      if (regState.symptoms.includes(sym)) {
        regState.symptoms = regState.symptoms.filter(s => s !== sym);
      } else {
        regState.symptoms.push(sym);
      }
      renderRegStep(3);
    });
  });

  const btnBack = document.getElementById("btn-reg3-back");
  if (btnBack) btnBack.addEventListener("click", () => goToRegStep(2));

  const btnContinue = document.getElementById("btn-reg3-continue");
  if (btnContinue) {
    btnContinue.addEventListener("click", async () => {
      if (!validateRegStep(3)) return;

      // Call PHP SymptomClassifier API
      await runServerSideClassification();
      goToRegStep(4);
    });
  }
}

async function runServerSideClassification() {
  try {
    const res = await fetch(window.SERVER_DATA?.apiEndpoints?.classify || '/api/registration/classify.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        complaint: regState.complaint,
        symptoms: regState.symptoms,
        bodyLocation: regState.bodyLocation
      })
    });

    const data = await res.json();
    if (data.success && data.body_system) {
      regState.bodySystemData = data.body_system;
      regState.bodySystemId = data.body_system.BodySystemID;
      regState.systemRelevanceScore = data.relevance_level || 95;
      regState.possibleConditions = data.conditions || [];
    }
  } catch (e) {
    console.error("Classification API error:", e);
  }
}

// ----------------------------------------------------
// STEP 4: CLASSIFICATION
// ----------------------------------------------------
function renderRegStep4() {
  const sys = regState.bodySystemData || {
    SystemName: 'Digestive System',
    Emoji: '🩺',
    Description: 'Involves esophagus, stomach, intestines, liver, and pancreas.'
  };

  const allSystems = window.SERVER_DATA?.bodySystems || [];

  return `
    <div class="step-container space-y-8">
      
      <div class="border-b border-slate-200/80 pb-5">
        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/60 mb-2">
          <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
          Step 4: Symptom Classification (PHP Model)
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Understanding Your Complaint</h2>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Pre-consultation classification generated by PHP OOP SymptomClassifier.</p>
      </div>

      <!-- Primary Highlight Card -->
      <div class="bg-white rounded-3xl p-6 sm:p-8 border-2 border-blue-500/80 shadow-md space-y-6">
        <div class="flex items-center justify-between gap-4">
          <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 shadow-inner">
              <i data-lucide="${getSystemLucideIcon(sys)}" class="w-8 h-8"></i>
            </div>
            <div>
              <div class="flex items-center gap-2">
                <h3 class="text-2xl font-extrabold text-slate-900">${sys.SystemName}</h3>
                <span class="text-xs font-extrabold text-blue-700 bg-blue-100 px-2.5 py-0.5 rounded-full">
                  ${regState.systemRelevanceScore}% Match
                </span>
              </div>
              <p class="text-xs text-slate-500 mt-1">${sys.Description}</p>
            </div>
          </div>
        </div>

        <!-- Conditions Grid -->
        <div class="pt-4 border-t border-slate-100">
          <div class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2.5">
            Possible Related Conditions (For Physician Discussion Only)
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
            ${regState.possibleConditions.map(c => `
              <div class="p-3 bg-blue-50/50 rounded-xl border border-blue-100 text-xs">
                <div class="font-bold text-slate-800">${c.ConditionName}</div>
                <div class="text-[11px] text-slate-500 mt-0.5">${c.Description}</div>
              </div>
            `).join('')}
          </div>
        </div>

        <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-900">
          <strong>Medical Notice:</strong> This suggestion is for consultation preparation only. A doctor must evaluate and diagnose your condition.
        </div>
      </div>

      <!-- 9 Systems Grid -->
      <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-2xs space-y-3">
        <h4 class="text-xs font-bold text-slate-700 uppercase">Switch or Explore System:</h4>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
          ${allSystems.map(s => `
            <button type="button" data-sys-id="${s.BodySystemID}" 
                    class="reg-sys-card p-3 rounded-xl border text-left transition ${
                      regState.bodySystemId == s.BodySystemID 
                        ? 'border-blue-600 bg-blue-50 text-blue-900 font-bold ring-2 ring-blue-400/20' 
                        : 'border-slate-200 bg-white hover:bg-slate-50 text-slate-700'
                    }">
              <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-1.5">
                <i data-lucide="${getSystemLucideIcon(s)}" class="w-4 h-4"></i>
              </div>
              <div class="text-xs font-bold truncate">${s.SystemName}</div>
            </button>
          `).join('')}
        </div>
      </div>

      <div class="flex items-center justify-between pt-2">
        <button type="button" id="btn-reg4-back" class="px-5 py-2.5 text-xs font-bold text-slate-700 hover:text-blue-600 bg-white border border-slate-200 rounded-xl transition flex items-center gap-1.5">
          <i data-lucide="arrow-left" class="w-4 h-4"></i>
          <span>Back to Complaint</span>
        </button>

        <button type="button" id="btn-reg4-continue" class="px-7 py-3 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-blue-600/20 transition flex items-center gap-2 group">
          <span>Continue to Body Location</span>
          <i data-lucide="arrow-right" class="w-4 h-4 transition-transform group-hover:translate-x-1"></i>
        </button>
      </div>

    </div>
  `;
}

function attachRegStep4Events() {
  document.querySelectorAll(".reg-sys-card").forEach(btn => {
    btn.addEventListener("click", () => {
      const sid = parseInt(btn.getAttribute("data-sys-id"), 10);
      regState.bodySystemId = sid;
      const all = window.SERVER_DATA?.bodySystems || [];
      regState.bodySystemData = all.find(s => s.BodySystemID == sid);
      renderRegStep(4);
    });
  });

  const btnBack = document.getElementById("btn-reg4-back");
  if (btnBack) btnBack.addEventListener("click", () => goToRegStep(3));

  const btnContinue = document.getElementById("btn-reg4-continue");
  if (btnContinue) btnContinue.addEventListener("click", () => goToRegStep(5));
}

// ----------------------------------------------------
// STEP 5: BODY MAP
// ----------------------------------------------------
function renderRegStep5() {
  return `
    <div class="step-container space-y-8">
      
      <div class="border-b border-slate-200/80 pb-5">
        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/60 mb-2">
          <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
          Step 5: Interactive Body Mapping
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Interactive Human Body Location</h2>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Pinpoint anatomical location on the vector model (Front / Back).</p>
      </div>

      <!-- Mount Visualizer -->
      <div id="reg-body-map-mount"></div>

      <div class="flex items-center justify-between pt-2">
        <button type="button" id="btn-reg5-back" class="px-5 py-2.5 text-xs font-bold text-slate-700 hover:text-blue-600 bg-white border border-slate-200 rounded-xl transition flex items-center gap-1.5">
          <i data-lucide="arrow-left" class="w-4 h-4"></i>
          <span>Back to Classification</span>
        </button>

        <button type="button" id="btn-reg5-continue" class="px-7 py-3 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-blue-600/20 transition flex items-center gap-2 group">
          <span>Confirm Location & Review</span>
          <i data-lucide="arrow-right" class="w-4 h-4 transition-transform group-hover:translate-x-1"></i>
        </button>
      </div>

    </div>
  `;
}

function attachRegStep5Events() {
  const mount = document.getElementById("reg-body-map-mount");
  if (mount && typeof BodyVisualizer !== 'undefined') {
    regBodyVisualizer = new BodyVisualizer("reg-body-map-mount", {
      initialRegion: regState.bodyLocation || "abdomen",
      initialView: "front",
      onSelect: (regionId) => {
        regState.bodyLocation = regionId;
      }
    });
  }

  const btnBack = document.getElementById("btn-reg5-back");
  if (btnBack) btnBack.addEventListener("click", () => goToRegStep(4));

  const btnContinue = document.getElementById("btn-reg5-continue");
  if (btnContinue) btnContinue.addEventListener("click", () => goToRegStep(6));
}

// ----------------------------------------------------
// STEP 6: SUMMARY REVIEW
// ----------------------------------------------------
function renderRegStep6() {
  const p = regState.personal;
  const sys = regState.bodySystemData || { SystemName: 'Digestive System', Emoji: '🩺' };

  return `
    <div class="step-container space-y-8">
      
      <div class="border-b border-slate-200/80 pb-5">
        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/60 mb-2">
          <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
          Step 6: Pre-Consultation Summary Review
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Pre-Consultation Summary</h2>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Review complete intake details before specialist selection.</p>
      </div>

      <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-2xs space-y-6">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-4">
            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Patient Demographics</h4>
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-2 text-xs">
              <div class="font-bold text-slate-900 text-sm">${p.firstName} ${p.lastName}</div>
              <div>${p.gender} • ${p.age} yrs • DOB: ${p.dob}</div>
              <div>Phone: ${p.phone} • Email: ${p.email}</div>
              <div>Address: ${p.address}</div>
            </div>
          </div>

          <div class="space-y-4">
            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Clinical Complaint</h4>
            <div class="p-4 bg-blue-50/50 rounded-2xl border border-blue-100 space-y-2 text-xs">
              <p class="font-bold text-slate-800 italic">"${regState.complaint}"</p>
              <div>Severity: Level ${regState.severity} • Duration: ${regState.duration}</div>
              <div class="pt-1 flex items-center gap-1.5 font-bold text-blue-700">
                <i data-lucide="${getSystemLucideIcon(sys)}" class="w-4 h-4 text-blue-600"></i>
                <span>${sys.SystemName}</span>
                <span class="text-xs text-blue-500 font-medium">(${regState.systemRelevanceScore}% Match)</span>
              </div>
            </div>
          </div>
        </div>

        <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-900">
          <strong>Important:</strong> This summary organizes information before consultation and does not constitute a clinical diagnosis.
        </div>

      </div>

      <div class="flex items-center justify-between pt-2">
        <button type="button" id="btn-reg6-back" class="px-5 py-2.5 text-xs font-bold text-slate-700 hover:text-blue-600 bg-white border border-slate-200 rounded-xl transition flex items-center gap-1.5">
          <i data-lucide="arrow-left" class="w-4 h-4"></i>
          <span>Back & Edit</span>
        </button>

        <button type="button" id="btn-reg6-continue" class="px-7 py-3 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-blue-600/20 transition flex items-center gap-2 group">
          <i data-lucide="user-plus" class="w-4 h-4"></i>
          <span>Find Matching Doctors</span>
          <i data-lucide="arrow-right" class="w-4 h-4 transition-transform group-hover:translate-x-1"></i>
        </button>
      </div>

    </div>
  `;
}

function attachRegStep6Events() {
  const btnBack = document.getElementById("btn-reg6-back");
  if (btnBack) btnBack.addEventListener("click", () => goToRegStep(5));

  const btnContinue = document.getElementById("btn-reg6-continue");
  if (btnContinue) btnContinue.addEventListener("click", () => goToRegStep(7));
}

// ----------------------------------------------------
// STEP 7: DOCTORS
// ----------------------------------------------------
function renderRegStep7() {
  const docs = window.SERVER_DATA?.doctors || [];

  return `
    <div class="step-container space-y-8">
      
      <div class="border-b border-slate-200/80 pb-5">
        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/60 mb-2">
          <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
          Step 7: Doctor Recommendation
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Find the Right Doctor</h2>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Specialists matching your classified body system.</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        ${docs.map(d => `
          <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-2xs space-y-4 flex flex-col justify-between">
            <div class="space-y-3">
              <div class="flex items-start gap-4">
                <img src="${d.ProfileImage || 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=150'}" class="w-16 h-16 rounded-2xl object-cover border" />
                <div>
                  <h4 class="text-base font-extrabold text-slate-900">Dr. ${d.FirstName} ${d.LastName}</h4>
                  <div class="text-xs font-bold text-blue-600">${d.Specialty}</div>
                  <div class="text-[11px] text-slate-500 mt-0.5">${d.ExperienceYears} yrs experience • ${d.ClinicRoom}</div>
                </div>
              </div>
              <p class="text-xs text-slate-600 line-clamp-2">${d.Bio || ''}</p>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
              <div>
                <span class="text-[10px] text-slate-400 font-bold uppercase">Fee:</span>
                <span class="text-xs font-bold text-slate-800 ml-1">${d.ConsultationFee}</span>
              </div>

              <button type="button" onclick="selectDoctorAndSubmit(${d.DoctorID})" 
                      class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                <span>Select & Register</span>
                <i data-lucide="check" class="w-3.5 h-3.5"></i>
              </button>
            </div>
          </div>
        `).join('')}
      </div>

      <div class="flex items-center justify-between pt-2">
        <button type="button" id="btn-reg7-back" class="px-5 py-2.5 text-xs font-bold text-slate-700 hover:text-blue-600 bg-white border border-slate-200 rounded-xl transition flex items-center gap-1.5">
          <i data-lucide="arrow-left" class="w-4 h-4"></i>
          <span>Back to Summary</span>
        </button>
      </div>

    </div>
  `;
}

function attachRegStep7Events() {
  const btnBack = document.getElementById("btn-reg7-back");
  if (btnBack) btnBack.addEventListener("click", () => goToRegStep(6));
}

// ----------------------------------------------------
// STEP 8: SUBMIT TO MYSQL & SUCCESS
// ----------------------------------------------------
async function selectDoctorAndSubmit(doctorId) {
  regState.selectedDoctor = (window.SERVER_DATA?.doctors || []).find(d => d.DoctorID == doctorId);
  
  // Submit complete record to PHP backend
  try {
    const payload = {
      personal: regState.personal,
      emergency: regState.emergency,
      medical: regState.medical,
      complaint: regState.complaint,
      severity: regState.severity,
      duration: regState.duration,
      aggravating: regState.aggravating,
      relieving: regState.relieving,
      symptoms: regState.symptoms,
      bodyLocation: regState.bodyLocation,
      bodySystemId: regState.bodySystemId,
      systemRelevanceScore: regState.systemRelevanceScore,
      possibleConditions: regState.possibleConditions.map(c => c.ConditionName),
      doctorId: doctorId,
      appointmentDate: regState.appointmentDate,
      selectedSlot: regState.selectedSlot,
      consultationType: regState.consultationType
    };

    const res = await fetch(window.SERVER_DATA?.apiEndpoints?.submit || '/api/registration/submit.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });

    const data = await res.json();
    if (data.success) {
      regState.createdRecord = data;
      goToRegStep(8);
    } else {
      alert("Registration error: " + (data.message || "Unknown error"));
    }
  } catch (e) {
    console.error("Submission failed:", e);
    alert("Connection error while registering patient.");
  }
}

function renderRegStep8() {
  const rec = regState.createdRecord || {
    patient_code: 'PAT-2026-0001',
    queue_number: '01',
    doctor: { FirstName: 'Maria', LastName: 'Santos', Specialty: 'Gastroenterology' }
  };

  const doc = rec.doctor || { FirstName: 'Maria', LastName: 'Santos', Specialty: 'Specialist' };
  const p = regState.personal;

  return `
    <div class="step-container space-y-8 text-center max-w-2xl mx-auto">
      
      <div class="w-20 h-20 mx-auto rounded-full bg-blue-600 text-white flex items-center justify-center shadow-xl shadow-blue-500/30">
        <i data-lucide="check" class="w-10 h-10 stroke-[3]"></i>
      </div>

      <div class="space-y-1">
        <div class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
          ✓ Persisted in MySQL Database
        </div>
        <h2 class="text-3xl font-black text-slate-900 tracking-tight">Patient Registered Successfully</h2>
        <p class="text-xs sm:text-sm text-slate-500">Patient profile generated and queued for consultation in MySQL.</p>
      </div>

      <!-- Intake Pass Card -->
      <div class="bg-white rounded-3xl p-6 sm:p-8 border border-blue-200 shadow-md text-left space-y-5">
        
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
          <div>
            <div class="text-[10px] font-bold text-slate-400 uppercase">Patient Digital ID</div>
            <div class="text-lg font-mono font-black text-blue-700">${rec.patient_code}</div>
          </div>
          <div class="text-right">
            <div class="text-[10px] font-bold text-slate-400 uppercase">Daily Queue Ticket</div>
            <div class="text-2xl font-mono font-black text-blue-700">#${rec.queue_number}</div>
          </div>
        </div>

        <div class="p-4 bg-blue-50/70 rounded-2xl border border-blue-100 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-sm">
            <i data-lucide="stethoscope" class="w-6 h-6"></i>
          </div>
          <div>
            <div class="text-[10px] font-bold text-blue-600 uppercase">Attending Physician</div>
            <div class="text-base font-bold text-slate-900">Dr. ${doc.FirstName} ${doc.LastName}</div>
            <div class="text-xs text-slate-500">${doc.Specialty}</div>
          </div>
        </div>

        <div class="text-xs text-slate-600">
          Registered: <strong>${p.firstName} ${p.lastName}</strong> (${p.gender}, ${p.age} yrs)
        </div>

      </div>

      <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
        <button type="button" onclick="window.print()" class="px-6 py-3 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold transition flex items-center gap-2">
          <i data-lucide="printer" class="w-4 h-4"></i>
          <span>Print Intake Ticket</span>
        </button>

        ${rec.patient_id ? `
          <a href="${window.SERVER_DATA?.baseUrl || '/'}views/patients/view.php?id=${rec.patient_id}" 
             class="px-6 py-3 bg-blue-50 text-blue-700 border border-blue-200 rounded-xl text-xs font-bold transition flex items-center gap-2">
            <i data-lucide="file-text" class="w-4 h-4"></i>
            <span>View Full Profile</span>
          </a>
        ` : ''}

        <a href="${window.SERVER_DATA?.baseUrl || '/'}views/dashboard/index.php" 
           class="px-7 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-lg shadow-blue-600/20 transition flex items-center gap-2">
          <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
          <span>Return to Dashboard</span>
        </a>
      </div>

    </div>
  `;
}

function attachRegStep8Events() {}
