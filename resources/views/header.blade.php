<!-- Header -->
<header id="header">
    <h1><a href="/">AC Journal</a></h1>
    <nav id="nav">
        <ul>
            <li><a href="{{ route('question.index') }}">Questions</a></li>
            <li><a href="{{ route('entry.index') }}">Entries</a></li>
            <li><a href="{{ route('entry.report') }}">Reports</a></li>
        </ul>
    </nav>
</header>

<!-- Banner -->
<section id="banner">
    @yield('page_title')

    <ul class="actions">
        @yield('action')
    </ul>
</section>
