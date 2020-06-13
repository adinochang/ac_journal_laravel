@extends('layout')

@section('page_title')
    <h2>New Entry</h2>
@endsection

@section('content')
    <div class="content-wrapper">
        <form method="post" action="/entry">
            @csrf

            <div class="row uniform 50%">
                @foreach ($questions as $question)
                    <div class="12u 12u$(4)">
                        <label for="answer_{{ $question->id }}">{{ $question->label }}</label>
                        <textarea name="answer_{{ $question->id }}" id="answer_{{ $question->id }}" placeholder="Enter your answer" rows="6" class="@error('answer_' . $question->id) error-textarea @enderror" ></textarea>

                        @error('answer_' . $question->id)
                        <p class="error_message">You must enter an answer for this question</p>
                        @enderror
                    </div>
                @endforeach

                <div class="12u$">
                    <ul class="actions">
                        <li><input type="submit" value="Save" class="special" /></li>
                        <li><input type="reset" value="Cancel" onclick="window.location = '{{ url()->previous() }}';" /></li>
                    </ul>
                </div>
            </div>

            <input type="hidden" name="previous_url" value="{{ url()->previous() }}" />
        </form>
    </div>
@endsection
