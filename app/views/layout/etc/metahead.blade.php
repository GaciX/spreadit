@yield('title')
@yield('description')
<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
    <script src="/assets/js/html5shiv.js"></script>
<![endif]-->
<meta name=viewport content="width=device-width, initial-scale=1">
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta name="copyright" content="Go ahead, copyme." />
<meta name="twitter:site" content="@spreadit" />
<meta name="twitter:domain" content="spreadit.io" />
<meta name="twitter:creator" content="@spreadit" />
<meta property="og:site_name" content="spreadit.io" />
<meta property="og:image" content="http://spreadit.dev/assets/images/spreadit-butter.jpg">
<link rel="icon" type="image/png" href="/favicon.ico?v=1
">
<link rel="stylesheet" href="{{ Bust::url('/assets/prod/build.min.css') }}">
{{ $customCss }}
<noscript>
<style>img.lazy-loaded { display:none; }</style>
</noscript>
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-42844259-2', 'auto');
ga('set', '&uid', {{ (Auth::check() ? Auth::user()->id : 0) }});
ga('require', 'linkid', 'linkid.js');
ga('require', 'displayfeatures');
ga('send', 'pageview');

</script>
@yield('style')
<!-- {{ RiddleController::render() }} -->
