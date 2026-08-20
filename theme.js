document.addEventListener('DOMContentLoaded', function () {

    // --- Header-Abteilungen: Hover per CSS, zusätzlich Klick zum Öffnen/Schließen der
    //     Kurzbeschreibung (wichtig für Touch-Geräte ohne Hover). Der eigentliche Link
    //     im Dropdown ("Mehr erfahren") wird davon nicht beeinflusst und navigiert normal. ---
    var catToggles = document.querySelectorAll('.category-nav .cat-toggle');
    function setCatExpanded(toggle, expanded) {
        toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
    }
    catToggles.forEach(function (toggle) {
        var li = toggle.closest('.cat-item');
        toggle.addEventListener('click', function (e) {
            var wasOpen = li.classList.contains('open');
            // Auf Touch-Geräten (kein Hover): erster Klick öffnet nur die Kurzbeschreibung,
            // navigiert aber noch nicht direkt weiter.
            if (window.matchMedia('(hover: none)').matches && !wasOpen) {
                e.preventDefault();
                document.querySelectorAll('.category-nav .cat-item.open').forEach(function (openLi) {
                    openLi.classList.remove('open');
                    setCatExpanded(openLi.querySelector('.cat-toggle'), false);
                });
                li.classList.add('open');
                setCatExpanded(toggle, true);
            }
        });
        // Für Maus/Tastatur ohne Touch: aria-expanded auch bei Hover/Fokus mitpflegen
        li.addEventListener('mouseenter', function () { setCatExpanded(toggle, true); });
        li.addEventListener('mouseleave', function () { setCatExpanded(toggle, false); });
        toggle.addEventListener('focus', function () { setCatExpanded(toggle, true); });
    });
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.category-nav')) {
            document.querySelectorAll('.category-nav .cat-item.open').forEach(function (li) {
                li.classList.remove('open');
                setCatExpanded(li.querySelector('.cat-toggle'), false);
            });
        }
    });

    // --- Linke Sidebar (Rubriken): Klick zum Auf-/Zuklappen ---
    var deptToggles = document.querySelectorAll('.dept-toggle');
    deptToggles.forEach(function (toggle) {
        toggle.addEventListener('click', function () {
            var item = toggle.closest('.dept-item');
            var isOpen = item.classList.toggle('open');
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    });

    // --- Linke Sidebar: "Alle Rubriken anzeigen" / "Weniger anzeigen" ---
    var deptShowAllToggle = document.getElementById('deptShowAllToggle');
    var deptHiddenGroup = document.getElementById('deptHiddenGroup');
    if (deptShowAllToggle && deptHiddenGroup) {
        var deptShowAllLabel = deptShowAllToggle.querySelector('.label');
        deptShowAllToggle.addEventListener('click', function () {
            var isOpen = deptHiddenGroup.classList.toggle('open');
            deptShowAllToggle.classList.toggle('open', isOpen);
            deptShowAllToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            if (deptShowAllLabel) {
                deptShowAllLabel.textContent = isOpen
                    ? deptShowAllToggle.getAttribute('data-label-less')
                    : deptShowAllToggle.getAttribute('data-label-more');
            }
            if (!isOpen) {
                deptShowAllToggle.scrollIntoView({ block: 'nearest' });
            }
        });
    }

    // --- Pop-up-Fenster (Rezepte/Videos): öffnen (Klick/Enter/Leertaste bei Buttons
    //     funktioniert automatisch), Fokus in den Dialog setzen, beim Schließen
    //     (ESC, Klick auf Hintergrund, Schließen-Button) den Fokus zum Auslöser zurückgeben ---
    var lastModalTrigger = null;
    function openModal(modal, trigger) {
        lastModalTrigger = trigger || null;
        modal.classList.add('active');
        var closeBtn = modal.querySelector('.modal-close');
        if (closeBtn) { closeBtn.focus(); }
    }
    function closeModal(modal) {
        modal.classList.remove('active');
        if (lastModalTrigger) { lastModalTrigger.focus(); }
    }
    document.querySelectorAll('[data-modal-target]').forEach(function (trigger) {
        trigger.addEventListener('click', function () {
            var modal = document.getElementById(trigger.getAttribute('data-modal-target'));
            if (modal) { openModal(modal, trigger); }
        });
    });
    document.querySelectorAll('[data-modal-close]').forEach(function (closeBtn) {
        closeBtn.addEventListener('click', function () {
            var modal = closeBtn.closest('.modal-overlay');
            if (modal) { closeModal(modal); }
        });
    });
    document.querySelectorAll('.modal-overlay').forEach(function (overlay) {
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) { closeModal(overlay); }
        });
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.active').forEach(function (overlay) {
                closeModal(overlay);
            });
        }
    });

    // --- Scroll-Pfeile: nach oben / nach unten ---
    var scrollTopBtn = document.getElementById('scrollTopBtn');
    var scrollBottomBtn = document.getElementById('scrollBottomBtn');
    if (scrollTopBtn) {
        scrollTopBtn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
    if (scrollBottomBtn) {
        scrollBottomBtn.addEventListener('click', function () {
            window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
        });
    }
    function toggleScrollTopVisibility() {
        if (!scrollTopBtn) { return; }
        if (window.scrollY < 300) {
            scrollTopBtn.classList.add('hidden');
        } else {
            scrollTopBtn.classList.remove('hidden');
        }
    }
    window.addEventListener('scroll', toggleScrollTopVisibility);
    toggleScrollTopVisibility();

    // --- Suchfeld: Vorschläge bei Klick/Fokus anzeigen ---
    var searchInput = document.getElementById('siteSearchInput');
    var searchSuggestions = document.getElementById('searchSuggestions');
    if (searchInput && searchSuggestions) {
        searchInput.addEventListener('focus', function () {
            searchSuggestions.classList.add('active');
        });
        searchSuggestions.querySelectorAll('.suggestion-item').forEach(function (item) {
            item.addEventListener('click', function () {
                searchInput.value = item.getAttribute('data-term');
                searchInput.closest('form').submit();
            });
        });
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.site-search')) {
                searchSuggestions.classList.remove('active');
            }
        });
    }
});
