<?php

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function isPost(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

