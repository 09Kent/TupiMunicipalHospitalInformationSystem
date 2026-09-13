<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tupi Municipal Hospital Information Management System | South Cotabato</title>
    <meta name="description" content="Official portal for Tupi Municipal Hospital in South Cotabato, Philippines. Access hospital services, medical departments, patient resources, health announcements, and emergency contacts.">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: {
                            800: '#0c2340',
                            900: '#0a192f',
                        },
                        gov: {
                            blue: '#1d4ed8',
                            light: '#eff6ff',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- AOS Animation CSS CDN -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css" />
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-blue-600 selection:text-white flex flex-col min-h-screen">

    <!-- Sticky Navigation Header -->
    <header id="navbar" class="sticky-header fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-100 transition-all duration-300">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20 gap-4">
                
                <!-- Logo & Branding -->
                <a href="#home" class="flex items-center gap-3 group shrink-0">
                    <img src="{{ asset('assets/logo.png') }}" alt="Tupi Municipal Hospital Seal" class="h-11 md:h-12 w-auto transition-transform duration-300 group-hover:scale-105">
                    <div>
                        <span class="block font-extrabold text-slate-900 text-base sm:text-lg lg:text-xl tracking-tight leading-none group-hover:text-blue-600 transition-colors whitespace-nowrap">
                            Tupi Municipal Hospital
                        </span>
                        <span class="block text-[11px] sm:text-xs font-semibold text-blue-600 tracking-wider uppercase mt-0.5 whitespace-nowrap">
                            Information Management System
                        </span>
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden lg:flex items-center gap-5 xl:gap-8 shrink-0">
                    <a href="#home" class="nav-link text-sm font-semibold text-slate-700 hover:text-blue-600 transition-colors whitespace-nowrap">Home</a>
                    <a href="#about" class="nav-link text-sm font-semibold text-slate-700 hover:text-blue-600 transition-colors whitespace-nowrap">About Us</a>
                    <a href="#services" class="nav-link text-sm font-semibold text-slate-700 hover:text-blue-600 transition-colors whitespace-nowrap">Services</a>
                    <a href="#departments" class="nav-link text-sm font-semibold text-slate-700 hover:text-blue-600 transition-colors whitespace-nowrap">Departments</a>
                    <a href="#contact" class="nav-link text-sm font-semibold text-slate-700 hover:text-blue-600 transition-colors whitespace-nowrap">Contact Us</a>
                </nav>

                <!-- Right Action & Hotline -->
                <div class="hidden md:flex items-center gap-4 xl:gap-6 shrink-0">
                    <div class="flex items-center gap-3 border-r border-slate-200 pr-4 xl:pr-6">
                        <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <i data-lucide="phone" class="w-5 h-5"></i>
                        </div>
                        <div class="whitespace-nowrap">
                            <span class="block text-xs font-medium text-slate-500">Hospital Hotline</span>
                            <a href="tel:+630832280001" class="block text-sm font-bold text-slate-900 hover:text-blue-600 transition-colors whitespace-nowrap">
                                +63 (083) 228-0001
                            </a>
                        </div>
                    </div>
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-5 xl:px-6 py-2.5 xl:py-3 rounded-full bg-blue-600 text-white font-semibold text-sm whitespace-nowrap shadow-md shadow-blue-500/20 hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-500/30 transition-all duration-200">
                        Book Appointment
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex lg:hidden items-center gap-3">
                    <a href="{{ route('login') }}" class="md:hidden px-4 py-2 rounded-full bg-blue-600 text-white text-xs font-semibold whitespace-nowrap">
                        Book Now
                    </a>
                    <button id="menuButton" type="button" class="p-2.5 rounded-xl text-slate-700 hover:bg-slate-100 focus:outline-none" aria-label="Toggle navigation menu">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                </div>

            </div>
        </div>

        <!-- Mobile Navigation Panel -->
        <div id="mobileMenu" class="hidden lg:hidden bg-white border-b border-slate-200 px-4 pt-2 pb-6 space-y-3 shadow-xl">
            <a href="#home" class="mobile-menu-link block px-4 py-2.5 rounded-xl text-base font-semibold text-slate-800 hover:bg-blue-50 hover:text-blue-600">Home</a>
            <a href="#about" class="mobile-menu-link block px-4 py-2.5 rounded-xl text-base font-semibold text-slate-800 hover:bg-blue-50 hover:text-blue-600">About Us</a>
            <a href="#services" class="mobile-menu-link block px-4 py-2.5 rounded-xl text-base font-semibold text-slate-800 hover:bg-blue-50 hover:text-blue-600">Services</a>
            <a href="#departments" class="mobile-menu-link block px-4 py-2.5 rounded-xl text-base font-semibold text-slate-800 hover:bg-blue-50 hover:text-blue-600">Departments</a>
            <a href="#contact" class="mobile-menu-link block px-4 py-2.5 rounded-xl text-base font-semibold text-slate-800 hover:bg-blue-50 hover:text-blue-600">Contact Us</a>
            <div class="pt-4 border-t border-slate-100 flex flex-col gap-3">
                <div class="flex items-center gap-3 px-4">
                    <i data-lucide="phone" class="w-5 h-5 text-blue-600"></i>
                    <div>
                        <span class="block text-xs text-slate-500">Hospital Hotline</span>
                        <span class="text-sm font-bold text-slate-800 whitespace-nowrap">+63 (083) 228-0001</span>
                    </div>
                </div>
                <a href="{{ route('login') }}" class="mobile-menu-link text-center px-4 py-3 rounded-xl bg-blue-600 text-white font-semibold text-sm">
                    Book Appointment
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow pt-20">

        <!-- 1. Hero Section -->
        <section id="home" class="relative bg-slate-50 overflow-hidden pt-8 pb-16 lg:pt-16 lg:pb-32">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                    
                    <!-- Left Hero Content -->
                    <div class="lg:col-span-6 space-y-6 lg:space-y-8" data-aos="fade-right">
                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-100/80 text-blue-700 text-xs font-bold tracking-wide uppercase">
                            <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                            Tupi Municipal Hospital, South Cotabato
                        </div>
                        
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-extrabold text-slate-900 tracking-tight leading-[1.1]">
                            Your Health,<br>
                            <span class="text-blue-600">Our Priority</span>
                        </h1>
                        
                        <div class="space-y-3 text-slate-600 text-base sm:text-lg leading-relaxed max-w-xl">
                            <p>
                                Compassionate care. Advanced technology. Your one-stop healthcare platform for medical services, patient resources, and health information.
                            </p>
                            <p class="font-medium text-slate-700">
                                Better health for a better tomorrow in Tupi, South Cotabato.
                            </p>
                        </div>

                        <!-- CTA Buttons -->
                        <div class="flex flex-wrap items-center gap-4 pt-2" data-aos="fade-up" data-aos-delay="200">
                            <a href="{{ route('login') }}" class="inline-flex items-center gap-3 px-8 py-4 rounded-full bg-blue-600 text-white font-bold text-base shadow-lg shadow-blue-600/25 hover:bg-blue-700 hover:shadow-xl hover:shadow-blue-600/35 transition-all duration-200 group">
                                <span>Book Appointment</span>
                                <i data-lucide="arrow-right" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
                            </a>
                            <a href="#about" class="inline-flex items-center gap-3 px-8 py-4 rounded-full bg-white text-slate-700 border border-slate-200 font-semibold text-base shadow-sm hover:bg-slate-50 hover:border-slate-300 transition-all duration-200">
                                <i data-lucide="play-circle" class="w-5 h-5 text-blue-600"></i>
                                <span>Our Services</span>
                            </a>
                        </div>
                    </div>

                    <!-- Right Hero Image (Hospital Building) -->
                    <div class="lg:col-span-6 relative" data-aos="fade-left">
                        <div class="relative rounded-3xl overflow-hidden shadow-2xl border border-slate-200/80 aspect-[4/3] lg:aspect-[16/11]">
                            <img src="{{ asset('assets/tupi-municipal-hall.jpg') }}" alt="Tupi Municipal Hospital in Tupi, South Cotabato" class="w-full h-full object-cover">
                            <div class="absolute inset-y-0 left-0 w-1/3 bg-gradient-to-r from-slate-50 via-slate-50/40 to-transparent hidden lg:block"></div>
                            
                            <div class="absolute bottom-4 right-4 bg-white/90 backdrop-blur-md px-4 py-2 rounded-xl border border-white/50 shadow-md text-xs font-semibold text-slate-700 flex items-center gap-2">
                                <i data-lucide="building-2" class="w-4 h-4 text-blue-600"></i>
                                Tupi Municipal Hospital
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Floating 4-Column Feature Bar -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 lg:-mb-16 relative z-30" data-aos="fade-up" data-aos-delay="300">
                <div class="bg-white rounded-3xl shadow-xl border border-slate-200/80 p-6 md:p-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-0 divide-y sm:divide-y-0 lg:divide-x divide-slate-100">
                    
                    <!-- 1. Expert Doctors -->
                    <div class="flex items-start gap-4 lg:px-6 first:lg:pl-0">
                        <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl shrink-0">
                            <i data-lucide="stethoscope" class="w-7 h-7"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 text-lg mb-1">Expert Doctors</h3>
                            <p class="text-sm text-slate-500 leading-snug">Experienced specialists providing the best care for every patient.</p>
                        </div>
                    </div>

                    <!-- 2. Advanced Care -->
                    <div class="flex items-start gap-4 pt-6 sm:pt-0 lg:px-6">
                        <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl shrink-0">
                            <i data-lucide="microscope" class="w-7 h-7"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 text-lg mb-1">Advanced Care</h3>
                            <p class="text-sm text-slate-500 leading-snug">State-of-the-art technology for accurate diagnosis and treatment.</p>
                        </div>
                    </div>

                    <!-- 3. 24/7 Services -->
                    <div class="flex items-start gap-4 pt-6 lg:pt-0 lg:px-6">
                        <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl shrink-0">
                            <i data-lucide="clock" class="w-7 h-7"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 text-lg mb-1">24/7 Services</h3>
                            <p class="text-sm text-slate-500 leading-snug">Round-the-clock care whenever you need it, day or night.</p>
                        </div>
                    </div>

                    <!-- 4. Patient First -->
                    <div class="flex items-start gap-4 pt-6 lg:pt-0 lg:px-6 last:lg:pr-0">
                        <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl shrink-0">
                            <i data-lucide="heart" class="w-7 h-7"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 text-lg mb-1">Patient First</h3>
                            <p class="text-sm text-slate-500 leading-snug">Compassionate care focused on you and your family's wellbeing.</p>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- 2. About Hospital & Statistics -->
        <section id="about" class="py-20 lg:py-28 bg-white relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
                    <span class="text-xs font-extrabold text-blue-600 uppercase tracking-widest block mb-2">
                        ABOUT TUPI MUNICIPAL HOSPITAL
                    </span>
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight">
                        Discover Our Hospital
                    </h2>
                    <p class="mt-4 text-base sm:text-lg text-slate-600 leading-relaxed">
                        Tupi Municipal Hospital is a government healthcare facility serving the people of Tupi and neighboring communities in South Cotabato, Philippines, providing quality and accessible medical care.
                    </p>
                    <p class="mt-2 text-sm sm:text-base text-slate-500 leading-relaxed">
                        The Tupi Municipal Hospital Information Management System provides patients, families, and healthcare partners with convenient access to hospital services, medical information, and patient resources.
                    </p>
                </div>

                <!-- Hospital Statistics Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6" data-aos="fade-up" data-aos-delay="150">
                    
                    <!-- Location Card -->
                    <div class="bg-slate-50 p-8 rounded-3xl border border-slate-100 text-center hover:bg-blue-50/50 hover:border-blue-200 transition-all">
                        <div class="w-14 h-14 bg-white text-blue-600 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="map-pin" class="w-7 h-7"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900">Tupi, South Cotabato</h3>
                        <p class="text-sm font-medium text-slate-500 mt-1">Philippines</p>
                    </div>

                    <!-- Bed Capacity Card -->
                    <div class="bg-slate-50 p-8 rounded-3xl border border-slate-100 text-center hover:bg-blue-50/50 hover:border-blue-200 transition-all">
                        <div class="w-14 h-14 bg-white text-blue-600 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="bed-double" class="w-7 h-7"></i>
                        </div>
                        <h3 class="text-3xl font-extrabold text-slate-900">50+</h3>
                        <p class="text-sm font-medium text-slate-500 mt-1">Bed Capacity</p>
                    </div>

                    <!-- Medical Staff Card -->
                    <div class="bg-slate-50 p-8 rounded-3xl border border-slate-100 text-center hover:bg-blue-50/50 hover:border-blue-200 transition-all">
                        <div class="w-14 h-14 bg-white text-blue-600 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="users" class="w-7 h-7"></i>
                        </div>
                        <h3 class="text-3xl font-extrabold text-slate-900">120+</h3>
                        <p class="text-sm font-medium text-slate-500 mt-1">Medical Staff</p>
                    </div>

                    <!-- Service Hours Card -->
                    <div class="bg-slate-50 p-8 rounded-3xl border border-slate-100 text-center hover:bg-blue-50/50 hover:border-blue-200 transition-all">
                        <div class="w-14 h-14 bg-white text-blue-600 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="clock" class="w-7 h-7"></i>
                        </div>
                        <h3 class="text-3xl font-extrabold text-slate-900">24/7</h3>
                        <p class="text-sm font-medium text-slate-500 mt-1">Emergency Care</p>
                    </div>

                </div>

            </div>
        </section>

        <!-- 3. Hospital Services Section (12 Responsive Cards) -->
        <section id="services" class="py-20 lg:py-28 bg-slate-50 border-y border-slate-200/60">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
                    <span class="text-xs font-extrabold text-blue-600 uppercase tracking-widest block mb-2">
                        MEDICAL & CLINICAL SERVICES
                    </span>
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight">
                        Hospital Services
                    </h2>
                    <p class="mt-4 text-base sm:text-lg text-slate-600">
                        Access quality healthcare services provided by Tupi Municipal Hospital.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    
                    <!-- 1. Emergency Care -->
                    <div class="service-card bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between" data-aos="fade-up">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-5">
                                <i data-lucide="siren" class="w-6 h-6"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-2">Emergency Care</h3>
                            <p class="text-sm text-slate-500 leading-relaxed mb-6">24/7 emergency room services for trauma, acute illness, and life-threatening conditions with rapid response teams.</p>
                        </div>
                        <button onclick="openServiceModal('Emergency Care', 'Emergency Services', 'Our 24/7 Emergency Department provides immediate medical attention for trauma, cardiac events, respiratory emergencies, and acute conditions.', ['Open 24 hours, 7 days a week', 'No appointment necessary', 'Ambulance dispatch available', 'Triage-based priority system'])" class="inline-flex items-center gap-1.5 text-sm font-bold text-blue-600 hover:text-blue-800 transition-colors group">
                            <span>Learn More</span>
                            <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </div>

                    <!-- 2. Outpatient Department -->
                    <div class="service-card bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between" data-aos="fade-up" data-aos-delay="50">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-5">
                                <i data-lucide="clipboard-list" class="w-6 h-6"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-2">Outpatient Department</h3>
                            <p class="text-sm text-slate-500 leading-relaxed mb-6">Scheduled consultations, follow-up visits, specialist referrals, and medical clearances without hospital admission.</p>
                        </div>
                        <button onclick="openServiceModal('Outpatient Department (OPD)', 'Clinical Services', 'Walk-in and scheduled consultations with general practitioners and specialists for non-emergency medical needs.', ['Valid ID or PhilHealth card', 'OPD card (for returning patients)', 'Referral letter (if applicable)', 'Mon-Fri 8:00 AM - 5:00 PM'])" class="inline-flex items-center gap-1.5 text-sm font-bold text-blue-600 hover:text-blue-800 transition-colors group">
                            <span>Learn More</span>
                            <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </div>

                    <!-- 3. Inpatient Care -->
                    <div class="service-card bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between" data-aos="fade-up" data-aos-delay="100">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-5">
                                <i data-lucide="bed-double" class="w-6 h-6"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-2">Inpatient Care</h3>
                            <p class="text-sm text-slate-500 leading-relaxed mb-6">Hospital admission, ward accommodation, round-the-clock nursing care, and monitored recovery programs.</p>
                        </div>
                        <button onclick="openServiceModal('Inpatient Care Services', 'Admission & Ward', 'Comprehensive inpatient services including ward and semi-private rooms, continuous nursing care, and physician monitoring.', ['Physician admission order', 'Valid ID and PhilHealth MDR', 'Consent forms', 'Deposit or letter of guarantee'])" class="inline-flex items-center gap-1.5 text-sm font-bold text-blue-600 hover:text-blue-800 transition-colors group">
                            <span>Learn More</span>
                            <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </div>

                    <!-- 4. Laboratory Services -->
                    <div class="service-card bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between" data-aos="fade-up" data-aos-delay="150">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-5">
                                <i data-lucide="microscope" class="w-6 h-6"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-2">Laboratory Services</h3>
                            <p class="text-sm text-slate-500 leading-relaxed mb-6">Complete blood work, urinalysis, drug testing, clinical chemistry, hematology, and diagnostic lab exams.</p>
                        </div>
                        <button onclick="openServiceModal('Clinical Laboratory', 'Diagnostic Services', 'Full-service clinical laboratory offering CBC, blood chemistry, urinalysis, fecalysis, serology, and specialized diagnostic tests.', ['Laboratory request from physician', 'Fasting may be required for some tests', 'Results available within 24-48 hours'])" class="inline-flex items-center gap-1.5 text-sm font-bold text-blue-600 hover:text-blue-800 transition-colors group">
                            <span>Learn More</span>
                            <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </div>

                    <!-- 5. Radiology & Imaging -->
                    <div class="service-card bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between" data-aos="fade-up">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-5">
                                <i data-lucide="scan" class="w-6 h-6"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-2">Radiology & Imaging</h3>
                            <p class="text-sm text-slate-500 leading-relaxed mb-6">X-ray, ultrasound, ECG, and diagnostic imaging services for accurate medical assessments and diagnoses.</p>
                        </div>
                        <button onclick="openServiceModal('Radiology & Imaging Department', 'Diagnostic Imaging', 'Digital X-ray, ultrasound, and electrocardiogram services for comprehensive diagnostic evaluations.', ['Physician imaging request', 'Previous imaging results (if any)', 'Remove metallic accessories before procedure'])" class="inline-flex items-center gap-1.5 text-sm font-bold text-blue-600 hover:text-blue-800 transition-colors group">
                            <span>Learn More</span>
                            <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </div>

                    <!-- 6. Maternal & Child Health -->
                    <div class="service-card bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between" data-aos="fade-up" data-aos-delay="50">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-5">
                                <i data-lucide="baby" class="w-6 h-6"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-2">Maternal & Child Health</h3>
                            <p class="text-sm text-slate-500 leading-relaxed mb-6">Prenatal care, normal and cesarean delivery, newborn screening, immunization, and postnatal checkups.</p>
                        </div>
                        <button onclick="openServiceModal('Maternal & Child Health Unit', 'OB-GYN & Pediatrics', 'Comprehensive obstetric and pediatric care including prenatal monitoring, safe delivery, newborn screening, and childhood immunization.', ['Maternal health record / Prenatal card', 'PhilHealth MDR for delivery', 'Newborn Screening consent form'])" class="inline-flex items-center gap-1.5 text-sm font-bold text-blue-600 hover:text-blue-800 transition-colors group">
                            <span>Learn More</span>
                            <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </div>

                    <!-- 7. Pharmacy -->
                    <div class="service-card bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between" data-aos="fade-up" data-aos-delay="100">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-5">
                                <i data-lucide="pill" class="w-6 h-6"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-2">Pharmacy</h3>
                            <p class="text-sm text-slate-500 leading-relaxed mb-6">In-house pharmacy providing prescription medications, over-the-counter drugs, and pharmaceutical counseling.</p>
                        </div>
                        <button onclick="openServiceModal('Hospital Pharmacy', 'Pharmaceutical Services', 'Dispensing of prescribed medications, generic drug availability, and pharmaceutical counseling for proper medication use.', ['Valid prescription from attending physician', 'PhilHealth or senior/PWD ID for discounts', 'Open during hospital operating hours'])" class="inline-flex items-center gap-1.5 text-sm font-bold text-blue-600 hover:text-blue-800 transition-colors group">
                            <span>Learn More</span>
                            <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </div>

                    <!-- 8. Surgery -->
                    <div class="service-card bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between" data-aos="fade-up" data-aos-delay="150">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-5">
                                <i data-lucide="scissors" class="w-6 h-6"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-2">Surgery</h3>
                            <p class="text-sm text-slate-500 leading-relaxed mb-6">Minor and major surgical procedures, pre-operative assessments, and post-operative recovery care.</p>
                        </div>
                        <button onclick="openServiceModal('Surgical Services', 'Operating Room', 'General and minor surgery services including appendectomy, hernia repair, cesarean section, and wound debridement.', ['Surgical clearance from physician', 'Pre-operative lab results', 'Anesthesia consent form', 'NPO (fasting) as instructed'])" class="inline-flex items-center gap-1.5 text-sm font-bold text-blue-600 hover:text-blue-800 transition-colors group">
                            <span>Learn More</span>
                            <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </div>

                    <!-- 9. Dental Services -->
                    <div class="service-card bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between" data-aos="fade-up">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-5">
                                <i data-lucide="smile" class="w-6 h-6"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-2">Dental Services</h3>
                            <p class="text-sm text-slate-500 leading-relaxed mb-6">Tooth extraction, oral prophylaxis, dental checkups, and basic oral surgery for patients of all ages.</p>
                        </div>
                        <button onclick="openServiceModal('Dental Clinic', 'Oral Health Services', 'Comprehensive dental care including tooth extraction, cleaning, fillings, and oral health education.', ['Dental record or referral', 'Walk-in patients accepted', 'Mon-Fri 8:00 AM - 5:00 PM'])" class="inline-flex items-center gap-1.5 text-sm font-bold text-blue-600 hover:text-blue-800 transition-colors group">
                            <span>Learn More</span>
                            <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </div>

                    <!-- 10. Mental Health -->
                    <div class="service-card bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between" data-aos="fade-up" data-aos-delay="50">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-5">
                                <i data-lucide="brain" class="w-6 h-6"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-2">Mental Health</h3>
                            <p class="text-sm text-slate-500 leading-relaxed mb-6">Psychiatric consultations, psychological counseling, stress management, and mental wellness programs.</p>
                        </div>
                        <button onclick="openServiceModal('Mental Health Unit', 'Behavioral Health', 'Confidential psychiatric consultations, counseling services, and community mental health programs.', ['Referral from physician or self-referral', 'All consultations are confidential', 'Crisis intervention available 24/7'])" class="inline-flex items-center gap-1.5 text-sm font-bold text-blue-600 hover:text-blue-800 transition-colors group">
                            <span>Learn More</span>
                            <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </div>

                    <!-- 11. Physical Therapy -->
                    <div class="service-card bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between" data-aos="fade-up" data-aos-delay="100">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-5">
                                <i data-lucide="activity" class="w-6 h-6"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-2">Physical Therapy</h3>
                            <p class="text-sm text-slate-500 leading-relaxed mb-6">Rehabilitation services, physical therapy sessions, post-surgical recovery, and mobility restoration programs.</p>
                        </div>
                        <button onclick="openServiceModal('Rehabilitation & Physical Therapy', 'Rehab Services', 'Physical therapy and rehabilitation for post-surgical recovery, stroke rehabilitation, and musculoskeletal conditions.', ['Physician referral for PT', 'Assessment by licensed Physical Therapist', 'Session schedule: Mon-Fri'])" class="inline-flex items-center gap-1.5 text-sm font-bold text-blue-600 hover:text-blue-800 transition-colors group">
                            <span>Learn More</span>
                            <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </div>

                    <!-- 12. Ambulance Services -->
                    <div class="service-card bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between" data-aos="fade-up" data-aos-delay="150">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-5">
                                <i data-lucide="truck" class="w-6 h-6"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-2">Ambulance Services</h3>
                            <p class="text-sm text-slate-500 leading-relaxed mb-6">24/7 emergency ambulance dispatch, patient transport, inter-facility transfer, and pre-hospital care.</p>
                        </div>
                        <button onclick="openServiceModal('Ambulance & Emergency Transport', 'Pre-Hospital Care', '24/7 ambulance dispatch for emergency response and inter-facility patient transfer across Tupi and surrounding areas.', ['Call Hospital Hotline: +63 (083) 228-0001', 'Free for indigent patients with Barangay certificate', 'Available for inter-facility transfers'])" class="inline-flex items-center gap-1.5 text-sm font-bold text-blue-600 hover:text-blue-800 transition-colors group">
                            <span>Learn More</span>
                            <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </div>

                </div>

            </div>
        </section>

        <!-- 4. Hospital Departments Section -->
        <section id="departments" class="py-20 lg:py-28 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
                    <span class="text-xs font-extrabold text-blue-600 uppercase tracking-widest block mb-2">
                        CLINICAL & ADMINISTRATIVE UNITS
                    </span>
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight">
                        Hospital Departments
                    </h2>
                    <p class="mt-4 text-base sm:text-lg text-slate-600">
                        Find the department or unit responsible for the care or service you need.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" data-aos="fade-up">
                    
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-200 hover:bg-blue-50/30 transition-all">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="p-2 bg-blue-100 text-blue-700 rounded-xl"><i data-lucide="siren" class="w-5 h-5"></i></span>
                            <h3 class="font-bold text-slate-900 text-lg">Emergency Department</h3>
                        </div>
                        <p class="text-sm text-slate-600">24/7 emergency medical care, trauma response, triage, and critical stabilization services.</p>
                    </div>

                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-200 hover:bg-blue-50/30 transition-all">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="p-2 bg-blue-100 text-blue-700 rounded-xl"><i data-lucide="clipboard-list" class="w-5 h-5"></i></span>
                            <h3 class="font-bold text-slate-900 text-lg">Outpatient Department</h3>
                        </div>
                        <p class="text-sm text-slate-600">General consultations, specialist referrals, medical clearances, and follow-up appointments.</p>
                    </div>

                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-200 hover:bg-blue-50/30 transition-all">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="p-2 bg-blue-100 text-blue-700 rounded-xl"><i data-lucide="stethoscope" class="w-5 h-5"></i></span>
                            <h3 class="font-bold text-slate-900 text-lg">Internal Medicine</h3>
                        </div>
                        <p class="text-sm text-slate-600">Diagnosis and treatment of adult diseases including diabetes, hypertension, and infectious diseases.</p>
                    </div>

                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-200 hover:bg-blue-50/30 transition-all">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="p-2 bg-blue-100 text-blue-700 rounded-xl"><i data-lucide="scissors" class="w-5 h-5"></i></span>
                            <h3 class="font-bold text-slate-900 text-lg">General Surgery</h3>
                        </div>
                        <p class="text-sm text-slate-600">Surgical procedures, pre-operative evaluations, operating room services, and post-operative care.</p>
                    </div>

                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-200 hover:bg-blue-50/30 transition-all">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="p-2 bg-blue-100 text-blue-700 rounded-xl"><i data-lucide="baby" class="w-5 h-5"></i></span>
                            <h3 class="font-bold text-slate-900 text-lg">Obstetrics & Gynecology</h3>
                        </div>
                        <p class="text-sm text-slate-600">Prenatal care, labor and delivery, postpartum care, and women's reproductive health services.</p>
                    </div>

                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-200 hover:bg-blue-50/30 transition-all">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="p-2 bg-blue-100 text-blue-700 rounded-xl"><i data-lucide="heart" class="w-5 h-5"></i></span>
                            <h3 class="font-bold text-slate-900 text-lg">Pediatrics</h3>
                        </div>
                        <p class="text-sm text-slate-600">Medical care for infants, children, and adolescents including immunization and developmental checkups.</p>
                    </div>

                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-200 hover:bg-blue-50/30 transition-all">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="p-2 bg-blue-100 text-blue-700 rounded-xl"><i data-lucide="microscope" class="w-5 h-5"></i></span>
                            <h3 class="font-bold text-slate-900 text-lg">Clinical Laboratory</h3>
                        </div>
                        <p class="text-sm text-slate-600">Blood tests, clinical chemistry, hematology, serology, urinalysis, and diagnostic examinations.</p>
                    </div>

                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-200 hover:bg-blue-50/30 transition-all">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="p-2 bg-blue-100 text-blue-700 rounded-xl"><i data-lucide="scan" class="w-5 h-5"></i></span>
                            <h3 class="font-bold text-slate-900 text-lg">Radiology Department</h3>
                        </div>
                        <p class="text-sm text-slate-600">X-ray, ultrasound, ECG, and diagnostic imaging for accurate diagnosis and treatment planning.</p>
                    </div>

                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-200 hover:bg-blue-50/30 transition-all">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="p-2 bg-blue-100 text-blue-700 rounded-xl"><i data-lucide="pill" class="w-5 h-5"></i></span>
                            <h3 class="font-bold text-slate-900 text-lg">Pharmacy Department</h3>
                        </div>
                        <p class="text-sm text-slate-600">Dispensing of medications, pharmaceutical counseling, and inventory of essential drugs and supplies.</p>
                    </div>

                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-200 hover:bg-blue-50/30 transition-all">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="p-2 bg-blue-100 text-blue-700 rounded-xl"><i data-lucide="bed-double" class="w-5 h-5"></i></span>
                            <h3 class="font-bold text-slate-900 text-lg">Nursing Services</h3>
                        </div>
                        <p class="text-sm text-slate-600">Patient care management, ward nursing, IV therapy, wound care, and bedside health monitoring.</p>
                    </div>

                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-200 hover:bg-blue-50/30 transition-all">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="p-2 bg-blue-100 text-blue-700 rounded-xl"><i data-lucide="file-text" class="w-5 h-5"></i></span>
                            <h3 class="font-bold text-slate-900 text-lg">Medical Records</h3>
                        </div>
                        <p class="text-sm text-slate-600">Patient record management, medical certificates, clinical abstracts, and health data archiving.</p>
                    </div>

                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-200 hover:bg-blue-50/30 transition-all">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="p-2 bg-blue-100 text-blue-700 rounded-xl"><i data-lucide="users" class="w-5 h-5"></i></span>
                            <h3 class="font-bold text-slate-900 text-lg">Medical Social Services</h3>
                        </div>
                        <p class="text-sm text-slate-600">PhilHealth processing, indigent patient assistance, financial counseling, and charity programs.</p>
                    </div>

                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-200 hover:bg-blue-50/30 transition-all">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="p-2 bg-blue-100 text-blue-700 rounded-xl"><i data-lucide="smile" class="w-5 h-5"></i></span>
                            <h3 class="font-bold text-slate-900 text-lg">Dental Department</h3>
                        </div>
                        <p class="text-sm text-slate-600">Tooth extraction, oral prophylaxis, dental checkups, and oral health education programs.</p>
                    </div>

                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-200 hover:bg-blue-50/30 transition-all">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="p-2 bg-blue-100 text-blue-700 rounded-xl"><i data-lucide="salad" class="w-5 h-5"></i></span>
                            <h3 class="font-bold text-slate-900 text-lg">Dietary & Nutrition</h3>
                        </div>
                        <p class="text-sm text-slate-600">Patient meal planning, therapeutic diets, nutrition counseling, and food service management.</p>
                    </div>

                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-200 hover:bg-blue-50/30 transition-all">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="p-2 bg-blue-100 text-blue-700 rounded-xl"><i data-lucide="briefcase" class="w-5 h-5"></i></span>
                            <h3 class="font-bold text-slate-900 text-lg">Hospital Administration</h3>
                        </div>
                        <p class="text-sm text-slate-600">Overall hospital management, billing, human resources, procurement, and facility maintenance.</p>
                    </div>

                </div>

            </div>
        </section>

        <!-- 5. Patient Information Center -->
        <section class="py-20 bg-slate-50 border-t border-slate-200/60">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
                    <span class="text-xs font-extrabold text-blue-600 uppercase tracking-widest block mb-2">
                        PATIENT RESOURCES & GUIDES
                    </span>
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight">
                        Patient Information Center
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6" data-aos="fade-up">
                    
                    <div class="bg-white p-8 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-6">
                                <i data-lucide="clipboard-list" class="w-7 h-7"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-3">Admission Guide</h3>
                            <p class="text-sm text-slate-500 leading-relaxed mb-6">Step-by-step guide to hospital admission, required documents, room options, and patient rights.</p>
                        </div>
                        <a href="#services" class="inline-flex items-center gap-2 text-sm font-bold text-blue-600 hover:text-blue-800 transition-colors">
                            <span>View Guide</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>

                    <div class="bg-white p-8 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-6">
                                <i data-lucide="shield" class="w-7 h-7"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-3">PhilHealth & Insurance</h3>
                            <p class="text-sm text-slate-500 leading-relaxed mb-6">Information about PhilHealth coverage, benefit packages, requirements, and financial assistance programs.</p>
                        </div>
                        <a href="#contact" class="inline-flex items-center gap-2 text-sm font-bold text-blue-600 hover:text-blue-800 transition-colors">
                            <span>Learn More</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>

                    <div class="bg-white p-8 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-6">
                                <i data-lucide="heart" class="w-7 h-7"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-3">Health & Wellness</h3>
                            <p class="text-sm text-slate-500 leading-relaxed mb-6">Health tips, disease prevention, vaccination schedules, nutrition advice, and wellness education resources.</p>
                        </div>
                        <a href="#community" class="inline-flex items-center gap-2 text-sm font-bold text-blue-600 hover:text-blue-800 transition-colors">
                            <span>Read Articles</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>

                    <div class="bg-white p-8 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-6">
                                <i data-lucide="siren" class="w-7 h-7"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-3">Emergency Guide</h3>
                            <p class="text-sm text-slate-500 leading-relaxed mb-6">Emergency hotlines, first aid basics, when to visit the ER, and ambulance dispatch instructions.</p>
                        </div>
                        <a href="#contact" class="inline-flex items-center gap-2 text-sm font-bold text-blue-600 hover:text-blue-800 transition-colors">
                            <span>Emergency Info</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>

                </div>

            </div>
        </section>

        <!-- 6. Latest Hospital Updates -->
        <section class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-12" data-aos="fade-up">
                    <div>
                        <span class="text-xs font-extrabold text-blue-600 uppercase tracking-widest block mb-2">
                            ADVISORIES & ANNOUNCEMENTS
                        </span>
                        <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                            Latest Hospital Updates
                        </h2>
                    </div>
                    <p class="text-sm text-slate-500 mt-2 md:mt-0">
                        Official health advisories and hospital announcements.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6" data-aos="fade-up" data-aos-delay="100">
                    
                    <div class="bg-slate-50 p-6 rounded-3xl border border-slate-200/70 flex flex-col justify-between hover:bg-white hover:shadow-lg hover:border-blue-200 transition-all">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-4">
                                <span class="px-2.5 py-1 bg-blue-100 text-blue-700 font-bold text-[11px] uppercase tracking-wider rounded-md">Sample Advisory</span>
                                <span class="text-xs text-slate-400 font-medium">Aug 2026</span>
                            </div>
                            <h3 class="font-bold text-slate-900 text-lg mb-2 leading-snug">Flu Vaccination Drive</h3>
                            <p class="text-sm text-slate-600 leading-relaxed mb-4">Free influenza vaccination is available for senior citizens, children, and healthcare workers at the OPD clinic.</p>
                        </div>
                        <button onclick="openServiceModal('Flu Vaccination Drive', 'Public Health Advisory', 'Free influenza vaccines are available for priority groups including senior citizens (60+), children under 5, pregnant women, and frontline healthcare workers.', ['Issued by: Tupi Municipal Hospital', 'Schedule: Mon-Fri, 8 AM - 12 PM', 'Bring valid ID or senior/PWD card'])" class="text-xs font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1">
                            <span>Read Advisory</span>
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>

                    <div class="bg-slate-50 p-6 rounded-3xl border border-slate-200/70 flex flex-col justify-between hover:bg-white hover:shadow-lg hover:border-blue-200 transition-all">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-4">
                                <span class="px-2.5 py-1 bg-green-100 text-green-700 font-bold text-[11px] uppercase tracking-wider rounded-md">Sample Advisory</span>
                                <span class="text-xs text-slate-400 font-medium">Aug 2026</span>
                            </div>
                            <h3 class="font-bold text-slate-900 text-lg mb-2 leading-snug">New Laboratory Equipment</h3>
                            <p class="text-sm text-slate-600 leading-relaxed mb-4">The hospital laboratory has been upgraded with new hematology and chemistry analyzers for faster and more accurate results.</p>
                        </div>
                        <button onclick="openServiceModal('Laboratory Upgrade', 'Facility Improvement', 'New automated hematology and chemistry analyzers have been installed to improve turnaround time and accuracy of diagnostic tests.', ['Issued by: Hospital Administration', 'Effective: Immediately'])" class="text-xs font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1">
                            <span>Read Details</span>
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>

                    <div class="bg-slate-50 p-6 rounded-3xl border border-slate-200/70 flex flex-col justify-between hover:bg-white hover:shadow-lg hover:border-blue-200 transition-all">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-4">
                                <span class="px-2.5 py-1 bg-amber-100 text-amber-800 font-bold text-[11px] uppercase tracking-wider rounded-md">Sample Advisory</span>
                                <span class="text-xs text-slate-400 font-medium">Aug 2026</span>
                            </div>
                            <h3 class="font-bold text-slate-900 text-lg mb-2 leading-snug">Prenatal Care Program</h3>
                            <p class="text-sm text-slate-600 leading-relaxed mb-4">Expecting mothers can register for free prenatal checkups, ultrasound, and maternal health counseling every Tuesday and Thursday.</p>
                        </div>
                        <button onclick="openServiceModal('Prenatal Care Program', 'Maternal Health', 'Comprehensive prenatal package including checkups, ultrasound, iron supplementation, and birth planning for all pregnant residents of Tupi.', ['Organized by: OB-GYN Department', 'Schedule: Tue & Thu, 8 AM - 12 PM', 'Bring prenatal record book'])" class="text-xs font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1">
                            <span>Read Details</span>
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>

                    <div class="bg-slate-50 p-6 rounded-3xl border border-slate-200/70 flex flex-col justify-between hover:bg-white hover:shadow-lg hover:border-blue-200 transition-all">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-4">
                                <span class="px-2.5 py-1 bg-purple-100 text-purple-700 font-bold text-[11px] uppercase tracking-wider rounded-md">Sample Advisory</span>
                                <span class="text-xs text-slate-400 font-medium">Aug 2026</span>
                            </div>
                            <h3 class="font-bold text-slate-900 text-lg mb-2 leading-snug">PhilHealth Reminder</h3>
                            <p class="text-sm text-slate-600 leading-relaxed mb-4">All admitted patients are reminded to present their PhilHealth MDR and updated membership to avail of benefit packages.</p>
                        </div>
                        <button onclick="openServiceModal('PhilHealth Benefit Reminder', 'Administrative Notice', 'Maximize your PhilHealth benefits by presenting updated membership documents upon admission. The Medical Social Services office can assist with processing.', ['Issued by: Billing & PhilHealth Section', 'Contact Medical Social Services for assistance'])" class="text-xs font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1">
                            <span>Read Notice</span>
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>

                </div>

            </div>
        </section>

        <!-- 7. Hospital Facilities (Interactive Search Grid) -->
        <section id="barangays" class="py-20 lg:py-28 bg-slate-50 border-t border-slate-200/60">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center max-w-3xl mx-auto mb-12" data-aos="fade-up">
                    <span class="text-xs font-extrabold text-blue-600 uppercase tracking-widest block mb-2">
                        WARDS, ROOMS & FACILITIES
                    </span>
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight">
                        Hospital Facilities
                    </h2>
                    <p class="mt-3 text-base text-slate-600">
                        Explore the wards, rooms, and specialized units available at Tupi Municipal Hospital.
                    </p>

                    <div class="mt-8 relative max-w-md mx-auto">
                        <div class="relative">
                            <i data-lucide="search" class="w-5 h-5 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2"></i>
                            <input type="text" id="barangaySearch" placeholder="Search facility, ward, or unit..." class="w-full pl-12 pr-4 py-3.5 rounded-2xl bg-white border border-slate-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent text-sm text-slate-800 placeholder-slate-400 transition-all">
                        </div>
                        <div id="searchCount" class="text-xs font-medium text-slate-500 mt-2 text-right">
                            Showing 15 of 15 Facilities
                        </div>
                    </div>
                </div>

                <div id="barangayGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" data-aos="fade-up">
                    <!-- Populated dynamically via main.js -->
                </div>

            </div>
        </section>

        <!-- 8. Hospital Accreditation & Reports -->
        <section class="py-20 lg:py-28 bg-white border-t border-slate-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
                    <span class="text-xs font-extrabold text-blue-600 uppercase tracking-widest block mb-2">
                        QUALITY & COMPLIANCE
                    </span>
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight">
                        Accreditation & Transparency
                    </h2>
                    <p class="mt-4 text-base sm:text-lg text-slate-600">
                        Committed to quality healthcare standards, regulatory compliance, and transparent hospital operations.
                    </p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6" data-aos="fade-up">
                    
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-200 transition-all text-center">
                        <i data-lucide="award" class="w-8 h-8 text-blue-600 mx-auto mb-3"></i>
                        <h3 class="font-bold text-slate-900 text-base mb-1">DOH License</h3>
                        <p class="text-xs text-slate-500">Licensed and regulated by the Department of Health.</p>
                    </div>

                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-200 transition-all text-center">
                        <i data-lucide="shield-check" class="w-8 h-8 text-blue-600 mx-auto mb-3"></i>
                        <h3 class="font-bold text-slate-900 text-base mb-1">PhilHealth Accredited</h3>
                        <p class="text-xs text-slate-500">Accredited provider for PhilHealth benefit packages.</p>
                    </div>

                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-200 transition-all text-center">
                        <i data-lucide="bar-chart-3" class="w-8 h-8 text-blue-600 mx-auto mb-3"></i>
                        <h3 class="font-bold text-slate-900 text-base mb-1">Annual Reports</h3>
                        <p class="text-xs text-slate-500">Hospital performance and accomplishment reports.</p>
                    </div>

                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-200 transition-all text-center">
                        <i data-lucide="file-text" class="w-8 h-8 text-blue-600 mx-auto mb-3"></i>
                        <h3 class="font-bold text-slate-900 text-base mb-1">Hospital Budget</h3>
                        <p class="text-xs text-slate-500">Annual budget allocations and financial transparency.</p>
                    </div>

                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-200 transition-all text-center">
                        <i data-lucide="users" class="w-8 h-8 text-blue-600 mx-auto mb-3"></i>
                        <h3 class="font-bold text-slate-900 text-base mb-1">Patient Feedback</h3>
                        <p class="text-xs text-slate-500">Patient satisfaction surveys and quality improvement.</p>
                    </div>

                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-200 transition-all text-center">
                        <i data-lucide="file-check" class="w-8 h-8 text-blue-600 mx-auto mb-3"></i>
                        <h3 class="font-bold text-slate-900 text-base mb-1">Clinical Protocols</h3>
                        <p class="text-xs text-slate-500">Standardized medical procedures and safety guidelines.</p>
                    </div>

                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-200 transition-all text-center">
                        <i data-lucide="shopping-bag" class="w-8 h-8 text-blue-600 mx-auto mb-3"></i>
                        <h3 class="font-bold text-slate-900 text-base mb-1">Procurement</h3>
                        <p class="text-xs text-slate-500">Bids, awards, and medical supply procurement notices.</p>
                    </div>

                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-200 transition-all text-center">
                        <i data-lucide="info" class="w-8 h-8 text-blue-600 mx-auto mb-3"></i>
                        <h3 class="font-bold text-slate-900 text-base mb-1">Citizen's Charter</h3>
                        <p class="text-xs text-slate-500">Service standards, processing times & requirements.</p>
                    </div>

                </div>

                <div class="mt-12 text-center">
                    <button onclick="openServiceModal('Hospital Transparency Portal', 'Quality & Compliance', 'All hospital financial records, procurement notices, and performance reports are published in compliance with DOH and government transparency standards.', ['Portal Access: Hospital Records Office', 'Contact: info@tupihospital.gov.ph'])" class="inline-flex items-center gap-2 px-8 py-4 rounded-full bg-slate-900 text-white font-bold text-sm hover:bg-blue-600 shadow-md transition-all">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                        <span>View Hospital Reports</span>
                    </button>
                </div>

            </div>
        </section>

        <!-- 9. About / Healthcare Community Feature -->
        <section id="community" class="py-20 lg:py-28 bg-slate-50 border-t border-slate-200/60">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                    
                    <div class="lg:col-span-6" data-aos="fade-right">
                        <div class="relative rounded-3xl overflow-hidden shadow-2xl border border-slate-200">
                            <img src="{{ asset('assets/tupi-landscape.jpg') }}" alt="Tupi South Cotabato Community Healthcare" class="w-full h-full object-cover aspect-[4/3]">
                            <div class="absolute bottom-4 left-4 bg-white/90 backdrop-blur-md px-4 py-2 rounded-xl text-xs font-bold text-slate-800 shadow-sm flex items-center gap-2">
                                <i data-lucide="heart" class="w-4 h-4 text-red-500"></i>
                                Serving the Community of Tupi, South Cotabato
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-6 space-y-6" data-aos="fade-left">
                        <span class="text-xs font-extrabold text-blue-600 uppercase tracking-widest block">
                            HEALTHCARE FOR EVERY TUPIHENYO
                        </span>
                        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight">
                            Compassionate Care for Our Community
                        </h2>
                        
                        <p class="text-base text-slate-600 leading-relaxed">
                            Tupi Municipal Hospital is committed to delivering quality, accessible, and affordable healthcare to the people of Tupi and the broader South Cotabato community. From emergency care to maternal health, our dedicated medical professionals are here for you.
                        </p>
                        
                        <div class="grid grid-cols-2 gap-4 pt-2">
                            <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
                                <div class="font-bold text-slate-900 text-base flex items-center gap-2 mb-1">
                                    <i data-lucide="stethoscope" class="w-4 h-4 text-blue-600"></i> Quality Care
                                </div>
                                <p class="text-xs text-slate-500">Skilled physicians and nurses providing evidence-based treatment.</p>
                            </div>

                            <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
                                <div class="font-bold text-slate-900 text-base flex items-center gap-2 mb-1">
                                    <i data-lucide="shield" class="w-4 h-4 text-blue-600"></i> Affordable
                                </div>
                                <p class="text-xs text-slate-500">PhilHealth-accredited with indigent patient support programs.</p>
                            </div>
                        </div>

                        <p class="text-sm text-slate-500 leading-relaxed pt-2">
                            Our hospital administration is committed to continuous improvement, facility upgrades, staff development, and community health outreach to ensure every patient receives the best possible care.
                        </p>
                    </div>

                </div>
            </div>
        </section>

        <!-- 10. Contact Section -->
        <section id="contact" class="py-20 lg:py-28 bg-white border-t border-slate-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
                    <span class="text-xs font-extrabold text-blue-600 uppercase tracking-widest block mb-2">
                        WE ARE HERE TO HELP
                    </span>
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight">
                        Get in Touch
                    </h2>
                    <p class="mt-4 text-base sm:text-lg text-slate-600">
                        Have a question or need medical assistance? Contact Tupi Municipal Hospital.
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                    
                    <div class="lg:col-span-5 space-y-8" data-aos="fade-right">
                        
                        <div class="bg-blue-600 text-white p-8 rounded-3xl shadow-xl space-y-6">
                            <h3 class="text-2xl font-extrabold">Tupi Municipal Hospital</h3>
                            <p class="text-blue-100 text-sm leading-relaxed">
                                Tupi, South Cotabato<br>
                                Philippines
                            </p>

                            <div class="space-y-4 pt-4 border-t border-blue-500/60">
                                
                                <div class="flex items-start gap-4">
                                    <div class="p-2.5 bg-blue-700/80 rounded-xl shrink-0">
                                        <i data-lucide="phone" class="w-5 h-5"></i>
                                    </div>
                                    <div>
                                        <span class="block text-xs text-blue-200 uppercase font-semibold">Hospital Hotline</span>
                                        <a href="tel:+630832280001" class="font-bold text-white hover:underline">+63 (083) 228-0001</a>
                                    </div>
                                </div>

                                <div class="flex items-start gap-4">
                                    <div class="p-2.5 bg-blue-700/80 rounded-xl shrink-0">
                                        <i data-lucide="mail" class="w-5 h-5"></i>
                                    </div>
                                    <div>
                                        <span class="block text-xs text-blue-200 uppercase font-semibold">Email Address</span>
                                        <a href="mailto:info@tupihospital.gov.ph" class="font-bold text-white hover:underline">info@tupihospital.gov.ph</a>
                                    </div>
                                </div>

                                <div class="flex items-start gap-4">
                                    <div class="p-2.5 bg-blue-700/80 rounded-xl shrink-0">
                                        <i data-lucide="clock" class="w-5 h-5"></i>
                                    </div>
                                    <div>
                                        <span class="block text-xs text-blue-200 uppercase font-semibold">OPD Hours</span>
                                        <p class="font-bold text-white">Monday – Friday</p>
                                        <p class="text-xs text-blue-200">8:00 AM – 5:00 PM</p>
                                        <p class="text-xs text-blue-200 mt-1 font-semibold">ER: Open 24/7</p>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="p-6 rounded-2xl bg-red-50 border border-red-200 flex items-start gap-4">
                            <i data-lucide="siren" class="w-6 h-6 text-red-600 shrink-0 mt-0.5"></i>
                            <div>
                                <h4 class="font-bold text-red-900 text-sm">24/7 Emergency & Ambulance Hotline</h4>
                                <p class="text-xs text-red-800 mt-1">For medical emergencies, call the hospital hotline immediately. Ambulance dispatch available round the clock.</p>
                            </div>
                        </div>

                    </div>

                    <div class="lg:col-span-7 bg-slate-50 p-8 sm:p-10 rounded-3xl border border-slate-200/80 shadow-sm" data-aos="fade-left">
                        <form id="contactForm" class="space-y-6">
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label for="fullName" class="block text-xs font-extrabold uppercase text-slate-700 mb-2">Full Name *</label>
                                    <input type="text" id="fullName" required placeholder="Juan dela Cruz" class="w-full px-4 py-3.5 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all">
                                </div>
                                <div>
                                    <label for="emailAddress" class="block text-xs font-extrabold uppercase text-slate-700 mb-2">Email Address *</label>
                                    <input type="email" id="emailAddress" required placeholder="juan@example.com" class="w-full px-4 py-3.5 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all">
                                </div>
                            </div>

                            <div>
                                <label for="subject" class="block text-xs font-extrabold uppercase text-slate-700 mb-2">Subject *</label>
                                <input type="text" id="subject" required placeholder="Inquiry about hospital service or appointment" class="w-full px-4 py-3.5 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all">
                            </div>

                            <div>
                                <label for="message" class="block text-xs font-extrabold uppercase text-slate-700 mb-2">Message *</label>
                                <textarea id="message" rows="5" required placeholder="How can Tupi Municipal Hospital assist you today?" class="w-full px-4 py-3.5 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all"></textarea>
                            </div>

                            <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 py-4 rounded-xl bg-blue-600 text-white font-bold text-base shadow-lg shadow-blue-600/20 hover:bg-blue-700 transition-all">
                                <span>Send Message</span>
                                <i data-lucide="send" class="w-5 h-5"></i>
                            </button>

                            <p class="text-xs text-slate-400 italic">
                                Note: For medical emergencies, please call the hospital hotline directly. This form is for general inquiries only.
                            </p>
                        </form>
                    </div>

                </div>

            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="bg-navy-900 text-slate-300 pt-16 pb-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-10 pb-12 border-b border-slate-800">
                
                <div class="lg:col-span-5 space-y-4">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('assets/logo.png') }}" alt="Tupi Hospital Seal" class="h-10 w-auto">
                        <div>
                            <span class="block font-bold text-white text-lg">Tupi Municipal Hospital</span>
                            <span class="block text-xs text-blue-400 uppercase font-semibold">Information System</span>
                        </div>
                    </div>
                    <p class="text-sm text-slate-400 leading-relaxed max-w-md">
                        Providing compassionate, quality, and accessible healthcare to the people of Tupi, South Cotabato. Your health is our mission.
                    </p>
                </div>

                <div class="lg:col-span-2 space-y-3">
                    <h4 class="text-sm font-extrabold uppercase tracking-wider text-white">Quick Links</h4>
                    <ul class="space-y-2 text-sm text-slate-400">
                        <li><a href="#home" class="hover:text-white transition-colors">Home</a></li>
                        <li><a href="#about" class="hover:text-white transition-colors">About Us</a></li>
                        <li><a href="#services" class="hover:text-white transition-colors">Services</a></li>
                        <li><a href="#departments" class="hover:text-white transition-colors">Departments</a></li>
                        <li><a href="#contact" class="hover:text-white transition-colors">Contact Us</a></li>
                    </ul>
                </div>

                <div class="lg:col-span-2 space-y-3">
                    <h4 class="text-sm font-extrabold uppercase tracking-wider text-white">Patient Resources</h4>
                    <ul class="space-y-2 text-sm text-slate-400">
                        <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Book Appointment</a></li>
                        <li><a href="#services" class="hover:text-white transition-colors">PhilHealth Info</a></li>
                        <li><a href="#departments" class="hover:text-white transition-colors">Find a Doctor</a></li>
                        <li><a href="#services" class="hover:text-white transition-colors">Health Advisories</a></li>
                    </ul>
                </div>

                <div class="lg:col-span-3 space-y-3">
                    <h4 class="text-sm font-extrabold uppercase tracking-wider text-white">Contact</h4>
                    <div class="text-sm text-slate-400 space-y-2">
                        <p class="text-white font-medium">Tupi Municipal Hospital</p>
                        <p>Tupi, South Cotabato, Philippines</p>
                        <p class="text-blue-400 font-semibold">+63 (083) 228-0001</p>
                        <p>info@tupihospital.gov.ph</p>
                    </div>
                </div>

            </div>

            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
                <p>© 2026 Tupi Municipal Hospital Information Management System. All rights reserved.</p>
                <div class="flex items-center gap-6">
                    <a href="#services" class="hover:text-slate-400 transition-colors">Privacy Policy</a>
                    <a href="#services" class="hover:text-slate-400 transition-colors">Patient Rights</a>
                    <a href="#services" class="hover:text-slate-400 transition-colors">Terms of Service</a>
                </div>
            </div>

        </div>
    </footer>

    <!-- Interactive Information Modal -->
    <div id="infoModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100 modal-content relative">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <span id="modalCategory" class="text-xs font-bold uppercase tracking-wider text-blue-600 px-2.5 py-1 bg-blue-50 rounded-full"></span>
                    <h3 id="modalTitle" class="text-2xl font-extrabold text-slate-900 mt-2"></h3>
                </div>
                <button id="closeModalBtn" type="button" class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-full transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <div id="modalBody" class="mt-4"></div>
            <div class="mt-8 pt-4 border-t border-slate-100 flex justify-end">
                <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="px-6 py-2.5 rounded-full bg-slate-100 text-slate-700 font-semibold text-sm hover:bg-slate-200 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Feedback Toast Notification -->
    <div id="toast" class="fixed bottom-6 right-6 z-50 bg-slate-900 text-white px-6 py-4 rounded-2xl shadow-2xl border border-slate-700 flex items-center gap-3 transform translate-y-20 opacity-0 transition-all pointer-events-none max-w-md">
        <div class="p-1 bg-green-500 text-white rounded-full">
            <i data-lucide="check" class="w-4 h-4"></i>
        </div>
        <span id="toastMessage" class="text-sm font-medium text-slate-200">Action completed successfully.</span>
    </div>

    <!-- AOS Animation Script CDN -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- Custom Main JS -->
    <script src="{{ asset('js/main.js') }}"></script>
</body>
</html>
