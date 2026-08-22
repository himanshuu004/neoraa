@php
$pageTitle = 'Resources';
$pageDesc  = 'Simple parent guides on autism, ADHD, late talking, daily skills, and hearing.';
$canonical = route('resources.index');
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
            <span>Resources</span>
        </nav>

        <h1 class="nd-wiki-title">Parent resources</h1>
        <p class="nd-wiki-tagline">Parent guides on talking, attention, daily skills, and hearing</p>

        <nav class="nd-wiki-toc" aria-label="On this page">
            <div class="nd-wiki-toc-head">Contents</div>
            <ol>
                <li><a href="#topics">Topics</a></li>
                <li><a href="#all-guides">All guides</a></li>
            </ol>
        </nav>

        @include('landing.partials.resource_disclaimer')

        <p class="nd-lead">Short guides for parents. If a medical word is used, the meaning is in brackets. Use the list on the left, or pick a topic below.</p>

        <h2 id="topics">Topics</h2>
        <ul class="nd-wiki-index">
            @foreach ($pillars as $pillar)
                <li>
                    <a href="{{ route('resources.pillar', $pillar['slug']) }}">{{ $pillar['title'] }}</a>: {{ $pillar['summary'] }}
                </li>
            @endforeach
        </ul>

        <h2 id="all-guides">All guides</h2>
        <ol class="nd-wiki-index">
            @foreach ($articles as $article)
                <li>
                    <a href="{{ route('resources.article', $article['slug']) }}">{{ $article['title'] }}</a>: {{ $article['excerpt'] }}
                </li>
            @endforeach
        </ol>
    </main>

    @include('landing.partials.resource_cta')
</div>

@include('landing.partials.footer')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof AOS !== 'undefined') AOS.init({ duration: 950, easing: 'ease-in-out', once: true, offset: 70 });
});
</script>
</body>
</html>
