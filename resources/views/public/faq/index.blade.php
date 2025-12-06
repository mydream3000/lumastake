@extends('layouts.public')

@section('content')
    <!-- Hero Section -->
    <section class="relative py-16 md:py-24 overflow-hidden">
        <div class="container mx-auto px-4 pb-10 text-left relative z-10">
            <h1 class="text-4xl md:text-6xl lg:text-8xl font-extrabold mb-6 gradient-text bg-clip-text text-transparent">
                Frequently Asked Questions
            </h1>
            <p class="text-lg md:text-2xl lg:text-5xl text-white max-w-8xl mx-auto">
                Here are some common questions about lumastake
            </p>
        </div>
        <div class="absolute top-20 right-10 w-[400px] h-[400px] z-[6]">
            <img src="assets/09af9496dcc2930ccbcbd9585a908dd018adf7b2.svg" alt="Background sphere" class="w-full h-full">
        </div>
        <div class="absolute top-10 right-20 w-[600px] h-[600px] z-[6]">
            <img src="assets/630ae6568f79e53581d752492d091b1828abdea6.svg" alt="Background sphere" class="w-full h-full">
        </div>

    <!-- FAQ Section -->

        <!-- Background Shape -->
        <div class="absolute inset-0 z-5">
            <img src="assets/79c6d9577f460daaeef00bd849f331f6ad393baa.png" alt="Background shape" class="w-full h-full object-cover">
        </div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-8xl mx-auto space-y-4">
                <!-- FAQ Items -->
                @foreach($faqs as $faq)
                    <div class="faq-item bg-[rgba(34,37,59,0.5)] border border-[rgba(217,217,217,0.42)] rounded-3xl p-6 md:p-8">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg md:text-2xl lg:text-3xl font-semibold text-white pr-4">
                                {{ $faq->question }}
                            </h3>
                            <button class="faq-toggle text-white text-2xl md:text-3xl font-semibold flex-shrink-0">
                                +
                            </button>
                        </div>
                        <div class="faq-content hidden mt-4">
                            <p class="text-white text-base md:text-lg">
                                {{ $faq->answer }}
                            </p>
                        </div>
                    </div>
                @endforeach

                <!-- See More Button -->
                @if($faqs->count() > 20)
                    <div class="text-center pt-8">
                        <button id="see-more-btn" class="text-[#00ffa3] text-xl md:text-2xl font-medium hover:text-[#00ffa3]/80 transition-colors">
                            See More..
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </section>

<style>
/* Hide FAQ items beyond the first 20 by default */
.faq-item:nth-child(n+21) {
    display: none;
}

/* FAQ Animation Styles */
.faq-content {
    max-height: 0;
    opacity: 0;
    overflow: hidden;
    transition: max-height 0.5s cubic-bezier(0.4, 0, 0.2, 1),
                opacity 0.4s ease-in-out 0.1s,
                transform 0.4s ease-in-out 0.1s;
    transform: translateY(-10px);
}

.faq-content.show {
    max-height: 300px;
    opacity: 1;
    transform: translateY(0);
}

.faq-toggle {
    transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.faq-toggle.rotated {
    transform: rotate(45deg);
}

/* See More Animation */
.faq-item.fade-in {
    animation: fadeInUp 0.6s ease-out forwards;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Hover effects */
.faq-item {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.faq-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 255, 163, 0.1);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get all FAQ items and buttons
    const faqItems = document.querySelectorAll('.faq-item');
    const faqToggles = document.querySelectorAll('.faq-toggle');
    const seeMoreBtn = document.getElementById('see-more-btn');

    let currentlyVisible = 20; // Initially show first 20 items
    const itemsPerPage = 20; // Show 20 more items each time
    const totalItems = faqItems.length;

    // Initialize FAQ content styles
    function initializeFAQStyles() {
        faqItems.forEach(item => {
            const content = item.querySelector('.faq-content');
            // Remove the hidden class and use CSS animations instead
            content.classList.remove('hidden');
            // Ensure content starts in collapsed state (CSS handles the styling)
            content.classList.remove('show');
        });
    }

    // Initially hide items beyond the first 20
    function initializePagination() {
        faqItems.forEach((item, index) => {
            if (index >= currentlyVisible) {
                item.style.display = 'none';
            }
        });

        // Hide "See More" button if all items are visible or doesn't exist
        if (!seeMoreBtn || currentlyVisible >= totalItems) {
            if (seeMoreBtn) seeMoreBtn.style.display = 'none';
        }
    }

    // Show more items when "See More" button is clicked with animation
    if (seeMoreBtn) {
        seeMoreBtn.addEventListener('click', function() {
            const newVisible = Math.min(currentlyVisible + itemsPerPage, totalItems);

            // Show the next batch of items with staggered animation
            for (let i = currentlyVisible; i < newVisible; i++) {
                if (faqItems[i]) {
                    const item = faqItems[i];

                    // Set initial state for animation
                    item.style.display = 'block';
                    item.style.opacity = '0';
                    item.style.transform = 'translateY(30px)';

                    // Add fade-in class with delay
                    setTimeout(() => {
                        item.classList.add('fade-in');
                        item.style.opacity = '1';
                        item.style.transform = 'translateY(0)';
                        item.style.transition = 'all 0.6s ease-out';
                    }, (i - currentlyVisible) * 100); // 100ms delay between each item
                }
            }

            currentlyVisible = newVisible;

            // Hide "See More" button if all items are now visible
            if (currentlyVisible >= totalItems) {
                seeMoreBtn.style.display = 'none';
            }
        });
    }

    // Enhanced FAQ toggle functionality with smooth animations
    faqToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const faqItem = this.closest('.faq-item');
            const content = faqItem.querySelector('.faq-content');
            const isCurrentlyOpen = content.classList.contains('show');

            // Close all FAQ items first with animation
            faqToggles.forEach(otherToggle => {
                const otherItem = otherToggle.closest('.faq-item');
                const otherContent = otherItem.querySelector('.faq-content');

                // Animate close using CSS classes only
                otherContent.classList.remove('show');

                // Reset button with rotation animation
                otherToggle.classList.remove('rotated');
                otherToggle.textContent = '+';
            });

            // If the clicked item wasn't open, open it with animation
            if (!isCurrentlyOpen) {
                // Small delay to ensure close animation completes first
                setTimeout(() => {
                    content.classList.add('show');

                    // Rotate button
                    this.classList.add('rotated');
                    this.textContent = '−'; // Using minus sign
                }, 150); // Slightly longer delay for smoother transition
            }
        });
    });

    // Initialize everything on page load
    initializeFAQStyles();
    initializePagination();
});
</script>
@endsection

