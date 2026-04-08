<?php
// 1. HIVATALOS SZÍN ÉS RANG LOGIKA
$official_blue = '#4C4FA3'; // A Nógrád Csodák hivatalos kékje
$nog_color = '#ffffff'; 

if (isset($_SESSION['status'])) {
    switch ($_SESSION['status']) {
        case 'C': $nog_color = '#0ea5e9'; break; // Admin
        case 'B': $nog_color = '#b4865a'; break; // VIP
        case 'A': $nog_color = '#22c55e'; break; // Tag
    }
}

// 2. Elérési út igazítás
$path_prefix = (basename(dirname($_SERVER['PHP_SELF'])) == 'index') ? '../' : '';
$turizm_base = ($path_prefix == '') ? 'index/' : '';

ob_start(); 
?>

<style>
    /* ASZTALI ELREJTÉS */
    .mobile-header, #mobile-panel { display: none; }

    @media (max-width: 767px) {
        /* FIXÁLT FEJLÉC */
        .mobile-header {
            display: flex !important;
            position: fixed;
            top: 0; left: 0; width: 100%;
            height: 65px;
            background-color: <?= $official_blue ?> !important;
            z-index: 10001;
            align-items: center;
            justify-content: space-between;
            padding: 0 15px;
            box-sizing: border-box;
            box-shadow: 0 2px 15px rgba(0,0,0,0.4);
        }
        
        .mobile-header.nav-up {
            top: 0 !important;
        }

        /* HÁROMOSZTATÚ ELRENDEZÉS A KÖZÉPRE ZÁRÁSHOZ */
        .header-left, .header-right { flex: 1; display: flex; align-items: center; }
        .header-center { flex: 0; display: flex; justify-content: center; align-items: center; }
        .header-right { justify-content: flex-end; }

        .mobile-logo {
            font-size: 18px;
            font-weight: 900;
            text-decoration: none;
            color: #fff;
            letter-spacing: 1px;
            white-space: nowrap;
        }

        /* HAMBURGER GOMB FIX KÖZÉPEN */
        .hamburger-btn {
            width: 40px; height: 40px;
            position: relative;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            z-index: 10002;
        }
        .hamburger-btn span {
            display: block; position: absolute;
            height: 3px; width: 28px;
            background: #ffffff; border-radius: 9px;
            transition: .3s ease-in-out;
        }
        .hamburger-btn span:nth-child(1) { transform: translateY(-9px); }
        .hamburger-btn span:nth-child(2) { opacity: 1; } 
        .hamburger-btn span:nth-child(3) { transform: translateY(9px); }

        .hamburger-btn.active span:nth-child(1) { transform: translateY(0) rotate(45deg); }
        .hamburger-btn.active span:nth-child(2) { opacity: 0; transform: translateX(-20px); }
        .hamburger-btn.active span:nth-child(3) { transform: translateY(0) rotate(-45deg); }

        /* PANEL - MINDEN KÖZÉPRE */
        #mobile-panel {
            position: fixed;
            top: 65px; left: 0; width: 100%;
            height: calc(100vh - 65px);
            background-color: #111111;
            z-index: 10000;
            overflow-y: auto;
            padding: 10px 0 40px 0;
            display: none; 
            border-top: 1px solid rgba(255,255,255,0.1);
            text-align: center;
            box-sizing: border-box;
        }

        .mobile-nav-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        /* KATEGÓRIA CÍMEK (desktop-szerű panel) */
        .m-section-toggle {
            padding: 12px 15px;
            color: #ffffff;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 14px;
            display: block;
            cursor: pointer;
            border-top: 1px solid #333;
            border-bottom: 1px solid #1a1a1a;
            background: #181818;
            position: relative;
            letter-spacing: 1px;
        }

        .m-section-toggle::after {
            content: "▸";
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            transition: transform 0.3s;
            color: #ffffff;
        }

        .m-has-submenu.open .m-section-toggle::after {
            transform: translateY(-50%) rotate(90deg);
        }

        /* ALMENÜK */
        .m-submenu {
            display: none;
            list-style: none;
            padding: 0;
            margin: 0;
            background: #161616;
        }

        .m-submenu li a {
            display: block;
            padding: 12px 25px;
            color: #ffffff;
            text-decoration: none;
            font-size: 14px;
            border-bottom: 1px solid #1a1a1a;
        }

        .mobile-nav-list > li > a {
            display: block;
            padding: 14px 30px;
            color: #ffffff;
            text-decoration: none;
            font-size: 18px;
            border-bottom: 1px solid #1a1a1a;
        }

        body { padding-top: 65px !important; overflow-x: hidden; }
        .sidebar-navigation { display: none !important; }
    }
</style>

<header class="mobile-header">
    <div class="header-left">
        <a href="<?= $path_prefix ?>index.php" class="mobile-logo">
            <span style="color: <?= $nog_color ?>;">NÓG</span>RÁD
        </a>
    </div>
    <div class="header-center">
        <div class="hamburger-btn" id="mobile-hamburger">
            <span></span><span></span><span></span>
        </div>
    </div>
    <div class="header-right"></div>
</header>
<?php include "weather_mobile.php"; ?>

<nav id="mobile-panel">
    <ul class="mobile-nav-list">
        <li><a href="<?= $path_prefix ?>index.php" style="background: #1a1a1a; font-weight: bold;">Kezdőlap</a></li>

        <li class="m-has-submenu">
            <span class="m-section-toggle">Kiemelt </span>
            <ul class="m-submenu">
                <li><a href="<?= $path_prefix ?>galeria.php">Galéria</a></li>
                <li><a href="<?= $path_prefix ?>index.php#video">Bemutató</a></li>
                <li><a href="<?= $path_prefix ?>index.php#map">Térképek</a></li>
                <li><a href="<?= $path_prefix ?>index.php#contact">Kapcsolat</a></li>
            </ul>
        </li>

        <li class="m-has-submenu">
            <span class="m-section-toggle">Felfedezés </span>
            <ul class="m-submenu">
                <li><a href="<?= $turizm_base ?>latnivalok.php">Látnivalók</a></li>
                <li><a href="<?= $turizm_base ?>programok.php">Programok</a></li>
                <li><a href="<?= $turizm_base ?>szallasok.php">Szállások</a></li>
                <li><a href="<?= $turizm_base ?>gasztronomia.php">Gasztro</a></li>
                <li><a href="<?= $turizm_base ?>turazas.php">Túrázás</a></li>
                <li><a href="<?= $turizm_base ?>utazasi-praktikak.php">Praktikák</a></li>
            </ul>
        </li>

        <li><a href="<?= $path_prefix ?>blog.php">Blog-fal</a></li>

        <?php if(isset($_SESSION['user_name'])): ?>
            <li><a href="<?= $path_prefix ?>profile.php" style="color: <?= $nog_color ?>;">Profil: <?= htmlspecialchars($_SESSION['user_name']) ?></a></li>

            <?php 
            if (isset($_SESSION['status']) && ($_SESSION['status'] === 'C' || $_SESSION['status'] === 'B')): 
                // Szín meghatározása a rang alapján
                $hd_color = ($_SESSION['status'] === 'C') ? '#0ea5e9' : '#b4865a';
            ?>
            <li>
                <a href="<?= $path_prefix ?>helpdesk.php" style="color: <?= $hd_color ?>; font-weight: 800; text-shadow: 0 0 10px <?= $hd_color ?>88;">
                    <i class="fa fa-shield"></i> HelpDesk
                </a>
            </li>
            <?php endif; ?>
            <li><a href="<?= $path_prefix ?>logout.php" style="color: #fb7185;">Kijelentkezés</a></li>
        <?php else: ?>
            <li><a href="<?= $path_prefix ?>login.php">Bejelentkezés</a></li>
            <li><a href="<?= $path_prefix ?>reg_id.php">Regisztráció</a></li>
        <?php endif; ?>
    </ul>
</nav>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script>
$(document).ready(function() {
    // Hamburger és panel
    $('#mobile-hamburger').on('click', function(e) {
        e.preventDefault();
        $(this).toggleClass('active');
        $('#mobile-panel').slideToggle(300);
        $('body').css('overflow', $(this).hasClass('active') ? 'hidden' : 'auto');
    });

    // Lenyitható kategóriák (mint PC-n)
    $('.m-section-toggle').on('click', function() {
        var parent = $(this).parent('.m-has-submenu');
        var submenu = $(this).next('.m-submenu');

        submenu.slideToggle(300);
        parent.toggleClass('open');

        $('.m-has-submenu').not(parent).removeClass('open').find('.m-submenu').slideUp(300); // Csak egy lehet nyitva
    });

    // Automatikus zárás minden menüpont kattintásnál
    $('.mobile-nav-list a').on('click', function() {
        var href = $(this).attr('href') || '';

        $('#mobile-hamburger').removeClass('active');
        $('#mobile-panel').stop(true, true).slideUp(300);
        $('body').css('overflow', 'auto');

        // Ha almenü link volt, a szülő almenüt is zárjuk
        if (href.indexOf('#') !== -1) {
            $(this).closest('.m-has-submenu').removeClass('open').find('.m-submenu').stop(true, true).slideUp(300);
        }
    });

    // Biztos reset oldalbetöltéskor, hogy ne ugráljon vissza a profil blokk
    $('#mobile-panel').hide();
    $('.m-has-submenu').removeClass('open').find('.m-submenu').hide();
    $('#mobile-hamburger').removeClass('active');
    $('body').css('overflow', 'auto');
});
</script>

<?php $kozos_mobile = ob_get_clean(); ?>

