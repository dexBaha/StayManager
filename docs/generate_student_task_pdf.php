<?php

function pdfEscape(string $text): string
{
    return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
}

function wrapLine(string $line, int $limit): array
{
    $words = preg_split('/\s+/', trim($line)) ?: [];
    $lines = [];
    $current = '';

    foreach ($words as $word) {
        $candidate = $current === '' ? $word : $current . ' ' . $word;

        if (strlen($candidate) > $limit && $current !== '') {
            $lines[] = $current;
            $current = $word;
        } else {
            $current = $candidate;
        }
    }

    if ($current !== '') {
        $lines[] = $current;
    }

    return $lines ?: [''];
}

$source = __DIR__ . '/student-task-code-guide.md';
$target = __DIR__ . '/student-task-code-guide.pdf';
$markdown = file($source, FILE_IGNORE_NEW_LINES) ?: [];
$pages = [];
$content = '';
$y = 760;
$lineHeight = 14;
$margin = 52;

$addText = function (string $text, int $size = 10, string $font = 'F1') use (&$content, &$y, &$pages, $lineHeight, $margin): void {
    if ($y < 58) {
        $pages[] = $content;
        $content = '';
        $y = 760;
    }

    $content .= "BT\n/" . $font . ' ' . $size . " Tf\n" . $margin . ' ' . $y . " Td\n(" . pdfEscape($text) . ") Tj\nET\n";
    $y -= $lineHeight + max(0, $size - 10);
};

foreach ($markdown as $raw) {
    $line = rtrim($raw);

    if ($line === '') {
        $y -= 7;
        continue;
    }

    if (str_starts_with($line, '# ')) {
        $y -= 8;
        foreach (wrapLine(substr($line, 2), 44) as $wrapped) {
            $addText($wrapped, 18, 'F2');
        }
        $y -= 6;
        continue;
    }

    if (str_starts_with($line, '## ')) {
        $y -= 6;
        foreach (wrapLine(substr($line, 3), 58) as $wrapped) {
            $addText($wrapped, 14, 'F2');
        }
        $y -= 3;
        continue;
    }

    if (str_starts_with($line, '### ')) {
        $y -= 4;
        foreach (wrapLine(substr($line, 4), 68) as $wrapped) {
            $addText($wrapped, 12, 'F2');
        }
        continue;
    }

    if (str_starts_with($line, '- ')) {
        foreach (wrapLine(substr($line, 2), 88) as $index => $wrapped) {
            $addText(($index === 0 ? '- ' : '  ') . $wrapped);
        }
        continue;
    }

    foreach (wrapLine($line, 92) as $wrapped) {
        $addText($wrapped);
    }
}

if ($content !== '') {
    $pages[] = $content;
}

$objects = [];
$pageObjects = [];
$contentObjects = [];
$nextObject = 5;

foreach ($pages as $_) {
    $pageObjects[] = $nextObject++;
    $contentObjects[] = $nextObject++;
}

$objects[1] = "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
$objects[2] = "2 0 obj\n<< /Type /Pages /Kids [" . implode(' ', array_map(fn (int $id): string => $id . ' 0 R', $pageObjects)) . "] /Count " . count($pageObjects) . " >>\nendobj\n";
$objects[3] = "3 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";
$objects[4] = "4 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>\nendobj\n";

foreach ($pages as $index => $pageContent) {
    $pageId = $pageObjects[$index];
    $contentId = $contentObjects[$index];
    $objects[$pageId] = $pageId . " 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> /Contents " . $contentId . " 0 R >>\nendobj\n";
    $objects[$contentId] = $contentId . " 0 obj\n<< /Length " . strlen($pageContent) . " >>\nstream\n" . $pageContent . "endstream\nendobj\n";
}

ksort($objects);

$pdf = "%PDF-1.4\n";
$offsets = [0];

foreach ($objects as $object) {
    $offsets[] = strlen($pdf);
    $pdf .= $object;
}

$xref = strlen($pdf);
$pdf .= "xref\n0 " . (count($objects) + 1) . "\n0000000000 65535 f \n";

for ($i = 1; $i < count($offsets); $i++) {
    $pdf .= str_pad((string) $offsets[$i], 10, '0', STR_PAD_LEFT) . " 00000 n \n";
}

$pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n" . $xref . "\n%%EOF";
file_put_contents($target, $pdf);

echo 'Generated ' . $target . ' with ' . count($pages) . ' pages' . PHP_EOL;
