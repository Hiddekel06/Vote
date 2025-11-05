<?php

namespace App\Helpers;

class FileChecker
{
    /**
     * Vérifie si un fichier asset existe dans public/
     */
    public static function checkAsset(string $path)
    {
        $fullPath = public_path($path);
        if (file_exists($fullPath)) {
            echo "✅ FOUND: " . $path . "<br>";
        } else {
            echo "❌ MISSING: " . $path . "<br>";
        }
    }
}
