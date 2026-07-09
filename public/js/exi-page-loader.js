/* ══════════════════════════════════════════════════════════
   EXICOMPRAS — Page Loader (Influencers)
   Intercepta clicks en links a /shop/influencers* y muestra
   un overlay premium antes de navegar. Graceful degradation
   total: si JS falla, los links funcionan normalmente.
   ══════════════════════════════════════════════════════════ */

(function () {
    'use strict';

    if (!document.addEventListener || !document.body) return;

    var NAV_DELAY = 2500;             // ms entre click y navegación
    var FADE_OUT  = 400;              // ms del fade-out si se cancela
    var reduceMotion = !!(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);

    var overlay = null;
    var redirectTimer = null;
    var escListener = null;

    function matchInfluencersHref(href) {
        if (!href) return false;
        try {
            var u = new URL(href, window.location.origin);
            // /shop/influencers~10, /shop/influencers/2/, /shop/influencers.html, etc.
            return /\/shop\/influencers(\b|~|\/|\.|$)/i.test(u.pathname);
        } catch (e) {
            return false;
        }
    }

    function isSamePage(href) {
        try {
            var u = new URL(href, window.location.origin);
            return u.pathname === window.location.pathname
                && u.search === window.location.search;
        } catch (e) {
            return false;
        }
    }

    function buildOverlay() {
        if (overlay) return overlay;
        overlay = document.createElement('div');
        overlay.className = 'exi-page-loader';
        overlay.setAttribute('aria-hidden', 'true');
        overlay.setAttribute('role', 'status');
        overlay.setAttribute('aria-live', 'polite');
        overlay.innerHTML = [
            '<div class="exi-page-loader__bg" aria-hidden="true"></div>',
            '<div class="exi-page-loader__container">',
                '<p class="exi-page-loader__kicker">Cargando</p>',
                '<h2 class="exi-page-loader__title">Influencers</h2>',
                '<div class="exi-page-loader__art" aria-hidden="true">',
                    '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" width="76" height="76" fill="none" aria-hidden="true">',
                        '<path class="exi-art__bolsa" d="M14 22 L50 22 Q54 22 54 26 L54 56 Q54 60 50 60 L14 60 Q10 60 10 56 L10 26 Q10 22 14 22 Z" stroke="#ffffff" stroke-width="2.5" stroke-linejoin="round" fill="none" />',
                        '<path class="exi-art__asa" d="M22 22 Q22 12 32 12 Q42 12 42 22" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" fill="none" />',
                        '<path class="exi-art__corazon" d="M32 46 C20 36 16 30 16 24 C16 19.6 19 16 23 16 C26 16 29.5 17.5 32 21 C34.5 17.5 38 16 41 16 C45 16 48 19.6 48 24 C48 30 44 36 32 46 Z" fill="#FF6B35" style="transform-origin: 32px 24px; transform: scale(0);" />',
                    '</svg>',
                '</div>',
                '<p class="exi-page-loader__tagline">Donde el estilo se encuentra con el producto perfecto</p>',
            '</div>',
            '<div class="exi-page-loader__bar"><div class="exi-page-loader__progress"></div></div>'
        ].join('');
        document.body.appendChild(overlay);

        // Stroke-dash setup para las animaciones de dibujo
        try {
            var bolsa = overlay.querySelector('.exi-art__bolsa');
            var asa   = overlay.querySelector('.exi-art__asa');
            if (bolsa && bolsa.getTotalLength) {
                bolsa.style.strokeDasharray  = bolsa.getTotalLength();
                bolsa.style.strokeDashoffset = bolsa.getTotalLength();
            }
            if (asa && asa.getTotalLength) {
                asa.style.strokeDasharray  = asa.getTotalLength();
                asa.style.strokeDashoffset = asa.getTotalLength();
            }
        } catch (e) { /* SVG animations skipped gracefully */ }

        return overlay;
    }

    function triggerAnimations() {
        if (!overlay) return;
        requestAnimationFrame(function () {
            overlay.querySelectorAll('.exi-art__bolsa, .exi-art__asa').forEach(function (el) {
                el.style.transition = 'stroke-dashoffset 1.4s cubic-bezier(.4, 0, .2, 1)';
                el.style.strokeDashoffset = '0';
            });
            var corazon = overlay.querySelector('.exi-art__corazon');
            if (corazon) {
                corazon.style.transition = 'transform .6s cubic-bezier(.34, 1.56, .64, 1) 1.9s';
                corazon.style.transform = 'scale(1)';
            }
        });
    }

    function cancel() {
        if (redirectTimer) {
            clearTimeout(redirectTimer);
            redirectTimer = null;
        }
        if (escListener) {
            document.removeEventListener('keydown', escListener, true);
            escListener = null;
        }
        if (overlay) {
            overlay.classList.remove('is-active');
            var toRemove = overlay;
            setTimeout(function () {
                if (toRemove && toRemove.parentNode) {
                    toRemove.parentNode.removeChild(toRemove);
                }
                if (toRemove === overlay) overlay = null;
            }, FADE_OUT);
        }
    }

    function showAndRedirect(url) {
        // Reduced motion: skip the loader entirely and navigate immediately
        if (reduceMotion) {
            window.location.href = url;
            return;
        }

        // 1. Set the navigation timer FIRST so a render failure doesn't trap the user
        redirectTimer = setTimeout(function () {
            window.location.href = url;
        }, NAV_DELAY);

        // 2. Render overlay + arm Esc-to-cancel
        try {
            buildOverlay();
            overlay.classList.add('is-active');
            overlay.setAttribute('aria-hidden', 'false');
            triggerAnimations();

            escListener = function (e) {
                if (e.key === 'Escape') cancel();
            };
            document.addEventListener('keydown', escListener, true);
        } catch (err) {
            // Si algo del render falla, la navegación igual ocurre por el timer.
            console.warn('[exi-page-loader]', err);
        }
    }

    function onClick(e) {
        // No interceptar clicks con modificadores (abrir en nueva pestaña, etc.)
        if (e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) return;
        if (e.button !== 0) return;

        var a = e.target.closest && e.target.closest('a[href]');
        if (!a) return;
        if (a.target === '_blank') return;
        if (typeof a.href !== 'string') return;
        if (!matchInfluencersHref(a.href)) return;
        if (isSamePage(a.href)) return;

        e.preventDefault();
        showAndRedirect(a.href);
    }

    document.addEventListener('click', onClick, { passive: false });
})();
