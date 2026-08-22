@php
    $wikiPillars = \App\Support\ResourceLibrary::pillars();
    $wikiArticles = \App\Support\ResourceLibrary::articles();
    $wikiSlug = request()->route('slug');
    $isPillar = request()->routeIs('resources.pillar');
    $isArticle = request()->routeIs('resources.article');
@endphp
<aside class="nd-wiki-rail" aria-label="Resources contents">
    <p class="nd-wiki-rail-label">Topics</p>
    <ul class="nd-wiki-rail-list">
        @foreach ($wikiPillars as $item)
            <li>
                <a href="{{ route('resources.pillar', $item['slug']) }}" class="{{ $isPillar && $wikiSlug === $item['slug'] ? 'is-active' : '' }}">{{ $item['title'] }}</a>
            </li>
        @endforeach
    </ul>

    <p class="nd-wiki-rail-label">All guides</p>
    <ul class="nd-wiki-rail-list nd-wiki-rail-list--all">
        @foreach ($wikiArticles as $item)
            <li>
                <a href="{{ route('resources.article', $item['slug']) }}" class="{{ $isArticle && $wikiSlug === $item['slug'] ? 'is-active' : '' }}">{{ $item['title'] }}</a>
            </li>
        @endforeach
    </ul>
</aside>
