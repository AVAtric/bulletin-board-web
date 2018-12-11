<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Bulletin board</title>

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>

        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    </head>

    <body>
        <section class="hero is-small is-light is-bold">
            <div class="hero-body">
                <div class="container">
                    <h1 class="title">
                        Verteilte Computersysteme - TCP/IP
                    </h1>
                    <h2 class="subtitle">
                        Bulletin Board
                    </h2>
                </div>
            </div>
        </section>

        <section class="section" id="app">
            <message-list></message-list>
        </section>
    </body>
</html>
