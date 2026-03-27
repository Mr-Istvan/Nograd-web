<?php
$file = 'index.php';
$lines = file($file, FILE_IGNORE_NEW_LINES);
$stack = [];
$issues = [];

$tagMap = [
    'div' => ['open' => '/<div\b/i', 'close' => '/<\/div>/i'],
    'a' => ['open' => '/<a\b/i', 'close' => '/<\/a>/i'],
    'p' => ['open' => '/<p\b/i', 'close' => '/<\/p>/i'],
    'section' => ['open' => '/<section\b/i', 'close' => '/<\/section>/i'],
    'article' => ['open' => '/<article\b/i', 'close' => '/<\/article>/i'],
    'ul' => ['open' => '/<ul\b/i', 'close' => '/<\/ul>/i'],
    'li' => ['open' => '/<li\b/i', 'close' => '/<\/li>/i'],
    'span' => ['open' => '/<span\b/i', 'close' => '/<\/span>/i'],
    'nav' => ['open' => '/<nav\b/i', 'close' => '/<\/nav>/i'],
];

foreach ($lines as $i => $line) {
    foreach ($tagMap as $tag => $patterns) {
        if (preg_match($patterns['open'], $line)) {
            $stack[] = [$tag, $i + 1];
        }
        if (preg_match($patterns['close'], $line)) {
            for ($s = count($stack) - 1; $s >= 0; $s--) {
                if ($stack[$s][0] === $tag) {
                    array_splice($stack, $s, 1);
                    continue 2;
                }
            }
            $issues[] = "Unmatched closing </$tag> at line " . ($i + 1);
        }
    }
}

foreach ($stack as [$tag, $line]) {
    $issues[] = "Unclosed <$tag> opened at line $line";
}

echo implode(PHP_EOL, $issues) . PHP_EOL;
