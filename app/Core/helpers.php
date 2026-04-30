<?php

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function basePath(): string
{
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $publicPosition = strpos($scriptName, '/public/');

    if ($publicPosition !== false) {
        return substr($scriptName, 0, $publicPosition + strlen('/public'));
    }

    if (str_ends_with($scriptName, '/public/index.php')) {
        return substr($scriptName, 0, -strlen('/index.php'));
    }

    return '';
}

function url(string $path = ''): string
{
    $path = '/' . ltrim($path, '/');
    return basePath() . ($path === '/' ? '' : $path);
}

function isCurrentPath(string $path): bool
{
    $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
    return str_ends_with($requestPath, $path);
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function isPost(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}
