<?php

class InvoiceService
{
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
        $nights = max(1, (new DateTime($reservation['check_in']))->diff(new DateTime($reservation['check_out']))->days);
        $deadline = (new DateTimeImmutable($reservation['check_in']))->modify('-2 days')->format('Y-m-d');
        $lines = [
            'STAYMANAGER',
            'HOTEL RESERVATION INVOICE',
            'Invoice Number: INV-' . str_pad((string) (int) $reservation['id'], 5, '0', STR_PAD_LEFT),
            'Issue Date: ' . date('Y-m-d'),
            '------------------------------------------------------------',
            '',
            'CUSTOMER',
            'Name: ' . $reservation['user_name'],
            'Email: ' . $reservation['user_email'],
            '',
            'BOOKING DETAILS',
            'Hotel: ' . $reservation['hotel_name'],
            'Location: ' . $reservation['city'] . ', ' . $reservation['country'],
            'Room: ' . $reservation['room_number'] . ' - ' . $reservation['type'],
            'Check-in: ' . $reservation['check_in'],
            'Check-out: ' . $reservation['check_out'],
            'Nights: ' . $nights,
            'Status: ' . ucfirst($reservation['status']),
            '',
            'PAYMENT SUMMARY',
            'Price per Night: $' . number_format((float) $reservation['price'], 2),
            'Total Amount: $' . number_format((float) $reservation['total_price'], 2),
            '------------------------------------------------------------',
            '',
            'FREE CANCELLATION POLICY',
            'You can cancel anytime before ' . $deadline . ' and get a full refund.',
            'After this deadline, cancellation may be reviewed by hotel policy.',
            '',
            'Thank you for choosing StayManager.',
        ];

        $content = "BT\n/F1 18 Tf\n72 760 Td\n";
        foreach ($lines as $index => $line) {
            $fontSize = in_array($line, ['STAYMANAGER', 'HOTEL RESERVATION INVOICE'], true) ? 18 : 11;
            $content .= '/F1 ' . $fontSize . " Tf\n";
            $content .= '(' . $this->escapePdfText($line) . ") Tj\n";
            $content .= "0 -" . ($line === '' ? 12 : 18) . " Td\n";
        }
        $content .= "ET";

        $objects = [
            "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n",
            "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n",
            "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>\nendobj\n",
            "4 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n",
            "5 0 obj\n<< /Length " . strlen($content) . " >>\nstream\n" . $content . "\nendstream\nendobj\n",
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
}
