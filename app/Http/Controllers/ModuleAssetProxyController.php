<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModuleAssetProxyController extends Controller
{
    /**
     * Serve module assets for local environments where webroot is /public.
     */
    public function __invoke(Request $request, string $module, string $path)
    {
        if (!preg_match('/^[A-Za-z0-9_-]+$/', $module)) {
            abort(404);
        }

        $assetsRoot = realpath(base_path("Modules/{$module}/Resources/assets"));
        if (!$assetsRoot) {
            abort(404);
        }

        $relativePath = ltrim($path, '/');
        $target = realpath($assetsRoot . DIRECTORY_SEPARATOR . $relativePath);

        if (!$target || !is_file($target) || strpos($target, $assetsRoot) !== 0) {
            abort(404);
        }

        return response()->file($target, [
            'Cache-Control' => 'public, max-age=31536000',
            'Content-Type' => $this->detectMimeType($target),
        ]);
    }

    /**
     * Ensure CSS/JS/fonts are served with browser-safe MIME types on Windows.
     */
    private function detectMimeType(string $path): string
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return match ($ext) {
            'css' => 'text/css; charset=UTF-8',
            'js' => 'application/javascript; charset=UTF-8',
            'mjs' => 'application/javascript; charset=UTF-8',
            'json' => 'application/json; charset=UTF-8',
            'map' => 'application/json; charset=UTF-8',
            'svg' => 'image/svg+xml',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'otf' => 'font/otf',
            'eot' => 'application/vnd.ms-fontobject',
            default => mime_content_type($path) ?: 'application/octet-stream',
        };
    }
}
