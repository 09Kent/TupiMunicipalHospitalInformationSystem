<?php
// Doctor/includes/anatomy_model.php

/**
 * Render Interactive Specialty / Patient Anatomy Body Model
 * 
 * @param string $highlightTarget (e.g., 'cardio', 'neuro', 'gastro', 'pulmo', 'ortho', 'derma', 'uro', 'head', 'chest', 'abdomen', etc.)
 * @param string $title
 * @param string $subtitle
 * @param bool $isInteractive
 */
function render_anatomy_model(string $highlightTarget = 'cardio', string $title = 'Body-System Insight', string $subtitle = 'Specialty Anatomy Model', bool $isInteractive = true): void
{
    $target = strtolower(trim($highlightTarget));
    
    // Normalize target
    $isCardio = in_array($target, ['cardio', 'cardiovascular', 'heart', 'cardiovascular system', '2']);
    $isNeuro  = in_array($target, ['neuro', 'nervous', 'brain', 'head', 'nervous system', '3']);
    $isPulmo  = in_array($target, ['pulmo', 'respiratory', 'lungs', 'throat', 'respiratory system', '4']);
    $isGastro = in_array($target, ['gastro', 'digestive', 'stomach', 'abdomen', 'digestive system', '1', '5']);
    $isOrtho  = in_array($target, ['ortho', 'musculoskeletal', 'bone', 'back', 'spine', 'joints', 'knee', '5', '6']);
    $isDerma  = in_array($target, ['derma', 'skin', 'integumentary', 'integumentary system', '6', '9']);
    $isUro    = in_array($target, ['uro', 'urinary', 'kidney', 'nephro', 'urinary system', '7', '11', '15']);
    $isPedia  = in_array($target, ['pedia', 'pediatrician', 'child', '7']);
    $isGeneral= in_array($target, ['gp', 'general', 'general practitioner', '1', '8']);

    // Default to Cardio if unknown or matching cardio
    if (!$isCardio && !$isNeuro && !$isPulmo && !$isGastro && !$isOrtho && !$isDerma && !$isUro && !$isPedia) {
        $isCardio = true;
    }

    $systemLabel = 'Cardiovascular Region';
    $systemColor = '#ef4444';
    $systemBadge = '🫀 Heart & Arteries Active';
    $systemDesc = 'Real-time focus: Myocardium, Coronary Arteries, Aorta, Thoracic Circulation';

    if ($isNeuro) {
        $systemLabel = 'Cranial & Nervous System';
        $systemColor = '#8b5cf6';
        $systemBadge = '🧠 Brain & Neural Paths';
        $systemDesc = 'Real-time focus: Cerebral Cortex, Cranial Nerves, Spinal Column';
    } elseif ($isPulmo) {
        $systemLabel = 'Respiratory & Pulmonary';
        $systemColor = '#06b6d4';
        $systemBadge = '🫁 Lungs & Bronchial Tree';
        $systemDesc = 'Real-time focus: Left/Right Lobes, Trachea, Gas Exchange';
    } elseif ($isGastro) {
        $systemLabel = 'Gastrointestinal System';
        $systemColor = '#f59e0b';
        $systemBadge = '🩺 Digestive & Visceral';
        $systemDesc = 'Real-time focus: Epigastrium, Gastric Mucosa, Intestinal Tract';
    } elseif ($isOrtho) {
        $systemLabel = 'Musculoskeletal Skeleton';
        $systemColor = '#10b981';
        $systemBadge = '🦴 Bones & Articular Joints';
        $systemDesc = 'Real-time focus: Spine Column, Pelvic Girdle, Patella & Appendages';
    } elseif ($isDerma) {
        $systemLabel = 'Integumentary Dermis';
        $systemColor = '#ec4899';
        $systemBadge = '🧴 Cutaneous Dermal Layer';
        $systemDesc = 'Real-time focus: Epidermis, Cutaneous Nerves, Skin Barrier';
    } elseif ($isUro) {
        $systemLabel = 'Renal & Urinary System';
        $systemColor = '#6366f1';
        $systemBadge = '🧪 Kidneys & Bladder';
        $systemDesc = 'Real-time focus: Bilateral Kidneys, Ureters, Fluid Clearance';
    }
    ?>
    
    <div class="bg-gradient-to-b from-slate-900 to-slate-950 text-white rounded-3xl p-6 shadow-2xl relative overflow-hidden border border-slate-800">
      
      <!-- Ambient Background Glows -->
      <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full blur-3xl opacity-20 pointer-events-none" style="background-color: <?= $systemColor ?>;"></div>
      <div class="absolute -bottom-20 -left-20 w-64 h-64 rounded-full blur-3xl opacity-15 pointer-events-none bg-blue-500"></div>

      <!-- Header -->
      <div class="relative z-10 flex items-center justify-between mb-4">
        <div>
          <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full animate-ping" style="background-color: <?= $systemColor ?>;"></span>
            <h3 class="text-sm font-extrabold tracking-wide uppercase text-slate-300 font-display"><?= e($title) ?></h3>
          </div>
          <p class="text-xs text-slate-400 font-medium"><?= e($subtitle) ?></p>
        </div>
        <span class="px-2.5 py-1 text-[11px] font-bold rounded-xl bg-slate-800/80 border border-slate-700 text-slate-200 flex items-center gap-1.5 shadow-xs">
          <?= $systemBadge ?>
        </span>
      </div>

      <!-- Human Anatomy Interactive Canvas -->
      <div class="relative z-10 flex flex-col items-center justify-center my-3 py-2">
        
        <div class="relative w-52 h-72 flex items-center justify-center">
          
          <!-- Base Human Body Silhouette SVG -->
          <svg class="w-full h-full drop-shadow-md text-slate-800" viewBox="0 0 200 320" fill="none" xmlns="http://www.w3.org/2000/svg">
            
            <!-- Grid Background Overlay -->
            <defs>
              <pattern id="anatomyGrid" width="10" height="10" patternUnits="userSpaceOnUse">
                <path d="M 10 0 L 0 0 0 10" fill="none" stroke="#1e293b" stroke-width="0.5"/>
              </pattern>
              <!-- Glowing filters -->
              <filter id="glow-cardio" x="-20%" y="-20%" width="140%" height="140%">
                <feGaussianBlur stdDeviation="3" result="blur" />
                <feMerge>
                  <feMergeNode in="blur"/>
                  <feMergeNode in="SourceGraphic"/>
                </feMerge>
              </filter>
            </defs>
            <rect width="200" height="320" fill="url(#anatomyGrid)" opacity="0.4" rx="16"/>

            <!-- Human Body Contours (Semi-transparent stylized medical silhouette) -->
            <!-- Head -->
            <path d="M100 20 C85 20 80 32 80 48 C80 62 88 74 100 74 C112 74 120 62 120 48 C120 32 115 20 100 20 Z" 
                  fill="#1e293b" stroke="#334155" stroke-width="1.5" />
            <!-- Neck -->
            <path d="M93 74 L93 84 L107 84 L107 74 Z" fill="#1e293b" stroke="#334155" stroke-width="1.5"/>
            <!-- Torso / Chest / Abdomen -->
            <path d="M60 92 C60 85 70 84 93 84 L107 84 C130 84 140 85 140 92 L148 135 C149 145 138 185 132 205 L68 205 C62 185 51 145 52 135 Z" 
                  fill="#1e293b" stroke="#334155" stroke-width="1.5"/>
            <!-- Arms -->
            <!-- Left Arm -->
            <path d="M56 94 C48 105 38 135 34 165 C30 190 28 215 26 235 C24 242 28 245 32 242 C38 225 44 195 48 165 L54 135 Z" 
                  fill="#1e293b" stroke="#334155" stroke-width="1.5"/>
            <!-- Right Arm -->
            <path d="M144 94 C152 105 162 135 166 165 C170 190 172 215 174 235 C176 242 172 245 168 242 C162 225 156 195 152 165 L146 135 Z" 
                  fill="#1e293b" stroke="#334155" stroke-width="1.5"/>
            <!-- Pelvis -->
            <path d="M68 205 C75 220 85 225 100 225 C115 225 125 220 132 205 Z" fill="#1e293b" stroke="#334155" stroke-width="1.5"/>
            <!-- Left Leg -->
            <path d="M72 218 C72 235 70 270 70 295 C70 305 65 315 65 318 C72 318 80 318 82 310 C86 285 90 250 92 225 Z" 
                  fill="#1e293b" stroke="#334155" stroke-width="1.5"/>
            <!-- Right Leg -->
            <path d="M128 218 C128 235 130 270 130 295 C130 305 135 315 135 318 C128 318 120 318 118 310 C114 285 110 250 108 225 Z" 
                  fill="#1e293b" stroke="#334155" stroke-width="1.5"/>

            <!-- ================= DYNAMIC SPECIALTY HIGHLIGHT LAYERS ================= -->

            <!-- 1. CARDIOVASCULAR (Heart & Arteries) -->
            <?php if ($isCardio): ?>
              <!-- Pulsing Heart Organ -->
              <g class="animate-heart-beat">
                <!-- Aorta & Vascular Arches -->
                <path d="M98 108 C98 98 108 96 112 102 C115 107 114 115 104 120" stroke="#ef4444" stroke-width="3" stroke-linecap="round" fill="none"/>
                <path d="M94 112 C90 105 84 105 82 110 C80 116 86 122 96 126" stroke="#3b82f6" stroke-width="2.5" stroke-linecap="round" fill="none"/>
                <!-- Heart Body -->
                <path d="M102 115 C102 110 110 106 116 112 C122 118 120 128 108 138 C100 130 96 122 96 116 C96 110 102 110 102 115 Z" 
                      fill="#ef4444" stroke="#f87171" stroke-width="2" filter="url(#glow-cardio)"/>
                <!-- Center pulse core -->
                <circle cx="106" cy="120" r="3" fill="#ffffff" class="animate-ping"/>
                <!-- Arterial branches -->
                <path d="M108 138 L108 170 M108 170 L98 190 M108 170 L118 190" stroke="#ef4444" stroke-width="1.5" stroke-dasharray="3,3" opacity="0.8"/>
              </g>
            <?php endif; ?>

            <!-- 2. NEUROLOGICAL (Brain & Nerves) -->
            <?php if ($isNeuro): ?>
              <g class="animate-neuro-glow">
                <!-- Brain Hemispheres -->
                <path d="M100 24 C88 24 84 34 84 46 C84 56 90 64 100 64 C110 64 116 56 116 46 C116 34 112 24 100 24 Z" 
                      fill="#8b5cf6" stroke="#c084fc" stroke-width="2"/>
                <!-- Neural Sulci & Gyri Lines -->
                <path d="M92 34 C98 38 94 46 90 50 M108 34 C102 38 106 46 110 50 M100 28 L100 60" 
                      stroke="#ffffff" stroke-width="1.5" stroke-linecap="round" opacity="0.9"/>
                <!-- Brainstem & Spine Cord -->
                <path d="M100 64 L100 150" stroke="#a855f7" stroke-width="2.5" stroke-dasharray="4,2"/>
                <circle cx="100" cy="42" r="3" fill="#ffffff" class="animate-ping"/>
              </g>
            <?php endif; ?>

            <!-- 3. PULMONARY (Lungs & Bronchial Tree) -->
            <?php if ($isPulmo): ?>
              <g class="animate-pulmo-breath">
                <!-- Trachea -->
                <path d="M100 82 L100 102 M100 102 L92 110 M100 102 L108 110" stroke="#06b6d4" stroke-width="2.5" stroke-linecap="round"/>
                <!-- Left Lung -->
                <path d="M88 104 C78 106 72 118 72 134 C72 146 80 152 90 148 C94 142 94 120 88 104 Z" 
                      fill="#06b6d4" stroke="#67e8f9" stroke-width="2" opacity="0.9"/>
                <!-- Right Lung -->
                <path d="M112 104 C122 106 128 118 128 134 C128 146 120 152 110 148 C106 142 106 120 112 104 Z" 
                      fill="#06b6d4" stroke="#67e8f9" stroke-width="2" opacity="0.9"/>
                <circle cx="82" cy="128" r="2.5" fill="#ffffff" class="animate-ping"/>
                <circle cx="118" cy="128" r="2.5" fill="#ffffff" class="animate-ping"/>
              </g>
            <?php endif; ?>

            <!-- 4. GASTROENTEROLOGY (Stomach, Liver & Intestines) -->
            <?php if ($isGastro): ?>
              <g class="animate-gastro-glow">
                <!-- Stomach J-shape -->
                <path d="M96 130 C108 128 118 134 116 148 C114 160 98 165 92 155 C88 145 90 134 96 130 Z" 
                      fill="#f59e0b" stroke="#fbbf24" stroke-width="2"/>
                <!-- Liver outline -->
                <path d="M78 128 C86 122 96 124 96 132 L92 144 C84 144 76 138 78 128 Z" fill="#d97706" opacity="0.85"/>
                <!-- Intestinal coil -->
                <path d="M88 165 C112 165 112 175 88 175 C112 175 112 185 88 185" stroke="#f59e0b" stroke-width="2.5" stroke-linecap="round" fill="none"/>
                <circle cx="104" cy="144" r="3" fill="#ffffff" class="animate-ping"/>
              </g>
            <?php endif; ?>

            <!-- 5. ORTHOPEDIC (Skeleton, Spine & Joint Articulations) -->
            <?php if ($isOrtho): ?>
              <g>
                <!-- Spine column -->
                <path d="M100 84 L100 205" stroke="#10b981" stroke-width="3" stroke-dasharray="2,3"/>
                <!-- Clavicle -->
                <path d="M70 90 L130 90" stroke="#34d399" stroke-width="2" stroke-linecap="round"/>
                <!-- Pelvic arch -->
                <path d="M78 205 C90 215 110 215 122 205" stroke="#10b981" stroke-width="3" fill="none"/>
                <!-- Highlight Knee & Shoulder Joints with glowing nodes -->
                <circle cx="68" cy="94" r="4" fill="#34d399" class="animate-pulse"/>
                <circle cx="132" cy="94" r="4" fill="#34d399" class="animate-pulse"/>
                <circle cx="76" cy="270" r="5" fill="#10b981" stroke="#ffffff" stroke-width="1.5" class="animate-ping"/>
                <circle cx="124" cy="270" r="4" fill="#34d399" class="animate-pulse"/>
              </g>
            <?php endif; ?>

            <!-- 6. UROLOGY / NEPHROLOGY -->
            <?php if ($isUro): ?>
              <g>
                <!-- Bilateral Kidneys -->
                <path d="M84 140 C88 140 90 146 88 152 C86 158 80 156 80 150 C80 144 82 140 84 140 Z" fill="#6366f1" stroke="#a5b4fc" stroke-width="1.5"/>
                <path d="M116 140 C120 140 120 146 118 152 C116 158 110 156 110 150 C110 144 112 140 116 140 Z" fill="#6366f1" stroke="#a5b4fc" stroke-width="1.5"/>
                <!-- Ureters to Bladder -->
                <path d="M86 152 L96 195 M114 152 L104 195" stroke="#818cf8" stroke-width="1.5" stroke-dasharray="2,2"/>
                <ellipse cx="100" cy="198" rx="8" ry="6" fill="#6366f1" stroke="#c7d2fe" stroke-width="1.5"/>
                <circle cx="100" cy="198" r="2" fill="#ffffff" class="animate-ping"/>
              </g>
            <?php endif; ?>

            <!-- 7. DERMATOLOGY -->
            <?php if ($isDerma): ?>
              <g>
                <circle cx="100" cy="120" r="18" fill="none" stroke="#ec4899" stroke-width="2" stroke-dasharray="4,4" class="animate-spin" style="animation-duration: 10s;"/>
                <circle cx="100" cy="120" r="8" fill="#ec4899" opacity="0.6"/>
                <circle cx="100" cy="120" r="3" fill="#ffffff" class="animate-ping"/>
              </g>
            <?php endif; ?>

          </svg>

        </div>

      </div>

      <!-- Footer Info -->
      <div class="relative z-10 pt-3 border-t border-slate-800 flex items-center justify-between text-xs">
        <div>
          <p class="text-slate-400 font-medium text-[11px]"><?= e($systemDesc) ?></p>
        </div>
        <div class="flex items-center gap-1 text-[11px] font-bold text-slate-300">
          <span class="w-1.5 h-1.5 rounded-full" style="background-color: <?= $systemColor ?>;"></span>
          <span>Matched to Clinical Queue</span>
        </div>
      </div>

    </div>

    <?php
}
