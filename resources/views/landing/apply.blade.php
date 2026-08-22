@php
$error = $error ?? '';
$generated_by = $generatedBy ?? ($generated_by ?? null);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Apply - {{ $siteName }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['DM Sans', 'system-ui', 'sans-serif'] },
                    colors: {
                        primary: { 50: '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe', 300: '#93c5fd', 400: '#60a5fa', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8', 800: '#1e40af', 900: '#1e3a8a' },
                        slate: { 850: '#172033' }
                    }
                }
            }
        }
    </script>
    <style>
        input:focus, select:focus, textarea:focus { outline: none; }
        /* 16px base on inputs to prevent iOS zoom */
        input, select, textarea { font-size: 16px; }
        @media (min-width: 640px) { input, select, textarea { font-size: 0.875rem; } }
        input[type="file"]::file-selector-button { padding: 0.5rem 0.75rem; margin-right: 0.75rem; border-radius: 0.375rem; border: 1px solid #e2e8f0; background: #f8fafc; font-size: 0.875rem; cursor: pointer; min-height: 44px; }
        input[type="file"]::file-selector-button:hover { background: #f1f5f9; }
        input[type="checkbox"] { accent-color: #2563eb; min-width: 1.25rem; min-height: 1.25rem; }
        select.select-arrow { background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3e%3cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3e%3c/svg%3e"); background-position: right 0.5rem center; background-repeat: no-repeat; background-size: 1.25rem; padding-right: 2.5rem; }
        .safe-area-padding { padding-left: env(safe-area-inset-left, 0); padding-right: env(safe-area-inset-right, 0); padding-top: env(safe-area-inset-top, 0); }
    </style>
</head>
<body class="min-h-screen bg-slate-50 font-sans antialiased text-slate-800 safe-area-padding">
    <div class="max-w-2xl lg:max-w-4xl xl:max-w-5xl mx-auto px-3 sm:px-6 lg:px-10 xl:px-12 py-5 sm:py-8 lg:py-12 w-full min-w-0">
        <!-- Header card -->
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden mb-5 sm:mb-8">
            <div class="bg-white px-4 sm:px-6 lg:px-10 xl:px-12 py-6 sm:py-8 lg:py-10 text-center border-b border-slate-200/80">
                <img src="{{ $baseUrl }}uploads/logo.png" alt="Logo" class="h-12 sm:h-14 lg:h-16 mx-auto object-contain w-auto">
                <p class="text-slate-600 text-xs sm:text-sm leading-relaxed max-w-xl lg:max-w-2xl mx-auto mt-4 sm:mt-6 px-0">
                    Neora is a therapy space providing individualized support for speech, language, communication, sensory, and developmental needs. We follow an evidence-based and structured approach, focusing on functional skills and meaningful progress through collaboration with families.
                </p>
                <h1 class="text-slate-800 font-semibold text-base sm:text-lg lg:text-xl mt-4 sm:mt-5 tracking-tight">Hiring Application Form</h1>
            </div>
        </div>

        <?php if ($error): ?>
        <div class="mb-4 sm:mb-6 p-3 sm:p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs sm:text-sm font-medium flex items-start gap-3">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/></svg>
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <form id="apply-form" method="post" action="{{ route('apply.store', array_filter(['ref' => request('ref')])) }}" enctype="multipart/form-data" class="space-y-4 sm:space-y-6 lg:space-y-8">
            @csrf
            <!-- Basic Details -->
            <section class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-slate-200/80 p-4 sm:p-6 lg:p-8 xl:p-10">
                <h2 class="text-slate-800 font-semibold text-sm sm:text-base mb-4 sm:mb-5 lg:mb-6 pb-3 border-b border-slate-200 flex items-center gap-2">
                    <span class="w-1 h-4 sm:h-5 bg-primary-500 rounded-full shrink-0"></span>
                    Basic Details
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-5 lg:gap-6 xl:gap-6">
                    <div class="min-w-0">
                        <label class="block text-sm font-medium text-slate-700 mb-1 sm:mb-1.5">1. Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required maxlength="255" class="w-full px-3 sm:px-4 py-3 sm:py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition min-h-[44px] sm:min-h-0">
                    </div>
                    <div class="min-w-0">
                        <label class="block text-sm font-medium text-slate-700 mb-1 sm:mb-1.5">2. Mobile <span class="text-red-500">*</span></label>
                        <input type="text" name="mobile" value="<?php echo htmlspecialchars($_POST['mobile'] ?? ''); ?>" required maxlength="50" placeholder="10 digits" class="w-full px-3 sm:px-4 py-3 sm:py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition min-h-[44px] sm:min-h-0">
                    </div>
                    <div class="min-w-0">
                        <label class="block text-sm font-medium text-slate-700 mb-1 sm:mb-1.5">3. Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required maxlength="255" class="w-full px-3 sm:px-4 py-3 sm:py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition min-h-[44px] sm:min-h-0">
                    </div>
                    <div class="min-w-0">
                        <label class="block text-sm font-medium text-slate-700 mb-1 sm:mb-1.5">4. City</label>
                        <input type="text" name="city" value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>" maxlength="100" class="w-full px-3 sm:px-4 py-3 sm:py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition min-h-[44px] sm:min-h-0">
                    </div>
                </div>
            </section>

            <!-- Professional Details -->
            <section class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-slate-200/80 p-4 sm:p-6 lg:p-8 xl:p-10">
                <h2 class="text-slate-800 font-semibold text-sm sm:text-base mb-4 sm:mb-5 lg:mb-6 pb-3 border-b border-slate-200 flex items-center gap-2">
                    <span class="w-1 h-4 sm:h-5 bg-primary-500 rounded-full shrink-0"></span>
                    Professional Details
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-5 lg:gap-6 xl:gap-6">
                    <div class="min-w-0">
                        <label class="block text-sm font-medium text-slate-700 mb-1 sm:mb-1.5">5. Applying For</label>
                        <select name="applying_for" class="select-arrow w-full px-3 sm:px-4 py-3 sm:py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition appearance-none min-h-[44px] sm:min-h-0">
                            <option value="">Select role...</option>
                            <option value="Speech therapist and pathologist"<?php echo (isset($_POST['applying_for']) && $_POST['applying_for'] === 'Speech therapist and pathologist') ? ' selected' : ''; ?>>Speech therapist and pathologist</option>
                            <option value="ABA therapist"<?php echo (isset($_POST['applying_for']) && $_POST['applying_for'] === 'ABA therapist') ? ' selected' : ''; ?>>ABA therapist</option>
                            <option value="Occupational therapist"<?php echo (isset($_POST['applying_for']) && $_POST['applying_for'] === 'Occupational therapist') ? ' selected' : ''; ?>>Occupational therapist</option>
                            <option value="Special educator"<?php echo (isset($_POST['applying_for']) && $_POST['applying_for'] === 'Special educator') ? ' selected' : ''; ?>>Special educator</option>
                            <option value="Coordinator"<?php echo (isset($_POST['applying_for']) && $_POST['applying_for'] === 'Coordinator') ? ' selected' : ''; ?>>Coordinator</option>
                        </select>
                    </div>
                    <div class="min-w-0">
                        <label class="block text-sm font-medium text-slate-700 mb-1 sm:mb-1.5">6. Qualification</label>
                        <input type="text" name="qualification" value="<?php echo htmlspecialchars($_POST['qualification'] ?? ''); ?>" maxlength="255" class="w-full px-3 sm:px-4 py-3 sm:py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition min-h-[44px] sm:min-h-0">
                    </div>
                    <div class="min-w-0">
                        <label class="block text-sm font-medium text-slate-700 mb-1 sm:mb-1.5">7. College</label>
                        <input type="text" name="college" value="<?php echo htmlspecialchars($_POST['college'] ?? ''); ?>" maxlength="255" class="w-full px-3 sm:px-4 py-3 sm:py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition min-h-[44px] sm:min-h-0">
                    </div>
                    <div class="min-w-0">
                        <label class="block text-sm font-medium text-slate-700 mb-1 sm:mb-1.5">8. Year</label>
                        <input type="text" name="year" value="<?php echo htmlspecialchars($_POST['year'] ?? ''); ?>" maxlength="20" placeholder="e.g. 2020" class="w-full px-3 sm:px-4 py-3 sm:py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition min-h-[44px] sm:min-h-0">
                    </div>
                </div>
            </section>

            <!-- Experience -->
            <section class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-slate-200/80 p-4 sm:p-6 lg:p-8 xl:p-10">
                <h2 class="text-slate-800 font-semibold text-sm sm:text-base mb-4 sm:mb-5 lg:mb-6 pb-3 border-b border-slate-200 flex items-center gap-2">
                    <span class="w-1 h-4 sm:h-5 bg-primary-500 rounded-full shrink-0"></span>
                    Experience
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-2 gap-4 sm:gap-5 lg:gap-6">
                    <div class="min-w-0">
                        <label class="block text-sm font-medium text-slate-700 mb-1 sm:mb-1.5">9. Years of Experience</label>
                        <input type="text" name="experience_years" value="<?php echo htmlspecialchars($_POST['experience_years'] ?? ''); ?>" maxlength="50" placeholder="e.g. 3 years" class="w-full px-3 sm:px-4 py-3 sm:py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition min-h-[44px] sm:min-h-0">
                    </div>
                    <div class="min-w-0">
                        <label class="block text-sm font-medium text-slate-700 mb-1 sm:mb-1.5">10. Current Place</label>
                        <input type="text" name="current_place" value="<?php echo htmlspecialchars($_POST['current_place'] ?? ''); ?>" maxlength="255" placeholder="Current organization" class="w-full px-3 sm:px-4 py-3 sm:py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition min-h-[44px] sm:min-h-0">
                    </div>
                </div>
            </section>

            <!-- Areas of Specialization -->
            <section class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-slate-200/80 p-4 sm:p-6 lg:p-8 xl:p-10">
                <h2 class="text-slate-800 font-semibold text-sm sm:text-base mb-4 sm:mb-5 lg:mb-6 pb-3 border-b border-slate-200 flex items-center gap-2">
                    <span class="w-1 h-4 sm:h-5 bg-primary-500 rounded-full shrink-0"></span>
                    12. Areas of Specialization
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-3 lg:gap-4">
                    <?php
                    $specializations = ['Autism', 'Speech Therapy', 'Occupational Therapy', 'Behavioural', 'Academic Support', 'Special Education', 'Counselling', 'Other'];
                    $selected = isset($_POST['areas_specialization']) && is_array($_POST['areas_specialization']) ? $_POST['areas_specialization'] : [];
                    foreach ($specializations as $s):
                        $checked = in_array($s, $selected) ? ' checked' : '';
                        $id = 'spec_' . preg_replace('/\s+/', '_', $s);
                    ?>
                    <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 hover:bg-slate-50 cursor-pointer transition min-h-[44px] sm:min-h-0 active:bg-slate-100">
                        <input type="checkbox" name="areas_specialization[]" value="<?php echo htmlspecialchars($s); ?>" id="<?php echo $id; ?>"<?php echo $checked; ?> class="w-5 h-5 sm:w-4 sm:h-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500 shrink-0">
                        <span class="text-sm font-medium text-slate-700"><?php echo htmlspecialchars($s); ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Languages & Joining -->
            <section class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-slate-200/80 p-4 sm:p-6 lg:p-8 xl:p-10">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-5 lg:gap-6">
                    <div class="min-w-0">
                        <label class="block text-sm font-medium text-slate-700 mb-1 sm:mb-1.5">13. Languages</label>
                        <input type="text" name="languages" value="<?php echo htmlspecialchars($_POST['languages'] ?? ''); ?>" maxlength="255" placeholder="e.g. Hindi, English" class="w-full px-3 sm:px-4 py-3 sm:py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition min-h-[44px] sm:min-h-0">
                    </div>
                    <div class="min-w-0">
                        <label class="block text-sm font-medium text-slate-700 mb-1 sm:mb-1.5">14. Joining Time</label>
                        <input type="text" name="joining_time" value="<?php echo htmlspecialchars($_POST['joining_time'] ?? ''); ?>" maxlength="100" placeholder="e.g. Immediate, 2 weeks" class="w-full px-3 sm:px-4 py-3 sm:py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition min-h-[44px] sm:min-h-0">
                    </div>
                </div>
            </section>

            <!-- Uploads -->
            <section class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-slate-200/80 p-4 sm:p-6 lg:p-8 xl:p-10">
                <h2 class="text-slate-800 font-semibold text-sm sm:text-base mb-4 sm:mb-5 lg:mb-6 pb-3 border-b border-slate-200 flex items-center gap-2">
                    <span class="w-1 h-4 sm:h-5 bg-primary-500 rounded-full shrink-0"></span>
                    Documents
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5 lg:gap-6">
                    <div class="min-w-0">
                        <label class="block text-sm font-medium text-slate-700 mb-1 sm:mb-1.5">15. Resume</label>
                        <input type="file" name="resume" accept=".pdf,.doc,.docx" class="w-full text-xs sm:text-sm text-slate-600 file:font-medium min-h-[44px] sm:min-h-0">
                        <p class="text-xs text-slate-500 mt-1">PDF, DOC or DOCX, max 5MB</p>
                    </div>
                    <div class="min-w-0">
                        <label class="block text-sm font-medium text-slate-700 mb-1 sm:mb-1.5">16. Certificate</label>
                        <input type="file" name="certificate" accept=".pdf,.doc,.docx" class="w-full text-xs sm:text-sm text-slate-600 file:font-medium min-h-[44px] sm:min-h-0">
                        <p class="text-xs text-slate-500 mt-1">PDF, DOC or DOCX, max 5MB</p>
                    </div>
                </div>
            </section>

            <!-- Why join Neora -->
            <section class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-slate-200/80 p-4 sm:p-6 lg:p-8 xl:p-10">
                <label class="block text-sm font-medium text-slate-700 mb-1 sm:mb-1.5 lg:mb-2">17. Why join Neora</label>
                <textarea name="why_join_neora" rows="4" maxlength="2000" placeholder="Share your motivation and how you align with our approach..." class="w-full px-3 sm:px-4 py-3 sm:py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition resize-y min-h-[100px]"><?php echo htmlspecialchars($_POST['why_join_neora'] ?? ''); ?></textarea>
            </section>

            <!-- Submit -->
            <div class="pt-0 sm:pt-2 pb-2 sm:pb-0">
                <button type="submit" id="submit-btn" class="w-full py-3.5 sm:py-3.5 px-4 sm:px-6 rounded-xl bg-primary-600 hover:bg-primary-700 active:bg-primary-800 text-white font-semibold text-base shadow-sm hover:shadow transition focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 flex items-center justify-center gap-2 min-h-[48px] touch-manipulation disabled:opacity-70 disabled:cursor-not-allowed">
                    <svg class="submit-icon w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    <span class="submit-text">Submit Application</span>
                </button>
            </div>
        </form>
    </div>
    <script>
        document.getElementById('apply-form').addEventListener('submit', function() {
            var btn = document.getElementById('submit-btn');
            if (btn.disabled) return;
            btn.disabled = true;
            btn.querySelector('.submit-text').textContent = 'Submitting…';
            btn.querySelector('.submit-icon').classList.add('animate-spin');
        });
    </script>
</body>
</html>
