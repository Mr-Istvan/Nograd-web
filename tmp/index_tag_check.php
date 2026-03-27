<?php
$path = __DIR__ . '/../index.php';
$html = file_get_contents($path);

$stack = [];
$issues = [];
$tags = ['div', 'a', 'section', 'nav', 'ul', 'li', 'span', 'header', 'footer'];

preg_match_all('/<\/?([a-z0-9]+)(?:\s[^>]*)?>/i', $html, $matches, PREG_OFFSET_CAPTURE);

foreach ($matches[0] as $idx => $fullMatch) {
    $tag = strtolower($matches[1][$idx][0]);
    if (!in_array($tag, $tags, true)) {
        continue;
    }

    $isClosing = strpos($fullMatch[0], '</') === 0;
    $line = substr_count(substr($html, 0, $fullMatch[1]), "\n") + 1;

    if (!$isClosing && !preg_match('/\/>\s*$/', $fullMatch[0])) {
        $stack[] = [$tag, $line];
        continue;
    }

    if ($isClosing) {
        for ($i = count($stack) - 1; $i >= 0; $i--) {
            if ($stack[$i][0] === $tag) {
                array_splice($stack, $i, 1);
                continue 2;
            }
        }
        $issues[] = "Unmatched closing </$tag> at line $line";
    }
}

foreach ($stack as [$tag, $line]) {
    $issues[] = "Unclosed <$tag> opened at line $line";
}

echo implode(PHP_EOL, $issues) . PHP_EOL;
