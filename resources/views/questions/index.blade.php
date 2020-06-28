@extends('layout')



@section('page_title')
    <h2>Setup Questions</h2>
@endsection



@section('content')
    <div class="content-wrapper">
        @if (session('message'))
        <div class="success-message">{{ session('message') }}</div>
        @endif

        <p>
            <a href="{{ route('question.create') }}" class="button alt icon fa-plus small" style="margin-right: 1em;">New Question</a>
        </p>

        <form method="GET" action="{{ route('question.index') }}">
            <div class="filter-div">
                <input type="text" name="filter_label" id="filter_label" value="{{ request('filter_label') }}" placeholder="Search labels" class="filter-input" />
            </div>
            @csrf
        </form>

        <div class="table-wrapper">
            <table class="alt">
                <thead>
                <tr>
                    <th>Label</th>
                    <th>Required</th>
                    <th>Enabled</th>
                    <th>Last Updated</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($questions as $question)
                    <tr>
                        <td><a href="{{ route('question.edit', $question) }}" class="button alt icon fa-pencil small" style="margin-right: 1em;">Edit</a>{{ $question->label }}</td>
                        <td>{{ $question->required ? 'Required' : 'Optional' }}</td>
                        <td>{{ $question->enabled ? 'Enabled' : 'Disabled' }}</td>
                        <td>{{ date('Y-m-d H:i:s',strtotime($question->updated_at)) }}</td>
                    </tr>
                @endforeach

                @if (sizeof($questions) == 0)
                    <tr>
                        <td colspan="4">No records found</td>
                    </tr>
                @endif

                </tbody>
                <tfoot>
                <tr>
                </tr>
                </tfoot>
            </table>

            {{ $questions->links() }}
        </div>
    </div>
@endsection

