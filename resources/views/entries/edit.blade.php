@extends('layout')

@section('page_title')
    <h2>Edit Entry</h2>
@endsection

@section('content')
    <div class="content-wrapper">
        <form method="post" action="/entry/{{ $entry->id }}">
            @csrf
            @method('PUT')

            <div class="row uniform 50%">
                @foreach ($entry->answers as $answer)
                    <div class="12u 12u$(4)">
                        <label for="answer_{{ $answer->question->id }}">{{ $answer->question->label }}</label>
                        <textarea name="answer_{{ $answer->question->id }}" id="answer_{{ $answer->question->id }}" placeholder="Enter your answer" rows="6" class="@error('answer_' . $answer->question->id) error-textarea @enderror" >{{ $answer->answer_text }}</textarea>

                        @error('answer_' . $answer->question->id)
                        <p class="error_message">You must enter an answer for this question</p>
                        @enderror
                    </div>
                @endforeach

                <div class="12u$">
                    <ul class="actions">
                        <li><input type="submit" value="Save" class="special" /></li>
                        <li><input type="reset" value="Cancel" onclick="window.location = '/entry';" /></li>
                    </ul>
                </div>
            </div>
        </form>



        <form method="post" action="/entry/{{ $entry->id }}">
            @csrf
            @method('DELETE')

            <div class="row uniform 50%">
                <div class="12u$" style="margin-top: 10em;">
                    <ul class="actions">
                        <li><input type="submit" value="Delete" class="delete-button" onclick="return confirm('Really delete?')" /></li>
                    </ul>
                </div>
            </div>
        </form>
    </div>
@endsection
