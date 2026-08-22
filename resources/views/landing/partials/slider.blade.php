<?php
// Simple PHP Slider Component
// Converted from React to vanilla JavaScript
?>
<div class="relative w-full h-screen overflow-hidden" id="slider-container">
    <!-- Image Container -->
    <div class="relative w-full h-full">
        <?php
        $images = [
            landing_asset('landing/sliders/2.webp'),
            landing_asset('landing/sliders/4.webp'),
            landing_asset('landing/sliders/3.webp'),
            landing_asset('landing/sliders/1.webp'),
        ];
        foreach ($images as $index => $image):
        ?>
            <div class="slider-image absolute inset-0 transition-opacity duration-600 <?php echo $index === 0 ? 'opacity-100' : 'opacity-0'; ?>" data-index="<?php echo $index; ?>">
                <img
                    src="<?php echo $image; ?>"
                    alt="Slide <?php echo $index + 1; ?>"
                    class="w-full h-full object-cover"
                    loading="<?php echo $index === 0 ? 'eager' : 'lazy'; ?>"
                    onerror="this.src='https://via.placeholder.com/800x500/4A90E2/FFFFFF?text=Slide+<?php echo $index + 1; ?>'"
                />
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Left Arrow -->
    <button
        onclick="window.goToPreviousSlide && window.goToPreviousSlide()"
        class="absolute left-2 sm:left-4 top-1/2 transform -translate-y-1/2 bg-white/90 hover:bg-white text-gray-800 p-2.5 sm:p-3 rounded-full shadow-lg transition-all z-30 min-h-[44px] min-w-[44px] flex items-center justify-center hover:scale-110 active:scale-90 pointer-events-auto"
        aria-label="Previous slide"
        type="button"
    >
        <svg
            class="w-5 h-5 sm:w-6 sm:h-6"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15 19l-7-7 7-7"
            />
        </svg>
    </button>

    <!-- Right Arrow -->
    <button
        onclick="window.goToNextSlide && window.goToNextSlide()"
        class="absolute right-2 sm:right-4 top-1/2 transform -translate-y-1/2 bg-white/90 hover:bg-white text-gray-800 p-2.5 sm:p-3 rounded-full shadow-lg transition-all z-30 min-h-[44px] min-w-[44px] flex items-center justify-center hover:scale-110 active:scale-90 pointer-events-auto"
        aria-label="Next slide"
        type="button"
    >
        <svg
            class="w-5 h-5 sm:w-6 sm:h-6"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 5l7 7-7 7"
            />
        </svg>
    </button>

    <!-- Dots Indicator -->
    <div class="absolute bottom-3 sm:bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2 z-30 pointer-events-auto">
        <?php foreach ($images as $index => $image): ?>
            <button
                onclick="window.goToSlide && window.goToSlide(<?php echo $index; ?>)"
                class="slider-dot h-2 rounded-full transition-all duration-300 <?php echo $index === 0 ? 'bg-white w-8' : 'bg-white/50 w-2'; ?> cursor-pointer"
                aria-label="Go to slide <?php echo $index + 1; ?>"
                data-index="<?php echo $index; ?>"
                type="button"
            ></button>
        <?php endforeach; ?>
    </div>
</div>

<script>
(function() {
    let currentSlideIndex = 0;
    const totalSlides = <?php echo count($images); ?>;
    let slideInterval;

    function showSlide(index) {
        const images = document.querySelectorAll('.slider-image');
        const dots = document.querySelectorAll('.slider-dot');
        
        // Hide all images
        images.forEach(img => {
            img.classList.remove('opacity-100');
            img.classList.add('opacity-0');
        });
        
        // Show current image
        if (images[index]) {
            images[index].classList.remove('opacity-0');
            images[index].classList.add('opacity-100');
        }
        
        // Update dots
        dots.forEach((dot, i) => {
            if (i === index) {
                dot.classList.remove('bg-white/50', 'w-2');
                dot.classList.add('bg-white', 'w-8');
            } else {
                dot.classList.remove('bg-white', 'w-8');
                dot.classList.add('bg-white/50', 'w-2');
            }
        });
        
        currentSlideIndex = index;
    }

    window.goToNextSlide = function() {
        const nextIndex = (currentSlideIndex + 1) % totalSlides;
        showSlide(nextIndex);
        resetAutoSlide();
    };

    window.goToPreviousSlide = function() {
        const prevIndex = currentSlideIndex === 0 ? totalSlides - 1 : currentSlideIndex - 1;
        showSlide(prevIndex);
        resetAutoSlide();
    };

    window.goToSlide = function(index) {
        showSlide(index);
        resetAutoSlide();
    };

    function resetAutoSlide() {
        clearInterval(slideInterval);
        slideInterval = setInterval(window.goToNextSlide, 4000);
    }

    // Initialize auto-slide when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            resetAutoSlide();
        });
    } else {
        resetAutoSlide();
    }

    // Pause on hover
    const sliderContainer = document.getElementById('slider-container');
    if (sliderContainer) {
        sliderContainer.addEventListener('mouseenter', () => {
            clearInterval(slideInterval);
        });

        sliderContainer.addEventListener('mouseleave', () => {
            resetAutoSlide();
        });
    }
})();
</script>

<style>
.slider-image {
    transition: opacity 0.6s ease-in-out;
}
</style>
