// Interactive Anatomical Human Body Visualizer Component

class BodyVisualizer {
  constructor(containerId, options = {}) {
    this.container = document.getElementById(containerId);
    this.currentView = options.initialView || 'front';
    this.selectedRegion = options.initialRegion || 'abdomen';
    this.onSelect = options.onSelect || (() => {});
    this.init();
  }

  init() {
    if (!this.container) return;
    this.render();
  }

  setView(view) {
    if (this.currentView === view) return;
    this.currentView = view;
    this.render();
  }

  setRegion(regionId) {
    this.selectedRegion = regionId;
    
    // Automatically switch view if region is primarily on back
    const regionData = BODY_REGIONS_MAP[regionId];
    if (regionData && regionData.view && regionData.view !== this.currentView) {
      this.currentView = regionData.view;
    }
    
    this.render();
    this.onSelect(this.selectedRegion);
  }

  render() {
    if (!this.container) return;
    
    const isFront = this.currentView === 'front';
    const active = this.selectedRegion;
    const regionInfo = BODY_REGIONS_MAP[active] || BODY_REGIONS_MAP.abdomen;

    this.container.innerHTML = `
      <div class="flex flex-col lg:flex-row items-center gap-8 bg-white/90 backdrop-blur-sm rounded-2xl p-6 border border-blue-100 shadow-sm">
        
        <!-- Left Side: Interactive Body Model Canvas -->
        <div class="flex-1 flex flex-col items-center w-full max-w-md">
          
          <!-- View Toggle Tabs -->
          <div class="flex items-center p-1 bg-slate-100/90 rounded-full border border-slate-200/80 mb-6 shadow-inner">
            <button type="button" id="btn-view-front" class="px-5 py-2 text-xs font-semibold rounded-full transition-all duration-200 flex items-center gap-1.5 ${isFront ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'text-slate-600 hover:text-slate-900'}">
              <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2a5 5 0 0 0-5 5v3a5 5 0 0 0 10 0V7a5 5 0 0 0-5-5z"></path><path d="M17 14h-1a6 6 0 0 0-8 0H7a3 3 0 0 0-3 3v4h16v-4a3 3 0 0 0-3-3z"></path></svg>
              Anterior (Front)
            </button>
            <button type="button" id="btn-view-back" class="px-5 py-2 text-xs font-semibold rounded-full transition-all duration-200 flex items-center gap-1.5 ${!isFront ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'text-slate-600 hover:text-slate-900'}">
              <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2a5 5 0 0 0-5 5v3a5 5 0 0 0 10 0V7a5 5 0 0 0-5-5z"></path><path d="M19 14h-2a5 5 0 0 0-10 0H5a3 3 0 0 0-3 3v4h20v-4a3 3 0 0 0-3-3z"></path><line x1="12" y1="11" x2="12" y2="19"></line></svg>
              Posterior (Back)
            </button>
          </div>

          <!-- SVG Anatomical Map Container -->
          <div class="relative w-full max-w-[280px] sm:max-w-[310px] aspect-[1/1.9] flex items-center justify-center p-2 rounded-2xl bg-gradient-to-b from-blue-50/50 via-slate-50/50 to-blue-50/30 border border-blue-50 shadow-inner">
            
            <!-- Grid Background Overlay -->
            <div class="absolute inset-0 bg-[radial-gradient(#93c5fd_1px,transparent_1px)] [background-size:16px_16px] opacity-40 rounded-2xl pointer-events-none"></div>
            
            <!-- Anatomical Scan Ring / Radar Line Animation -->
            <div class="scan-beam-line pointer-events-none"></div>

            <!-- SVG Vector Body -->
            <svg viewBox="0 0 300 560" class="w-full h-full drop-shadow-sm select-none" xmlns="http://www.w3.org/2000/svg">
              <defs>
                <!-- Glow Filters -->
                <filter id="bodyGlow" x="-20%" y="-20%" width="140%" height="140%">
                  <feGaussianBlur stdDeviation="5" result="blur" />
                  <feComposite in="SourceGraphic" in2="blur" operator="over" />
                </filter>
                <filter id="activePinGlow" x="-30%" y="-30%" width="160%" height="160%">
                  <feGaussianBlur stdDeviation="6" result="blur" />
                  <feMerge>
                    <feMergeNode in="blur" />
                    <feMergeNode in="SourceGraphic" />
                  </feMerge>
                </filter>
                <linearGradient id="bodyBaseGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%" stop-color="#E2E8F0" />
                  <stop offset="100%" stop-color="#CBD5E1" />
                </linearGradient>
                <linearGradient id="activeGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%" stop-color="#3B82F6" />
                  <stop offset="100%" stop-color="#1D4ED8" />
                </linearGradient>
              </defs>

              ${isFront ? this.renderFrontSVG(active) : this.renderBackSVG(active)}
            </svg>

            <!-- Floating Locator Radar Pin Overlay -->
            ${this.renderLocatorPin(active, isFront)}

          </div>

          <!-- Interaction Hint -->
          <div class="mt-4 text-center">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 border border-blue-200/60 rounded-full text-blue-700 text-xs font-medium">
              <svg class="w-3.5 h-3.5 text-blue-500 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/></svg>
              Click any body region or select shortcuts below
            </span>
          </div>

        </div>

        <!-- Right Side: Anatomical Details & Confirmation Panel -->
        <div class="flex-1 flex flex-col w-full space-y-4">
          
          <!-- Selected Region Header Card -->
          <div class="bg-gradient-to-br from-blue-50/90 via-sky-50/50 to-white p-5 rounded-xl border border-blue-200/80 shadow-sm relative overflow-hidden">
            <div class="absolute -right-8 -top-8 w-28 h-28 bg-blue-400/10 rounded-full blur-xl pointer-events-none"></div>
            
            <div class="flex items-start justify-between gap-3 mb-2">
              <div>
                <span class="inline-flex items-center gap-1 text-[11px] font-bold uppercase tracking-wider text-blue-600 bg-blue-100/80 px-2.5 py-0.5 rounded-md mb-1.5">
                  <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-ping"></span>
                  Detected Anatomical Region
                </span>
                <h3 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                  ${regionInfo.name}
                </h3>
              </div>
              <span class="px-3 py-1 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold rounded-full flex items-center gap-1">
                <svg class="w-3 h-3 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                ${regionInfo.confidence}
              </span>
            </div>

            <p class="text-xs text-slate-600 leading-relaxed mb-4">
              ${regionInfo.clinicalNotes}
            </p>

            <!-- Metrics Grid -->
            <div class="grid grid-cols-2 gap-3 pt-3 border-t border-blue-200/60">
              <div class="bg-white/90 p-2.5 rounded-lg border border-slate-100 shadow-2xs">
                <div class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">Estimated Sub-Region</div>
                <div class="text-xs font-semibold text-slate-700 mt-0.5 truncate">${regionInfo.subRegion}</div>
              </div>
              <div class="bg-white/90 p-2.5 rounded-lg border border-slate-100 shadow-2xs">
                <div class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">Related Body System</div>
                <div class="text-xs font-semibold text-blue-600 mt-0.5 flex items-center gap-1">
                  ${BODY_SYSTEMS_DATA[regionInfo.defaultSystem]?.icon || '🩺'} 
                  ${BODY_SYSTEMS_DATA[regionInfo.defaultSystem]?.name || 'General'}
                </div>
              </div>
            </div>
          </div>

          <!-- Quick Region Shortcut Pills -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wide">
              Quick Anatomical Selector:
            </label>
            <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
              ${this.renderQuickButtons(active)}
            </div>
          </div>

          <!-- Medical Disclaimer Box -->
          <div class="p-3.5 bg-amber-50/80 border border-amber-200/90 rounded-xl text-amber-900 text-xs leading-relaxed flex items-start gap-2.5">
            <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            <div>
              <span class="font-bold text-amber-950">Clinical Mapping Notice:</span>
              The highlighted location is an automated interpretation of your reported complaint. Your attending physician will conduct an in-person physical assessment.
            </div>
          </div>

        </div>

      </div>
    `;

    this.attachEvents();
  }

  renderFrontSVG(active) {
    const isAct = (key) => active === key;
    const getFill = (key) => isAct(key) ? 'url(#activeGrad)' : '#E2E8F0';
    const getStroke = (key) => isAct(key) ? '#1D4ED8' : '#94A3B8';
    const getFilter = (key) => isAct(key) ? 'filter="url(#bodyGlow)"' : '';
    const getCls = (key) => `body-part transition-all duration-300 cursor-pointer ${isAct(key) ? 'active-part stroke-2' : 'hover:fill-blue-200 hover:stroke-blue-400 stroke-[1.5]'}`;

    return `
      <!-- Head / Cranium -->
      <g data-part="head" class="${getCls('head')}">
        <path d="M 150,22 C 128,22 120,40 120,62 C 120,82 130,100 142,106 L 142,120 L 158,120 L 158,106 C 170,100 180,82 180,62 C 180,40 172,22 150,22 Z"
              fill="${getFill('head')}" stroke="${getStroke('head')}" ${getFilter('head')} />
        <!-- Head Features / Face detail -->
        <circle cx="138" cy="58" r="2.5" fill="${isAct('head') ? '#ffffff' : '#94A3B8'}" opacity="0.6" />
        <circle cx="162" cy="58" r="2.5" fill="${isAct('head') ? '#ffffff' : '#94A3B8'}" opacity="0.6" />
        <path d="M 145,78 Q 150,82 155,78" fill="none" stroke="${isAct('head') ? '#ffffff' : '#94A3B8'}" stroke-width="1.5" stroke-linecap="round" opacity="0.6" />
      </g>

      <!-- Neck & Thyroid Area -->
      <g data-part="neck" class="${getCls('neck')}">
        <path d="M 142,110 L 158,110 L 165,130 L 135,130 Z"
              fill="${getFill('neck')}" stroke="${getStroke('neck')}" ${getFilter('neck')} />
      </g>

      <!-- Chest / Thorax Area -->
      <g data-part="chest" class="${getCls('chest')}">
        <path d="M 135,130 L 165,130 L 195,142 L 190,205 L 110,205 L 105,142 Z"
              fill="${getFill('chest')}" stroke="${getStroke('chest')}" ${getFilter('chest')} />
        <!-- Ribcage / Sternum Subtle Lines -->
        <line x1="150" y1="135" x2="150" y2="200" stroke="${isAct('chest') ? '#93C5FD' : '#CBD5E1'}" stroke-width="1.5" stroke-dasharray="2 2" />
        <path d="M 125,160 Q 150,170 175,160" fill="none" stroke="${isAct('chest') ? '#93C5FD' : '#CBD5E1'}" stroke-width="1.5" opacity="0.7" />
        <path d="M 122,180 Q 150,190 178,180" fill="none" stroke="${isAct('chest') ? '#93C5FD' : '#CBD5E1'}" stroke-width="1.5" opacity="0.7" />
      </g>

      <!-- Abdomen / Stomach & GI Area -->
      <g data-part="abdomen" class="${getCls('abdomen')}">
        <path d="M 110,205 L 190,205 L 186,275 L 114,275 Z"
              fill="${getFill('abdomen')}" stroke="${getStroke('abdomen')}" ${getFilter('abdomen')} />
        <!-- Abdominal Quadrants Sub-lines -->
        <circle cx="150" cy="245" r="3" fill="${isAct('abdomen') ? '#ffffff' : '#94A3B8'}" opacity="0.8" />
        <line x1="150" y1="210" x2="150" y2="270" stroke="${isAct('abdomen') ? '#93C5FD' : '#CBD5E1'}" stroke-width="1" stroke-dasharray="2 2" />
        <line x1="120" y1="240" x2="180" y2="240" stroke="${isAct('abdomen') ? '#93C5FD' : '#CBD5E1'}" stroke-width="1" stroke-dasharray="2 2" />
      </g>

      <!-- Pelvis & Groin Area -->
      <g data-part="pelvis" class="${getCls('pelvis')}">
        <path d="M 114,275 L 186,275 L 178,320 L 150,332 L 122,320 Z"
              fill="${getFill('pelvis')}" stroke="${getStroke('pelvis')}" ${getFilter('pelvis')} />
      </g>

      <!-- Left Arm & Shoulder -->
      <g data-part="arms" class="${getCls('arms')}">
        <!-- Upper Arm / Bicep -->
        <path d="M 105,142 L 80,155 L 68,225 L 88,230 L 100,175 L 110,165 Z"
              fill="${getFill('arms')}" stroke="${getStroke('arms')}" ${getFilter('arms')} />
        <!-- Forearm -->
        <path d="M 68,225 L 56,290 L 74,295 L 88,230 Z"
              fill="${getFill('arms')}" stroke="${getStroke('arms')}" />
      </g>

      <!-- Right Arm & Shoulder -->
      <g data-part="arms" class="${getCls('arms')}">
        <!-- Upper Arm / Bicep -->
        <path d="M 195,142 L 220,155 L 232,225 L 212,230 L 200,175 L 190,165 Z"
              fill="${getFill('arms')}" stroke="${getStroke('arms')}" ${getFilter('arms')} />
        <!-- Forearm -->
        <path d="M 232,225 L 244,290 L 226,295 L 212,230 Z"
              fill="${getFill('arms')}" stroke="${getStroke('arms')}" />
      </g>

      <!-- Left Hand -->
      <g data-part="hands" class="${getCls('hands')}">
        <path d="M 56,290 L 46,335 C 46,345 56,350 62,342 L 74,295 Z"
              fill="${getFill('hands')}" stroke="${getStroke('hands')}" ${getFilter('hands')} />
      </g>

      <!-- Right Hand -->
      <g data-part="hands" class="${getCls('hands')}">
        <path d="M 244,290 L 254,335 C 254,345 244,350 238,342 L 226,295 Z"
              fill="${getFill('hands')}" stroke="${getStroke('hands')}" ${getFilter('hands')} />
      </g>

      <!-- Left Leg (Thigh, Knee, Shin) -->
      <g data-part="legs" class="${getCls('legs')}">
        <!-- Thigh -->
        <path d="M 122,320 L 150,332 L 144,420 L 112,420 L 114,275 Z"
              fill="${getFill('legs')}" stroke="${getStroke('legs')}" ${getFilter('legs')} />
        <!-- Knee & Shin / Calf -->
        <path d="M 112,420 L 144,420 L 140,505 L 116,505 Z"
              fill="${getFill('legs')}" stroke="${getStroke('legs')}" />
        <ellipse cx="128" cy="425" rx="10" ry="6" fill="${isAct('legs') ? '#93C5FD' : '#CBD5E1'}" opacity="0.6" />
      </g>

      <!-- Right Leg (Thigh, Knee, Shin) -->
      <g data-part="legs" class="${getCls('legs')}">
        <!-- Thigh -->
        <path d="M 178,320 L 150,332 L 156,420 L 188,420 L 186,275 Z"
              fill="${getFill('legs')}" stroke="${getStroke('legs')}" ${getFilter('legs')} />
        <!-- Knee & Shin / Calf -->
        <path d="M 156,420 L 188,420 L 184,505 L 160,505 Z"
              fill="${getFill('legs')}" stroke="${getStroke('legs')}" />
        <ellipse cx="172" cy="425" rx="10" ry="6" fill="${isAct('legs') ? '#93C5FD' : '#CBD5E1'}" opacity="0.6" />
      </g>

      <!-- Left Foot & Ankle -->
      <g data-part="feet" class="${getCls('feet')}">
        <path d="M 116,505 L 140,505 L 142,545 C 142,552 110,555 106,545 L 116,505 Z"
              fill="${getFill('feet')}" stroke="${getStroke('feet')}" ${getFilter('feet')} />
      </g>

      <!-- Right Foot & Ankle -->
      <g data-part="feet" class="${getCls('feet')}">
        <path d="M 160,505 L 184,505 L 194,545 C 194,552 158,555 158,545 L 160,505 Z"
              fill="${getFill('feet')}" stroke="${getStroke('feet')}" ${getFilter('feet')} />
      </g>
    `;
  }

  renderBackSVG(active) {
    const isAct = (key) => active === key;
    const getFill = (key) => isAct(key) ? 'url(#activeGrad)' : '#E2E8F0';
    const getStroke = (key) => isAct(key) ? '#1D4ED8' : '#94A3B8';
    const getFilter = (key) => isAct(key) ? 'filter="url(#bodyGlow)"' : '';
    const getCls = (key) => `body-part transition-all duration-300 cursor-pointer ${isAct(key) ? 'active-part stroke-2' : 'hover:fill-blue-200 hover:stroke-blue-400 stroke-[1.5]'}`;

    return `
      <!-- Occipital Head (Back) -->
      <g data-part="head" class="${getCls('head')}">
        <path d="M 150,22 C 128,22 120,40 120,62 C 120,82 130,100 142,106 L 142,120 L 158,120 L 158,106 C 170,100 180,82 180,62 C 180,40 172,22 150,22 Z"
              fill="${getFill('head')}" stroke="${getStroke('head')}" ${getFilter('head')} />
      </g>

      <!-- Posterior Cervical Spine / Neck -->
      <g data-part="neck" class="${getCls('neck')}">
        <path d="M 142,110 L 158,110 L 165,130 L 135,130 Z"
              fill="${getFill('neck')}" stroke="${getStroke('neck')}" ${getFilter('neck')} />
      </g>

      <!-- Back / Thoracic & Lumbar Spine -->
      <g data-part="back" class="${getCls('back')}">
        <!-- Upper & Lower Back Unified Block -->
        <path d="M 135,130 L 165,130 L 195,142 L 186,275 L 114,275 L 105,142 Z"
              fill="${getFill('back')}" stroke="${getStroke('back')}" ${getFilter('back')} />
        <!-- Spine Vertebrae Central Line & Scapulae Lines -->
        <line x1="150" y1="130" x2="150" y2="275" stroke="${isAct('back') ? '#93C5FD' : '#CBD5E1'}" stroke-width="2.5" stroke-dasharray="3 3" />
        <!-- Left Scapula -->
        <path d="M 125,145 Q 118,170 135,185" fill="none" stroke="${isAct('back') ? '#93C5FD' : '#CBD5E1'}" stroke-width="1.5" />
        <!-- Right Scapula -->
        <path d="M 175,145 Q 182,170 165,185" fill="none" stroke="${isAct('back') ? '#93C5FD' : '#CBD5E1'}" stroke-width="1.5" />
      </g>

      <!-- Gluteal / Pelvis Back -->
      <g data-part="pelvis" class="${getCls('pelvis')}">
        <path d="M 114,275 L 186,275 L 180,328 L 150,332 L 120,328 Z"
              fill="${getFill('pelvis')}" stroke="${getStroke('pelvis')}" ${getFilter('pelvis')} />
        <line x1="150" y1="280" x2="150" y2="330" stroke="${isAct('pelvis') ? '#93C5FD' : '#CBD5E1'}" stroke-width="1.5" />
      </g>

      <!-- Left Arm Back -->
      <g data-part="arms" class="${getCls('arms')}">
        <path d="M 105,142 L 80,155 L 68,225 L 88,230 L 100,175 L 110,165 Z"
              fill="${getFill('arms')}" stroke="${getStroke('arms')}" ${getFilter('arms')} />
        <path d="M 68,225 L 56,290 L 74,295 L 88,230 Z"
              fill="${getFill('arms')}" stroke="${getStroke('arms')}" />
      </g>

      <!-- Right Arm Back -->
      <g data-part="arms" class="${getCls('arms')}">
        <path d="M 195,142 L 220,155 L 232,225 L 212,230 L 200,175 L 190,165 Z"
              fill="${getFill('arms')}" stroke="${getStroke('arms')}" ${getFilter('arms')} />
        <path d="M 232,225 L 244,290 L 226,295 L 212,230 Z"
              fill="${getFill('arms')}" stroke="${getStroke('arms')}" />
      </g>

      <!-- Left Hand Back -->
      <g data-part="hands" class="${getCls('hands')}">
        <path d="M 56,290 L 46,335 C 46,345 56,350 62,342 L 74,295 Z"
              fill="${getFill('hands')}" stroke="${getStroke('hands')}" ${getFilter('hands')} />
      </g>

      <!-- Right Hand Back -->
      <g data-part="hands" class="${getCls('hands')}">
        <path d="M 244,290 L 254,335 C 254,345 244,350 238,342 L 226,295 Z"
              fill="${getFill('hands')}" stroke="${getStroke('hands')}" ${getFilter('hands')} />
      </g>

      <!-- Posterior Legs (Hamstrings, Calves) -->
      <g data-part="legs" class="${getCls('legs')}">
        <path d="M 120,328 L 150,332 L 144,420 L 112,420 Z"
              fill="${getFill('legs')}" stroke="${getStroke('legs')}" ${getFilter('legs')} />
        <path d="M 112,420 L 144,420 L 140,505 L 116,505 Z"
              fill="${getFill('legs')}" stroke="${getStroke('legs')}" />
        <path d="M 180,328 L 150,332 L 156,420 L 188,420 Z"
              fill="${getFill('legs')}" stroke="${getStroke('legs')}" ${getFilter('legs')} />
        <path d="M 156,420 L 188,420 L 184,505 L 160,505 Z"
              fill="${getFill('legs')}" stroke="${getStroke('legs')}" />
      </g>

      <!-- Posterior Feet (Heels & Achilles) -->
      <g data-part="feet" class="${getCls('feet')}">
        <path d="M 116,505 L 140,505 L 138,545 C 138,552 114,555 110,545 L 116,505 Z"
              fill="${getFill('feet')}" stroke="${getStroke('feet')}" ${getFilter('feet')} />
        <path d="M 160,505 L 184,505 L 190,545 C 190,552 162,555 162,545 L 160,505 Z"
              fill="${getFill('feet')}" stroke="${getStroke('feet')}" ${getFilter('feet')} />
      </g>
    `;
  }

  renderLocatorPin(active, isFront) {
    // Pin coordinates relative to % in SVG container
    const PIN_COORDS = {
      head: { top: '12%', left: '50%' },
      neck: { top: '22%', left: '50%' },
      chest: { top: '32%', left: '50%' },
      abdomen: { top: '44%', left: '50%' },
      back: { top: '38%', left: '50%' },
      pelvis: { top: '56%', left: '50%' },
      arms: { top: '38%', left: '26%' },
      hands: { top: '58%', left: '19%' },
      legs: { top: '72%', left: '42%' },
      feet: { top: '93%', left: '42%' },
      whole_body: { top: '50%', left: '50%' },
      other: { top: '50%', left: '50%' }
    };

    const coords = PIN_COORDS[active] || { top: '44%', left: '50%' };

    return `
      <div class="absolute -translate-x-1/2 -translate-y-1/2 pointer-events-none transition-all duration-500 ease-out"
           style="top: ${coords.top}; left: ${coords.left};">
        <!-- Pulsing radar rings -->
        <span class="absolute -inset-3 rounded-full bg-blue-500/20 animate-ping"></span>
        <span class="absolute -inset-1.5 rounded-full bg-blue-500/40 animate-pulse"></span>
        <!-- Central Glow Pin -->
        <div class="relative w-4 h-4 rounded-full bg-blue-600 border-2 border-white shadow-lg flex items-center justify-center">
          <div class="w-1.5 h-1.5 rounded-full bg-white"></div>
        </div>
      </div>
    `;
  }

  renderQuickButtons(active) {
    const buttons = [
      { id: 'head', label: 'Head & Brain' },
      { id: 'neck', label: 'Neck & Throat' },
      { id: 'chest', label: 'Chest & Heart' },
      { id: 'abdomen', label: 'Abdomen (Stomach)' },
      { id: 'back', label: 'Back & Spine' },
      { id: 'arms', label: 'Arms & Shoulders' },
      { id: 'hands', label: 'Hands & Wrists' },
      { id: 'pelvis', label: 'Pelvis & Groin' },
      { id: 'legs', label: 'Legs & Knees' },
      { id: 'feet', label: 'Feet & Ankles' },
      { id: 'whole_body', label: 'Whole Body' },
      { id: 'other', label: 'Other' }
    ];

    return buttons.map(btn => {
      const isSelected = btn.id === active;
      return `
        <button type="button" 
                data-region="${btn.id}"
                class="quick-region-btn px-2.5 py-2 rounded-xl text-xs font-semibold transition-all duration-150 text-left truncate flex items-center justify-between border ${
                  isSelected 
                    ? 'bg-blue-600 text-white border-blue-600 shadow-md shadow-blue-500/20 ring-2 ring-blue-300/40' 
                    : 'bg-white hover:bg-blue-50/70 text-slate-700 border-slate-200/90 hover:border-blue-200 shadow-2xs'
                }">
          <span class="truncate">${btn.label}</span>
          ${isSelected ? '<svg class="w-3 h-3 text-white shrink-0 ml-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>' : ''}
        </button>
      `;
    }).join('');
  }

  attachEvents() {
    // View Switchers
    const btnFront = this.container.querySelector('#btn-view-front');
    const btnBack = this.container.querySelector('#btn-view-back');
    
    if (btnFront) btnFront.addEventListener('click', () => this.setView('front'));
    if (btnBack) btnBack.addEventListener('click', () => this.setView('back'));

    // SVG Body Part Clicks
    const bodyParts = this.container.querySelectorAll('.body-part');
    bodyParts.forEach(el => {
      el.addEventListener('click', (e) => {
        const part = el.getAttribute('data-part');
        if (part) {
          this.setRegion(part);
        }
      });
    });

    // Quick Region Selector Buttons
    const quickBtns = this.container.querySelectorAll('.quick-region-btn');
    quickBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        const region = btn.getAttribute('data-region');
        if (region) {
          this.setRegion(region);
        }
      });
    });
  }
}
