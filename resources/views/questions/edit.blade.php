@extends('layout')

@section('page_title')
    <h2>Edit Question</h2>
@endsection

@section('content')
    <div class="content-wrapper">
        <form method="post" action="/question/{{ $question->id }}">
            @csrf
            @method('PUT')

            <div class="row uniform 50%">
                <div class="12u 12u$(4)">
                    <input type="text" name="label" id="label" value="{{ $question->label }}" placeholder="What question do you want to ask?" class="@error('label') error-input @enderror" />

                    @error('label')
                    <p class="error_message">{{ $errors->first('label') }}</p>
                    @enderror
                </div>

                <div class="6u 12u$(2)">
                    <div class="select-wrapper">
                        <select name="required" id="required">
                            <option value="0" {{ $question->required == 0 ? 'selected' : '' }} >Optional</option>
                            <option value="1" {{ $question->required == 1 ? 'selected' : '' }} >Required</option>
                        </select>

                        @error('required')
                        <p class="error_message">{{ $errors->first('required') }}</p>
                        @enderror
                    </div>
                </div>

                <div class="6u 12u$(2)">
                    <div class="select-wrapper">
                        <select name="enabled" id="enabled">
                            <option value="1" {{ $question->enabled == 1 ? 'selected' : '' }} >Enabled</option>
                            <option value="0" {{ $question->enabled == 0 ? 'selected' : '' }} >Disabled</option>
                        </select>

                        @error('enabled')
                        <p class="error_message">{{ $errors->first('enabled') }}</p>
                        @enderror
                    </div>
                </div>

                <div class="12u$">
                    <ul class="actions">
                        <li><input type="submit" value="Save" class="special" /></li>
                        <li><input type="reset" value="Cancel" onclick="window.location = '/question';" /></li>
                    </ul>
                </div>
            </div>
        </form>



        <form method="post" action="/question/{{ $question->id }}">
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
