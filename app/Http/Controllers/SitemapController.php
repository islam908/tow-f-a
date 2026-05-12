<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class SitemapController extends BaseController
{
    public function index(Request $request)
    {
        $base = url('/');

        $urls = [
            url('/'),
            url('/merchant/login'),
            url('/admin/login'),
            url('/info'),
        ];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>\n';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n';

        foreach ($urls as $u) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$u}</loc>\n";
            $xml .= "    <changefreq>weekly</changefreq>\n";
            $xml .= "    <priority>0.5</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
