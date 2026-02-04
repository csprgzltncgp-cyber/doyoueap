@foreach ($quizzes as $quiz)
    @include('components.eap-online.quizzes_line_component', ['quizzes' => $quiz, 'translation' => $translation])
@endforeach

{{ $quizzes->links() }}