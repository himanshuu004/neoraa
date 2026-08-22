@php
$pageTitle = $pillar['meta_title'];
$pageDesc  = $pillar['meta_desc'];
$canonical = route('resources.pillar', $pillar['slug']);
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
            <span>›</span>
            <span>{{ $pillar['nav'] }}</span>
        </nav>

        <div class="nd-wiki-head">
            <div>
                <h1 class="nd-wiki-title">{{ $pillar['title'] }}</h1>
                <p class="nd-wiki-tagline">{{ $pillar['eyebrow'] }}</p>
            </div>
            <aside class="nd-wiki-infobox">
                <div class="nd-wiki-infobox-head">{{ $pillar['nav'] }}</div>
                <dl>
                    <div><dt>For</dt><dd>Parents &amp; caregivers</dd></div>
                    <div><dt>Type</dt><dd>Parent guide</dd></div>
                </dl>
                <a class="nd-wiki-infobox-cta" href="#book">Book a session</a>
            </aside>
        </div>

        <nav class="nd-wiki-toc" aria-label="On this page">
            <div class="nd-wiki-toc-head">Contents</div>
            <ol>
                @foreach ($pillar['sections'] as $i => $section)
                    <li><a href="#s{{ $i }}">{{ $section['h2'] }}</a></li>
                @endforeach
                @if (!empty($pillar['faqs']))
                    <li><a href="#questions">Questions we hear in clinic</a></li>
                @endif
                @if (count($articles))
                    <li><a href="#in-this-topic">Guides in this topic</a></li>
                @endif
            </ol>
        </nav>

        @include('landing.partials.resource_disclaimer')
        <p class="nd-lead">{{ $pillar['summary'] }}</p>

        @foreach ($pillar['sections'] as $i => $section)
            <h2 id="s{{ $i }}">{{ $section['h2'] }}</h2>
            {!! $section['html'] !!}
        @endforeach

        @if (!empty($pillar['faqs']))
            <h2 id="questions">Questions we hear in clinic</h2>
            <div class="nd-faq">
                @foreach ($pillar['faqs'] as $faq)
                    <details>
                        <summary>{{ $faq['q'] }}</summary>
                        <p>{{ $faq['a'] }}</p>
                    </details>
                @endforeach
            </div>
        @endif

        @if (count($articles))
            <h2 id="in-this-topic">Guides in this topic</h2>
            <ul class="nd-wiki-index">
                @foreach ($articles as $item)
                    <li><a href="{{ route('resources.article', $item['slug']) }}">{{ $item['title'] }}</a>: {{ $item['excerpt'] }}</li>
                @endforeach
            </ul>
        @endif

        @if (count($related))
            <h2>See also</h2>
            <ul class="nd-wiki-index">
                @foreach ($related as $item)
                    <li><a href="{{ route('resources.article', $item['slug']) }}">{{ $item['title'] }}</a></li>
                @endforeach
            </ul>
        @endif
    </main>

    @include('landing.partials.resource_cta')
</div>

@if (!empty($pillar['faqs']))
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => array_map(fn ($f) => [
        '@type' => 'Question',
        'name' => $f['q'],
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']],
    ], $pillar['faqs']),
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
