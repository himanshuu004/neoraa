<?php

namespace App\Support;

class ResourceLibrary
{
    public static function pillars(): array
    {
        return require app_path('Support/resource_pillars.php');
    }

    public static function articles(): array
    {
        return require app_path('Support/resource_articles.php');
    }

    public static function pillar(string $slug): ?array
    {
        return self::pillars()[$slug] ?? null;
    }

    public static function article(string $slug): ?array
    {
        return self::articles()[$slug] ?? null;
    }

    public static function articlesForPillar(string $pillarSlug): array
    {
        return array_values(array_filter(self::articles(), function (array $article) use ($pillarSlug) {
            return ($article['pillar'] ?? '') === $pillarSlug;
        }));
    }

    public static function relatedArticles(array $slugs, ?string $exclude = null): array
    {
        $out = [];
        foreach ($slugs as $slug) {
            if ($exclude && $slug === $exclude) {
                continue;
            }
            $article = self::article($slug);
            if ($article) {
                $out[] = $article;
            }
        }

        return $out;
    }

    public static function allPublicUrls(): array
    {
        $urls = [route('resources.index')];
        foreach (self::pillars() as $pillar) {
            $urls[] = route('resources.pillar', $pillar['slug']);
        }
        foreach (self::articles() as $article) {
            $urls[] = route('resources.article', $article['slug']);
        }

        return $urls;
    }
}
