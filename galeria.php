<?php
require_once __DIR__ . '/init.php';

/**
 * Galéria:
 * - Blog képek: img/blog_*.jpg (blog_back.jpg kizárva)
 * - Portfólió: img/portfolio_*.jpg (thumb), megnyitva: img/portfolio_big_*.jpg ha létezik
 * - Lightbox funkció: js/plugins.js (Lightbox2) + css/light-box.css
 */

// Blog képek pool (kizárjuk a blog_back.jpg-t)
$blogPoolFs = glob(__DIR__ . '/img/blog_*.jpg') ?: [];
$blogImages = [];
foreach ($blogPoolFs as $p) {
    if (basename($p) === 'blog_back.jpg') continue;
    $blogImages[] = 'img/' . basename($p);
}
sort($blogImages);

// Portfólió thumbs
$portfolioThumbFs = glob(__DIR__ . '/img/portfolio_*.jpg') ?: [];
$portfolioThumbs = [];
foreach ($portfolioThumbFs as $p) {
    $portfolioThumbs[] = 'img/' . basename($p);
}
sort($portfolioThumbs);

function portfolio_full_from_thumb(string $thumbPath): string {
    // img/portfolio_4.jpg -> img/portfolio_big_4.jpg (ha létezik), különben thumb
    if (preg_match('~^img/portfolio_(\d+)\.jpg$~', $thumbPath, $m)) {
        $candidate = 'img/portfolio_big_' . $m[1] . '.jpg';
        if (file_exists(__DIR__ . '/' . $candidate)) return $candidate;
    }
    return $thumbPath;
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>NÓGRÁD - Galéria</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/fontAwesome.css">
    <link rel="stylesheet" href="css/light-box.css">
    <link rel="stylesheet" href="css/templatemo-style.css">

    <style>
        body { background: #111; color: #fff; }

        .gallery-hero {
            padding: 60px 0;
            text-align: center;
            background: #000;
        }
        .gallery-hero h1 em { color: #fec107; font-style: normal; }
        .gallery-hero a { color: #fec107; text-decoration: none; }

        .gallery-wrap { padding: 40px 0 70px 0; }

        .section-title {
            margin: 25px 0 15px 0;
            color: rgba(255,255,255,0.85);
            font-weight: 800;
            letter-spacing: 0.3px;
        }
        .section-title i { color: #fec107; margin-right: 8px; }

        .gallery-card {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.10);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.45);
            margin-bottom: 25px;
        }

        /* Képrács megjelenés (kérés: fix + fehér szegély) */
        .gallery-card img {
            width: 250px;
            height: 250px;
            object-fit: cover;
            display: block;
            border: 3px solid #fff;
            box-sizing: border-box;
            margin: 0 auto;
        }

        /* Reszponzív: ha a kijelző keskenyebb, a kép ne lógjon ki */
        @media (max-width: 820px) {
            .gallery-card img {
                width: 100%;
                height: auto;
            }
        }
    </style>
</head>
<body>

<section class="gallery-hero">
    <h1>Nógrád <em>Galéria</em></h1>
    <a href="index.php"><i class="fa fa-arrow-left"></i> Vissza a főoldalra</a>
</section>

<div class="container gallery-wrap">
    <h3 class="section-title"><i class="fa fa-camera"></i> Blog képek</h3>
    <div class="row">
        <?php foreach ($blogImages as $path): ?>
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <div class="gallery-card">
                    <a href="<?php echo htmlspecialchars($path); ?>" data-lightbox="nograd-blog">
                        <img src="<?php echo htmlspecialchars($path); ?>" alt="Blog kép">
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <h3 class="section-title"><i class="fa fa-image"></i> Portfólió képek</h3>
    <div class="row">
        <?php foreach ($portfolioThumbs as $thumb): $full = portfolio_full_from_thumb($thumb); ?>
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <div class="gallery-card">
                    <a href="<?php echo htmlspecialchars($full); ?>" data-lightbox="nograd-portfolio">
                        <img src="<?php echo htmlspecialchars($thumb); ?>" alt="Portfólió kép">
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Lightbox2 a js/plugins.js-ben van, ezért azt kell betölteni -->
<script src="js/vendor/jquery-1.11.2.min.js"></script>
<script src="js/plugins.js"></script>
<script src="js/main.js"></script>

<script>
    if (window.lightbox && window.lightbox.option) {
        lightbox.option({
            albumLabel: "%1 / %2 kép",
            wrapAround: true,
            fadeDuration: 300
        });
    }
</script>
</body>
</html>
