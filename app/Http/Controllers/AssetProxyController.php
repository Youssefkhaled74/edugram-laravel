<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AssetProxyController extends Controller
{
    /**
     * Serve legacy "/public/*" asset URLs when local webroot is already /public
     * (e.g. php artisan serve on 127.0.0.1:8000).
     */
    public function __invoke(Request $request, string $path)
    {
        $relativePath = ltrim($path, '/');
        $publicRoot = realpath(public_path());

        if (!$publicRoot) {
            abort(404);
        }

        $target = realpath(public_path($relativePath));

        if (!$target || !is_file($target) || strpos($target, $publicRoot) !== 0) {
            abort(404);
        }

        return response()->file($target, [
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }
}

