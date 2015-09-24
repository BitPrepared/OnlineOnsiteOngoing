<!DOCTYPE html>
<html lang="en">
<head>
    <title>Verifica Indaba 2015</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <script src="config.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="lib/base.css">

    <style>
        .affix {
            top: 0;
            width: 100%;
        }
        .affix + .container-fluid {
            padding-top: 70px;
        }

        .img-attached {
            width: 300px;
        }

        @font-face {
            font-family: Humanist;
            src: url('font/Humanist 521 Light BT.ttf');
        }

        @font-face {
            font-family: Humanist-Bold;
            src: url('font/Humanist 521 Bold Condensed BT.ttf');
        }

        #progress-load-contents{
            position: absolute;
            top: 80px;

            left:10%;
            right:10%;

            margin-left: auto;
            margin-right: auto;
            max-width: 600px;
        }

        body {
            font-family : Humanist;
        }

        li > p {
            font-size:35pt;
        }

        li {
            margin-bottom: 50px;
        }

        .hashtag-container {
            font-size: 18px;
        }

        .label {
            font-size: 10pt;
            margin: 5px;
        }

        nav {
            font-family: Humanist-Bold;
        }

    </style>

    <script type="text/javascript">

        if ( typeof TEXT_PREVIEW_LENGTH === 'undefined' ) {
            alert("Attenzione: crea il file config.js a partire "+
            "dal config.js.sample prima di poter utilizzare " +
            "ooo-visualizer");
        }

        var MAX_ID = 0;

        var MIN_ID = 0;

        function create_new_update( uobj ) {

            var new_el = $('<li>');
            new_el.addClass("list-group-item");

            new_el.hide();

            if(MAX_ID < uobj.id){
                MAX_ID = uobj.id;
            }

            if(MIN_ID > uobj.id) {
                MIN_ID = uobj.id;
            }

            if(uobj.text != ""){
                var quote = $('<blockquote>');

                var beforeQuote = uobj.text.slice(0,TEXT_PREVIEW_LENGTH);
                var beforeQuoteEl = $("<p>").text(beforeQuote);
                quote.append(beforeQuoteEl);

                if(uobj.text.length > TEXT_PREVIEW_LENGTH){

                    beforeQuoteEl.text(beforeQuoteEl.text() + "...");

                    var more = $("<div id=\"more-"+ uobj.id+"\">");

                    more.text("..." + uobj.text.slice(TEXT_PREVIEW_LENGTH + 1));

                    more.addClass("collapse");

                    quote.append(more);

                    quote.append($("<button>", {
                        type: "button",
                        "class" : "btn btn-link btn-xs",
                        "data-toggle": "collapse",
                        "data-target": "#more-" + uobj.id,
                        "text" : "Continua a leggere"

                    }));
                }

                var cd = new Date(uobj.created);

                quote.append($("<footer>", {
                    text :
                    cd.toLocaleTimeString() + " " + cd.toLocaleDateString()
                    + " - From " + uobj.sourceLabel
                }));
                new_el.append(quote);
            }

            /* ***  Per il momento l'HTML è disabilitato *** */

            /*if(v[i].textHtml != ""){
             var content = $('<div>',{
             html : v[i].textHtml
             });
             new_el.append(content);
             }*/

            /* Inseriamo gli hashtags */
            var new_ht;
            var h;
            var hashtagsContainer = $("<p class=\"hashtag-container\">")
            for(h = 0; h < uobj.hashtags.length; h++){
                new_ht = $('<span>');
                new_ht.addClass("label");
                new_ht.addClass("label-primary");
                new_ht.text(uobj.hashtags[h]);

                hashtagsContainer.append(new_ht);
            }
            new_el.append(hashtagsContainer)

            /* Inserisci l'immagine */
            for(h = 0; h < uobj.attachments.length; h++){
                new_el.append($("<img>", {
                    "class" : "img-thumbnail img-responsive img-attached",
                    "src" : uobj.attachments[h].fileName
                }));
            }

            /* Salviamogli il JSON, per ridondanza */
            new_el.original_json = uobj;
            return new_el;
        }

        function load_new_updates ( last_id, where ) {

            /* Decidere quale endpoint chiamare */

            if(where == 'pre'){
                /* Carica i vecchi messaggi dell'infinite scrolling */

                //var res = 'data.json';

                // la risorsa è da configurare nel file config.js
                var res = API_RES_PRE + last_id;

            } else {
                /* Carica i nuovi messaggi in cima alla pagina */

                //var res = 'data.json';
                // la risorsa è da configurare nel file config.js
                var res = API_RES_POST + last_id;
                if(last_id != 0){
                    // var res = res + last_id;
                }
            }

            $.ajax({
                url: res,
                type: "GET",
                dataType: "json",
                success: function(data,textStatus,jqXHR){

                    /* La barra di caricamento viene mostrata solo all'avvio*/
                    if(last_id == 0) {
                        $("#progress-load-contents").fadeOut("slow");
                    }

                    var v = data.verifies;
                    var i;
                    for( i = 0; i < v.length; i++){

                        var el = create_new_update(v[i]);

                        /* Inserisci l'update */
                        if(where == 'pre'){
                            $('#verifies').append(el);
                        } else {
                            $('#verifies').prepend(el);
                        }

                        $("#stub-info-element").remove();
                        el.fadeIn({
                            queue : true,
                            duration : "slow"
                        });
                    }
                }
            });
        }

        function show_success_message ( ) {
            $("#success-update-alert").fadeOut();
            $("#success-update-alert").remove();
            $("#content-container").prepend($('<div id="success-update-alert" class="alert alert-success alert-dismissible" style="display:none" role="alert">' +
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
            'Contenuti aggiornati <span class="glyphicon glyphicon-ok"/></div>'));
            $("#success-update-alert").fadeIn();
        }

        function show_error_message ( ) {
            $("#alert-update-alert").fadeOut();
            $("#alert-update-alert").remove();
            $("#content-container").prepend($('<div id="alert-update-alert" class="alert alert-danger alert-dismissible" style="display:none" role="alert">' +
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
            'Si è verificato un errore... <span class="glyphicon glyphicon-remove"/></div>'));
            $("#alert-update-alert").fadeIn();
        }


        $(document).ready(function() {
            // $("#progress-load-contents").fadeIn();
            // load_new_updates(0);
            $(window).scroll(function() {
                if($(window).scrollTop() + $(window).height() == $(document).height()) {
                    load_new_updates(0, 'pre');
                }
            });
            setInterval(function(){
                $("#progress-load-contents").fadeIn();
                load_new_updates(MAX_ID, "post");
                $("#progress-load-contents").fadeOut("slow");
            }, MS_RELOAD_DELAY)
        });

    </script>
</head>
<body>
<div id="ptr">
    <!-- Pull down arrow indicator -->
    <span class="genericon genericon-next"></span>

    <!-- CSS-based loading indicator -->
    <div class="loading">
        <span id="l1"></span>
        <span id="l2"></span>
        <span id="l3"></span>
    </div>
</div>
<div id="content">
    <nav class="navbar navbar-inverse navbar-fixed-top affix">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="#">
                    <img src="logo.png" height="100%" align="left"/><strong>AGESCI Indaba 2015</strong>
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid" id="content-container">
        <div class="progress progress-striped active" style="display:none" id="progress-load-contents">
            <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">Caricamento nuovi contenuti...
            </div>
        </div>
        <ul id="verifies" class="list-group">
            <li class="list-group-item update" id="stub-info-element">
                <blockquote>
                    <h2>Benvenuto sull'aggregatore delle verifiche!</h2>
                    <p>Le verifiche dei vari capi che hanno partecipato all'Indaba appariranno in questa sezione in automatico.</p>
                    <footer>BitPrepared</footer>
                </blockquote>
            </li>
        </ul>
    </div>
</div>
<script src="lib/hammer.2.0.4.js"></script>
<script src="lib/wptr.1.1.js"></script>

<script>
    window.onload = function() {
        WebPullToRefresh.init( {
            loadingFunction: exampleLoadingFunction
        } );
    };

    var exampleLoadingFunction = function() {
        return new Promise( function( resolve, reject ) {
            load_new_updates(MAX_ID);
            if (true) {
                show_success_message();
                resolve();
            } else {
                reject();
            }

        });
    };
</script>
</body>
</html>