document.addEventListener('DOMContentLoaded', function () {
    const maps = document.querySelectorAll('.swell-styled-map-container.-fade-in');

    if (maps.length === 0) return;

    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.3
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    maps.forEach(map => {
        observer.observe(map);
    });
});
