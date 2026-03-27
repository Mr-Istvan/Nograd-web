<?php if(isset($_SESSION['user_name'])): ?>
    <li class="profile-link" style="background: rgba(255,255,255,0.05); padding: 5px 10px; border-radius: 3px; margin-bottom: 2px; list-style: none;">
        <a href="../profile.php" style="font-weight: bold; color: #3498db !important; text-decoration: none; font-size: 15px;">
            <i class="fa fa-user"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?>
        </a>
    </li>
    <li style="margin-bottom: 5px; list-style: none;">
        <a href="../logout.php" style="color: #ff4d4d !important; font-size: 12px; text-decoration: none;">[ Kilépés ]</a>
    </li>
<?php else: ?>
    <li style="list-style: none; margin-bottom: 1px;"><a href="../login.php" style="color: #3498db !important; text-decoration: none; padding: 2px 0; display: block;"><i class="fa fa-sign-in"></i> Bejelentkezés</a></li>
    <li style="list-style: none; margin-bottom: 1px;"><a href="../reg_id.php" style="text-decoration: none; padding: 5px 0; display: block;"><i class="fa fa-user-plus"></i> Regisztráció</a></li>
<?php endif; ?>

<li style="height: 1px; background: rgba(255,255,255,0.2); margin: 2px 0; list-style: none;"></li>

<li style="list-style: none; margin-bottom: -5px;"><a href="../index.php" style="padding: 3px 0; display: block; text-decoration: none;">Kezdőlap</a></li>
<li style="list-style: none; margin-bottom: -5px;"><a href="latnivalok.php" style="padding: 3px 0; display: block; text-decoration: none;">Látnivalók</a></li>
<li style="list-style: none; margin-bottom: -5px;"><a href="programok.php" style="padding: 5px 0; display: block; text-decoration: none;">Programok</a></li>
<li style="list-style: none; margin-bottom: -5px;"><a href="szallasok.php" style="padding: 5px 0; display: block; text-decoration: none;">Szállások</a></li>
<li style="list-style: none; margin-bottom: -5px;"><a href="gasztronomia.php" style="padding: 5px 0; display: block; text-decoration: none;">Gasztro</a></li>
<li style="list-style: none; margin-bottom: -5px;"><a href="turazas.php" style="padding: 5px 0; display: block; text-decoration: none;">Túrázás</a></li>
<li style="list-style: none; margin-bottom: -5px;"><a href="utazasi-praktikak.php" style="padding: 5px 0; display: block; text-decoration: none;">Praktikák</a></li>