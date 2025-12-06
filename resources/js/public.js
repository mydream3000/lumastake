import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

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
