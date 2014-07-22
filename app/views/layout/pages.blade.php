<!doctype html>
<html>
    <head>
        @include('metahead')
    </head>
    <body data-spy="scroll" data-target=".bs-docs-sidebar" class="user-layout">
        <div id="fullpage-container">
            <div class="navbar navbar-inverse">
                @include('user_actions_nav')
                @include ('sections_nav', ['sections' => $sections])
            </div>


            @yield('content')

            @include('footer_nav')
        </div>
        @include('commonscripts')
        @yield('script')        
    </body>
</html>
