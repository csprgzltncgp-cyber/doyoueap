@foreach ($articles as $article)
    @include('components.eap-online.articles_line_component', ['articles' => $article, 'translation' => $translation])
@endforeach

{{ $articles->links() }}