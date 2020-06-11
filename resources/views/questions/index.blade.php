@extends('layout')

@section('content')
    @foreach ($questions as $question)
        <p>{{ $question }}</p>
    @endforeach
@endsection
