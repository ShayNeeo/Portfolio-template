<?php

namespace PortfolioTools\QR;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

/**
 * QR Code generation service
 */
class QRGenerator
{
    private $options;
    private $outputDir;

    public function __construct(string $outputDir)
    {
        $this->outputDir = $outputDir;
        $this->options = new QROptions([
            'version'      => 5,
            'outputType'   => QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel'     => QRCode::ECC_M,
            'scale'        => 6,
            'imageBase64'  => false,
        ]);
    }

    /**
     * Generate QR code for a URL
     */
    public function generateForUrl(string $url): array
    {
        try {
            $qrcode = new QRCode($this->options);
            $filename = 'link_qr_' . time() . '_' . bin2hex(random_bytes(4)) . '.png';
            $filepath = $this->outputDir . '/' . $filename;

            // Generate QR code
            $qrcode->render($url, $filepath);

            return [
                'success' => true,
                'filename' => $filename,
                'filepath' => $filepath,
                'url' => $url
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate QR code for a file download
     */
    public function generateForFile(string $downloadUrl, string $filename): array
    {
        try {
            $qrcode = new QRCode($this->options);
            $qrFilename = 'file_dl_qr_' . time() . '_' . bin2hex(random_bytes(4)) . '.png';
            $qrFilepath = $this->outputDir . '/' . $qrFilename;

            // Generate QR code
            $qrcode->render($downloadUrl, $qrFilepath);

            return [
                'success' => true,
                'filename' => $qrFilename,
                'filepath' => $qrFilepath,
                'download_url' => $downloadUrl,
                'original_filename' => $filename
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Set QR code options
     */
    public function setOptions(array $options): void
    {
        foreach ($options as $key => $value) {
            $this->options->{$key} = $value;
        }
    }

    /**
     * Get available error correction levels
     */
    public static function getErrorCorrectionLevels(): array
    {
        return [
            'L' => QRCode::ECC_L, // ~7%
            'M' => QRCode::ECC_M, // ~15%
            'Q' => QRCode::ECC_Q, // ~25%
            'H' => QRCode::ECC_H  // ~30%
        ];
    }

    /**
     * Get QR code info
     */
    public function getQRInfo(string $filename): array
    {
        $filepath = $this->outputDir . '/' . $filename;
        
        if (!file_exists($filepath)) {
            return ['exists' => false];
        }

        return [
            'exists' => true,
            'filename' => $filename,
            'filepath' => $filepath,
            'size' => filesize($filepath),
            'created' => filemtime($filepath),
            'url' => '/qr-code/view-qr.php?file=' . urlencode($filename)
        ];
    }
}
