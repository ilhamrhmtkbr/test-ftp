<?php

namespace ilhamrhmtkbr\App\Helper;

use Dompdf\Dompdf;

class PDFGeneratorHelper
{
    private $dompdf;

    public function __construct()
    {
        $this->dompdf = new Dompdf();
    }

    public function loadHtml(string $html): void
    {
        $this->dompdf->loadHtml($html);
    }

    public function setPaper(string $size = 'A4', string $orientation = 'portrait'): void
    {
        $this->dompdf->setPaper($size, $orientation);
    }

    public function render(): void
    {
        $this->dompdf->render();
    }

    public function stream(string $fileName = 'document.pdf'): void
    {
        $this->dompdf->stream($fileName, ["Attachment" => false]); // Attachment=false untuk tampil di browser
    }

    public function saveToFile(string $filePath): void
    {
        $output = $this->dompdf->output();
        file_put_contents($filePath, $output);
    }

    public function loadHtmlFromPhpFile(string $filePath, array $data = []): void
    {
        $path = __DIR__ . '/../../resources/views/PDF/' . $filePath . '.php';
        if (!file_exists($path)) {
            throw new \Exception("File not found: $filePath");
        }

        // Ekstrak data untuk digunakan di file PHP
        extract($data);

        // Tangkap output file PHP
        ob_start();
        include $path;
        $html = ob_get_clean();

        // Muat HTML ke Dompdf
        $this->loadHtml($html);
    }
}
