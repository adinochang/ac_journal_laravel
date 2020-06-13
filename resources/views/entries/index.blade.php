@extends('layout')



@section('page_title')
    <h2>All Entries</h2>
@endsection



@section('content')
    <div class="content-wrapper">
        <p>
            <a href="/entry/create/" class="button alt icon fa-plus small" style="margin-right: 1em;">New Entry</a>
        </p>

        <div class="table-wrapper">
            <table class="alt">
                <thead>
                <tr>
                    <th>Summary</th>
                    <th>Last Updated</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($entries as $entry)
                    <tr>
                        <td><a href="/entry/edit/{{ $entry->id }}" class="button alt icon fa-pencil small" style="margin-right: 1em;">Edit</a> {{ $entry->answer_excerpt(50) }}</td>
                        <td>{{ date('Y-m-d H:i:s',strtotime($entry->updated_at)) }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection
