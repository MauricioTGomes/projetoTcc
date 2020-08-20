<!DOCTYPE html>
<!--[if IE 9]>
<html class="no-js lt-ie10"> <![endif]-->
<!--[if gt IE 9]><!-->
<html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <title>Footer</title>
    <meta name="description" content="SISCOM">
    <meta name="author" content="RBR Informática">
    <meta name="robots" content="noindex, nofollow">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1.0">
    <link rel="stylesheet" href="{{public_path('css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{public_path('css/impressao_pdf.css')}}">
    <script>
        function subst() {
            var vars = {};
            var x = document.location.search.substring(1).split('&');
            for (var i in x) {
                var z = x[i].split('=', 2);
                vars[z[0]] = decodeURI(z[1]);
            }
            var x = ['frompage', 'topage', 'page', 'webpage', 'section', 'subsection', 'subsubsection'];
            for (var i in x) {
                var y = document.getElementsByClassName(x[i]);
                for (var j = 0; j < y.length; ++j) y[j].textContent = vars[x[i]];
            }
        }
    </script>
</head>

<body onload="subst()">

<div class="row">

    <hr>

    <footer style="font-size: 12px; font-weight: bold;">
        <div class="col-sm-12 text-center">
            <a class="text-danger">www.ERPWEB.com</a>
            <div class="pull-right">
                Página <span class="page"></span> de <span class="topage"></span>
            </div>
        </div>
    </footer>
</div>
</body>
</html>