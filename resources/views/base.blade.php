<!DOCTYPE html>
<html lang="de">
    <head>
        <base href="{{ route('developers.index') }}">

        <meta charset="utf-8" />

        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        <meta name="viewport" content="width=device-width" />

        <meta property="og:locale" content="de_DE">
        <meta property="og:type" content="website">
        <meta property="og:title" content="OParl - Initiative für Offenheit parlamentarischer Informationssysteme">
        <meta property="og:description" content="Initiative für Offenheit parlamentarischer Informationssysteme">
        <meta property="og:site_name" content="OParl">
        <meta property="og:url" content="{{ url('/') }}">

        <title>
            @if (isset($title))
                {{ $title.' - ' }}OParl.org
            @else
                @yield('title')
            @endif
        </title>

        <link href="{{ asset('/css/app.css') }}" rel="stylesheet" />
        <link href="{{ asset('/css/lib.css') }}" rel="stylesheet" />

        @yield('styles')

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

        <link rel="icon" href="{{ asset('/img/favicon.png') }}" />
        <link rel="shortcut icon" href="{{ asset('/img/favicon.png') }}" />
    </head>
    <body>
        <div class="sr-only">
            <ul>
                <li><a href="#content" class="sr-only-focusable">Direkt zum Inhalt</a></li>
                <!-- TODO: insert links to main chapters -->
            </ul>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    @include ('header')

                    <div class="row">
                        <div class="col-md-12">
                            <a name="content">&nbsp;</a>
                            @yield('content')
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="{{ asset('/js/lib.js') }}"></script>

        @yield ('scripts')
    </body>
</html>
