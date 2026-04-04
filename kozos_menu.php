<?php
// 1. SZÍN LOGIKA (Rang alapján)
$nog_color = '#ffffff'; 

if (isset($_SESSION['status'])) {
    switch ($_SESSION['status']) {
        case 'C': $nog_color = '#0ea5e9'; break; // Admin: Kék
        case 'B': $nog_color = '#b4865a'; break; // VIP: Arany
        case 'A': $nog_color = '#22c55e'; break; // Tag: Zöld
        default:  $nog_color = '#ffffff';
    }
}

// Biztosítjuk, hogy az elérési útvonalak a blogból is jók legyenek
$path_prefix = (basename(dirname($_SERVER['PHP_SELF'])) == 'index') ? '../' : '';
$turizm_base = ($path_prefix == '') ? 'index/' : '';

ob_start(); 
?>

<div class="sidebar-navigation">
    <div class="sidebar-header">
        <span style="color: <?= $nog_color ?>;">NÓG</span>RÁD
    </div>

    <ul class="menu">
        <li><a href="<?= $path_prefix ?>index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>">Kezdőlap</a></li>

        <li class="menu-section pc-menu-section <?= (in_array($current_page, $kiemelt_pages ?? []) || ($current_page == 'index.php' && strpos($_SERVER['REQUEST_URI'], '#') !== false)) ? 'active' : '' ?>">
            <span class="menu-label" style="cursor: pointer;">Kiemelt</i></span>
            <ul class="submenu pc-submenu <?= (in_array($current_page, $kiemelt_pages ?? []) || ($current_page == 'index.php' && strpos($_SERVER['REQUEST_URI'], '#') !== false)) ? 'open' : '' ?>" style="display: <?= (in_array($current_page, $kiemelt_pages ?? []) || ($current_page == 'index.php' && strpos($_SERVER['REQUEST_URI'], '#') !== false)) ? 'block' : 'none' ?>;">
                <li><a href="<?= $path_prefix ?>galeria.php">Galéria</a></li>
                <li><a href="<?= $path_prefix ?>index.php#video">Bemutató</a></li>
                <li><a href="<?= $path_prefix ?>index.php#map">Térképek</a></li>
                <li><a href="<?= $path_prefix ?>index.php#contact">Kapcsolat</a></li>
            </ul>
        </li>

        <li class="menu-section pc-menu-section <?= in_array($current_page, $felfedezes_pages ?? []) ? 'active' : '' ?>">
            <span class="menu-label" style="cursor: pointer;">Felfedezés</i></span>
            <ul class="submenu pc-submenu <?= in_array($current_page, $felfedezes_pages ?? []) ? 'open' : '' ?>" style="display: <?= in_array($current_page, $felfedezes_pages ?? []) ? 'block' : 'none' ?>;">
                <li><a href="<?= $turizm_base ?>latnivalok.php">Látnivalók</a></li>
                <li><a href="<?= $turizm_base ?>programok.php">Programok</a></li>
                <li><a href="<?= $turizm_base ?>szallasok.php">Szállások</a></li>
                <li><a href="<?= $turizm_base ?>gasztronomia.php">Gasztro</a></li>
                <li><a href="<?= $turizm_base ?>turazas.php">Túrázás</a></li>
                <li><a href="<?= $turizm_base ?>utazasi-praktikak.php">Praktikák</a></li>
            </ul>
        </li>

        

       <li class="menu-section pc-menu-section <?= in_array($current_page, $blog_pages ?? ['blog.php', 'login.php', 'reg_id.php', 'profile.php']) ? 'active' : '' ?>">
    <span class="menu-label" style="cursor: pointer;">Profil</span>
    
    <ul class="submenu pc-submenu" style="display: none;">
        
        <li class="<?= $current_page == 'blog.php' ? 'active' : '' ?>">
            <a href="<?= $path_prefix ?>blog.php">Blog-fal</a>
        </li>

        <?php if(isset($_SESSION['user_name'])): ?>
            <li class="<?= $current_page == 'profile.php' ? 'active' : '' ?>">
                <a href="<?= $path_prefix ?>profile.php">
                    Profil : <span style="color: <?= $nog_color ?>; font-weight: 800;"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                </a>
            </li>
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

            <li><a href="<?= $path_prefix ?>logout.php" style="color: #fb7185 !important;">Kilépés</a></li>
        <?php else: ?>
            <li class="<?= $current_page == 'login.php' ? 'active' : '' ?>">
                <a href="<?= $path_prefix ?>login.php">Bejelentkezés</a>
            </li>
            <li class="<?= $current_page == 'reg_id.php' ? 'active' : '' ?>">
                <a href="<?= $path_prefix ?>reg_id.php">Regisztráció</a>
            </li>
        <?php endif; ?>
    </ul>
</li>
    </ul>

    <div style="margin-top: 20px; padding: 0 15px;">
        <?php include "weather.php"; ?>
    </div>

    <ul class="social-icons" style="padding: 3px; margin: 3px;">
        <li><a href="https://www.facebook.com/" target="_blank"><i class="fa fa-facebook-official"></i></a></li>
        <li><a href="https://twitter.com/" target="_blank"><i class="fa fa-twitter"></i></a></li>
        <li><a href="https://mail.google.com/" target="_blank"><i class="fa fa-envelope"></i></a></li>
        <li><a href="https://www.youtube.com/" target="_blank"><i class="fa fa-youtube-play"></i></a></li>
        <li><a href="https://wm-iskola.hu/" target="_blank"><i class="fa fa-graduation-cap"></i></a></li>
    </ul>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Most már a teljes menüpontot (li) figyeljük, nem csak a szöveget
    var pcSections = document.querySelectorAll('.pc-menu-section');
    
    pcSections.forEach(function(li) {
        // A 'true' paraméter a végén azt jelenti, hogy mi kapjuk el legelőször a kattintást (Capture fázis)!
        li.addEventListener('click', function(e) {
            
            // Ha a felhasználó egy már kinyitott ALMENÜ linkjére kattint, azt hagyjuk működni!
            if (e.target.closest('.pc-submenu')) {
                return; 
            }
            
            e.preventDefault(); 
            e.stopPropagation(); // Blokkoljuk a sablon beépített gombját és minden más scriptet!
            
            var submenu = this.querySelector('.pc-submenu');
            
            // Ha már nyitva van, zárjuk be
                    if (submenu.style.display === 'block' || this.classList.contains('open')) {
                submenu.style.display = 'none';
                this.classList.remove('open');
                 
                } else {
                // Zárjuk be az összes többit
                document.querySelectorAll('.pc-submenu').forEach(function(sub) {
                    sub.style.display = 'none';
                });
                document.querySelectorAll('.pc-menu-section').forEach(function(item) {
                    item.classList.remove('open');
                });
                
                // Nyissuk ki ezt
                submenu.style.display = 'block';
                this.classList.add('open');
            }
        }, true); // <-- EZ A TITKOS FEGYVER
    });
});
</script>

<?php
$kozos_menu = ob_get_clean();
?>
