@extends('layout')



@section('pagejs')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
@endsection



@section('pagecss')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endsection



@section('page_title')
    <h2>All Entries</h2>
@endsection



@section('content')
    <div class="content-wrapper">
        @if (session('message'))
            <div class="success-message">{{ session('message') }}</div>
        @endif

        <p>
            <a href="{{ route('entry.create') }}" class="button alt icon fa-plus small" style="margin-right: 1em;">New Entry</a>
        </p>

        <form method="GET" action="{{ route('entry.index') }}">
            <div class="filter-div">
                Last Updated Date Filter: <input type="text" name="filter_date" id="filter_date" value="{{ request('filter_date') }}" class="filter-date-input" />
            </div>
            @csrf
        </form>

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
                        <td><a href="{{ route('entry.edit', $entry) }}" class="button alt icon fa-pencil small" style="margin-right: 1em;">Edit</a> {{ $entry->answer_excerpt(50) }}</td>
                        <td>{{ date('Y-m-d H:i:s',strtotime($entry->updated_at)) }}</td>
                    </tr>
                @endforeach

                @if (sizeof($entries) == 0)
                    <tr>
                        <td colspan="2">No records found</td>
                    </tr>
                @endif
                </tbody>
                <tfoot>
                <tr>
                </tr>
                </tfoot>
            </table>

            {{ $entries->links() }}
        </div>
    </div>

    <script>
        $( function() {
            $( "#filter_date" ).datepicker({dateFormat: 'yy-mm-dd'});
        } );
    </script>
@endsection
