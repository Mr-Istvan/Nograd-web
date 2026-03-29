jQuery(document).ready(function($) {

    'use strict';

    // 1. Fő Slider inicializálása
    if ($.fn && $.fn.slick) {
        $(".Modern-Slider").slick({
            autoplay: true,
            speed: 1000,
            slidesToShow: 1,
            slidesToScroll: 1,
            pauseOnHover: false,
            dots: true,
            fade: true,
            pauseOnDotsHover: true,
            cssEase: 'linear',
            draggable: false,
            prevArrow: '<button class="PrevArrow"></button>',
            nextArrow: '<button class="NextArrow"></button>',
        });
    }

    // 2. Mobil menü lenyitása és bezárása
    $('#nav-toggle').on('click', function (e) {
        e.preventDefault();
        $('#main-nav').stop(true, true).slideToggle(300);
    });

    // Menüpontra kattintáskor csukódjon be a menü mobilban (JAVÍTOTT)
    $('#main-nav a').on('click', function() {
        if ($(window).width() < 768) { // Mobil nézet: 767px alatt
            $('#main-nav').slideUp(300);
        }
    });

    // 3. Tabok kezelése (Blog/Hírek szekcióhoz)
    $('.tabgroup > div').hide();
    $('.tabgroup > div:first-of-type').show();
    $('.tabs a').click(function(e) {
        e.preventDefault();
        var $this = $(this),
            tabgroup = '#' + $this.parents('.tabs').data('tabgroup'),
            others = $this.closest('li').siblings().children('a'),
            target = $this.attr('href');
        
        others.removeClass('active');
        $this.addClass('active');
        $(tabgroup).children('div').hide();
        $(target).show();
    });

    // 4. Videó lejátszás kezelése
    $(".box-video").click(function() {
        var $iframe = $(this).find('iframe');
        if ($iframe.length > 0) {
            var currentSrc = $iframe.attr('src');
            if (currentSrc.indexOf('autoplay') === -1) {
                var newSrc = currentSrc + (currentSrc.indexOf('?') === -1 ? '?' : '&') + "autoplay=1";
                $iframe.attr('src', newSrc);
            }
        }
        $(this).addClass('open');
    });

    // 5. Owl Carousel inicializálása - MODERNEK, KÉPEN BELÜLI NYILAKKAL
    $('.owl-carousel').owlCarousel({
        loop: true,
        margin: 30,
        responsiveClass: true,
        dragEndSpeed: 200,
        smartSpeed: 500,
        nav: true, 
        dots: true,
        navText: ["&#10094;", "&#10095;"], 
        responsive: {
            0: { 
                items: 1, 
                nav: false, // Mobilon továbbra is maradjon kikapcsolva a zavaró nyilak miatt
                dots: true 
            },
            600: { 
                items: 2, 
                nav: false 
            },
            1000: { 
                items: 3, 
                nav: true,
                loop: true 
            }
        }
    });

    // 6. Navigáció és Gördülés (Smooth Scroll)
    var contentSection = $('.content-section, .main-banner');
    var navigation = $('nav');

    navigation.on('click', 'a', function(event) {
        // Csak akkor avatkozunk be, ha hash (#) linkről van szó
        if (this.hash !== "") {
            event.preventDefault();
            smoothScroll($(this.hash));
        }
    });

    $(window).on('scroll', function() {
        updateNavigation();
    });

    updateNavigation();

    function updateNavigation() {
        contentSection.each(function() {
            var sectionName = $(this).attr('id');
            var navigationMatch = $('nav a[href="#' + sectionName + '"]');
            if (($(this).offset().top - $(window).height() / 2 <= $(window).scrollTop()) &&
                ($(this).offset().top + $(this).height() - $(window).height() / 2 > $(window).scrollTop())) {
                navigationMatch.addClass('active-section');
            } else {
                navigationMatch.removeClass('active-section');
            }
        });
    }

    function smoothScroll(target) {
        if (target.length) {
            $('body,html').animate({
                scrollTop: target.offset().top
            }, 800);
        }
    }

    // 7. Javított gomb-kattintás kezelő (Bejelentkezés/Regisztráció hiba javítása)
    $('.button a[href*="#"]').on('click', function(e) {
        var targetHref = $(this).attr('href');
        
        // Csak akkor indítunk animációt, ha a célpont egy létező ID az oldalon
        if ($(targetHref).length && targetHref.startsWith('#')) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: $(targetHref).offset().top
            }, 500, 'linear');
        } else {
            // Ha nem belső link (pl. login.php vagy reg.php), hagyjuk, hogy a böngésző megnyissa
            return true;
        }
    });

    // Mobil menüpontra kattintva a menü magától bezáródik
    $('#main-nav ul li a').on('click', function() {
        if ($(window).width() < 768) { // Csak mobil nézetben
            $("#main-nav").collapse('hide'); // Bootstrap collapse bezárása
            $('#main-nav').removeClass('open'); // Template egyedi osztály eltávolítása
        }
    });

    // Egységes onpageshow kezelés a formok törléséhez
    window.onpageshow = function(event) {
        const forceClear = () => {
            const forms = document.querySelectorAll('#regForm, #loginForm');
            forms.forEach(f => f.reset());
            document.querySelectorAll('input[type="password"]').forEach(i => i.value = "");
        };

        if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
            forceClear();
        }

        forceClear();
        setTimeout(forceClear, 100);
    };
    
    if (window.history.replaceState) {
        // Kicseréljük az aktuális URL-t az előzményekben ugyanerre, 
        // így a böngésző "elfelejti" a sokadik újratöltést.
        window.history.replaceState(null, null, window.location.href);
    } 

    // =========================================================================
    // 8. OKOS MOBIL MENÜ: Eltűnik lefelé, megjelenik felfelé görgetéskor (Blog)
    // =========================================================================
    if ($('header.blog-mobile-nav').length) {
        var didScroll;
        var lastScrollTop = 0;
        var delta = 5;

        $(window).scroll(function() {
            didScroll = true;
        });

        setInterval(function() {
            if (didScroll) {
                hasScrolled();
                didScroll = false;
            }
        }, 250);

        function hasScrolled() {
            var st = $(window).scrollTop();
            var navbar = $('header.blog-mobile-nav');
            var navbarHeight = navbar.outerHeight() || 0;

            if (Math.abs(lastScrollTop - st) <= delta) return;

            if (st > lastScrollTop && st > navbarHeight) {
                navbar.addClass('nav-up');

                var nav = document.getElementById('blog-mobile-menu');
                var btn = document.getElementById('blog-mobile-toggle');
                if (nav && nav.style.display === 'block') {
                    nav.style.display = 'none';
                    if (btn) btn.setAttribute('aria-expanded', 'false');
                }
            } else {
                if (st + $(window).height() < $(document).height()) {
                    navbar.removeClass('nav-up');
                }
            }

            lastScrollTop = st;
        }
    }

}); // <--- EZ ZÁRJA LE A LEGELSŐ SORBAN MEGNYITOTT JQUERY BLOKKOT.

// =========================================================================
// GLOBÁLIS FÜGGVÉNYEK (Ide jönnek azok a függvények, amiket a HTML-ből hívsz)
// =========================================================================
function updateMap(placeName, element) {
    // Ez a hivatalos Google Maps Embed URL formátum
    const url = "https://maps.google.com/maps?q=" + placeName + "+Nógrád&t=&z=13&ie=UTF8&iwloc=&output=embed";
    document.getElementById('map-frame').src = url;
    
    var cards = document.getElementsByClassName('tour-plan-card');
    for (var i = 0; i < cards.length; i++) {
        cards[i].classList.remove('active-tour');
    }
    element.classList.add('active-tour');
}
