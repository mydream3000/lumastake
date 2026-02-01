import './bootstrap';
import Alpine from 'alpinejs';
import { createApp } from 'vue'

window.Alpine = Alpine;

// Register global function for Alpine.js phone input component BEFORE Alpine.start()
// This function is called from x-data attribute in Blade template
// Countries are preloaded in window.__GEOIP_COUNTRIES__ from public layout
// Team Carousel component
window.teamCarousel = function() {
    return {
        currentIndex: 0,
        autoplayInterval: null,
        slidesPerView: 3,
        team: [
            {
                name: 'Alan Campbell',
                role: 'CEO',
                image: '/img/home/team-1.svg',
                description: 'Responsible for the overall vision, strategy, and direction of the organization. Ensures alignment between all departments and represents the company to stakeholders, investors, and the public.'
            },
            {
                name: 'Emily Rossi',
                role: 'CFO',
                image: '/img/home/team-2.svg',
                description: 'Manages the financial aspects, including budgeting, forecasting, and financial reporting. Ensures the platform\'s financial health and compliance with regulatory standards.'
            },
            {
                name: 'Christopher Taylor',
                role: 'COO',
                image: '/img/home/team-3.svg',
                description: 'Responsible for optimizing operational processes and ensuring efficient day-to-day functioning. Manages teams to enhance productivity and streamline services.'
            },
            {
                name: 'Fawzi Khabaz',
                role: 'CMO',
                image: '/img/home/team-4.svg',
                description: 'Develops and implements marketing strategies to promote the platform, attract users, and build brand presence. Focuses on community engagement and user retention.'
            },
            {
                name: 'Daniel Craig',
                role: 'CTO',
                image: '/img/home/team-5.svg',
                description: 'Oversees the technological development and infrastructure of the platform. Ensures robust security, scalability, and innovative technological solutions that drive the platform\'s success.'
            }
        ],

        init() {
            this.updateSlidesPerView();
            window.addEventListener('resize', () => this.updateSlidesPerView());
            this.startAutoplay();
        },

        updateSlidesPerView() {
            this.slidesPerView = window.innerWidth < 768 ? 1 : 3;
        },

        get totalDots() {
            return Math.ceil(this.team.length - this.slidesPerView + 1);
        },

        get maxIndex() {
            return Math.max(0, this.team.length - this.slidesPerView);
        },

        goTo(index) {
            this.currentIndex = Math.max(0, Math.min(index, this.maxIndex));
            this.restartAutoplay();
        },

        next() {
            if (this.currentIndex < this.maxIndex) {
                this.currentIndex++;
            } else {
                this.currentIndex = 0;
            }
        },

        startAutoplay() {
            this.autoplayInterval = setInterval(() => this.next(), 4000);
        },

        stopAutoplay() {
            if (this.autoplayInterval) {
                clearInterval(this.autoplayInterval);
            }
        },

        restartAutoplay() {
            this.stopAutoplay();
            this.startAutoplay();
        },

        get translateX() {
            const slideWidth = 100 / this.slidesPerView;
            return this.currentIndex * slideWidth;
        }
    }
}

window.phoneInput = function() {
    return {
        open: false,
        phone: '',
        countries: [],
        selectedCountry: {code: 'US', name: 'United States', phone_code: '+1', flag_class: 'fi fi-us'},

        init() {
            // Use preloaded countries from layout (no API call needed)
            this.countries = window.__GEOIP_COUNTRIES__ || [];

            // Default to US
            if (this.countries.length > 0) {
                const us = this.countries.find(c => c.code === 'US');
                if (us) this.selectedCountry = us;
            }
        },

        selectCountry(country) {
            this.selectedCountry = country;
            this.open = false;
        },

        formatPhone(event) {
            let value = event.target.value.replace(/\D/g, '');
            if (value.length > 15) {
                value = value.slice(0, 15);
            }
            let formatted = '';
            for (let i = 0; i < value.length; i++) {
                if (i > 0 && i % 3 === 0) {
                    formatted += ' ';
                }
                formatted += value[i];
            }
            this.phone = formatted;
        }
    }
}

Alpine.start();

/**
 * Кастомный лоадер для монтирования Vue компонентов
 */
function mountComponent(selector, component, props = {}) {
    const element = document.querySelector(selector)
    if (!element) return null

    if (typeof component === 'string') {
        import(`./components/${component}.vue`)
            .then(module => {
                const mountPoint = document.createElement('div')
                element.appendChild(mountPoint)
                const app = createApp(module.default, props)
                app.mount(mountPoint)
            })
            .catch(err => console.error(`Failed to load component ${component}:`, err))
    } else {
        const mountPoint = document.createElement('div')
        element.appendChild(mountPoint)
        const app = createApp(component, props)
        return app.mount(mountPoint)
    }
}

window.mountComponent = mountComponent

// Автоматическое монтирование компонентов
document.addEventListener('DOMContentLoaded', () => {
    const registerFormEl = document.querySelector('.js-register-form')
    if (registerFormEl) {
        import('./components/auth/RegisterForm.vue').then(module => {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            const refCode = registerFormEl.dataset.ref
            mountComponent('.js-register-form', module.default, { csrfToken, refCode })
        })
    }
});

// === CUSTOM CURSOR ===
document.addEventListener('DOMContentLoaded', () => {
    // Проверка на мобильные устройства
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) || window.innerWidth < 1024;

    if (isMobile) return; // Не запускать на мобильных

    // Создаем элементы курсора
    const cursor = document.createElement('div');
    const cursorDot = document.createElement('div');

    cursor.className = 'custom-cursor';
    cursorDot.className = 'custom-cursor-dot';

    document.body.appendChild(cursor);
    document.body.appendChild(cursorDot);

    // Позиции курсора
    let mouseX = 0;
    let mouseY = 0;

    // Текущие позиции элементов (для плавной анимации)
    let cursorX = 0;
    let cursorY = 0;
    let dotX = 0;
    let dotY = 0;

    // Скорость следования (чем меньше - тем медленнее)
    const cursorSpeed = 0.15; // Окружность следует медленнее
    const dotSpeed = 0.3;     // Точка следует быстрее

    // Переменная для отслеживания скорости движения мыши
    let lastMouseX = 0;
    let lastMouseY = 0;
    let mouseSpeed = 0;

    // Отслеживание позиции мыши
    document.addEventListener('mousemove', (e) => {
        mouseX = e.clientX;
        mouseY = e.clientY;

        // Вычисляем скорость движения мыши
        const deltaX = mouseX - lastMouseX;
        const deltaY = mouseY - lastMouseY;
        mouseSpeed = Math.sqrt(deltaX * deltaX + deltaY * deltaY);

        lastMouseX = mouseX;
        lastMouseY = mouseY;
    });

    // Функция плавной интерполяции (lerp)
    function lerp(start, end, factor) {
        return start + (end - start) * factor;
    }

    // Анимация курсора
    function animateCursor() {
        // Динамическая скорость в зависимости от скорости движения мыши
        const dynamicDotSpeed = mouseSpeed > 10 ? 0.5 : dotSpeed; // Резкое ускорение при быстром движении
        const dynamicCursorSpeed = mouseSpeed > 10 ? 0.25 : cursorSpeed;

        // Плавное следование за мышью
        cursorX = lerp(cursorX, mouseX, dynamicCursorSpeed);
        cursorY = lerp(cursorY, mouseY, dynamicCursorSpeed);

        dotX = lerp(dotX, mouseX, dynamicDotSpeed);
        dotY = lerp(dotY, mouseY, dynamicDotSpeed);

        // Применяем позиции
        cursor.style.left = cursorX + 'px';
        cursor.style.top = cursorY + 'px';

        cursorDot.style.left = dotX + 'px';
        cursorDot.style.top = dotY + 'px';

        // Постепенно уменьшаем скорость
        mouseSpeed *= 0.95;

        requestAnimationFrame(animateCursor);
    }

    // Запускаем анимацию
    animateCursor();

    // Эффекты при наведении на интерактивные элементы
    const interactiveElements = document.querySelectorAll('a, button, input, textarea, select, [role="button"]');

    interactiveElements.forEach(element => {
        element.addEventListener('mouseenter', () => {
            document.body.classList.add('cursor-hover');
        });

        element.addEventListener('mouseleave', () => {
            document.body.classList.remove('cursor-hover');
        });
    });

    // Эффект при клике
    document.addEventListener('mousedown', () => {
        document.body.classList.add('cursor-click');
    });

    document.addEventListener('mouseup', () => {
        document.body.classList.remove('cursor-click');
    });

    // Скрыть курсор когда мышь покидает окно
    document.addEventListener('mouseleave', () => {
        cursor.style.opacity = '0';
        cursorDot.style.opacity = '0';
    });

    document.addEventListener('mouseenter', () => {
        cursor.style.opacity = '1';
        cursorDot.style.opacity = '1';
    });
});
