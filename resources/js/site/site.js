import 'intersection-observer';

document.addEventListener('DOMContentLoaded', function () {
    registerPublicVisit();

    var navbar = document.querySelector('.navbar-site');
    var navbarCollapse = document.getElementById('navbarMain');
    var backToTop = document.querySelector('.back-to-top');

    if (navbarCollapse && typeof bootstrap !== 'undefined' && window.innerWidth < 992) {
        var mobileMenu = bootstrap.Collapse.getOrCreateInstance(navbarCollapse, { toggle: false });

        function setMobileMenuState(isOpen) {
            document.body.classList.toggle('mobile-menu-open', isOpen);
            var toggler = document.querySelector('.navbar-toggler');
            if (toggler) {
                toggler.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            }
        }

        navbarCollapse.addEventListener('show.bs.collapse', function () {
            setMobileMenuState(true);
        });

        navbarCollapse.addEventListener('hidden.bs.collapse', function () {
            setMobileMenuState(false);
        });

        document.querySelectorAll('#navbarMain .nav-link').forEach(function (link) {
            link.addEventListener('click', function () {
                mobileMenu.hide();
            });
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth >= 992) {
                setMobileMenuState(false);
                mobileMenu.hide();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && document.body.classList.contains('mobile-menu-open')) {
                mobileMenu.hide();
            }
        });
    }

    function onScroll() {
        var y = window.scrollY;
        if (navbar) {
            navbar.classList.toggle('navbar-scrolled', y > 50);
        }
        if (backToTop) {
            backToTop.classList.toggle('show', y > 400);
        }
    }

    window.addEventListener('scroll', onScroll, { passive: true });

    if (backToTop) {
        backToTop.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            var href = this.getAttribute('href');
            if (href && href !== '#') {
                var target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    var offset = navbar ? navbar.offsetHeight : 0;
                    var top = target.getBoundingClientRect().top + window.scrollY - offset;
                    window.scrollTo({ top: top, behavior: 'smooth' });
                }
            }
        });
    });

    var cookieBanner = document.querySelector('.cookie-banner');
    var cookieBtn = document.querySelector('.btn-cookie');
    if (cookieBanner && !localStorage.getItem('lgpd_cookies_accepted')) {
        setTimeout(function () {
            cookieBanner.classList.add('show');
        }, 1500);
        if (cookieBtn) {
            cookieBtn.addEventListener('click', function () {
                localStorage.setItem('lgpd_cookies_accepted', 'true');
                cookieBanner.classList.remove('show');
                gtagConsentUpdate();
            });
        }
    } else if (cookieBanner) {
        cookieBanner.style.display = 'none';
    }

    function gtagConsentUpdate() {
        if (typeof gtag === 'function') {
            gtag('consent', 'update', {
                analytics_storage: 'granted',
                ad_storage: 'granted',
            });
        }
    }

    document.querySelectorAll('.navbar-nav .nav-link').forEach(function (link) {
        var href = link.getAttribute('href');
        if (href && href !== '#' && href !== '/') {
            if (window.location.pathname.indexOf(href) === 0) {
                link.classList.add('active');
            }
        } else if (href === '/' && window.location.pathname === '/') {
            link.classList.add('active');
        }
    });

    var currentPath = window.location.pathname;
    document.querySelectorAll('.navbar-nav .nav-link').forEach(function (link) {
        var href = link.getAttribute('href');
        if (href && href !== '#' && href !== '/' && currentPath.startsWith(href)) {
            link.classList.add('active');
        }
    });

    document.querySelectorAll('.alert-auto-hide').forEach(function (alert) {
        setTimeout(function () {
            alert.classList.add('fade');
            setTimeout(function () { alert.remove(); }, 300);
        }, 5000);
    });

    var newsletterForm = document.querySelector('.newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var form = this;
            var btn = form.querySelector('button[type="submit"]');
            var input = form.querySelector('input[type="email"]');
            var originalText = btn ? btn.innerHTML : '';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';
            }
            var formData = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            })
            .then(function (resp) { return resp.json(); })
            .then(function (data) {
                if (data.success) {
                    var wrapper = form.closest('.newsletter-wrapper') || form.parentNode;
                    wrapper.innerHTML =
                        '<div class="alert alert-success text-center mb-0">' +
                        (data.message || 'Inscrição realizada com sucesso!') +
                        '</div>';
                } else {
                    toastrError(data.message || 'Erro ao realizar inscrição.');
                    if (btn) { btn.disabled = false; btn.innerHTML = originalText; }
                }
            })
            .catch(function () {
                toastrError('Erro de conexão. Tente novamente.');
                if (btn) { btn.disabled = false; btn.innerHTML = originalText; }
            });
        });
    }

    var galleryItems = document.querySelectorAll('.gallery-item');
    if (galleryItems.length) {
        galleryItems.forEach(function (item) {
            item.addEventListener('click', function () {
                var img = this.querySelector('img');
                var caption = this.getAttribute('data-caption') || '';
                if (img) {
                    var overlay = document.createElement('div');
                    overlay.className = 'gallery-lightbox';
                    overlay.innerHTML =
                        '<div class="lightbox-overlay-bg"></div>' +
                        '<div class="lightbox-container">' +
                        '<button type="button" class="lightbox-close">&times;</button>' +
                        '<img src="' + img.getAttribute('src') + '" alt="' + (img.getAttribute('alt') || '') + '">' +
                        (caption ? '<p class="lightbox-caption mt-3">' + caption + '</p>' : '') +
                        '</div>';
                    document.body.appendChild(overlay);
                    setTimeout(function () { overlay.classList.add('active'); }, 10);
                    overlay.querySelector('.lightbox-close').addEventListener('click', function () {
                        overlay.classList.remove('active');
                        setTimeout(function () { overlay.remove(); }, 300);
                    });
                    overlay.addEventListener('click', function (e) {
                        if (e.target === overlay || e.target.classList.contains('lightbox-overlay-bg')) {
                            overlay.classList.remove('active');
                            setTimeout(function () { overlay.remove(); }, 300);
                        }
                    });
                    document.addEventListener('keydown', function lightboxKey(e) {
                        if (e.key === 'Escape') {
                            overlay.classList.remove('active');
                            setTimeout(function () { overlay.remove(); }, 300);
                            document.removeEventListener('keydown', lightboxKey);
                        }
                    });
                }
            });
        });
    }

    var searchInput = document.querySelector('.search-input');
    var searchResults = document.querySelector('.search-results');
    if (searchInput) {
        var searchTimeout = null;
        searchInput.addEventListener('input', function () {
            var q = this.value.trim();
            if (searchTimeout) clearTimeout(searchTimeout);
            if (q.length < 2) {
                if (searchResults) searchResults.classList.add('d-none');
                return;
            }
            var endpoint = this.getAttribute('data-search-url') || '/search';
            searchTimeout = setTimeout(function () {
                var container = searchResults || document.createElement('div');
                if (!searchResults) {
                    container.className = 'search-results dropdown-menu show';
                    searchInput.parentNode.appendChild(container);
                }
                container.classList.remove('d-none');
                container.innerHTML =
                    '<div class="px-3 py-2 text-muted small"><span class="spinner-border spinner-border-sm me-2"></span>Buscando...</div>';
                fetch(endpoint + '?q=' + encodeURIComponent(q), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.html) {
                        container.innerHTML = data.html;
                    } else if (data.results && data.results.length) {
                        var html = '';
                        data.results.forEach(function (item) {
                            html += '<a href="' + item.url + '" class="dropdown-item py-2">';
                            if (item.image) {
                                html += '<img src="' + item.image + '" class="me-2" style="width:40px;height:40px;object-fit:cover;border-radius:6px;" alt="">';
                            }
                            html +=
                                '<div class="d-inline-block"><strong>' + item.title + '</strong>';
                            if (item.description) {
                                html += '<br><small class="text-muted">' + item.description.substring(0, 80) + '</small>';
                            }
                            html += '</div></a>';
                        });
                        container.innerHTML = html;
                    } else {
                        container.innerHTML =
                            '<div class="px-3 py-2 text-muted small">Nenhum resultado encontrado.</div>';
                    }
                })
                .catch(function () {
                    container.innerHTML =
                        '<div class="px-3 py-2 text-danger small">Erro ao buscar.</div>';
                });
            }, 400);
        });
        document.addEventListener('click', function (e) {
            if (!searchInput.contains(e.target) && searchResults) {
                searchResults.classList.add('d-none');
            }
        });
    }

    var whatsappBtn = document.querySelector('.whatsapp-float');
    if (whatsappBtn) {
        function checkWhatsAppVisibility() {
            var scrollBottom = window.scrollY + window.innerHeight;
            var docHeight = document.documentElement.scrollHeight;
            if (scrollBottom >= docHeight - 100) {
                whatsappBtn.style.bottom = '120px';
            } else {
                whatsappBtn.style.bottom = '100px';
            }
        }
        window.addEventListener('scroll', checkWhatsAppVisibility, { passive: true });
    }

    var animElements = document.querySelectorAll('[data-animate]');
    if (animElements.length && 'IntersectionObserver' in window) {
        var animObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    var el = entry.target;
                    var anim = el.getAttribute('data-animate') || 'fade-up';
                    var delay = el.getAttribute('data-delay') || '0';
                    el.style.animationDelay = delay + 'ms';
                    el.classList.add('animated', 'animate__' + anim);
                    animObserver.unobserve(el);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
        animElements.forEach(function (el) { animObserver.observe(el); });
    }

    var stats = document.querySelectorAll('.stat-number');
    if (stats.length && 'IntersectionObserver' in window) {
        var counted = false;
        var statObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting && !counted) {
                    counted = true;
                    stats.forEach(function (stat) {
                        var target = parseInt(stat.getAttribute('data-target')) || parseInt(stat.textContent.replace(/\D/g, '')) || 0;
                        var suffix = stat.getAttribute('data-suffix') || '';
                        var duration = parseInt(stat.getAttribute('data-duration')) || 2000;
                        var start = 0;
                        var increment = Math.ceil(target / (duration / 16));
                        function update() {
                            start += increment;
                            if (start >= target) {
                                stat.textContent = target.toLocaleString('pt-BR') + suffix;
                                return;
                            }
                            stat.textContent = start.toLocaleString('pt-BR') + suffix;
                            requestAnimationFrame(update);
                        }
                        update();
                    });
                    statObserver.disconnect();
                }
            });
        }, { threshold: 0.5 });
        stats.forEach(function (s) { statObserver.observe(s); });
    }

    function toastrError(message) {
        var container = document.querySelector('.toastr-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toastr-container';
            container.style.cssText =
                'position:fixed;top:20px;right:20px;z-index:99999;max-width:350px;';
            document.body.appendChild(container);
        }
        var toast = document.createElement('div');
        toast.className = 'toast-notification toast-error';
        toast.style.cssText =
            'background:#dc3545;color:white;padding:12px 20px;border-radius:8px;margin-bottom:8px;box-shadow:0 4px 12px rgba(0,0,0,0.15);display:flex;align-items:center;gap:10px;animation:slideInRight 0.3s ease;';
        toast.innerHTML =
            '<i class="fas fa-exclamation-circle"></i><span>' + message + '</span>';
        container.appendChild(toast);
        setTimeout(function () {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            toast.style.transition = 'all 0.3s ease';
            setTimeout(function () { toast.remove(); }, 300);
        }, 4000);
    }

    var style = document.createElement('style');
    style.textContent =
        '@keyframes slideInRight{from{opacity:0;transform:translateX(100%)}to{opacity:1;transform:translateX(0)}}';
    document.head.appendChild(style);

    function registerPublicVisit() {
        if (document.body && document.body.dataset.visitTracked === '1') {
            return;
        }

        var payload = JSON.stringify({
            page_url: window.location.href,
        });

        if (document.body) {
            document.body.dataset.visitTracked = '1';
        }

        if (navigator.sendBeacon) {
            navigator.sendBeacon('/api/visitas/registrar', new Blob([payload], { type: 'application/json' }));
            return;
        }

        fetch('/api/visitas/registrar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-Page-Url': window.location.href,
            },
            body: payload,
            keepalive: true,
        }).catch(function () {
            if (document.body) {
                document.body.dataset.visitTracked = '0';
            }
        });
    }
});
