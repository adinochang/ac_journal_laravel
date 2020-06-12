<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AC Journal</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <!--[if lte IE 8]><script src="/js/html5shiv.js"></script><![endif]-->
    <script src="/js/jquery.min.js"></script>
    <script src="/js/skel.min.js"></script>
    <script src="/js/skel-layers.min.js"></script>
    <script src="/js/init.js"></script>

    <noscript>
        <link rel="stylesheet" href="/css/skel.css" />
        <link rel="stylesheet" href="/css/style.css" />
        <link rel="stylesheet" href="/css/style-xlarge.css" />
    </noscript>
</head>
<body class="landing">
    @include('header')

    @yield('content')

    <!-- Footer -->
    <footer id="footer">
        <div class="container">
            <section class="links">
            </section>
            <div class="row">
                <div class="8u 12u$(medium)">
                    <ul class="copyright">
                        <li>Design: <a href="http://templated.co">TEMPLATED</a></li>
                        <li>Images: <a href="http://unsplash.com">Unsplash</a></li>
                    </ul>
                </div>
                <div class="4u$ 12u$(medium)">
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
