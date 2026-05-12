/* ── IPTV HOME PAGE SCRIPTS ── */

document.addEventListener('DOMContentLoaded', function () {

    // ── AUTO-SCROLL MOVIE SLIDER ──
    const slider = document.getElementById('movieSlider');
    if (slider) {
        let paused = false;
        let interval = setInterval(() => {
            if (paused) return;
            const card = slider.querySelector('.movie-card');
            const cardW = card ? card.offsetWidth + 18 : 238;
            if (slider.scrollLeft + slider.clientWidth >= slider.scrollWidth - cardW) {
                slider.scrollTo({ left: 0, behavior: 'smooth' });
            } else {
                slider.scrollBy({ left: cardW, behavior: 'smooth' });
            }
        }, 3000);
        slider.addEventListener('mouseenter', () => paused = true);
        slider.addEventListener('mouseleave', () => paused = false);
        slider.addEventListener('touchstart', () => paused = true, { passive: true });
        slider.addEventListener('touchend', () => setTimeout(() => paused = false, 2000), { passive: true });
    }

    // ── STAGGERED FADE-IN ON SCROLL ──
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, i) => {
            if (entry.isIntersecting) {
                entry.target.style.animationDelay = (i * 0.06) + 's';
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    const cardObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                cardObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll(
        '.feat-card, .price-card, .review-card, .device-card, .why-card, .category-card'
    ).forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(el);
        cardObserver.observe(el);
    });

    // ── PRICING PERIOD TOGGLE ──
    (function () {
        var pricing = {
            monthly: {
                starter: { price: '$11<span style="font-size:1.5rem">.99</span>', period: '/ month', savings: null },
                standard: { price: '$19<span style="font-size:1.5rem">.99</span>', period: '/ month', savings: null },
                family: { price: '$26<span style="font-size:1.5rem">.99</span>', period: '/ month', savings: null },
                premium: { price: '$34<span style="font-size:1.5rem">.99</span>', period: '/ month', savings: null }
            },
            quarterly: {
                starter: { price: '$30<span style="font-size:1.5rem">.57</span>', period: '/ 3 months', savings: 'Save 15%' },
                standard: { price: '$50<span style="font-size:1.5rem">.97</span>', period: '/ 3 months', savings: 'Save 15%' },
                family: { price: '$68<span style="font-size:1.5rem">.82</span>', period: '/ 3 months', savings: 'Save 15%' },
                premium: { price: '$89<span style="font-size:1.5rem">.22</span>', period: '/ 3 months', savings: 'Save 15%' }
            },
            biannual: {
                starter: { price: '$53<span style="font-size:1.5rem">.94</span>', period: '/ 6 months', savings: 'Save 25%' },
                standard: { price: '$89<span style="font-size:1.5rem">.94</span>', period: '/ 6 months', savings: 'Save 25%' },
                family: { price: '$121<span style="font-size:1.5rem">.44</span>', period: '/ 6 months', savings: 'Save 25%' },
                premium: { price: '$157<span style="font-size:1.5rem">.44</span>', period: '/ 6 months', savings: 'Save 25%' }
            },
            yearly: {
                starter: { price: '$96<span style="font-size:1.5rem">.00</span>', period: '/ year', savings: 'Save 33%' },
                standard: { price: '$159<span style="font-size:1.5rem">.84</span>', period: '/ year', savings: 'Save 33%' },
                family: { price: '$215<span style="font-size:1.5rem">.88</span>', period: '/ year', savings: 'Save 33%' },
                premium: { price: '$279<span style="font-size:1.5rem">.88</span>', period: '/ year', savings: 'Save 33%' }
            }
        };

        function applyPeriod(period) {
            ['starter', 'standard', 'family', 'premium'].forEach(function (plan) {
                var card = document.querySelector('[data-plan="' + plan + '"]');
                if (!card) return;
                var data = pricing[period][plan];
                var priceEl   = card.querySelector('.plan-price');
                var periodEl  = card.querySelector('.plan-period');
                var savingsEl = card.querySelector('.plan-savings');
                if (priceEl)   priceEl.innerHTML = data.price;
                if (periodEl)  periodEl.textContent = data.period;
                if (savingsEl) {
                    if (data.savings) {
                        savingsEl.textContent = data.savings;
                        savingsEl.style.display = 'inline-flex';
                    } else {
                        savingsEl.style.display = 'none';
                    }
                }
            });
        }

        var toggleWrap = document.getElementById('pricingToggle');
        if (toggleWrap) {
            var btns = toggleWrap.querySelectorAll('.ptoggle-btn');
            btns.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    btns.forEach(function (b) { b.classList.remove('active'); });
                    btn.classList.add('active');
                    applyPeriod(btn.getAttribute('data-period'));
                });
            });
        }
    })();

});
