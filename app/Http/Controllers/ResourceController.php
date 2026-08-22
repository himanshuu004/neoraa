<?php

namespace App\Http\Controllers;

use App\Support\ResourceLibrary;
use Illuminate\Http\Response;

class ResourceController extends Controller
{
    public function index()
    {
        return view('landing.resources.index', [
            'pillars' => ResourceLibrary::pillars(),
            'articles' => ResourceLibrary::articles(),
        ]);
    }

    public function pillar(string $slug)
    {
        $pillar = ResourceLibrary::pillar($slug);
        if (! $pillar) {
            abort(404);
        }

        return view('landing.resources.pillar', [
            'pillar' => $pillar,
            'articles' => ResourceLibrary::articlesForPillar($slug),
            'related' => ResourceLibrary::relatedArticles($pillar['related'] ?? []),
        ]);
    }

    public function article(string $slug)
    {
        $article = ResourceLibrary::article($slug);
        if (! $article) {
            abort(404);
        }

        $pillar = ResourceLibrary::pillar($article['pillar']);

        return view('landing.resources.article', [
            'article' => $article,
            'pillar' => $pillar,
            'related' => ResourceLibrary::relatedArticles($article['related'] ?? [], $slug),
        ]);
    }

    public function sitemap(): Response
    {
        $urls = array_merge(
            [
                route('home'),
                route('about'),
                route('services'),
                route('gallery'),
                route('testimonials'),
                route('reviews'),
                route('contact'),
                route('careers'),
                route('resources.index'),
            ],
            ResourceLibrary::allPublicUrls()
        );
        $urls = array_unique($urls);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
        foreach ($urls as $url) {
            $xml .= '  <url><loc>'.htmlspecialchars($url, ENT_XML1).'</loc></url>'."\n";
        }
        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
