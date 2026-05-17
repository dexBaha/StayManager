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

function publicPath(string $path = ''): string
{
    return __DIR__ . '/../../public' . ($path === '' ? '' : '/' . ltrim($path, '/'));
}

function mediaUrl(?string $path): string
{
    $path = trim($path ?? '');

    if ($path === '') {
        return '';
    }

    if (preg_match('/^https?:\/\//i', $path)) {
        return $path;
    }

    return url($path);
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

function money(float|int|string $amount): string
{
    return '$' . number_format((float) $amount, 2);
}

function uploadImage(string $field, string $directory = 'uploads', int $maxBytes = 2097152): ?string
{
    if (empty($_FILES[$field]) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $file = $_FILES[$field];

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Image upload failed.');
    }

    if (($file['size'] ?? 0) > $maxBytes) {
        throw new RuntimeException('Image is too large. Maximum size is 2 MB.');
    }

    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];
    $mimeType = mime_content_type($file['tmp_name']);

    if (!isset($allowedTypes[$mimeType])) {
        throw new RuntimeException('Only JPG, PNG, WEBP, and GIF images are allowed.');
    }

    $uploadDir = publicPath($directory);

    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true)) {
        throw new RuntimeException('Could not create upload directory.');
    }

    $filename = bin2hex(random_bytes(12)) . '.' . $allowedTypes[$mimeType];
    $target = $uploadDir . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        throw new RuntimeException('Could not save uploaded image.');
    }

    return '/' . trim($directory, '/') . '/' . $filename;
}

function isPost(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function parseHotelDate(string $date): DateTimeImmutable
{
    $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
    $errors = DateTimeImmutable::getLastErrors();

    if (!$parsed || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) {
        throw new InvalidArgumentException('Invalid date format.');
    }

    return $parsed;
}

function isValidStayRange(string $checkIn, string $checkOut): bool
{
    try {
        return parseHotelDate($checkOut) > parseHotelDate($checkIn);
    } catch (InvalidArgumentException) {
        return false;
    }
}

function nightsBetween(string $checkIn, string $checkOut): int
{
    return (int) parseHotelDate($checkIn)->diff(parseHotelDate($checkOut))->days;
}

function freeCancellationDeadline(string $checkIn): string
{
    return parseHotelDate($checkIn)->modify('-2 days')->format('Y-m-d');
}

function daysUntil(?string $date): int
{
    if (!$date) {
        return 0;
    }

    $today = new DateTimeImmutable('today');
    $target = parseHotelDate($date);

    return max(0, (int) $today->diff($target)->format('%r%a'));
}

function roomTypePhoto(string $type, int $width = 900): string
{
    $type = strtolower($type);
    $images = [
        'suite' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427',
        'family' => 'https://images.unsplash.com/photo-1584132915807-fd1f5fbc078f',
        'double' => 'https://images.unsplash.com/photo-1566665797739-1674de7a421a',
        'executive' => 'https://images.unsplash.com/photo-1618773928121-c32242e63f39',
        'default' => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304',
    ];

    $image = match (true) {
        str_contains($type, 'suite') || str_contains($type, 'riad') => $images['suite'],
        str_contains($type, 'family') => $images['family'],
        str_contains($type, 'double') || str_contains($type, 'deluxe') => $images['double'],
        str_contains($type, 'executive') => $images['executive'],
        default => $images['default'],
    };

    return $image . '?auto=format&fit=crop&w=' . $width . '&q=80';
}

function roomGallery(string $type): array
{
    $type = strtolower($type);

    if (str_contains($type, 'suite') || str_contains($type, 'riad') || str_contains($type, 'executive')) {
        return [
            roomTypePhoto($type, 1200),
            'https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1200&q=80',
        ];
    }

    if (str_contains($type, 'family')) {
        return [
            roomTypePhoto($type, 1200),
            'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1595576508898-0ad5c879a061?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1560448075-bb485b067938?auto=format&fit=crop&w=1200&q=80',
        ];
    }

    if (str_contains($type, 'double') || str_contains($type, 'deluxe')) {
        return [
            roomTypePhoto($type, 1200),
            'https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=1200&q=80',
        ];
    }

    return [
        roomTypePhoto($type, 1200),
        'https://images.unsplash.com/photo-1560448204-603b3fc33ddc?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1618220179428-22790b461013?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1590490359683-658d3d23f972?auto=format&fit=crop&w=1200&q=80',
    ];
}

function hotelGallery(string $mainPhoto): array
{
    return [
        mediaUrl($mainPhoto),
        'https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1564501049412-61c2a3083791?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1445019980597-93fa8acb246c?auto=format&fit=crop&w=1200&q=80',
    ];
}

function roomAvailability(array $room): array
{
    if (!empty($room['reserved_until'])) {
        $days = daysUntil($room['reserved_until']);

        return [
            'status' => 'reserved',
            'label' => 'Reserved',
            'message' => $days > 0 ? 'Available after ' . $room['reserved_until'] . ' (' . $days . ' day' . ($days === 1 ? '' : 's') . ' left)' : 'Available soon',
            'can_book' => false,
        ];
    }

    if (($room['status'] ?? '') === 'maintenance') {
        $days = daysUntil($room['unavailable_until'] ?? null);

        return [
            'status' => 'maintenance',
            'label' => 'Maintenance',
            'message' => !empty($room['unavailable_until'])
                ? 'Maintenance ends on ' . $room['unavailable_until'] . ' (' . $days . ' day' . ($days === 1 ? '' : 's') . ' left)'
                : 'Maintenance in progress. End date not set.',
            'can_book' => false,
        ];
    }

    if (($room['status'] ?? '') === 'reserved') {
        $days = daysUntil($room['unavailable_until'] ?? null);

        return [
            'status' => 'reserved',
            'label' => 'Reserved',
            'message' => !empty($room['unavailable_until'])
                ? 'Available after ' . $room['unavailable_until'] . ' (' . $days . ' day' . ($days === 1 ? '' : 's') . ' left)'
                : 'Reserved. Return date not set.',
            'can_book' => false,
        ];
    }

    return [
        'status' => 'available',
        'label' => 'Available',
        'message' => 'Ready to book now',
        'can_book' => true,
    ];
}
