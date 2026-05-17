<?php

class InvoiceService
{
    private const COLOR_INK = '0.08 0.12 0.20';
    private const COLOR_MUTED = '0.35 0.41 0.50';
    private const COLOR_HEADER_SUBTLE = '0.82 0.88 0.96';
    private const COLOR_SUCCESS = '0.12 0.70 0.46';
    private const COLOR_WHITE = '1 1 1';

    public function generate(array $reservation): string
    {
        $invoiceDir = __DIR__ . '/../../public/invoices';

        if (!is_dir($invoiceDir)) {
            mkdir($invoiceDir, 0777, true);
        }

        $filename = 'invoice-' . (int) $reservation['id'] . '.pdf';
        $path = $invoiceDir . '/' . $filename;

        file_put_contents($path, $this->buildPdf($reservation));

        return '/invoices/' . $filename;
    }

    private function buildPdf(array $reservation): string
    {
        $nights = nightsBetween($reservation['check_in'], $reservation['check_out']);
        $deadline = freeCancellationDeadline($reservation['check_in']);
        $invoiceNumber = 'INV-' . str_pad((string) (int) $reservation['id'], 5, '0', STR_PAD_LEFT);
        $price = (float) $reservation['price'];
        $total = (float) $reservation['total_price'];
        $paymentMethod = $reservation['payment_method'] ?? 'Not paid yet';
        $card = !empty($reservation['card_last4']) ? 'Card ending ' . $reservation['card_last4'] : '';

        $content = [
            self::COLOR_INK . " rg\n0 692 612 100 re f\n",
            "0.97 0.98 1 rg\n40 598 532 62 re f\n",
            "0.94 0.96 0.98 rg\n40 424 532 132 re f\n",
            self::COLOR_INK . " RG\n1.2 w\n40 598 532 62 re S\n40 424 532 132 re S\n40 255 532 112 re S\n",
            "0.80 0.84 0.90 RG\n0.8 w\n40 322 m 572 322 l S\n390 255 m 390 367 l S\n",
            self::COLOR_SUCCESS . " rg\n390 178 182 54 re f\n",
            $this->drawText('STAYMANAGER', 40, 748, 24, 'F2', self::COLOR_WHITE),
            $this->drawText('Hotel Reservation Invoice', 40, 725, 13, 'F1', self::COLOR_HEADER_SUBTLE),
            $this->drawText('INVOICE', 450, 748, 24, 'F2', self::COLOR_WHITE),
            $this->drawText($invoiceNumber, 456, 725, 12, 'F1', self::COLOR_HEADER_SUBTLE),
            $this->drawText('Bill To', 58, 638, 10, 'F2', self::COLOR_MUTED),
            $this->drawText($reservation['user_name'], 58, 618, 16, 'F2', self::COLOR_INK),
            $this->drawText($reservation['user_email'], 58, 600, 11, 'F1', self::COLOR_MUTED),
            $this->drawText('Invoice Date', 410, 638, 10, 'F2', self::COLOR_MUTED),
            $this->drawText(date('Y-m-d'), 410, 620, 12, 'F1', self::COLOR_INK),
            $this->drawText('Status', 500, 638, 10, 'F2', self::COLOR_MUTED),
            $this->drawText(ucfirst($reservation['payment_status'] ?? 'unpaid'), 500, 620, 12, 'F2', self::COLOR_SUCCESS),
            $this->drawText('Booking Details', 58, 532, 14, 'F2', self::COLOR_INK),
            $this->drawText('Hotel', 58, 506, 9, 'F2', self::COLOR_MUTED),
            $this->drawText($reservation['hotel_name'], 58, 489, 12, 'F1', self::COLOR_INK),
            $this->drawText('Location', 58, 465, 9, 'F2', self::COLOR_MUTED),
            $this->drawText($reservation['city'] . ', ' . $reservation['country'], 58, 448, 12, 'F1', self::COLOR_INK),
            $this->drawText('Room', 310, 506, 9, 'F2', self::COLOR_MUTED),
            $this->drawText($reservation['room_number'] . ' - ' . $reservation['type'], 310, 489, 12, 'F1', self::COLOR_INK),
            $this->drawText('Stay Dates', 310, 465, 9, 'F2', self::COLOR_MUTED),
            $this->drawText($reservation['check_in'] . ' to ' . $reservation['check_out'] . ' (' . $nights . ' nights)', 310, 448, 12, 'F1', self::COLOR_INK),
            $this->drawText('Description', 58, 344, 10, 'F2', self::COLOR_MUTED),
            $this->drawText('Qty', 415, 344, 10, 'F2', self::COLOR_MUTED),
            $this->drawText('Unit Price', 458, 344, 10, 'F2', self::COLOR_MUTED),
            $this->drawText('Amount', 528, 344, 10, 'F2', self::COLOR_MUTED),
            $this->drawText($reservation['type'] . ' room stay', 58, 294, 12, 'F1', self::COLOR_INK),
            $this->drawText((string) $nights, 418, 294, 12, 'F1', self::COLOR_INK),
            $this->drawText(money($price), 458, 294, 12, 'F1', self::COLOR_INK),
            $this->drawText(money($total), 528, 294, 12, 'F2', self::COLOR_INK),
            $this->drawText('Total Due', 410, 210, 11, 'F2', self::COLOR_WHITE),
            $this->drawText(money($total), 410, 188, 20, 'F2', self::COLOR_WHITE),
            $this->drawText('Payment Method: ' . $paymentMethod, 40, 210, 11, 'F1', self::COLOR_INK),
        ];

        if ($card !== '') {
            $content[] = $this->drawText($card, 40, 193, 11, 'F1', self::COLOR_MUTED);
        }

        $content[] = $this->drawText('Cancellation Policy', 40, 135, 12, 'F2', self::COLOR_INK);
        $content[] = $this->drawText('Free cancellation is available before ' . $deadline . '.', 40, 116, 10, 'F1', self::COLOR_MUTED);
        $content[] = $this->drawText('Thank you for choosing StayManager.', 40, 76, 11, 'F2', self::COLOR_INK);
        $content = implode('', $content);

        $objects = [
            "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n",
            "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n",
            "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 4 0 R /F2 6 0 R >> >> /Contents 5 0 R >>\nendobj\n",
            "4 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n",
            "5 0 obj\n<< /Length " . strlen($content) . " >>\nstream\n" . $content . "\nendstream\nendobj\n",
            "6 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>\nendobj\n",
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $object) {
            $offsets[] = strlen($pdf);
            $pdf .= $object;
        }

        $xref = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i < count($offsets); $i++) {
            $pdf .= str_pad((string) $offsets[$i], 10, '0', STR_PAD_LEFT) . " 00000 n \n";
        }

        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n" . $xref . "\n%%EOF";

        return $pdf;
    }

    private function escapePdfText(string $text): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }

    private function drawText(string $text, int $x, int $y, int $size, string $font = 'F1', string $color = '0 0 0'): string
    {
        return "BT\n" . $color . " rg\n/" . $font . ' ' . $size . " Tf\n" . $x . ' ' . $y . " Td\n(" . $this->escapePdfText($text) . ") Tj\nET\n";
    }
}
