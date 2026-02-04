@foreach ($articles as $article)
    @include('components.company-website.article_line_component', ['article' => $article, 'translation' => $translation])
@endforeach

{{ $articles->links() }}
