<?php

namespace ilhamrhmtkbr\App\Helper\Components;

class GenerateBreadcrumbHelper
{
    public static function getComponent(): string
    {
        $path = $_SERVER['REQUEST_URI'];
        $path = trim($path, '/'); // Hapus '/' di awal dan akhir
        $segments = explode('/', $path); // Pisah berdasarkan '/'

        $elements = [];
        foreach ($segments as $segment) {
            if (strpos($segment, '?') !== false) {
                $frag = explode('?', $segment);
                $elements[] = $frag[0]; // Ambil segmen sebelum '?'
            } else {
                $elements[] = $segment;
            }
        }

        $breadcrumb = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
        $url = '/';

        foreach ($elements as $index => $element) {
            $url .= $element . '/'; // Tambahkan segmen ke URL

            if ($index === count($elements) - 1) {
                // Elemen terakhir (aktif)
                $breadcrumb .= '<li class="breadcrumb-item active" aria-current="page">' . ucfirst($element) . '</li>';
            } else {
                // Elemen breadcrumb dengan link
                $breadcrumb .= '<li class="breadcrumb-item"><a href="' . rtrim($url, '/') . '">' . ucfirst($element) . '</a>';
            }
        }
        $breadcrumb .= '</ol></nav>';

        return $breadcrumb;
    }
}
