@foreach ($webinars as $webinar)
    @include('components.eap-online.webinars_line_component', ['webinars' => $webinar, 'translation' => $translation])
@endforeach

{{ $webinars->links() }}