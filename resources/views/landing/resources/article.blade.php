@php
$pageTitle = $article['meta_title'];
$pageDesc  = $article['meta_desc'];
$canonical = route('resources.article', $article['slug']);
$pageTitleIsFull = true;
$bodyClass = 'nd-wiki';
@endphp
@include('landing.partials.page_head')
@include('landing.partials.navbar')

<div class="nd-wiki-shell">
    @include('landing.partials.wiki_rail')

    <main class="nd-wiki-main">
        <nav class="nd-wiki-bc" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Home</a>
            <span>›</span>
            <a href="{{ route('resources.index') }}">Resources</a>
            @if ($pillar)
                <span>›</span>
                <a href="{{ route('resources.pillar', $pillar['slug']) }}">{{ $pillar['nav'] }}</a>
            @endif
            <span>›</span>
            <span>{{ $article['title'] }}</span>
        </nav>

        <div class="nd-wiki-head">
            <div>
                <h1 class="nd-wiki-title">{{ $article['title'] }}</h1>
                <p class="nd-wiki-tagline">{{ $article['mins'] }} min read</p>
            </div>
            <aside class="nd-wiki-infobox">
                <div class="nd-wiki-infobox-head">Guide</div>
                <dl>
                    @if ($pillar)
                        <div><dt>Topic</dt><dd><a href="{{ route('resources.pillar', $pillar['slug']) }}">{{ $pillar['nav'] }}</a></dd></div>
                    @endif
                    <div><dt>Read time</dt><dd>{{ $article['mins'] }} minutes</dd></div>
                </dl>
                <a class="nd-wiki-infobox-cta" href="#book">Book a session</a>
            </aside>
        </div>

        <nav class="nd-wiki-toc" aria-label="On this page">
            <div class="nd-wiki-toc-head">Contents</div>
            <ol>
                @foreach ($article['sections'] as $i => $section)
                    <li><a href="#s{{ $i }}">{{ $section['h2'] }}</a></li>
                @endforeach
                @if (!empty($article['faqs']))
                    <li><a href="#questions">Quick answers</a></li>
                @endif
                @if (count($related))
                    <li><a href="#see-also">See also</a></li>
                @endif
            </ol>
        </nav>

        @include('landing.partials.resource_disclaimer')
        <p class="nd-lead">{{ $article['excerpt'] }}</p>

        @foreach ($article['sections'] as $i => $section)
            <h2 id="s{{ $i }}">{{ $section['h2'] }}</h2>
            {!! $section['html'] !!}
        @endforeach

        @if (!empty($article['faqs']))
            <h2 id="questions">Quick answers</h2>
            <div class="nd-faq">
                @foreach ($article['faqs'] as $faq)
                    <details>
                        <summary>{{ $faq['q'] }}</summary>
                        <p>{{ $faq['a'] }}</p>
                    </details>
                @endforeach
            </div>
        @endif

        @if (count($related))
            <h2 id="see-also">See also</h2>
            <ul class="nd-wiki-index">
                @foreach ($related as $item)
                    <li><a href="{{ route('resources.article', $item['slug']) }}">{{ $item['title'] }}</a></li>
                @endforeach
            </ul>
        @endif
    </main>

    @include('landing.partials.resource_cta')
</div>

<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'headline' => $article['title'],
    'description' => $article['meta_desc'],
    'author' => ['@type' => 'Organization', 'name' => 'NEORA Therapy & Audiology Clinic'],
    'publisher' => ['@type' => 'Organization', 'name' => 'NEORA Therapy & Audiology Clinic'],
    'about' => 'Parent education on child communication and development',
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@if (!empty($article['faqs']))
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => array_map(fn ($f) => [
        '@type' => 'Question',
        'name' => $f['q'],
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']],
    ], $article['faqs']),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endif

@include('landing.partials.footer')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof AOS !== 'undefined') AOS.init({ duration: 950, easing: 'ease-in-out', once: true, offset: 70 });
});
</script>
</body>
</html>
