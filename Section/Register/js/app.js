// Main Application Controller & State Engine for Pre-Consultation Intake

// Global Application State
const state = {
  currentStep: 1,
  totalSteps: 8,
  
  // Patient Details
  personal: {
    firstName: "",
    middleName: "",
    lastName: "",
    dob: "",
    age: "",
    gender: "",
    civilStatus: "Single",
    phone: "",
    email: "",
    address: ""
  },
  
  // Emergency Contact
  emergency: {
    name: "",
    relationship: "Parent",
    phone: ""
  },
  
  // Medical Background
  medical: {
    allergies: "",
    conditions: "",
    medications: "",
    hospitalization: "",
    bloodType: "Unknown"
  },
  
  // Clinical Complaint & Symptoms
  complaint: "",
  bodyLocation: "abdomen",
  subRegion: "Upper Abdominal / Epigastric Region",
  severity: 3,
  duration: "1-3 days ago",
  aggravating: "",
  relieving: "",
  symptoms: [],
  
  // System Analysis
  bodySystemId: "digestive",
  systemRelevanceScore: 96,
  manualSystemOverride: false,
  
  // Doctor Selection
  selectedDoctor: null,
  selectedSlot: "2:30 PM",
  consultationType: "In-Person Consultation",
  intakeTicketId: "AH-" + Math.floor(100000 + Math.random() * 900000),
  timestamp: new Date().toISOString()
};

let bodyVisualizerInstance = null;

// Initialize on DOM Ready
document.addEventListener("DOMContentLoaded", () => {
  loadSavedState();
  bindFormEvents();
  renderStep(state.currentStep);
  initLucide();
});

// Helper to initialize Lucide icons
function initLucide() {
  if (window.lucide) {
    window.lucide.createIcons();
  }
}

// Save State to LocalStorage
function saveState() {
  try {
    localStorage.setItem("aurahealth_intake_state", JSON.stringify(state));
    updateDraftStatus("Draft Saved");
  } catch (e) {
    console.error("Storage error:", e);
  }
}

// Load State from LocalStorage
function loadSavedState() {
  try {
    const saved = localStorage.getItem("aurahealth_intake_state");
    if (saved) {
      const parsed = JSON.parse(saved);
      Object.assign(state, parsed);
    }
  } catch (e) {
    console.error("Failed to parse saved state:", e);
  }
}

function updateDraftStatus(msg) {
  const el = document.getElementById("draft-status-indicator");
  if (el) {
    el.textContent = msg;
    setTimeout(() => {
      if (el) el.textContent = "Auto-Saved";
    }, 2000);
  }
}

// Step Navigation Controller
function goToStep(stepNumber) {
  if (stepNumber < 1 || stepNumber > state.totalSteps) return;
  
  // Validate before proceeding forward
  if (stepNumber > state.currentStep) {
    if (!validateCurrentStep(state.currentStep)) {
      return;
    }
  }

  state.currentStep = stepNumber;
  saveState();
  renderStep(state.currentStep);
  updateProgressBar();
  
  // Smooth scroll to top of form card
  const container = document.getElementById("intake-card-container");
  if (container) {
    container.scrollIntoView({ behavior: "smooth", block: "start" });
  }

  initLucide();
}

// Update Stepper Progress UI
function updateProgressBar() {
  const steps = [
    { num: 1, title: "Personal Info" },
    { num: 2, title: "Verification" },
    { num: 3, title: "Complaint" },
    { num: 4, title: "Analysis" },
    { num: 5, title: "Body Map" },
    { num: 6, title: "Review" },
    { num: 7, title: "Find Doctor" },
    { num: 8, title: "Complete" }
  ];

  // Desktop Stepper
  const stepperContainer = document.getElementById("desktop-stepper");
  if (stepperContainer) {
    stepperContainer.innerHTML = steps.map(step => {
      const isCompleted = step.num < state.currentStep;
      const isCurrent = step.num === state.currentStep;
      const isUpcoming = step.num > state.currentStep;

      let iconHtml = `<span>${step.num}</span>`;
      if (isCompleted) {
        iconHtml = `<svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>`;
      }

      let circleClass = "bg-slate-100 text-slate-400 border border-slate-200";
      let textClass = "text-slate-400 font-medium";

      if (isCurrent) {
        circleClass = "bg-blue-600 text-white border-2 border-blue-600 shadow-md shadow-blue-500/30 ring-4 ring-blue-100 glow-pulse";
        textClass = "text-blue-700 font-bold";
      } else if (isCompleted) {
        circleClass = "bg-blue-600 text-white border border-blue-600 cursor-pointer hover:bg-blue-700";
        textClass = "text-slate-700 font-semibold cursor-pointer hover:text-blue-600";
      }

      return `
        <div class="flex items-center gap-2 group" ${isCompleted ? `onclick="goToStep(${step.num})"` : ''}>
          <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs transition-all duration-200 ${circleClass}">
            ${iconHtml}
          </div>
          <div class="hidden xl:block">
            <span class="text-xs transition-colors duration-200 ${textClass}">${step.title}</span>
          </div>
          ${step.num < steps.length ? `
            <div class="w-4 lg:w-8 h-0.5 mx-0.5 rounded ${step.num < state.currentStep ? 'bg-blue-600' : 'bg-slate-200'} transition-all duration-300"></div>
          ` : ''}
        </div>
      `;
    }).join("");
  }

  // Mobile Progress Bar
  const mobileProgress = document.getElementById("mobile-progress-fill");
  const mobileStepText = document.getElementById("mobile-step-text");
  if (mobileProgress && mobileStepText) {
    const percent = ((state.currentStep - 1) / (state.totalSteps - 1)) * 100;
    mobileProgress.style.width = `${percent}%`;
    mobileStepText.textContent = `Step ${state.currentStep} of ${state.totalSteps}: ${steps[state.currentStep - 1].title}`;
  }
}

// Calculate Age Automatically from Date of Birth
function calculateAge(dobString) {
  if (!dobString) return "";
  const today = new Date();
  const birthDate = new Date(dobString);
  let age = today.getFullYear() - birthDate.getFullYear();
  const monthDiff = today.getMonth() - birthDate.getMonth();
  if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
    age--;
  }
  return age > 0 ? age : 0;
}

// Real-time NLP-like Clinical Classifier
function analyzeComplaintText(text) {
  const lower = (text || "").toLowerCase();
  const scores = {};

  // Evaluate matches for each body system
  Object.keys(BODY_SYSTEMS_DATA).forEach(sysKey => {
    const sys = BODY_SYSTEMS_DATA[sysKey];
    let matchCount = 0;
    sys.keywords.forEach(kw => {
      if (lower.includes(kw)) {
        matchCount += 1;
      }
    });
    scores[sysKey] = matchCount;
  });

  // Check selected body location
  const locData = BODY_REGIONS_MAP[state.bodyLocation];
  if (locData && locData.defaultSystem && scores[locData.defaultSystem] !== undefined) {
    scores[locData.defaultSystem] += 3; // strong weight to location
  }

  // Determine highest scoring system
  let highestSys = "digestive";
  let maxScore = -1;
  Object.keys(scores).forEach(sysKey => {
    if (scores[sysKey] > maxScore) {
      maxScore = scores[sysKey];
      highestSys = sysKey;
    }
  });

  // Calculate simulated confidence percentage
  const confidence = Math.min(98, Math.max(78, 75 + (maxScore * 6)));

  return {
    systemId: highestSys,
    confidence: confidence,
    systemData: BODY_SYSTEMS_DATA[highestSys]
  };
}

// Check for emergency / urgent red-flag keywords
function checkForUrgentSymptoms(text) {
  const urgentKeywords = [
    "severe chest pain", "crushing chest", "cannot breathe", "can't breathe",
    "passed out", "unconscious", "stroke", "facial drooping", "coughing blood",
    "seizure", "worst headache of my life", "sudden numbness in arm"
  ];
  const lower = (text || "").toLowerCase();
  return urgentKeywords.some(kw => lower.includes(kw));
}

// Validation Logic per Step
function validateCurrentStep(step) {
  hideAllErrors();
  
  if (step === 1) {
    // Validate Step 1: Personal Info
    const p = state.personal;
    let isValid = true;

    if (!p.firstName.trim()) {
      showError("err-first-name", "First name is required.");
      isValid = false;
    }
    if (!p.lastName.trim()) {
      showError("err-last-name", "Last name is required.");
      isValid = false;
    }
    if (!p.dob) {
      showError("err-dob", "Date of birth is required.");
      isValid = false;
    } else {
      const birth = new Date(p.dob);
      if (birth > new Date()) {
        showError("err-dob", "Date of birth cannot be in the future.");
        isValid = false;
      }
    }
    if (!p.gender) {
      showError("err-gender", "Please select a gender.");
      isValid = false;
    }
    if (!p.phone.trim() || p.phone.trim().length < 7) {
      showError("err-phone", "Please provide a valid contact number.");
      isValid = false;
    }
    if (!p.email.trim() || !/^\S+@\S+\.\S+$/.test(p.email.trim())) {
      showError("err-email", "Please provide a valid email address.");
      isValid = false;
    }
    if (!p.address.trim()) {
      showError("err-address", "Street address is required.");
      isValid = false;
    }

    // Emergency Contact
    if (!state.emergency.name.trim()) {
      showError("err-emg-name", "Emergency contact name is required.");
      isValid = false;
    }
    if (!state.emergency.phone.trim() || state.emergency.phone.trim().length < 7) {
      showError("err-emg-phone", "Emergency phone number is required.");
      isValid = false;
    }

    return isValid;
  }

  if (step === 3) {
    // Validate Step 3: Complaint
    if (!state.complaint.trim() || state.complaint.trim().length < 8) {
      showError("err-complaint", "Please describe your complaint in more detail (at least 8 characters).");
      return false;
    }
    if (!state.bodyLocation) {
      showError("err-location", "Please select the primary area where you feel the issue.");
      return false;
    }
    return true;
  }

  return true;
}

function showError(elementId, message) {
  const el = document.getElementById(elementId);
  if (el) {
    el.textContent = message;
    el.classList.remove("hidden");
  }
}

function hideAllErrors() {
  document.querySelectorAll(".form-error-msg").forEach(el => {
    el.textContent = "";
    el.classList.add("hidden");
  });
}

// Main Step Renderer Router
function renderStep(step) {
  const mainContent = document.getElementById("step-content-area");
  if (!mainContent) return;

  switch (step) {
    case 1:
      mainContent.innerHTML = renderStep1Registration();
      attachStep1Events();
      break;
    case 2:
      mainContent.innerHTML = renderStep2Verification();
      attachStep2Events();
      break;
    case 3:
      mainContent.innerHTML = renderStep3Complaint();
      attachStep3Events();
      break;
    case 4:
      mainContent.innerHTML = renderStep4Analysis();
      attachStep4Events();
      break;
    case 5:
      mainContent.innerHTML = renderStep5BodyMap();
      attachStep5Events();
      break;
    case 6:
      mainContent.innerHTML = renderStep6Review();
      attachStep6Events();
      break;
    case 7:
      mainContent.innerHTML = renderStep7Doctors();
      attachStep7Events();
      break;
    case 8:
      mainContent.innerHTML = renderStep8Success();
      attachStep8Events();
      break;
    default:
      mainContent.innerHTML = renderStep1Registration();
      attachStep1Events();
  }

  initLucide();
}

// ----------------------------------------------------
// STEP 1: PATIENT REGISTRATION
// ----------------------------------------------------
function renderStep1Registration() {
  const p = state.personal;
  const emg = state.emergency;
  const med = state.medical;
  const age = calculateAge(p.dob);
  p.age = age;

  return `
    <div class="step-container space-y-8">
      
      <!-- Step Header -->
      <div class="border-b border-slate-200/80 pb-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/60 mb-2">
              <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
              Step 1 of 8: Registration
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
              Patient Registration
            </h2>
            <p class="text-sm text-slate-500 mt-1">
              Let's start by getting to know you. Please provide your basic personal and medical background.
            </p>
          </div>

          <!-- 1-Click Demo Loader Dropdown -->
          <div class="shrink-0 flex items-center gap-2">
            <label class="text-xs font-semibold text-slate-500">Preset Demo:</label>
            <select id="select-demo-preset" class="text-xs bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5 font-medium text-slate-700 focus:ring-2 focus:ring-blue-500 outline-none">
              <option value="">-- Choose Sample Case --</option>
              <option value="gastritis">🩺 Stomach Pain & Nausea (GI)</option>
              <option value="migraine">🧠 Severe Migraine (Neuro)</option>
              <option value="chest_palp">🫀 Chest Palpitations (Cardio)</option>
              <option value="knee_sprain">🦴 Knee Joint Pain (Ortho)</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Registration Form -->
      <form id="form-step-1" class="space-y-8" onsubmit="return false;">
        
        <!-- Section A: Personal Information -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-2xs space-y-6">
          <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm">
              <i data-lucide="user" class="w-4 h-4"></i>
            </div>
            <h3 class="text-base font-bold text-slate-900">Personal Information</h3>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">First Name <span class="text-rose-500">*</span></label>
              <input type="text" id="input-first-name" value="${p.firstName}" placeholder="e.g. Kent Carl" 
                     class="medical-input w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none bg-slate-50/50" />
              <p id="err-first-name" class="form-error-msg text-rose-500 text-xs mt-1 hidden"></p>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">Middle Name <span class="text-slate-400 font-normal">(Optional)</span></label>
              <input type="text" id="input-middle-name" value="${p.middleName}" placeholder="e.g. Dela" 
                     class="medical-input w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none bg-slate-50/50" />
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">Last Name <span class="text-rose-500">*</span></label>
              <input type="text" id="input-last-name" value="${p.lastName}" placeholder="e.g. Amit" 
                     class="medical-input w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none bg-slate-50/50" />
              <p id="err-last-name" class="form-error-msg text-rose-500 text-xs mt-1 hidden"></p>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-5 items-start">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">Date of Birth <span class="text-rose-500">*</span></label>
              <input type="date" id="input-dob" value="${p.dob}" max="${new Date().toISOString().split('T')[0]}" 
                     class="medical-input w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none bg-slate-50/50" />
              <p id="err-dob" class="form-error-msg text-rose-500 text-xs mt-1 hidden"></p>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">Calculated Age</label>
              <div class="flex items-center h-[42px] px-3.5 bg-blue-50/70 border border-blue-200/80 rounded-xl">
                <span id="display-age-badge" class="text-sm font-bold text-blue-800">
                  ${age ? `${age} years old` : 'Enter DOB above'}
                </span>
              </div>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">Civil Status</label>
              <select id="input-civil-status" class="medical-input w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none bg-slate-50/50">
                <option value="Single" ${p.civilStatus === 'Single' ? 'selected' : ''}>Single</option>
                <option value="Married" ${p.civilStatus === 'Married' ? 'selected' : ''}>Married</option>
                <option value="Divorced" ${p.civilStatus === 'Divorced' ? 'selected' : ''}>Divorced</option>
                <option value="Widowed" ${p.civilStatus === 'Widowed' ? 'selected' : ''}>Widowed</option>
              </select>
            </div>
          </div>

          <!-- Gender Radio Cards -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-2">Gender <span class="text-rose-500">*</span></label>
            <div class="grid grid-cols-3 gap-3">
              <label class="gender-card cursor-pointer border ${p.gender === 'Male' ? 'border-blue-600 bg-blue-50/70 ring-2 ring-blue-500/20' : 'border-slate-200 bg-slate-50/40 hover:bg-slate-50'} p-3 rounded-xl flex items-center justify-center gap-2 transition-all">
                <input type="radio" name="gender" value="Male" class="hidden" ${p.gender === 'Male' ? 'checked' : ''}>
                <span class="text-base">👨</span>
                <span class="text-xs font-bold text-slate-800">Male</span>
              </label>
              <label class="gender-card cursor-pointer border ${p.gender === 'Female' ? 'border-blue-600 bg-blue-50/70 ring-2 ring-blue-500/20' : 'border-slate-200 bg-slate-50/40 hover:bg-slate-50'} p-3 rounded-xl flex items-center justify-center gap-2 transition-all">
                <input type="radio" name="gender" value="Female" class="hidden" ${p.gender === 'Female' ? 'checked' : ''}>
                <span class="text-base">👩</span>
                <span class="text-xs font-bold text-slate-800">Female</span>
              </label>
              <label class="gender-card cursor-pointer border ${p.gender === 'Other' ? 'border-blue-600 bg-blue-50/70 ring-2 ring-blue-500/20' : 'border-slate-200 bg-slate-50/40 hover:bg-slate-50'} p-3 rounded-xl flex items-center justify-center gap-2 transition-all">
                <input type="radio" name="gender" value="Other" class="hidden" ${p.gender === 'Other' ? 'checked' : ''}>
                <span class="text-base">🧑</span>
                <span class="text-xs font-bold text-slate-800">Other</span>
              </label>
            </div>
            <p id="err-gender" class="form-error-msg text-rose-500 text-xs mt-1 hidden"></p>
          </div>

          <!-- Contact & Address -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">Contact Phone Number <span class="text-rose-500">*</span></label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                  <i data-lucide="phone" class="w-4 h-4"></i>
                </div>
                <input type="tel" id="input-phone" value="${p.phone}" placeholder="+1 (555) 000-0000" 
                       class="medical-input w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none bg-slate-50/50" />
              </div>
              <p id="err-phone" class="form-error-msg text-rose-500 text-xs mt-1 hidden"></p>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">Email Address <span class="text-rose-500">*</span></label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                  <i data-lucide="mail" class="w-4 h-4"></i>
                </div>
                <input type="email" id="input-email" value="${p.email}" placeholder="patient@example.com" 
                       class="medical-input w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none bg-slate-50/50" />
              </div>
              <p id="err-email" class="form-error-msg text-rose-500 text-xs mt-1 hidden"></p>
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Complete Street Address <span class="text-rose-500">*</span></label>
            <div class="relative">
              <div class="absolute top-3 left-3 pointer-events-none text-slate-400">
                <i data-lucide="map-pin" class="w-4 h-4"></i>
              </div>
              <input type="text" id="input-address" value="${p.address}" placeholder="House/Unit #, Street, City, State/Province, Postal Code" 
                     class="medical-input w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none bg-slate-50/50" />
            </div>
            <p id="err-address" class="form-error-msg text-rose-500 text-xs mt-1 hidden"></p>
          </div>
        </div>

        <!-- Section B: Emergency Contact -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-2xs space-y-5">
          <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-sm">
              <i data-lucide="heart-handshake" class="w-4 h-4"></i>
            </div>
            <h3 class="text-base font-bold text-slate-900">Emergency Contact Person</h3>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">Contact Full Name <span class="text-rose-500">*</span></label>
              <input type="text" id="input-emg-name" value="${emg.name}" placeholder="e.g. Carmen Amit" 
                     class="medical-input w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none bg-slate-50/50" />
              <p id="err-emg-name" class="form-error-msg text-rose-500 text-xs mt-1 hidden"></p>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">Relationship to Patient <span class="text-rose-500">*</span></label>
              <select id="input-emg-rel" class="medical-input w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none bg-slate-50/50">
                <option value="Parent" ${emg.relationship === 'Parent' ? 'selected' : ''}>Parent</option>
                <option value="Spouse" ${emg.relationship === 'Spouse' ? 'selected' : ''}>Spouse / Partner</option>
                <option value="Sibling" ${emg.relationship === 'Sibling' ? 'selected' : ''}>Sibling</option>
                <option value="Child" ${emg.relationship === 'Child' ? 'selected' : ''}>Child</option>
                <option value="Guardian" ${emg.relationship === 'Guardian' ? 'selected' : ''}>Guardian</option>
                <option value="Friend" ${emg.relationship === 'Friend' ? 'selected' : ''}>Friend / Colleague</option>
                <option value="Other" ${emg.relationship === 'Other' ? 'selected' : ''}>Other</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">Emergency Phone Number <span class="text-rose-500">*</span></label>
              <input type="tel" id="input-emg-phone" value="${emg.phone}" placeholder="+1 (555) 000-0000" 
                     class="medical-input w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none bg-slate-50/50" />
              <p id="err-emg-phone" class="form-error-msg text-rose-500 text-xs mt-1 hidden"></p>
            </div>
          </div>
        </div>

        <!-- Section C: Optional Medical Background -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-2xs space-y-5">
          <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
              <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">
                <i data-lucide="file-text" class="w-4 h-4"></i>
              </div>
              <h3 class="text-base font-bold text-slate-900">Medical Background <span class="text-slate-400 font-normal text-xs">(Optional)</span></h3>
            </div>
            <span class="text-xs text-slate-400 font-medium">Helps personalize your care</span>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">Known Allergies (Food / Drugs)</label>
              <input type="text" id="input-med-allergies" value="${med.allergies}" placeholder="e.g. Penicillin, Peanuts, Latex, or None" 
                     class="medical-input w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none bg-slate-50/50" />
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">Existing Medical Conditions</label>
              <input type="text" id="input-med-conditions" value="${med.conditions}" placeholder="e.g. Hypertension, Asthma, Diabetes, or None" 
                     class="medical-input w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none bg-slate-50/50" />
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">Current Medications</label>
              <input type="text" id="input-med-medications" value="${med.medications}" placeholder="e.g. Daily Vitamins, Omeprazole 20mg" 
                     class="medical-input w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none bg-slate-50/50" />
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">Previous Hospitalization</label>
              <input type="text" id="input-med-hospital" value="${med.hospitalization}" placeholder="e.g. None / Appendectomy in 2020" 
                     class="medical-input w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none bg-slate-50/50" />
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">Blood Type</label>
              <select id="input-med-blood" class="medical-input w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none bg-slate-50/50">
                <option value="Unknown" ${med.bloodType === 'Unknown' ? 'selected' : ''}>Unknown</option>
                <option value="A+" ${med.bloodType === 'A+' ? 'selected' : ''}>A+</option>
                <option value="A-" ${med.bloodType === 'A-' ? 'selected' : ''}>A-</option>
                <option value="B+" ${med.bloodType === 'B+' ? 'selected' : ''}>B+</option>
                <option value="B-" ${med.bloodType === 'B-' ? 'selected' : ''}>B-</option>
                <option value="AB+" ${med.bloodType === 'AB+' ? 'selected' : ''}>AB+</option>
                <option value="AB-" ${med.bloodType === 'AB-' ? 'selected' : ''}>AB-</option>
                <option value="O+" ${med.bloodType === 'O+' ? 'selected' : ''}>O+</option>
                <option value="O-" ${med.bloodType === 'O-' ? 'selected' : ''}>O-</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-between pt-4">
          <button type="button" id="btn-clear-form" class="px-5 py-2.5 text-xs font-semibold text-slate-500 hover:text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
            Reset Form
          </button>
          <button type="button" id="btn-step1-continue" class="px-7 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-blue-600/20 hover:shadow-blue-600/30 transition-all flex items-center gap-2 group">
            <span>Save & Continue</span>
            <i data-lucide="arrow-right" class="w-4 h-4 transition-transform group-hover:translate-x-1"></i>
          </button>
        </div>

      </form>
    </div>
  `;
}

function attachStep1Events() {
  const p = state.personal;
  const emg = state.emergency;
  const med = state.medical;

  // Auto DOB to Age
  const dobInput = document.getElementById("input-dob");
  const ageDisplay = document.getElementById("display-age-badge");
  if (dobInput) {
    dobInput.addEventListener("input", (e) => {
      p.dob = e.target.value;
      const age = calculateAge(p.dob);
      p.age = age;
      if (ageDisplay) {
        ageDisplay.textContent = age !== "" ? `${age} years old` : "Enter DOB above";
      }
      saveState();
    });
  }

  // Gender Radio Card selection
  const genderCards = document.querySelectorAll(".gender-card");
  genderCards.forEach(card => {
    card.addEventListener("click", () => {
      const radio = card.querySelector("input[type=radio]");
      if (radio) {
        radio.checked = true;
        p.gender = radio.value;
        genderCards.forEach(c => {
          c.className = "gender-card cursor-pointer border border-slate-200 bg-slate-50/40 hover:bg-slate-50 p-3 rounded-xl flex items-center justify-center gap-2 transition-all";
        });
        card.className = "gender-card cursor-pointer border border-blue-600 bg-blue-50/70 ring-2 ring-blue-500/20 p-3 rounded-xl flex items-center justify-center gap-2 transition-all";
        saveState();
      }
    });
  });

  // Sync inputs to state
  const bindInput = (id, obj, key) => {
    const el = document.getElementById(id);
    if (el) {
      el.addEventListener("input", (e) => {
        obj[key] = e.target.value;
        saveState();
      });
    }
  };

  bindInput("input-first-name", p, "firstName");
  bindInput("input-middle-name", p, "middleName");
  bindInput("input-last-name", p, "lastName");
  bindInput("input-civil-status", p, "civilStatus");
  bindInput("input-phone", p, "phone");
  bindInput("input-email", p, "email");
  bindInput("input-address", p, "address");

  bindInput("input-emg-name", emg, "name");
  bindInput("input-emg-rel", emg, "relationship");
  bindInput("input-emg-phone", emg, "phone");

  bindInput("input-med-allergies", med, "allergies");
  bindInput("input-med-conditions", med, "conditions");
  bindInput("input-med-medications", med, "medications");
  bindInput("input-med-hospital", med, "hospitalization");
  bindInput("input-med-blood", med, "bloodType");

  // Preset Selector
  const presetSelect = document.getElementById("select-demo-preset");
  if (presetSelect) {
    presetSelect.addEventListener("change", (e) => {
      const val = e.target.value;
      if (DEMO_PRESETS[val]) {
        applyPreset(DEMO_PRESETS[val]);
      }
    });
  }

  // Reset button
  const btnClear = document.getElementById("btn-clear-form");
  if (btnClear) {
    btnClear.addEventListener("click", () => {
      if (confirm("Reset all patient registration fields?")) {
        localStorage.removeItem("aurahealth_intake_state");
        location.reload();
      }
    });
  }

  // Continue Button
  const btnContinue = document.getElementById("btn-step1-continue");
  if (btnContinue) {
    btnContinue.addEventListener("click", () => {
      if (validateCurrentStep(1)) {
        goToStep(2);
      }
    });
  }
}

// 1-Click Preset Loader
function applyPreset(preset) {
  Object.assign(state.personal, preset.personal);
  Object.assign(state.emergency, preset.emergency);
  Object.assign(state.medical, preset.medical);
  state.complaint = preset.complaint;
  state.bodyLocation = preset.bodyLocation;
  state.severity = preset.severity;
  state.duration = preset.duration;
  state.aggravating = preset.aggravating;
  state.relieving = preset.relieving;
  state.symptoms = [...preset.symptoms];

  // Auto classify
  const analysis = analyzeComplaintText(preset.complaint);
  state.bodySystemId = analysis.systemId;
  state.systemRelevanceScore = analysis.confidence;

  saveState();
  renderStep(state.currentStep);
  initLucide();
}

// ----------------------------------------------------
// STEP 2: VERIFY PATIENT INFORMATION
// ----------------------------------------------------
function renderStep2Verification() {
  const p = state.personal;
  const emg = state.emergency;
  const med = state.medical;
  const fullName = `${p.firstName} ${p.middleName ? p.middleName + ' ' : ''}${p.lastName}`;

  // Format DOB nicely
  let formattedDob = p.dob;
  try {
    if (p.dob) {
      const d = new Date(p.dob);
      formattedDob = d.toLocaleDateString("en-US", { year: "numeric", month: "long", day: "numeric" });
    }
  } catch (e) {}

  return `
    <div class="step-container space-y-8">
      
      <!-- Step Header -->
      <div class="border-b border-slate-200/80 pb-5">
        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/60 mb-2">
          <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
          Step 2 of 8: Verification
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
          Review Your Information
        </h2>
        <p class="text-sm text-slate-500 mt-1">
          Please check that your registered profile is accurate before proceeding to the symptom intake.
        </p>
      </div>

      <!-- Verification Cards Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- Card 1: Personal Info -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-2xs space-y-4">
          <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center">
                <i data-lucide="user-check" class="w-4 h-4"></i>
              </div>
              <h3 class="text-sm font-bold text-slate-900">Personal Identity</h3>
            </div>
            <span class="text-[11px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
              Verified Format
            </span>
          </div>

          <div class="space-y-3">
            <div>
              <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Full Legal Name</div>
              <div class="text-base font-bold text-slate-800 mt-0.5">${fullName || '—'}</div>
            </div>
            
            <div class="grid grid-cols-2 gap-3 pt-1">
              <div>
                <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Demographics</div>
                <div class="text-xs font-semibold text-slate-700 mt-0.5">
                  ${p.gender || '—'} • ${p.age ? `${p.age} yrs old` : '—'}
                </div>
              </div>
              <div>
                <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Civil Status</div>
                <div class="text-xs font-semibold text-slate-700 mt-0.5">${p.civilStatus || 'Single'}</div>
              </div>
            </div>

            <div class="pt-1">
              <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Date of Birth</div>
              <div class="text-xs font-semibold text-slate-700 mt-0.5">${formattedDob || '—'}</div>
            </div>
          </div>
        </div>

        <!-- Card 2: Contact Information -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-2xs space-y-4">
          <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center">
                <i data-lucide="phone-call" class="w-4 h-4"></i>
              </div>
              <h3 class="text-sm font-bold text-slate-900">Contact & Residence</h3>
            </div>
            <span class="text-[11px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-200">
              Primary Channel
            </span>
          </div>

          <div class="space-y-3">
            <div>
              <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Phone Number</div>
              <div class="text-xs font-bold text-slate-800 mt-0.5 flex items-center gap-1.5">
                <i data-lucide="phone" class="w-3.5 h-3.5 text-blue-500"></i>
                ${p.phone || '—'}
              </div>
            </div>

            <div>
              <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Email Address</div>
              <div class="text-xs font-bold text-slate-800 mt-0.5 flex items-center gap-1.5">
                <i data-lucide="mail" class="w-3.5 h-3.5 text-blue-500"></i>
                ${p.email || '—'}
              </div>
            </div>

            <div>
              <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Registered Address</div>
              <div class="text-xs font-medium text-slate-700 mt-0.5 leading-relaxed">
                ${p.address || '—'}
              </div>
            </div>
          </div>
        </div>

        <!-- Card 3: Emergency Contact -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-2xs space-y-4">
          <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center">
                <i data-lucide="shield-alert" class="w-4 h-4"></i>
              </div>
              <h3 class="text-sm font-bold text-slate-900">Emergency Contact</h3>
            </div>
          </div>

          <div class="space-y-3">
            <div>
              <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Designated Person</div>
              <div class="text-sm font-bold text-slate-800 mt-0.5">${emg.name || '—'}</div>
            </div>
            
            <div class="grid grid-cols-2 gap-3 pt-1">
              <div>
                <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Relationship</div>
                <div class="text-xs font-semibold text-slate-700 mt-0.5">${emg.relationship || '—'}</div>
              </div>
              <div>
                <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Emergency Hotline</div>
                <div class="text-xs font-bold text-slate-800 mt-0.5">${emg.phone || '—'}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Card 4: Clinical History -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-2xs space-y-4">
          <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center">
                <i data-lucide="clipboard-list" class="w-4 h-4"></i>
              </div>
              <h3 class="text-sm font-bold text-slate-900">Medical Background</h3>
            </div>
            <span class="text-[11px] font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded-full">
              Blood: ${med.bloodType || 'Unknown'}
            </span>
          </div>

          <div class="space-y-2.5 text-xs">
            <div>
              <span class="font-semibold text-slate-500">Allergies:</span>
              <span class="font-bold text-slate-800 ml-1">${med.allergies || 'None reported'}</span>
            </div>
            <div>
              <span class="font-semibold text-slate-500">Conditions:</span>
              <span class="font-bold text-slate-800 ml-1">${med.conditions || 'None reported'}</span>
            </div>
            <div>
              <span class="font-semibold text-slate-500">Current Medications:</span>
              <span class="font-bold text-slate-800 ml-1">${med.medications || 'None reported'}</span>
            </div>
            <div>
              <span class="font-semibold text-slate-500">Past Hospitalization:</span>
              <span class="font-bold text-slate-800 ml-1">${med.hospitalization || 'None reported'}</span>
            </div>
          </div>
        </div>

      </div>

      <!-- Action Buttons -->
      <div class="flex items-center justify-between pt-4">
        <button type="button" id="btn-verify-edit" class="px-6 py-2.5 text-xs font-bold text-slate-700 hover:text-blue-600 bg-white border border-slate-200 hover:border-blue-300 rounded-xl transition flex items-center gap-2">
          <i data-lucide="edit-3" class="w-4 h-4"></i>
          <span>Edit Information</span>
        </button>

        <button type="button" id="btn-verify-confirm" class="px-7 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-blue-600/20 hover:shadow-blue-600/30 transition-all flex items-center gap-2 group">
          <i data-lucide="check-circle-2" class="w-4 h-4"></i>
          <span>Confirm & Continue</span>
          <i data-lucide="arrow-right" class="w-4 h-4 transition-transform group-hover:translate-x-1"></i>
        </button>
      </div>

    </div>
  `;
}

function attachStep2Events() {
  const btnEdit = document.getElementById("btn-verify-edit");
  if (btnEdit) {
    btnEdit.addEventListener("click", () => goToStep(1));
  }

  const btnConfirm = document.getElementById("btn-verify-confirm");
  if (btnConfirm) {
    btnConfirm.addEventListener("click", () => {
      // Trigger short verification toast / animation modal before moving to Step 3
      showVerificationSuccessModal(() => {
        goToStep(3);
      });
    });
  }
}

// Verification Modal Animation
function showVerificationSuccessModal(onComplete) {
  const modal = document.createElement("div");
  modal.className = "fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs transition-opacity duration-300";
  modal.innerHTML = `
    <div class="bg-white rounded-3xl p-8 max-w-sm w-full text-center shadow-2xl border border-blue-100 animate-in zoom-in-95 duration-200">
      
      <!-- SVG Animated Checkmark -->
      <div class="w-16 h-16 rounded-full bg-blue-50 text-blue-600 mx-auto flex items-center justify-center mb-4 ring-8 ring-blue-50/50">
        <svg class="w-10 h-10 checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
          <circle class="checkmark__circle" cx="26" cy="26" r="23" fill="none"/>
          <path class="checkmark__check" fill="none" stroke="#2563EB" stroke-width="3.5" stroke-linecap="round" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
        </svg>
      </div>

      <h3 class="text-xl font-extrabold text-slate-900 mb-1">Information Verified</h3>
      <p class="text-xs text-slate-500 mb-4">Patient identity locked in. Proceeding to clinical symptom evaluation...</p>

      <div class="w-full bg-blue-100 rounded-full h-1.5 overflow-hidden">
        <div class="bg-blue-600 h-full w-0 animate-[progress_1s_ease-in-out_forwards]"></div>
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
  }, 1100);
}

// ----------------------------------------------------
// STEP 3: CHIEF COMPLAINT & SYMPTOMS
// ----------------------------------------------------
function renderStep3Complaint() {
  const c = state.complaint;
  const isUrgent = checkForUrgentSymptoms(c);
  const analysis = analyzeComplaintText(c);

  const symptomList = [
    "Fever", "Headache", "Dizziness", "Nausea", "Vomiting", 
    "Fatigue", "Cough", "Difficulty Breathing", "Diarrhea", 
    "Constipation", "Chest Pain / Pressure", "Muscle Pain", 
    "Joint Pain", "Skin Rash / Itching", "Sore Throat", "Indigestion / Bloating"
  ];

  const bodyLocations = [
    { id: "head", label: "Head & Brain", icon: "🧠" },
    { id: "neck", label: "Neck & Throat", icon: "🧣" },
    { id: "chest", label: "Chest & Heart", icon: "🫀" },
    { id: "abdomen", label: "Abdomen (Stomach)", icon: "🩺" },
    { id: "back", label: "Back & Spine", icon: "🦴" },
    { id: "arms", label: "Arms & Shoulders", icon: "💪" },
    { id: "hands", label: "Hands & Wrists", icon: "🖐️" },
    { id: "pelvis", label: "Pelvis & Groin", icon: "🧪" },
    { id: "legs", label: "Legs & Knees", icon: "🦵" },
    { id: "feet", label: "Feet & Ankles", icon: "🦶" },
    { id: "whole_body", label: "Whole Body", icon: "🌐" },
    { id: "other", label: "Other Area", icon: "📍" }
  ];

  return `
    <div class="step-container space-y-8">
      
      <!-- Step Header -->
      <div class="border-b border-slate-200/80 pb-5">
        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/60 mb-2">
          <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
          Step 3 of 8: Patient Complaint
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
          What brings you in today?
        </h2>
        <p class="text-sm text-slate-500 mt-1">
          Describe what you are currently experiencing. The information you provide will help prepare you for your clinical consultation.
        </p>
      </div>

      <!-- Urgent Symptom Red-Flag Banner (Reveals dynamically if urgent symptoms detected) -->
      <div id="urgent-alert-banner" class="${isUrgent ? 'block' : 'hidden'} p-4 bg-rose-50 border-2 border-rose-300 rounded-2xl text-rose-900 shadow-sm animate-bounce-short">
        <div class="flex items-start gap-3">
          <div class="w-8 h-8 rounded-xl bg-rose-200/80 text-rose-700 flex items-center justify-center shrink-0">
            <i data-lucide="alert-triangle" class="w-5 h-5"></i>
          </div>
          <div>
            <h4 class="text-sm font-extrabold text-rose-950">Clinical Advisory: Potentially Urgent Symptoms</h4>
            <p class="text-xs text-rose-800 mt-0.5 leading-relaxed">
              Your reported symptoms may require immediate medical attention. If you are experiencing severe chest pain, shortness of breath, sudden numbness, or loss of consciousness, please call emergency services (911) or proceed immediately to the nearest Emergency Department.
            </p>
          </div>
        </div>
      </div>

      <!-- Complaint Input Area -->
      <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-2xs space-y-6">
        <div>
          <div class="flex items-center justify-between mb-2">
            <label class="block text-sm font-bold text-slate-800">
              Describe your condition or complaint <span class="text-rose-500">*</span>
            </label>
            <span id="char-counter" class="text-xs text-slate-400 font-medium">${(c || "").length} characters</span>
          </div>
          
          <textarea id="input-complaint-text" rows="4" 
                    placeholder="Example: I have been experiencing stomach pain since yesterday. The pain becomes worse after eating and I sometimes feel nauseous." 
                    class="medical-input w-full p-4 rounded-xl border border-slate-200 text-sm focus:outline-none bg-slate-50/50 leading-relaxed">${c}</textarea>
          <p id="err-complaint" class="form-error-msg text-rose-500 text-xs mt-1 hidden"></p>

          <!-- Live Smart Suggestion Bar (Updates dynamically as patient types) -->
          <div id="live-suggestions-bar" class="mt-3 p-3.5 bg-blue-50/70 border border-blue-100 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
            <div class="flex items-center gap-2 text-blue-900 font-medium">
              <span class="text-base">${analysis.systemData?.icon || '🩺'}</span>
              <span>Detected Category: <strong class="text-blue-700">${analysis.systemData?.name || 'General'}</strong></span>
              <span class="text-[10px] bg-blue-200/80 text-blue-800 px-2 py-0.5 rounded-full font-bold">Live AI Preview</span>
            </div>
            <div class="text-slate-500 text-[11px]">
              Possible Topics: ${analysis.systemData?.possibleConditions.slice(0, 2).map(x => x.name).join(", ") || 'General Consultation'}
            </div>
          </div>
        </div>

        <!-- Structured Symptom Inputs -->
        <!-- 1. Where do you feel the problem? -->
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wide">
            Where do you feel the problem primarily? <span class="text-rose-500">*</span>
          </label>
          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2.5">
            ${bodyLocations.map(loc => `
              <button type="button" 
                      data-loc-id="${loc.id}"
                      class="loc-select-card p-3 rounded-xl border text-left transition-all duration-150 flex items-center gap-2.5 ${
                        state.bodyLocation === loc.id 
                          ? 'border-blue-600 bg-blue-50/80 text-blue-900 ring-2 ring-blue-400/20 font-bold shadow-2xs' 
                          : 'border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-medium'
                      }">
                <span class="text-lg">${loc.icon}</span>
                <span class="text-xs truncate">${loc.label}</span>
              </button>
            `).join('')}
          </div>
          <p id="err-location" class="form-error-msg text-rose-500 text-xs mt-1 hidden"></p>
        </div>

        <!-- 2. Severity Scale (1 - 5) -->
        <div>
          <div class="flex items-center justify-between mb-2">
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide">
              How severe is the discomfort? (1 - 5 Scale)
            </label>
            <span id="severity-label-badge" class="text-xs font-bold px-2.5 py-0.5 rounded-full ${getSeverityColorClass(state.severity)}">
              Level ${state.severity}: ${getSeverityLabel(state.severity)}
            </span>
          </div>

          <div class="grid grid-cols-5 gap-2">
            ${[1, 2, 3, 4, 5].map(lvl => `
              <button type="button" 
                      data-severity="${lvl}" 
                      class="severity-tile py-3 rounded-xl border text-center transition-all ${
                        state.severity === lvl 
                          ? 'border-blue-600 bg-blue-600 text-white shadow-md shadow-blue-500/20 font-bold ring-2 ring-blue-300' 
                          : 'border-slate-200 bg-slate-50/70 hover:bg-slate-100 text-slate-700 font-semibold'
                      }">
                <div class="text-sm sm:text-base font-extrabold">${lvl}</div>
                <div class="text-[10px] hidden sm:block mt-0.5 opacity-90">${getSeverityLabel(lvl)}</div>
              </button>
            `).join('')}
          </div>
        </div>

        <!-- 3. Onset / Duration -->
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wide">
            When did your symptoms start?
          </label>
          <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
            ${["Today", "1–3 days ago", "Less than a week", "1–4 weeks", "More than a month", "Not sure"].map(dur => `
              <button type="button" 
                      data-duration="${dur}" 
                      class="duration-chip px-3.5 py-2.5 rounded-xl border text-xs text-left transition-all ${
                        state.duration === dur 
                          ? 'border-blue-600 bg-blue-50/80 text-blue-900 font-bold ring-1 ring-blue-400/30' 
                          : 'border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-medium'
                      }">
                ${dur}
              </button>
            `).join('')}
          </div>
        </div>

        <!-- 4. Aggravating & Relieving Factors -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Does anything make it worse? <span class="text-slate-400 font-normal">(Optional)</span></label>
            <input type="text" id="input-aggravating" value="${state.aggravating || ''}" placeholder="e.g. Eating spicy meals, walking, lying flat" 
                   class="medical-input w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:outline-none bg-slate-50/50" />
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Does anything make it better? <span class="text-slate-400 font-normal">(Optional)</span></label>
            <input type="text" id="input-relieving" value="${state.relieving || ''}" placeholder="e.g. Drinking warm water, resting, antacids" 
                   class="medical-input w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:outline-none bg-slate-50/50" />
          </div>
        </div>

        <!-- 5. Additional Associated Symptoms Grid -->
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wide">
            Additional Symptoms Experienced (Select all that apply)
          </label>
          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
            ${symptomList.map(sym => {
              const isChecked = state.symptoms.includes(sym);
              return `
                <button type="button" 
                        data-symptom="${sym}" 
                        class="symptom-tag-btn px-3 py-2 rounded-xl border text-xs text-left transition-all flex items-center justify-between ${
                          isChecked 
                            ? 'border-blue-600 bg-blue-600 text-white font-bold shadow-2xs' 
                            : 'border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-medium'
                        }">
                  <span class="truncate">${sym}</span>
                  ${isChecked ? '<i data-lucide="check" class="w-3.5 h-3.5 ml-1 shrink-0"></i>' : ''}
                </button>
              `;
            }).join('')}
          </div>
        </div>

      </div>

      <!-- Action Buttons -->
      <div class="flex items-center justify-between pt-4">
        <button type="button" id="btn-step3-back" class="px-6 py-2.5 text-xs font-bold text-slate-700 hover:text-blue-600 bg-white border border-slate-200 hover:border-blue-300 rounded-xl transition flex items-center gap-2">
          <i data-lucide="arrow-left" class="w-4 h-4"></i>
          <span>Back to Verification</span>
        </button>

        <button type="button" id="btn-step3-continue" class="px-7 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-blue-600/20 hover:shadow-blue-600/30 transition-all flex items-center gap-2 group">
          <i data-lucide="sparkles" class="w-4 h-4"></i>
          <span>Analyze Complaint</span>
          <i data-lucide="arrow-right" class="w-4 h-4 transition-transform group-hover:translate-x-1"></i>
        </button>
      </div>

    </div>
  `;
}

function getSeverityLabel(level) {
  switch (level) {
    case 1: return "Very Mild";
    case 2: return "Mild";
    case 3: return "Moderate";
    case 4: return "Severe";
    case 5: return "Very Severe";
    default: return "Moderate";
  }
}

function getSeverityColorClass(level) {
  switch (level) {
    case 1: return "bg-emerald-100 text-emerald-800 border border-emerald-200";
    case 2: return "bg-teal-100 text-teal-800 border border-teal-200";
    case 3: return "bg-blue-100 text-blue-800 border border-blue-200";
    case 4: return "bg-amber-100 text-amber-800 border border-amber-200";
    case 5: return "bg-rose-100 text-rose-800 border border-rose-200";
    default: return "bg-blue-100 text-blue-800";
  }
}

function attachStep3Events() {
  const textarea = document.getElementById("input-complaint-text");
  const charCounter = document.getElementById("char-counter");
  const urgentBanner = document.getElementById("urgent-alert-banner");
  const liveBar = document.getElementById("live-suggestions-bar");

  if (textarea) {
    textarea.addEventListener("input", (e) => {
      state.complaint = e.target.value;
      if (charCounter) charCounter.textContent = `${state.complaint.length} characters`;

      // Check urgent
      const isUrgent = checkForUrgentSymptoms(state.complaint);
      if (urgentBanner) {
        if (isUrgent) urgentBanner.classList.remove("hidden");
        else urgentBanner.classList.add("hidden");
      }

      // Live classification
      const analysis = analyzeComplaintText(state.complaint);
      state.bodySystemId = analysis.systemId;
      state.systemRelevanceScore = analysis.confidence;

      if (liveBar && analysis.systemData) {
        liveBar.innerHTML = `
          <div class="flex items-center gap-2 text-blue-900 font-medium">
            <span class="text-base">${analysis.systemData.icon}</span>
            <span>Detected Category: <strong class="text-blue-700">${analysis.systemData.name}</strong></span>
            <span class="text-[10px] bg-blue-200/80 text-blue-800 px-2 py-0.5 rounded-full font-bold">Live AI Preview</span>
          </div>
          <div class="text-slate-500 text-[11px]">
            Possible Topics: ${analysis.systemData.possibleConditions.slice(0, 2).map(x => x.name).join(", ")}
          </div>
        `;
      }

      saveState();
    });
  }

  // Location selector
  const locCards = document.querySelectorAll(".loc-select-card");
  locCards.forEach(card => {
    card.addEventListener("click", () => {
      const locId = card.getAttribute("data-loc-id");
      state.bodyLocation = locId;
      const reg = BODY_REGIONS_MAP[locId];
      if (reg) state.subRegion = reg.subRegion;

      // Re-run analysis
      const analysis = analyzeComplaintText(state.complaint);
      state.bodySystemId = analysis.systemId;
      state.systemRelevanceScore = analysis.confidence;

      saveState();
      renderStep(3);
    });
  });

  // Severity selector
  const severityTiles = document.querySelectorAll(".severity-tile");
  severityTiles.forEach(tile => {
    tile.addEventListener("click", () => {
      state.severity = parseInt(tile.getAttribute("data-severity"), 10);
      saveState();
      renderStep(3);
    });
  });

  // Duration chips
  const durationChips = document.querySelectorAll(".duration-chip");
  durationChips.forEach(chip => {
    chip.addEventListener("click", () => {
      state.duration = chip.getAttribute("data-duration");
      saveState();
      renderStep(3);
    });
  });

  // Aggravating & Relieving inputs
  const inputAgg = document.getElementById("input-aggravating");
  if (inputAgg) {
    inputAgg.addEventListener("input", (e) => {
      state.aggravating = e.target.value;
      saveState();
    });
  }
  const inputRel = document.getElementById("input-relieving");
  if (inputRel) {
    inputRel.addEventListener("input", (e) => {
      state.relieving = e.target.value;
      saveState();
    });
  }

  // Symptom Multi-select
  const symptomBtns = document.querySelectorAll(".symptom-tag-btn");
  symptomBtns.forEach(btn => {
    btn.addEventListener("click", () => {
      const sym = btn.getAttribute("data-symptom");
      if (state.symptoms.includes(sym)) {
        state.symptoms = state.symptoms.filter(s => s !== sym);
      } else {
        state.symptoms.push(sym);
      }
      saveState();
      renderStep(3);
    });
  });

  // Navigation
  const btnBack = document.getElementById("btn-step3-back");
  if (btnBack) btnBack.addEventListener("click", () => goToStep(2));

  const btnContinue = document.getElementById("btn-step3-continue");
  if (btnContinue) {
    btnContinue.addEventListener("click", () => {
      if (validateCurrentStep(3)) {
        // Show simulated 1.2s analysis scan before Step 4
        showAnalysisScanSimulation(() => {
          goToStep(4);
        });
      }
    });
  }
}

// Simulated Analysis Animation Screen
function showAnalysisScanSimulation(onComplete) {
  const modal = document.createElement("div");
  modal.className = "fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs transition-opacity duration-300";
  modal.innerHTML = `
    <div class="bg-white rounded-3xl p-8 max-w-md w-full text-center shadow-2xl border border-blue-100 space-y-5">
      
      <div class="relative w-20 h-20 mx-auto flex items-center justify-center">
        <div class="absolute inset-0 rounded-full border-4 border-blue-100"></div>
        <div class="absolute inset-0 rounded-full border-4 border-blue-600 border-t-transparent animate-spin"></div>
        <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 font-bold text-xl">
          🩺
        </div>
      </div>

      <div>
        <span class="inline-flex items-center gap-1 text-[11px] font-bold uppercase tracking-wider text-blue-600 bg-blue-100/80 px-2.5 py-0.5 rounded-md mb-2">
          Clinical NLP Classifier
        </span>
        <h3 class="text-xl font-extrabold text-slate-900">Analyzing Your Complaint...</h3>
        <p class="text-xs text-slate-500 mt-1">
          Evaluating reported clinical keywords, symptom clusters, and anatomical associations...
        </p>
      </div>

      <div class="space-y-2 text-left bg-slate-50 p-3 rounded-xl border border-slate-100 text-xs">
        <div class="flex items-center justify-between text-slate-600">
          <span>Parsing medical vocabulary...</span>
          <span class="text-emerald-600 font-bold">Done</span>
        </div>
        <div class="flex items-center justify-between text-slate-600">
          <span>Correlating physiological body systems...</span>
          <span class="text-emerald-600 font-bold">Done</span>
        </div>
        <div class="flex items-center justify-between text-slate-600">
          <span>Generating consultation discussion topics...</span>
          <span class="text-blue-600 font-bold animate-pulse">Processing</span>
        </div>
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
  }, 1200);
}

// ----------------------------------------------------
// STEP 4: MEDICAL CLASSIFICATION & SUGGESTED BODY SYSTEM
// ----------------------------------------------------
function renderStep4Analysis() {
  const analysis = analyzeComplaintText(state.complaint);
  const activeSystemKey = state.bodySystemId || analysis.systemId;
  const currentSys = BODY_SYSTEMS_DATA[activeSystemKey] || BODY_SYSTEMS_DATA.digestive;
  const score = state.systemRelevanceScore || analysis.confidence;

  const allSystems = Object.keys(BODY_SYSTEMS_DATA);

  return `
    <div class="step-container space-y-8">
      
      <!-- Step Header -->
      <div class="border-b border-slate-200/80 pb-5">
        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/60 mb-2">
          <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
          Step 4 of 8: Medical Classification
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
          Understanding Your Complaint
        </h2>
        <p class="text-sm text-slate-500 mt-1">
          Automated symptom categorization and physiological system correlation for consultation readiness.
        </p>
      </div>

      <!-- Complaint Recap Quote Box -->
      <div class="bg-gradient-to-r from-blue-50/80 via-white to-sky-50/80 p-5 rounded-2xl border border-blue-100 flex items-start gap-3 shadow-2xs">
        <div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-sm mt-0.5">
          <i data-lucide="message-square" class="w-4 h-4"></i>
        </div>
        <div class="flex-1">
          <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Your Reported Complaint</div>
          <p class="text-sm font-semibold text-slate-800 mt-0.5 italic">
            "${state.complaint}"
          </p>
        </div>
      </div>

      <!-- Primary Matched System Card (Prominent Highlight) -->
      <div class="bg-white rounded-3xl p-6 sm:p-8 border-2 border-blue-500/80 shadow-md shadow-blue-500/5 relative overflow-hidden space-y-6">
        <div class="absolute top-0 right-0 bg-blue-600 text-white px-4 py-1 rounded-bl-2xl text-xs font-bold flex items-center gap-1 shadow-sm">
          <i data-lucide="award" class="w-3.5 h-3.5"></i>
          Primary Associated System
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center text-3xl shadow-inner">
              ${currentSys.icon}
            </div>
            <div>
              <div class="flex items-center gap-2">
                <h3 class="text-2xl font-extrabold text-slate-900">${currentSys.name}</h3>
                <span class="text-xs font-extrabold text-blue-700 bg-blue-100 px-2.5 py-0.5 rounded-full">
                  ${score}% Relevance
                </span>
              </div>
              <p class="text-xs text-slate-500 mt-1 max-w-xl">
                ${currentSys.description}
              </p>
            </div>
          </div>
        </div>

        <!-- Symptom Categories & Possible Topics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
          
          <!-- Column 1: Symptom Categories -->
          <div>
            <div class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2.5 flex items-center gap-1.5">
              <i data-lucide="tag" class="w-3.5 h-3.5 text-blue-600"></i>
              Related Symptom Categories
            </div>
            <div class="flex flex-wrap gap-2">
              ${currentSys.symptomCategories.map(cat => `
                <span class="px-3 py-1 bg-slate-100 text-slate-700 font-semibold text-xs rounded-lg border border-slate-200/80">
                  ${cat}
                </span>
              `).join('')}
            </div>
          </div>

          <!-- Column 2: Possible Related Conditions (For Discussion Only) -->
          <div>
            <div class="flex items-center justify-between mb-2.5">
              <div class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                <i data-lucide="book-open" class="w-3.5 h-3.5 text-blue-600"></i>
                Possible Discussion Topics
              </div>
              <span class="text-[10px] font-bold text-amber-700 bg-amber-100/90 px-2 py-0.5 rounded-full">
                For Doctor Discussion Only
              </span>
            </div>
            
            <div class="space-y-2">
              ${currentSys.possibleConditions.map(cond => `
                <div class="p-2.5 bg-blue-50/40 rounded-xl border border-blue-100/80">
                  <div class="text-xs font-bold text-slate-800">${cond.name}</div>
                  <div class="text-[11px] text-slate-500 mt-0.5">${cond.note}</div>
                </div>
              `).join('')}
            </div>
          </div>

        </div>

        <!-- Mandatory Safety Disclaimer Notice -->
        <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-600 flex items-start gap-3">
          <i data-lucide="info" class="w-4 h-4 text-blue-600 shrink-0 mt-0.5"></i>
          <div>
            <span class="font-bold text-slate-800">System Relevance Estimate — Not a Medical Diagnosis:</span>
            The suggestions above are generated to help organize your health data for clinical discussion. A certified physician must evaluate, examine, and diagnose your condition.
          </div>
        </div>

      </div>

      <!-- System Re-classification Grid (Allows patient to override or choose other systems) -->
      <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-2xs space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <h4 class="text-sm font-bold text-slate-900">Explore or Switch Body Systems</h4>
            <p class="text-xs text-slate-500">If your primary symptoms fit another system more closely, select it below:</p>
          </div>
          <span class="text-xs font-semibold text-blue-600">9 Major Systems</span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 gap-3">
          ${allSystems.map(sysKey => {
            const sys = BODY_SYSTEMS_DATA[sysKey];
            const isSelected = activeSystemKey === sysKey;
            return `
              <button type="button" 
                      data-system-key="${sysKey}" 
                      class="system-card-btn p-3.5 rounded-xl border text-left transition-all ${
                        isSelected 
                          ? 'border-blue-600 bg-blue-50/90 text-blue-950 font-bold ring-2 ring-blue-500/20 shadow-2xs' 
                          : 'border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-medium'
                      }">
                <div class="text-xl mb-1">${sys.icon}</div>
                <div class="text-xs font-bold truncate">${sys.name}</div>
                <div class="text-[10px] text-slate-400 mt-0.5 truncate">${sys.recommendedSpecialties[0]}</div>
              </button>
            `;
          }).join('')}
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex items-center justify-between pt-4">
        <button type="button" id="btn-step4-back" class="px-6 py-2.5 text-xs font-bold text-slate-700 hover:text-blue-600 bg-white border border-slate-200 hover:border-blue-300 rounded-xl transition flex items-center gap-2">
          <i data-lucide="arrow-left" class="w-4 h-4"></i>
          <span>Back to Complaint</span>
        </button>

        <button type="button" id="btn-step4-continue" class="px-7 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-blue-600/20 hover:shadow-blue-600/30 transition-all flex items-center gap-2 group">
          <span>Continue to Body Location</span>
          <i data-lucide="arrow-right" class="w-4 h-4 transition-transform group-hover:translate-x-1"></i>
        </button>
      </div>

    </div>
  `;
}

function attachStep4Events() {
  const sysCards = document.querySelectorAll(".system-card-btn");
  sysCards.forEach(card => {
    card.addEventListener("click", () => {
      const key = card.getAttribute("data-system-key");
      state.bodySystemId = key;
      state.systemRelevanceScore = 90;
      state.manualSystemOverride = true;
      
      const sysData = BODY_SYSTEMS_DATA[key];
      if (sysData && sysData.defaultLocation) {
        state.bodyLocation = sysData.defaultLocation;
        state.subRegion = sysData.defaultSubRegion;
      }

      saveState();
      renderStep(4);
    });
  });

  const btnBack = document.getElementById("btn-step4-back");
  if (btnBack) btnBack.addEventListener("click", () => goToStep(3));

  const btnContinue = document.getElementById("btn-step4-continue");
  if (btnContinue) btnContinue.addEventListener("click", () => goToStep(5));
}

// ----------------------------------------------------
// STEP 5: INTERACTIVE HUMAN BODY LOCATION
// ----------------------------------------------------
function renderStep5BodyMap() {
  return `
    <div class="step-container space-y-8">
      
      <!-- Step Header -->
      <div class="border-b border-slate-200/80 pb-5">
        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/60 mb-2">
          <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
          Step 5 of 8: Anatomical Localization
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
          Interactive Human Body Location
        </h2>
        <p class="text-sm text-slate-500 mt-1">
          Pinpoint the anatomical region experiencing discomfort. You can switch between Front and Back views or click any body region to adjust.
        </p>
      </div>

      <!-- Interactive Body Visualizer Mount Point -->
      <div id="body-map-mount-point"></div>

      <!-- Action Buttons -->
      <div class="flex items-center justify-between pt-4">
        <button type="button" id="btn-step5-back" class="px-6 py-2.5 text-xs font-bold text-slate-700 hover:text-blue-600 bg-white border border-slate-200 hover:border-blue-300 rounded-xl transition flex items-center gap-2">
          <i data-lucide="arrow-left" class="w-4 h-4"></i>
          <span>Back to Analysis</span>
        </button>

        <button type="button" id="btn-step5-continue" class="px-7 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-blue-600/20 hover:shadow-blue-600/30 transition-all flex items-center gap-2 group">
          <span>Confirm Location & Review</span>
          <i data-lucide="arrow-right" class="w-4 h-4 transition-transform group-hover:translate-x-1"></i>
        </button>
      </div>

    </div>
  `;
}

function attachStep5Events() {
  const mount = document.getElementById("body-map-mount-point");
  if (mount) {
    bodyVisualizerInstance = new BodyVisualizer("body-map-mount-point", {
      initialRegion: state.bodyLocation || "abdomen",
      initialView: BODY_REGIONS_MAP[state.bodyLocation]?.view || "front",
      onSelect: (regionId) => {
        state.bodyLocation = regionId;
        const regInfo = BODY_REGIONS_MAP[regionId];
        if (regInfo) {
          state.subRegion = regInfo.subRegion;
          if (!state.manualSystemOverride && regInfo.defaultSystem) {
            state.bodySystemId = regInfo.defaultSystem;
          }
        }
        saveState();
      }
    });
  }

  const btnBack = document.getElementById("btn-step5-back");
  if (btnBack) btnBack.addEventListener("click", () => goToStep(4));

  const btnContinue = document.getElementById("btn-step5-continue");
  if (btnContinue) btnContinue.addEventListener("click", () => goToStep(6));
}

// ----------------------------------------------------
// STEP 6: PRE-CONSULTATION SUMMARY REVIEW
// ----------------------------------------------------
function renderStep6Review() {
  const p = state.personal;
  const emg = state.emergency;
  const med = state.medical;
  const currentSys = BODY_SYSTEMS_DATA[state.bodySystemId] || BODY_SYSTEMS_DATA.digestive;
  const locInfo = BODY_REGIONS_MAP[state.bodyLocation] || BODY_REGIONS_MAP.abdomen;
  const fullName = `${p.firstName} ${p.middleName ? p.middleName + ' ' : ''}${p.lastName}`;

  return `
    <div class="step-container space-y-8">
      
      <!-- Step Header -->
      <div class="border-b border-slate-200/80 pb-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/60 mb-2">
              <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
              Step 6 of 8: Summary Review
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
              Pre-Consultation Summary
            </h2>
            <p class="text-sm text-slate-500 mt-1">
              Please review all compiled health information before proceeding to specialist selection.
            </p>
          </div>

          <button type="button" id="btn-print-summary" class="no-print shrink-0 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition flex items-center gap-2 border border-slate-200">
            <i data-lucide="printer" class="w-4 h-4"></i>
            <span>Print / Export Summary</span>
          </button>
        </div>
      </div>

      <!-- Master Summary Card -->
      <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-8" id="printable-summary-content">
        
        <!-- Header Clinical Badge -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-100 gap-4">
          <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-bold text-xl shadow-md shadow-blue-500/20">
              ${p.firstName ? p.firstName[0] : 'P'}
            </div>
            <div>
              <h3 class="text-lg font-extrabold text-slate-900">${fullName || 'Patient Name'}</h3>
              <div class="text-xs text-slate-500 flex items-center gap-2 mt-0.5">
                <span>${p.gender || '—'}</span> • 
                <span>${p.age ? `${p.age} years old` : '—'}</span> • 
                <span>DOB: ${p.dob || '—'}</span> •
                <span class="font-semibold text-blue-600">Blood: ${med.bloodType}</span>
              </div>
            </div>
          </div>

          <div class="text-right">
            <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Intake Record ID</div>
            <div class="text-sm font-mono font-bold text-slate-800">${state.intakeTicketId}</div>
          </div>
        </div>

        <!-- Two Columns: Section 1 (Personal & Contact) + Section 2 (Clinical Presentation) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          
          <!-- Column 1: Patient Background -->
          <div class="space-y-6">
            <div>
              <div class="flex items-center justify-between mb-2">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Contact & Emergency</h4>
                <button type="button" onclick="goToStep(1)" class="no-print text-xs text-blue-600 hover:underline font-semibold">Edit</button>
              </div>
              <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-2 text-xs">
                <div class="flex justify-between"><span class="text-slate-500">Phone:</span> <span class="font-bold text-slate-800">${p.phone}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Email:</span> <span class="font-bold text-slate-800">${p.email}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Address:</span> <span class="font-medium text-slate-800 text-right max-w-[200px] truncate">${p.address}</span></div>
                <div class="pt-2 border-t border-slate-200/60 flex justify-between">
                  <span class="text-slate-500">Emergency Contact:</span>
                  <span class="font-bold text-slate-800">${emg.name} (${emg.relationship}) - ${emg.phone}</span>
                </div>
              </div>
            </div>

            <div>
              <div class="flex items-center justify-between mb-2">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Medical History</h4>
                <button type="button" onclick="goToStep(1)" class="no-print text-xs text-blue-600 hover:underline font-semibold">Edit</button>
              </div>
              <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-2 text-xs">
                <div class="flex justify-between"><span class="text-slate-500">Known Allergies:</span> <span class="font-bold text-slate-800">${med.allergies || 'None'}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Existing Conditions:</span> <span class="font-bold text-slate-800">${med.conditions || 'None'}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Current Medications:</span> <span class="font-bold text-slate-800">${med.medications || 'None'}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Prior Hospitalization:</span> <span class="font-bold text-slate-800">${med.hospitalization || 'None'}</span></div>
              </div>
            </div>
          </div>

          <!-- Column 2: Clinical Intake & Complaint -->
          <div class="space-y-6">
            <div>
              <div class="flex items-center justify-between mb-2">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Chief Complaint</h4>
                <button type="button" onclick="goToStep(3)" class="no-print text-xs text-blue-600 hover:underline font-semibold">Edit</button>
              </div>
              <div class="p-4 bg-blue-50/50 rounded-2xl border border-blue-100 space-y-2">
                <p class="text-xs font-bold text-slate-800 leading-relaxed italic">
                  "${state.complaint}"
                </p>
                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-blue-100 text-xs">
                  <div>
                    <span class="text-slate-400">Severity:</span> 
                    <span class="font-bold text-blue-700 ml-1">Level ${state.severity} (${getSeverityLabel(state.severity)})</span>
                  </div>
                  <div>
                    <span class="text-slate-400">Duration:</span> 
                    <span class="font-bold text-slate-700 ml-1">${state.duration}</span>
                  </div>
                </div>
                ${state.aggravating ? `<div class="text-[11px] text-slate-600"><strong class="text-slate-700">Worse with:</strong> ${state.aggravating}</div>` : ''}
                ${state.relieving ? `<div class="text-[11px] text-slate-600"><strong class="text-slate-700">Better with:</strong> ${state.relieving}</div>` : ''}
              </div>
            </div>

            <div>
              <div class="flex items-center justify-between mb-2">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">System & Location Classification</h4>
                <button type="button" onclick="goToStep(5)" class="no-print text-xs text-blue-600 hover:underline font-semibold">Edit</button>
              </div>
              <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-3">
                <div class="flex items-center justify-between">
                  <span class="text-xs text-slate-500">Related Body System:</span>
                  <span class="px-2.5 py-1 bg-blue-100 text-blue-800 font-bold text-xs rounded-lg flex items-center gap-1">
                    ${currentSys.icon} ${currentSys.name}
                  </span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-xs text-slate-500">Estimated Location:</span>
                  <span class="font-bold text-slate-800 text-xs">${locInfo.name} (${locInfo.subRegion})</span>
                </div>
                <div class="pt-2 border-t border-slate-200/60">
                  <div class="text-[11px] font-semibold text-slate-400 mb-1">Selected Symptoms:</div>
                  <div class="flex flex-wrap gap-1.5">
                    ${state.symptoms.length > 0 ? state.symptoms.map(s => `
                      <span class="px-2 py-0.5 bg-white border border-slate-200 text-slate-700 text-[11px] rounded-md font-medium">
                        ${s}
                      </span>
                    `).join('') : '<span class="text-xs text-slate-400">None additional selected</span>'}
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- Consultation Discussion Topics -->
        <div class="p-4 bg-sky-50/70 rounded-2xl border border-sky-100">
          <div class="text-xs font-bold text-sky-950 mb-1 flex items-center gap-1.5">
            <i data-lucide="compass" class="w-4 h-4 text-sky-600"></i>
            Relevant Discussion Topics for Your Attending Physician:
          </div>
          <div class="flex flex-wrap gap-2 mt-2">
            ${currentSys.possibleConditions.map(c => `
              <span class="px-3 py-1 bg-white border border-sky-200 text-sky-900 text-xs rounded-lg font-bold shadow-2xs">
                ${c.name}
              </span>
            `).join('')}
          </div>
        </div>

        <!-- Prominent Disclaimer Banner -->
        <div class="p-4 bg-amber-50/90 border border-amber-200 rounded-2xl text-xs text-amber-950 flex items-start gap-3">
          <i data-lucide="shield-alert" class="w-5 h-5 text-amber-600 shrink-0 mt-0.5"></i>
          <div>
            <h5 class="font-bold text-amber-950 mb-0.5">Important Medical & Consultation Disclaimer</h5>
            This pre-consultation summary does not constitute a medical diagnosis, clinical judgment, or treatment prescription. It is designed solely to structure patient-reported health complaints for efficient review by your attending healthcare provider.
          </div>
        </div>

      </div>

      <!-- Action Buttons -->
      <div class="flex items-center justify-between pt-4">
        <button type="button" id="btn-step6-back" class="px-6 py-2.5 text-xs font-bold text-slate-700 hover:text-blue-600 bg-white border border-slate-200 hover:border-blue-300 rounded-xl transition flex items-center gap-2">
          <i data-lucide="arrow-left" class="w-4 h-4"></i>
          <span>Back & Edit</span>
        </button>

        <button type="button" id="btn-step6-continue" class="px-7 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-blue-600/20 hover:shadow-blue-600/30 transition-all flex items-center gap-2 group">
          <i data-lucide="user-plus" class="w-4 h-4"></i>
          <span>Confirm & Find Matching Doctors</span>
          <i data-lucide="arrow-right" class="w-4 h-4 transition-transform group-hover:translate-x-1"></i>
        </button>
      </div>

    </div>
  `;
}

function attachStep6Events() {
  const btnPrint = document.getElementById("btn-print-summary");
  if (btnPrint) {
    btnPrint.addEventListener("click", () => {
      window.print();
    });
  }

  const btnBack = document.getElementById("btn-step6-back");
  if (btnBack) btnBack.addEventListener("click", () => goToStep(5));

  const btnContinue = document.getElementById("btn-step6-continue");
  if (btnContinue) btnContinue.addEventListener("click", () => goToStep(7));
}

// ----------------------------------------------------
// STEP 7: DOCTOR RECOMMENDATION & SELECTION
// ----------------------------------------------------
function renderStep7Doctors() {
  const currentSys = BODY_SYSTEMS_DATA[state.bodySystemId] || BODY_SYSTEMS_DATA.digestive;

  // Smart Sort Doctors: Primary matched system first, then others
  const sortedDoctors = [...MOCK_DOCTORS].sort((a, b) => {
    const aMatch = a.systemId === state.bodySystemId ? 1 : 0;
    const bMatch = b.systemId === state.bodySystemId ? 1 : 0;
    if (aMatch !== bMatch) return bMatch - aMatch;
    return b.rating - a.rating;
  });

  return `
    <div class="step-container space-y-8">
      
      <!-- Step Header -->
      <div class="border-b border-slate-200/80 pb-5">
        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/60 mb-2">
          <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
          Step 7 of 8: Doctor Recommendation
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
          Find the Right Doctor
        </h2>
        <p class="text-sm text-slate-500 mt-1">
          Based on your reported <strong>${currentSys.name}</strong> symptoms, we have prioritized relevant clinical specialists for you.
        </p>
      </div>

      <!-- Filter / Specialty Bar -->
      <div class="flex flex-wrap items-center justify-between gap-3 p-4 bg-white rounded-2xl border border-slate-200 shadow-2xs">
        <div class="flex flex-wrap items-center gap-2">
          <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mr-1">Filter:</span>
          <button type="button" class="doctor-filter-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-blue-600 text-white shadow-xs" data-filter="all">
            All Specialists (${MOCK_DOCTORS.length})
          </button>
          <button type="button" class="doctor-filter-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700" data-filter="match">
            Matched System Only (${currentSys.icon} ${currentSys.name})
          </button>
          <button type="button" class="doctor-filter-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700" data-filter="today">
            Available Today
          </button>
        </div>

        <div class="text-xs text-slate-500 font-medium">
          Showing sorted by clinical match & ratings
        </div>
      </div>

      <!-- Doctor Cards Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="doctor-cards-grid">
        ${sortedDoctors.map(doc => renderDoctorCard(doc)).join('')}
      </div>

      <!-- Action Buttons -->
      <div class="flex items-center justify-between pt-4">
        <button type="button" id="btn-step7-back" class="px-6 py-2.5 text-xs font-bold text-slate-700 hover:text-blue-600 bg-white border border-slate-200 hover:border-blue-300 rounded-xl transition flex items-center gap-2">
          <i data-lucide="arrow-left" class="w-4 h-4"></i>
          <span>Back to Summary</span>
        </button>

        <div class="text-xs text-slate-400">
          Click "View Profile & Select" on any doctor card to proceed.
        </div>
      </div>

    </div>
  `;
}

function renderDoctorCard(doc) {
  const isMatch = doc.systemId === state.bodySystemId;

  return `
    <div class="interactive-card doctor-card bg-white rounded-3xl p-6 border ${
      isMatch ? 'border-blue-300 ring-2 ring-blue-500/10 shadow-sm' : 'border-slate-200'
    } flex flex-col justify-between space-y-5">
      
      <div>
        <!-- Card Top Bar with Match Tag & Badge -->
        <div class="flex items-start justify-between gap-2 mb-4">
          <div class="flex flex-wrap items-center gap-1.5">
            ${isMatch ? `
              <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-blue-600 text-white flex items-center gap-1 shadow-2xs">
                <i data-lucide="check" class="w-3 h-3"></i> Top Specialty Match
              </span>
            ` : `
              <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600">
                ${doc.system}
              </span>
            `}
            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
              ${doc.availability}
            </span>
          </div>

          <div class="flex items-center gap-1 text-amber-500 font-bold text-xs bg-amber-50 px-2 py-0.5 rounded-lg">
            <span>★</span>
            <span class="text-slate-800">${doc.rating}</span>
            <span class="text-slate-400 font-normal">(${doc.reviewsCount})</span>
          </div>
        </div>

        <!-- Doctor Avatar & Details -->
        <div class="flex items-start gap-4">
          <div class="relative shrink-0">
            <img src="${doc.avatar}" alt="${doc.name}" class="w-16 h-16 rounded-2xl object-cover border-2 border-slate-100 shadow-sm" />
            <div class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-emerald-500 border-2 border-white flex items-center justify-center" title="Online & Available"></div>
          </div>
          
          <div class="flex-1 min-w-0">
            <h4 class="text-base font-extrabold text-slate-900 truncate">${doc.name}</h4>
            <div class="text-xs font-bold text-blue-600">${doc.specialty}</div>
            <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-2">
              <span>${doc.experience}</span> • 
              <span>${doc.title}</span>
            </div>
            <div class="text-[11px] text-slate-400 truncate mt-1 flex items-center gap-1">
              <i data-lucide="map-pin" class="w-3 h-3 text-slate-400 shrink-0"></i>
              ${doc.hospital} (${doc.clinicRoom})
            </div>
          </div>
        </div>

        <p class="text-xs text-slate-600 mt-4 leading-relaxed line-clamp-2">
          ${doc.description}
        </p>

        <!-- Expertise Chips -->
        <div class="flex flex-wrap gap-1.5 mt-3">
          ${doc.expertise.slice(0, 3).map(exp => `
            <span class="px-2 py-0.5 bg-slate-50 text-slate-600 border border-slate-200/80 rounded-md text-[10px] font-medium">
              ${exp}
            </span>
          `).join('')}
        </div>
      </div>

      <!-- Card Footer -->
      <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
        <div>
          <div class="text-[10px] text-slate-400 uppercase font-semibold">Consultation</div>
          <div class="text-xs font-extrabold text-slate-800">${doc.consultationFee}</div>
        </div>

        <button type="button" 
                onclick="openDoctorModal('${doc.id}')"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-md shadow-blue-600/15 hover:shadow-blue-600/25 transition-all flex items-center gap-1.5 group">
          <span>View Profile & Select</span>
          <i data-lucide="chevron-right" class="w-3.5 h-3.5 transition-transform group-hover:translate-x-0.5"></i>
        </button>
      </div>

    </div>
  `;
}

function attachStep7Events() {
  const filterBtns = document.querySelectorAll(".doctor-filter-btn");
  const grid = document.getElementById("doctor-cards-grid");

  filterBtns.forEach(btn => {
    btn.addEventListener("click", () => {
      filterBtns.forEach(b => {
        b.className = "doctor-filter-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700";
      });
      btn.className = "doctor-filter-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-blue-600 text-white shadow-xs";

      const filterType = btn.getAttribute("data-filter");
      let filtered = [...MOCK_DOCTORS];

      if (filterType === "match") {
        filtered = filtered.filter(d => d.systemId === state.bodySystemId);
      } else if (filterType === "today") {
        filtered = filtered.filter(d => d.availability.toLowerCase().includes("today"));
      }

      if (grid) {
        grid.innerHTML = filtered.map(doc => renderDoctorCard(doc)).join('');
        initLucide();
      }
    });
  });

  const btnBack = document.getElementById("btn-step7-back");
  if (btnBack) btnBack.addEventListener("click", () => goToStep(6));
}

// ----------------------------------------------------
// DOCTOR DETAIL & SELECTION MODAL
// ----------------------------------------------------
function openDoctorModal(doctorId) {
  const doc = MOCK_DOCTORS.find(d => d.id === doctorId);
  if (!doc) return;

  const modal = document.createElement("div");
  modal.id = "doctor-detail-modal";
  modal.className = "fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs transition-opacity duration-300";
  
  modal.innerHTML = `
    <div class="bg-white rounded-3xl max-w-xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border border-blue-100 p-6 sm:p-8 space-y-6 animate-in zoom-in-95 duration-200">
      
      <!-- Modal Top Close & Badge -->
      <div class="flex items-start justify-between">
        <div class="flex items-center gap-2">
          <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-blue-100 text-blue-800">
            ${doc.badge}
          </span>
          <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
            ${doc.availability}
          </span>
        </div>
        <button type="button" id="btn-close-doc-modal" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition">
          <i data-lucide="x" class="w-4 h-4"></i>
        </button>
      </div>

      <!-- Doctor Profile Header -->
      <div class="flex items-start gap-4">
        <img src="${doc.avatar}" alt="${doc.name}" class="w-20 h-20 rounded-2xl object-cover border-2 border-blue-100 shadow-md" />
        <div>
          <h3 class="text-xl font-extrabold text-slate-900">${doc.name}</h3>
          <div class="text-sm font-bold text-blue-600">${doc.specialty}</div>
          <div class="text-xs text-slate-500 mt-1">${doc.education}</div>
          <div class="flex items-center gap-2 mt-2 text-xs font-semibold text-slate-700">
            <span class="text-amber-500">★ ${doc.rating}</span>
            <span>•</span>
            <span>${doc.experience}</span>
            <span>•</span>
            <span class="text-blue-600">${doc.consultationFee}</span>
          </div>
        </div>
      </div>

      <!-- Full Biography & Credentials -->
      <div class="space-y-3 text-xs">
        <div>
          <div class="font-bold text-slate-700 uppercase tracking-wider mb-1">Clinical Biography</div>
          <p class="text-slate-600 leading-relaxed">${doc.description}</p>
        </div>

        <div class="grid grid-cols-2 gap-4 pt-2">
          <div>
            <div class="font-bold text-slate-700 uppercase tracking-wider mb-1">Languages Spoken</div>
            <div class="text-slate-600">${doc.languages.join(", ")}</div>
          </div>
          <div>
            <div class="font-bold text-slate-700 uppercase tracking-wider mb-1">Clinic Room</div>
            <div class="text-slate-600">${doc.clinicRoom}</div>
          </div>
        </div>

        <div>
          <div class="font-bold text-slate-700 uppercase tracking-wider mb-1.5">Areas of Clinical Expertise</div>
          <div class="flex flex-wrap gap-1.5">
            ${doc.expertise.map(exp => `
              <span class="px-2.5 py-1 bg-blue-50 text-blue-800 border border-blue-200/60 rounded-lg text-xs font-medium">
                ${exp}
              </span>
            `).join('')}
          </div>
        </div>

        <div>
          <div class="font-bold text-slate-700 uppercase tracking-wider mb-1.5">Accepted Health Insurance</div>
          <div class="flex flex-wrap gap-1.5">
            ${doc.insuranceAccepted.map(ins => `
              <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded-md text-[11px]">
                ${ins}
              </span>
            `).join('')}
          </div>
        </div>
      </div>

      <!-- Consultation Format & Time Slot Picker -->
      <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 space-y-3">
        <label class="block text-xs font-bold text-slate-800 uppercase tracking-wide">
          Select Consultation Format & Time Slot:
        </label>
        
        <div class="grid grid-cols-2 gap-2">
          <label class="p-2.5 rounded-xl border border-blue-600 bg-blue-50/70 text-xs font-bold text-blue-900 flex items-center gap-2 cursor-pointer">
            <input type="radio" name="cons-type" value="In-Person Consultation" checked class="text-blue-600">
            <span>🏥 In-Clinic Visit</span>
          </label>
          <label class="p-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 flex items-center gap-2 cursor-pointer">
            <input type="radio" name="cons-type" value="Secure Telehealth Video" class="text-blue-600">
            <span>💻 Telehealth Video</span>
          </label>
        </div>

        <div class="flex items-center gap-2 pt-1">
          <span class="text-xs text-slate-500 font-medium">Available Slot:</span>
          <select id="modal-slot-picker" class="text-xs font-bold bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-blue-800">
            <option value="${doc.availableTime}">${doc.availableTime}</option>
            <option value="Today at 5:15 PM">Today at 5:15 PM</option>
            <option value="Tomorrow at 9:30 AM">Tomorrow at 9:30 AM</option>
            <option value="Tomorrow at 2:00 PM">Tomorrow at 2:00 PM</option>
          </select>
        </div>
      </div>

      <!-- Action Button -->
      <div class="pt-2">
        <button type="button" id="btn-select-doctor-confirm" class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-extrabold rounded-xl shadow-lg shadow-blue-600/20 transition-all flex items-center justify-center gap-2">
          <i data-lucide="check-circle" class="w-4 h-4"></i>
          <span>Select Dr. ${doc.name.replace('Dr. ', '')} & Complete Intake</span>
        </button>
      </div>

    </div>
  `;

  document.body.appendChild(modal);
  initLucide();

  // Close Modal
  const btnClose = modal.querySelector("#btn-close-doc-modal");
  if (btnClose) {
    btnClose.addEventListener("click", () => modal.remove());
  }

  // Confirm Selection
  const btnSelect = modal.querySelector("#btn-select-doctor-confirm");
  if (btnSelect) {
    btnSelect.addEventListener("click", () => {
      const slotPicker = modal.querySelector("#modal-slot-picker");
      const consTypeRadio = modal.querySelector("input[name='cons-type']:checked");

      state.selectedDoctor = doc;
      state.selectedSlot = slotPicker ? slotPicker.value : doc.availableTime;
      state.consultationType = consTypeRadio ? consTypeRadio.value : "In-Person Consultation";

      saveState();
      modal.remove();

      // Show animated transition to Step 8
      showDoctorSelectedModal(doc, () => {
        goToStep(8);
      });
    });
  }
}

function showDoctorSelectedModal(doc, onComplete) {
  const modal = document.createElement("div");
  modal.className = "fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs transition-opacity duration-300";
  modal.innerHTML = `
    <div class="bg-white rounded-3xl p-8 max-w-sm w-full text-center shadow-2xl border border-blue-100 animate-in zoom-in-95 duration-200">
      
      <div class="w-16 h-16 rounded-full bg-blue-50 text-blue-600 mx-auto flex items-center justify-center mb-4 ring-8 ring-blue-50/50">
        <svg class="w-10 h-10 checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
          <circle class="checkmark__circle" cx="26" cy="26" r="23" fill="none"/>
          <path class="checkmark__check" fill="none" stroke="#2563EB" stroke-width="3.5" stroke-linecap="round" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
        </svg>
      </div>

      <h3 class="text-xl font-extrabold text-slate-900 mb-1">Doctor Assigned</h3>
      <p class="text-xs text-slate-500 mb-2">Pre-consultation queue registered for <strong>${doc.name}</strong>.</p>
      <div class="inline-block px-3 py-1 bg-blue-50 text-blue-700 text-xs font-bold rounded-full mb-4">
        ${doc.specialty}
      </div>

      <div class="w-full bg-blue-100 rounded-full h-1.5 overflow-hidden">
        <div class="bg-blue-600 h-full w-0 animate-[progress_1s_ease-in-out_forwards]"></div>
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
  }, 1200);
}

// ----------------------------------------------------
// STEP 8: FINAL SUCCESS & INTAKE PASS
// ----------------------------------------------------
function renderStep8Success() {
  const doc = state.selectedDoctor || MOCK_DOCTORS[0];
  const p = state.personal;
  const sys = BODY_SYSTEMS_DATA[state.bodySystemId] || BODY_SYSTEMS_DATA.digestive;
  const fullName = `${p.firstName} ${p.lastName}`;

  return `
    <div class="step-container space-y-8 text-center max-w-2xl mx-auto">
      
      <!-- Big Glowing Medical Checkmark -->
      <div class="relative w-24 h-24 mx-auto flex items-center justify-center">
        <div class="absolute inset-0 rounded-full bg-blue-500/20 animate-ping"></div>
        <div class="relative w-20 h-20 rounded-full bg-blue-600 text-white flex items-center justify-center shadow-xl shadow-blue-500/30">
          <i data-lucide="check" class="w-10 h-10 stroke-[3]"></i>
        </div>
      </div>

      <!-- Success Header -->
      <div class="space-y-2">
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
          <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
          Intake Status: Pre-Consultation Prepared
        </div>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
          Pre-Consultation Complete
        </h2>
        <p class="text-sm text-slate-500 max-w-lg mx-auto">
          Your patient profile, symptom assessment, and clinical classifications have been assembled and queued for your assigned specialist.
        </p>
      </div>

      <!-- Official Intake Pass Card -->
      <div class="bg-white rounded-3xl p-6 sm:p-8 border border-blue-200 shadow-md shadow-blue-500/5 text-left space-y-6">
        
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
          <div>
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Clinical Queue Reference</div>
            <div class="text-lg font-mono font-extrabold text-blue-700">${state.intakeTicketId}</div>
          </div>
          <div class="text-right">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Scheduled Time</div>
            <div class="text-xs font-bold text-slate-800">${state.selectedSlot}</div>
          </div>
        </div>

        <!-- Doctor Card Preview -->
        <div class="flex items-center gap-4 p-4 bg-blue-50/60 rounded-2xl border border-blue-100">
          <img src="${doc.avatar}" alt="${doc.name}" class="w-16 h-16 rounded-xl object-cover border border-blue-200 shadow-2xs shrink-0" />
          <div class="flex-1 min-w-0">
            <div class="text-[11px] font-bold text-blue-600 uppercase tracking-wider">Attending Specialist</div>
            <h4 class="text-base font-extrabold text-slate-900 truncate">${doc.name}</h4>
            <div class="text-xs font-semibold text-slate-600">${doc.specialty} • ${doc.clinicRoom}</div>
            <div class="text-[11px] text-slate-500 mt-0.5">${state.consultationType}</div>
          </div>
        </div>

        <!-- Patient & Complaint Snapshot -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
          <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100 space-y-1">
            <div class="text-[10px] font-bold text-slate-400 uppercase">Patient Record</div>
            <div class="font-bold text-slate-800">${fullName || 'Patient'} (${p.gender || '—'}, ${p.age || '—'} yrs)</div>
            <div class="text-slate-500">${p.phone}</div>
          </div>

          <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100 space-y-1">
            <div class="text-[10px] font-bold text-slate-400 uppercase">Clinical Concern</div>
            <div class="font-bold text-blue-700 flex items-center gap-1">
              ${sys.icon} ${sys.name}
            </div>
            <div class="text-slate-600 truncate">"${state.complaint}"</div>
          </div>
        </div>

      </div>

      <!-- Action Navigation Buttons -->
      <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
        <button type="button" id="btn-print-intake-pass" class="w-full sm:w-auto px-6 py-3 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2">
          <i data-lucide="printer" class="w-4 h-4"></i>
          <span>Print Intake Pass</span>
        </button>

        <button type="button" onclick="goToStep(6)" class="w-full sm:w-auto px-6 py-3 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2">
          <i data-lucide="file-text" class="w-4 h-4"></i>
          <span>View Full Summary</span>
        </button>

        <button type="button" id="btn-new-intake" class="w-full sm:w-auto px-7 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-lg shadow-blue-600/20 transition flex items-center justify-center gap-2">
          <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
          <span>Start New Intake</span>
        </button>
      </div>

    </div>
  `;
}

function attachStep8Events() {
  const btnPrint = document.getElementById("btn-print-intake-pass");
  if (btnPrint) {
    btnPrint.addEventListener("click", () => window.print());
  }

  const btnNew = document.getElementById("btn-new-intake");
  if (btnNew) {
    btnNew.addEventListener("click", () => {
      if (confirm("Start a new patient intake? This will reset the current form session.")) {
        localStorage.removeItem("aurahealth_intake_state");
        location.reload();
      }
    });
  }
}

// Global Form Events
function bindFormEvents() {
  // Demo presets in header dropdown if available
  const topPreset = document.getElementById("header-demo-preset-select");
  if (topPreset) {
    topPreset.addEventListener("change", (e) => {
      const val = e.target.value;
      if (DEMO_PRESETS[val]) {
        applyPreset(DEMO_PRESETS[val]);
      }
    });
  }
}
