<?php ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews - NEORA Therapy & Audiology Clinic</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Custom Styles -->
    <style>
        html {
            scroll-behavior: smooth;
            -webkit-text-size-adjust: 100%;
        }
        
        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen',
                'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue',
                sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
            -webkit-tap-highlight-color: transparent;
        }
        
        * {
            box-sizing: border-box;
        }
        
        img {
            max-width: 100%;
            height: auto;
            display: block;
        }
        
        button, a, input, select, textarea {
            touch-action: manipulation;
        }
        
        button {
            user-select: none;
            -webkit-user-select: none;
        }
        
        * {
            transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
        }
        
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }
    </style>
</head>
<body>
    @include('landing.partials.navbar')

    <!-- Reviews Section -->
    <section class="py-12 sm:py-16 md:py-20 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="text-center mb-8 sm:mb-12" data-aos="fade-up">
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                    Reviews from Families
                </h1>
                <p class="text-gray-600 text-base sm:text-lg max-w-2xl mx-auto">
                    Read what families have to say about their experience with NEORA Therapy & Audiology Clinic
                </p>
            </div>

            <!-- Back to Home Link -->
            <div class="mb-6 sm:mb-8" data-aos="fade-up" data-aos-delay="100">
                <a href="{{ $baseUrl }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-semibold transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Home
                </a>
            </div>

            <?php
            $testimonials = [];
            try {
                $stmt = $pdo->query("SELECT id, `text`, author, `location`, photo_path FROM reviews ORDER BY display_order ASC, id DESC");
                $testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                // keep empty; section still renders
            }
            ?>

            <!-- Reviews Grid -->
            <?php if (!empty($testimonials)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 md:gap-8 mb-8 sm:mb-12">
                <?php
                foreach ($testimonials as $index => $testimonial):
                    $photoPath = !empty($testimonial['photo_path']) ? $testimonial['photo_path'] : null;
                ?>
                    <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-all duration-300 p-5 sm:p-6 border border-gray-200" data-aos="fade-up" data-aos-delay="<?php echo ($index % 3) * 100; ?>">
                        <div class="flex items-center gap-4 mb-4">
                            <?php if ($photoPath && file_exists(public_path($photoPath))): ?>
                                <img src="<?php echo htmlspecialchars($photoPath); ?>" alt="<?php echo htmlspecialchars($testimonial['author']); ?>" class="w-14 h-14 sm:w-16 sm:h-16 rounded-full object-cover flex-shrink-0">
                            <?php else: ?>
                                <?php 
                                $colors = ['bg-amber-700', 'bg-teal-600', 'bg-slate-700', 'bg-rose-600', 'bg-indigo-600', 'bg-emerald-600'];
                                $colorIndex = $index % count($colors);
                                ?>
                                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full <?php echo $colors[$colorIndex]; ?> flex items-center justify-center flex-shrink-0">
                                    <span class="text-white text-lg sm:text-xl font-semibold">
                                        <?php echo strtoupper(substr($testimonial['author'], 0, 1)); ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-base sm:text-lg text-gray-800">
                                    <?php echo htmlspecialchars($testimonial['author']); ?>
                                </div>
                                <?php if (!empty($testimonial['location'])): ?>
                                    <div class="text-sm text-gray-500">
                                        <?php echo htmlspecialchars($testimonial['location']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="text-sm sm:text-base text-gray-700 leading-relaxed">
                            "<?php echo htmlspecialchars($testimonial['text']); ?>"
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-12 sm:py-16" data-aos="fade-up">
                <svg class="w-16 h-16 sm:w-20 sm:h-20 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <p class="text-gray-500 text-lg sm:text-xl mb-4">No reviews yet.</p>
                <p class="text-gray-400 text-sm sm:text-base">Check back soon for reviews from our families.</p>
            </div>
            <?php endif; ?>

            <!-- Review Submission Form -->
            <div class="max-w-2xl mx-auto mt-12 sm:mt-16" data-aos="fade-up">
                <!-- Write Review Button -->
                <div class="text-center mb-4">
                    <button 
                        type="button"
                        id="reviewFormToggle"
                        class="inline-flex items-center justify-center px-6 sm:px-8 py-3 sm:py-3.5 bg-cyan-50 hover:bg-cyan-100 border-2 border-cyan-400 text-cyan-700 font-semibold rounded-lg transition-all duration-300 shadow-md hover:shadow-lg hover:scale-105 active:scale-95 text-sm sm:text-base ring-2 ring-cyan-200 ring-offset-2"
                    >
                        <svg 
                            class="w-5 h-5 sm:w-6 sm:h-6 mr-2"
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Write a review
                    </button>
                </div>
                
                <!-- Collapsible Form Content -->
                <div id="reviewFormContent" class="hidden bg-white rounded-xl sm:rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="px-6 sm:px-8 py-6 sm:py-8">
                        <form id="reviewForm" enctype="multipart/form-data" class="space-y-4 sm:space-y-5">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="author" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Your Name <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="author" 
                                        name="author" 
                                        required
                                        class="w-full px-4 py-2.5 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm sm:text-base"
                                        placeholder="Enter your name"
                                    >
                                </div>
                                
                                <div>
                                    <label for="photo" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Profile Photo <span class="text-xs font-normal text-gray-500">(Optional)</span>
                                    </label>
                                    <div class="flex items-center gap-2">
                                        <input 
                                            type="file" 
                                            id="photo" 
                                            name="photo"
                                            accept="image/jpeg,image/jpg,image/png"
                                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer"
                                        >
                                        <div id="photoPreview" class="hidden">
                                            <img id="previewImg" src="" alt="Preview" class="w-12 h-12 rounded-full object-cover border-2 border-gray-200">
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">For your profile picture, not session photos</p>
                                </div>
                            </div>
                            
                            <div>
                                <label for="location" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Location
                                </label>
                                <input 
                                    type="text" 
                                    id="location" 
                                    name="location"
                                    class="w-full px-4 py-2.5 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm sm:text-base"
                                    placeholder="Enter your location (optional)"
                                >
                            </div>
                            
                            <div>
                                <label for="text" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Your Review <span class="text-red-500">*</span>
                                </label>
                                <textarea 
                                    id="text" 
                                    name="text" 
                                    required
                                    rows="4"
                                    class="w-full px-4 py-2.5 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all resize-none text-sm sm:text-base"
                                    placeholder="Share your experience with us..."
                                ></textarea>
                            </div>
                            
                            
                            <div id="formMessage" class="hidden p-3 rounded-lg text-sm"></div>
                            
                            <button 
                                type="submit"
                                id="submitBtn"
                                class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-3 sm:py-3.5 rounded-lg font-semibold hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-[1.02] active:scale-[0.98] text-sm sm:text-base disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span id="submitBtnText">Submit Review</span>
                                <span id="submitBtnLoading" class="hidden">Submitting...</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('landing.partials.footer')

    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            offset: 100
        });

        // Collapsible form toggle
        const reviewFormToggle = document.getElementById('reviewFormToggle');
        const reviewFormContent = document.getElementById('reviewFormContent');

        if (reviewFormToggle && reviewFormContent) {
            reviewFormToggle.addEventListener('click', function() {
                const isHidden = reviewFormContent.classList.contains('hidden');
                if (isHidden) {
                    reviewFormContent.classList.remove('hidden');
                    // Scroll to form smoothly
                    reviewFormContent.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                } else {
                    reviewFormContent.classList.add('hidden');
                }
            });
        }

        // Photo preview functionality
        const photoInput = document.getElementById('photo');
        const photoPreview = document.getElementById('photoPreview');
        const previewImg = document.getElementById('previewImg');

        if (photoInput) {
            photoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        photoPreview.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                } else {
                    photoPreview.classList.add('hidden');
                }
            });
        }

        // Review form submission
        const reviewForm = document.getElementById('reviewForm');
        if (reviewForm) {
            reviewForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const submitBtn = document.getElementById('submitBtn');
                const submitBtnText = document.getElementById('submitBtnText');
                const submitBtnLoading = document.getElementById('submitBtnLoading');
                const formMessage = document.getElementById('formMessage');
                
                // Disable submit button
                submitBtn.disabled = true;
                submitBtnText.classList.add('hidden');
                submitBtnLoading.classList.remove('hidden');
                formMessage.classList.add('hidden');
                
                // Get form data
                const formData = new FormData(reviewForm);
                
                try {
                    const response = await fetch('{{ route('api.submit-review') }}', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        // Show success message
                        formMessage.textContent = result.message || 'Thank you for your review! It has been submitted successfully.';
                        formMessage.className = 'p-3 rounded-lg text-sm bg-green-100 text-green-800 border border-green-200';
                        formMessage.classList.remove('hidden');
                        
                        // Reset form
                        reviewForm.reset();
                        photoPreview.classList.add('hidden');
                        
                        // Reload page after 2 seconds to show the new review
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    } else {
                        // Show error message
                        formMessage.textContent = result.message || 'Failed to submit review. Please try again.';
                        formMessage.className = 'p-3 rounded-lg text-sm bg-red-100 text-red-800 border border-red-200';
                        formMessage.classList.remove('hidden');
                        
                        // Re-enable submit button
                        submitBtn.disabled = false;
                        submitBtnText.classList.remove('hidden');
                        submitBtnLoading.classList.add('hidden');
                    }
                } catch (error) {
                    // Show error message
                    formMessage.textContent = 'An error occurred. Please try again later.';
                    formMessage.className = 'p-3 rounded-lg text-sm bg-red-100 text-red-800 border border-red-200';
                    formMessage.classList.remove('hidden');
                    
                    // Re-enable submit button
                    submitBtn.disabled = false;
                    submitBtnText.classList.remove('hidden');
                    submitBtnLoading.classList.add('hidden');
                }
            });
        }
    </script>
</body>
</html>
