@foreach ($videos as $video)
    @include('components.eap-online.videos_line_component', ['videos' => $video, 'translation' => $translation])
@endforeach

{{ $videos->links() }}