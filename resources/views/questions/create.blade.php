@extends('layout')

@section('page_title')
    <h2>New Question</h2>
@endsection

@section('content')
    <div class="content-wrapper">
        <form method="post" action="/question">
            @csrf

            <div class="row uniform 50%">
                <div class="12u 12u$(4)">
                    <input type="text" name="label" id="label" value="" placeholder="What question do you want to ask?" />
                </div>

                <div class="6u 12u$(2)">
                    <div class="select-wrapper">
                        <select name="required" id="required">
                            <option value="1">Required</option>
                            <option value="0">Not Required</option>
                        </select>
                    </div>
                </div>

                <div class="6u 12u$(2)">
                    <div class="select-wrapper">
                        <select name="enabled" id="enabled">
                            <option value="1">Enabled</option>
                            <option value="0">Disabled</option>
                        </select>
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
    </div>
@endsection
