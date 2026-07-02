/* ══════════════════════════════════════════════════════════
   EXICOMPRAS — NAVBAR JS
   Vanilla JS (sin dependencias).
   1) Sticky shrink on scroll (IntersectionObserver + fallback)
   2) Drawer móvil open/close + focus trap + push pattern
   3) Wishlist AJAX (POST/DELETE/GET)
   4) Clonado del árbol de categorías al drawer

   Estado: <html class="exi-drawer-open"> cuando el drawer está abierto
   ══════════════════════════════════════════════════════════ */
(function () {
    'use strict';

    const $$  = (sel, ctx) => Array.from((ctx || document).querySelectorAll(sel));
    const $   = (sel, ctx) => (ctx || document).querySelector(sel);
    const on  = (el, ev, fn, opt) => el && el.addEventListener(ev, fn, opt || false);

    const SHELL         = $('.exi-navbar-shell');
    const SENTINEL      = $('.exi-navbar-sentinel');
    const DRAWER        = $('#exiDrawer');
    const TREE_IN_NAV   = $('.exi-navbar-collapse .catalog-filter-tree');
    const BREAKPOINT    = 992;

    /* ──────────────────────────────────────────────────
       1) STICKY SHRINK ON SCROLL
       Usa IntersectionObserver para detectar cuándo el
       sentinel (1px invisible encima del navbar) sale del
       viewport. Si sale → clase 'exi-shrunk'.
    ────────────────────────────────────────────────── */
    function initStickyShrink() {
        if (!SHELL || !SENTINEL) return;

        const add  = () => SHELL.classList.add('exi-shrunk');
        const rem  = () => SHELL.classList.remove('exi-shrunk');

        if ('IntersectionObserver' in window) {
            const io = new IntersectionObserver(
                (entries) => entries.forEach((e) => e.isIntersecting ? rem() : add()),
                { threshold: 0, rootMargin: '0px 0px 0px 0px' }
            );
            io.observe(SENTINEL);
        } else {
            // Fallback: scroll listener throttled con rAF
            let ticking = false;
            const update = () => {
                ticking = false;
                if (window.scrollY > 60) add(); else rem();
            };
            on(window, 'scroll', () => {
                if (!ticking) { requestAnimationFrame(update); ticking = true; }
            }, { passive: true });
        }
    }

    /* ──────────────────────────────────────────────────
       2) DRAWER MÓVIL
    ────────────────────────────────────────────────── */
    function isMobile() { return window.innerWidth < BREAKPOINT; }

    function getFocusable(root) {
        return $$(
            'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])',
            root
        ).filter(el => el.offsetParent !== null);
    }

    function openDrawer() {
        if (!DRAWER) return;
        document.documentElement.classList.add('exi-drawer-open');
        DRAWER.setAttribute('aria-hidden', 'false');
        const btn = $('[data-exi-drawer-open]');
        if (btn) btn.setAttribute('aria-expanded', 'true');
        // focus al primer elemento focusable del panel
        requestAnimationFrame(() => {
            const f = getFocusable(DRAWER)[0];
            if (f) f.focus();
        });
        document.addEventListener('keydown', onDrawerKeydown);
        // Bloquear scroll de fondo (mantener scroll del drawer)
        document.body.style.overflow = 'hidden';
    }

    function closeDrawer() {
        if (!DRAWER) return;
        document.documentElement.classList.remove('exi-drawer-open');
        DRAWER.setAttribute('aria-hidden', 'true');
        const btn = $('[data-exi-drawer-open]');
        if (btn) btn.setAttribute('aria-expanded', 'false');
        document.removeEventListener('keydown', onDrawerKeydown);
        document.body.style.overflow = '';
        // Reset sub-panels
        $$('.exi-drawer-sub-panel.active').forEach(p => p.classList.remove('active'));
    }

    function onDrawerKeydown(e) {
        if (e.key === 'Escape') {
            e.preventDefault();
            closeDrawer();
            return;
        }
        if (e.key !== 'Tab') return;
        // Focus trap
        const focusables = getFocusable(DRAWER);
        if (!focusables.length) return;
        const first = focusables[0];
        const last  = focusables[focusables.length - 1];
        if (e.shiftKey && document.activeElement === first) {
            e.preventDefault(); last.focus();
        } else if (!e.shiftKey && document.activeElement === last) {
            e.preventDefault(); first.focus();
        }
    }

    function initDrawer() {
        if (!DRAWER) return;

        on(document, 'click', (e) => {
            const open  = e.target.closest('[data-exi-drawer-open]');
            const close = e.target.closest('[data-exi-drawer-close]');
            if (open) {
                e.preventDefault();
                openDrawer();
                return;
            }
            if (close) {
                e.preventDefault();
                closeDrawer();
                return;
            }
            // Click en cualquier <a> del drawer (excepto push) → cerrar tras navegar
            if (e.target.closest('.exi-drawer-panel a')) {
                setTimeout(closeDrawer, 100);
            }
        });

        // Cerrar al cambiar a desktop
        on(window, 'resize', () => {
            if (!isMobile() && document.documentElement.classList.contains('exi-drawer-open')) {
                closeDrawer();
            }
        });
    }

    /* ──────────────────────────────────────────────────
       3) CLONADO DEL ÁRBOL DE CATEGORÍAS AL DRAWER
       Recorre el árbol inyectado por Aimeos y genera
       la lista plana con items + botones "next" para
       sub-paneles con push pattern.
    ────────────────────────────────────────────────── */
    function buildDrawerCats() {
        const container = $('.exi-drawer-cats');
        if (!container) return;

        // Esperar a que Aimeos inyecte el árbol (puede ser tras DOMContentLoaded)
        if (!TREE_IN_NAV) {
            // Reintentar con polling corto
            let attempts = 0;
            const wait = setInterval(() => {
                attempts++;
                const tree = $('.exi-navbar-collapse .catalog-filter-tree .level-1');
                if (tree || attempts > 30) {
                    clearInterval(wait);
                    if (tree) buildFromTree(tree);
                }
            }, 200);
            return;
        }
        const level1 = $('.level-1', TREE_IN_NAV);
        if (level1) buildFromTree(level1);
    }

    function buildFromTree(level1) {
        const container = $('.exi-drawer-cats');
        if (!container) return;
        container.innerHTML = ''; // limpiar

        const ul = document.createElement('ul');
        $$(':scope > .cat-item', level1).forEach((catItem) => {
            const li = renderCatItem(catItem, 0);
            if (li) ul.appendChild(li);
        });
        container.appendChild(ul);
    }

    function renderCatItem(catItem, depth) {
        const linkEl   = $('.item-links > .cat-link', catItem);
        if (!linkEl) return null;
        const href     = linkEl.getAttribute('href') || '#';
        const name     = $('.cat-name', linkEl)?.textContent.trim() || '';
        const count    = $('.cat-count', linkEl)?.textContent.trim() || '';
        const isActive = linkEl.classList.contains('active') || catItem.classList.contains('active');

        // Buscar subniveles (Aimeos los inyecta en .submenu dentro de catItem)
        const sub = $('.submenu', catItem) || $('.megamenu', catItem);
        const hasChildren = !!(sub && (sub.querySelector('.level-2') || sub.children.length > 0));

        const li = document.createElement('li');
        const a  = document.createElement('a');
        a.className = 'exi-drawer-item' + (isActive ? ' active' : '');
        a.href = href;
        a.innerHTML = '<span class="exi-drawer-item-label"></span>';
        a.querySelector('.exi-drawer-item-label').textContent = name;
        if (count) {
            const b = document.createElement('span');
            b.className = 'badge';
            b.textContent = count;
            a.appendChild(b);
        }
        li.appendChild(a);

        if (hasChildren) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'exi-drawer-item-next';
            btn.setAttribute('aria-label', name);
            btn.innerHTML = '<i class="bi bi-chevron-right" aria-hidden="true"></i>';
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                openSubPanel(catItem, name);
            });
            li.appendChild(btn);
        }
        return li;
    }

    function openSubPanel(catItem, title) {
        const panel = $('.exi-drawer-panel');
        if (!panel) return;

        // Cerrar sub-panel previo
        const prev = $('.exi-drawer-sub-panel.active', panel);
        if (prev) prev.remove();

        // Construir sub-panel
        const sub = document.createElement('div');
        sub.className = 'exi-drawer-sub-panel';
        sub.innerHTML = `
            <header class="exi-drawer-sub-header">
                <button type="button" class="exi-drawer-back" aria-label="${__('Volver')}">
                    <i class="bi bi-arrow-left" aria-hidden="true"></i>
                </button>
                <h3 class="exi-drawer-sub-title"></h3>
            </header>
            <div class="exi-drawer-sub-body"></div>
        `;
        sub.querySelector('.exi-drawer-sub-title').textContent = title;
        const body = sub.querySelector('.exi-drawer-sub-body');

        // Clonar sub-árbol (level-2)
        const subTree = $('.submenu .level-2, .megamenu .level-2, .submenu ul, .megamenu ul', catItem);
        if (subTree) {
            const ul = document.createElement('ul');
            $$(':scope > .cat-item', subTree).forEach((c) => {
                const li = renderCatItem(c, 1);
                if (li) ul.appendChild(li);
            });
            body.appendChild(ul);

            // Recursivo: si los items tienen sus propios submenús
            $$(':scope > li', ul).forEach((li, idx) => {
                const childCat = $$(':scope > .cat-item', subTree)[idx];
                if (childCat) attachRecursive(li, childCat, 2);
            });
        }

        on(sub.querySelector('.exi-drawer-back'), 'click', () => {
            sub.classList.remove('active');
            setTimeout(() => sub.remove(), 320);
        });

        panel.appendChild(sub);
        requestAnimationFrame(() => sub.classList.add('active'));
    }

    function attachRecursive(li, catItem, depth) {
        const sub = $('.submenu', catItem);
        if (!sub) return;
        const subTree = $('.level-3, ul', sub);
        if (!subTree) return;
        const hasChildren = !!$('.cat-item', subTree);
        if (!hasChildren) return;
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'exi-drawer-item-next';
        btn.setAttribute('aria-label', __('Más'));
        btn.innerHTML = '<i class="bi bi-chevron-right" aria-hidden="true"></i>';
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const name = $('.cat-name', $('.item-links > .cat-link', catItem))?.textContent.trim() || '';
            openSubPanel(catItem, name);
        });
        li.appendChild(btn);
    }

    /* helper: traducción i18n básica via meta o atributo data */
    function __(text) { return text; }

    /* ──────────────────────────────────────────────────
       4) WISHLIST AJAX
    ────────────────────────────────────────────────── */
    const CSRF = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

    function getCookie(name) {
        const m = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
        return m ? decodeURIComponent(m[2]) : null;
    }

    function updateBadge(count) {
        $$('.exi-wishlist').forEach((el) => {
            el.setAttribute('data-count', String(count));
            const b = el.querySelector('.badge');
            if (b) b.textContent = count;
            el.setAttribute('aria-label',
                count > 0
                    ? `Tienes ${count} favoritos`
                    : 'Ver favoritos'
            );
        });
    }

    async function wishlistFetch(url, method) {
        try {
            const res = await fetch(url, {
                method: method,
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': CSRF,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: method === 'DELETE' ? null : undefined,
            });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const data = await res.json().catch(() => ({}));
            if (typeof data.count === 'number') updateBadge(data.count);
            return data;
        } catch (err) {
            console.error('[exinavbar] wishlist fetch error', err);
            return null;
        }
    }

    function bindWishlist() {
        // Botón "Quitar" en la página de favoritos
        $$('form[action*="/favorites/"]').forEach((form) => {
            on(form, 'submit', async (e) => {
                e.preventDefault();
                const action = form.getAttribute('action');
                const data = await wishlistFetch(action, 'POST'); // DELETE vía _method
                if (data) {
                    // Soft reload para refrescar la lista
                    window.location.reload();
                }
            });
        });
    }

    /* Exponer API global mínima para botones "añadir a favoritos" en otros lugares */
    window.ExiFavorites = {
        add(productId, meta) {
            meta = meta || {};
            return fetch('/favorites', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': CSRF,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId,
                    product_code: meta.code || null,
                    name: meta.name || null,
                    price: meta.price || null,
                    media_url: meta.media || null,
                }),
            })
            .then(r => r.json().then(d => { updateBadge(d.count); return d; }))
            .catch(err => { console.error(err); return null; });
        },
        remove(productId) {
            return wishlistFetch('/favorites/' + encodeURIComponent(productId), 'DELETE');
        },
        refresh() {
            // Recargar el conteo desde el backend (útil tras login)
            return fetch('/favorites?count=1', { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
                .then(r => r.ok ? r.json() : null)
                .then(d => { if (d && typeof d.count === 'number') updateBadge(d.count); return d; });
        }
    };

    /* ──────────────────────────────────────────────────
       5) CENTRAR CATEGORÍAS EN LA BARRA (desktop)
       Si el contenido de .level-1 desborda, el scrollLeft
       inicial se posiciona en el centro para que el usuario
       vea las categorías del medio primero (no las de la izq).
    ────────────────────────────────────────────────── */
    function centerNavbarCategories() {
        const level1 = $('.exi-navbar-row--sub .catalog-filter-tree .level-1');
        if (!level1) return;

        const doCenter = () => {
            // Solo centrar si hay overflow horizontal
            if (level1.scrollWidth > level1.clientWidth) {
                level1.scrollLeft = (level1.scrollWidth - level1.clientWidth) / 2;
            } else {
                level1.scrollLeft = 0;
            }
        };

        // Ejecutar tras un frame para asegurar que el layout está listo
        requestAnimationFrame(doCenter);

        // Re-centrar al cambiar el tamaño de la ventana
        on(window, 'resize', () => {
            requestAnimationFrame(doCenter);
        });

        // Re-centrar cuando las imágenes/fuentes terminen de cargar
        on(window, 'load', () => requestAnimationFrame(doCenter));
    }

    /* ──────────────────────────────────────────────────
       INIT
    ────────────────────────────────────────────────── */
    function init() {
        initStickyShrink();
        initDrawer();
        buildDrawerCats();
        centerNavbarCategories();
        bindWishlist();

        // Tras login: sincronizar favoritos de session a user
        // (El backend lo hace vía /favorites/sync; aquí solo observamos)
        on(document, 'visibilitychange', () => {
            if (document.visibilityState === 'visible') {
                window.ExiFavorites.refresh();
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
