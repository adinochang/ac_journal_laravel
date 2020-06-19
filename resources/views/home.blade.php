@extends('layout')

@section('page_title')
    <h2>Hi. How was your day?</h2>
@endsection

@section('action')
    <li>
        <a href="{{ route('entry.create') }}" class="button big">New Entry</a>
    </li>
@endsection

@section('content')
    @foreach($entries as $entry)
    <section id="main_{{ $entry->id }}" class="wrapper">
        <div class="container">
            <header class="major">
                <h2>{{ date('F d Y H:i',strtotime($entry->updated_at)) }}</h2>
                <p>Recorded by AC</p>
            </header>

            @foreach($entry->answers as $answer)
                <h4>{{ $answer->question->label }}</h4>

                <p>{{ $answer->answer_text }}</p>
            @endforeach
        </div>
    </section>
    @endforeach

    <section id="pagination-section" class="wrapper" style="padding-left: 11em;">
        {{ $entries->links() }}
    </section>
@endsection
