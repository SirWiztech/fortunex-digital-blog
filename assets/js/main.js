/* Fortunexdigital — front-end behaviors */
(function () {
    'use strict';

    // Mobile nav toggle
    var toggle = document.querySelector('.nav-toggle');
    var nav = document.querySelector('.main-nav');
    if (toggle && nav) {
        toggle.addEventListener('click', function () {
            var open = nav.classList.toggle('open');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }

    // Cookie banner
    var banner = document.getElementById('cookie-banner');
    if (banner) {
        var accepted = false;
        try { accepted = localStorage.getItem('fxd_cookie_ok') === '1'; } catch (e) {}
        if (!accepted) {
            banner.hidden = false;
        }
        var btn = document.getElementById('cookie-accept');
        if (btn) {
            btn.addEventListener('click', function () {
                try { localStorage.setItem('fxd_cookie_ok', '1'); } catch (e) {}
                banner.hidden = true;
            });
        }
    }

    // Lazy-load images via data-src
    var lazyImgs = document.querySelectorAll('img[data-src]');
    if ('IntersectionObserver' in window && lazyImgs.length) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    var img = entry.target;
                    img.src = img.getAttribute('data-src');
                    img.removeAttribute('data-src');
                    io.unobserve(img);
                }
            });
        }, { rootMargin: '200px' });
        lazyImgs.forEach(function (img) { io.observe(img); });
    } else {
        lazyImgs.forEach(function (img) {
            img.src = img.getAttribute('data-src');
            img.removeAttribute('data-src');
        });
    }
})();
