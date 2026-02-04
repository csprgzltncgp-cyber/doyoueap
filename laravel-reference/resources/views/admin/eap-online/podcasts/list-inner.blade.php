@foreach ($podcasts as $podcast)
    @include('components.eap-online.podcasts_line_component', ['podcasts' => $podcast])
@endforeach

{{ $podcasts->links() }}