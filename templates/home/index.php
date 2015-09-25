<!DOCTYPE html>
<html lang="en">
<head>
    <title>Verifica Indaba 2015</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    

    <!-- <link rel='shortcut icon' type='image/x-icon' href='favicon.ico' /> -->
    <link rel="apple-touch-icon" sizes="57x57" href="/icons/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/icons/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/icons/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/icons/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/icons/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/icons/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/icons/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/icons/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/icons/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="/icons/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/icons/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/icons/favicon-16x16.png">
    <link rel="manifest" href="/icons/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/icons/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">


    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="/lib/base.css">
    <link rel="stylesheet" href="/ooo-vis.css">

    <script src="/config.js"></script>

    <script type="text/javascript">

        if ( typeof TEXT_PREVIEW_LENGTH === 'undefined' ) {
            alert("Attenzione: crea il file config.js a partire "+
            "dal config.js.sample prima di poter utilizzare " +
            "ooo-visualizer");
        }

        var MAX_ID = 0;

        var MIN_ID = 0;

        function set_text (uobj, new_el) {
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
                    + " - Fonte " + uobj.sourceLabel
                }));
                new_el.append(quote);
            }
        }

        function create_new_update( uobj ) {

            var new_el = $('<li>', {
                'class' : "list-group-item"
            });

            new_el.hide();

            if(MAX_ID < uobj.id){
                MAX_ID = uobj.id;
            }

            if(MIN_ID > uobj.id) {
                MIN_ID = uobj.id;
            }

            set_text(uobj, new_el);

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
            var hashtagsContainer = $("<p style=\"word-break: break-all\" class=\"hashtag-container\">")
            for(h = 0; h < uobj.hashtags.length; h++){
                hashtagsContainer.append($('<span>', {
                    'class' : "label label-primary",
                    'text' : uobj.hashtags[h]
                }));
                hashtagsContainer.append(" ");
            }
            new_el.append(hashtagsContainer)

            /* Inserisci l'immagine */
            for(h = 0; h < uobj.attachments.length; h++){
                new_el.append($("<img>", {
                    "class" : "img-thumbnail img-responsive img-attached",
                    "src" : API_RES_IMGS + uobj.attachments[h].id + "/" + screen.width
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

                // la risorsa è da configurare nel file config.js
                var res = API_RES_PRE + last_id;

            } else {
                /* Carica i nuovi messaggi in cima alla pagina */

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

                    var v = data;
                    var i;

                    if (where == 'pre'){
                        for( i = 0; i < v.length; i++){
                            var el = create_new_update(v[i]);
                            if (v[i].id <= MIN_ID ){
                                $('#verifies').append(el);
                                el.fadeIn({
                                    queue : true,
                                    duration : "slow"
                                });
                            }
                        }
                    } else {
                        for( i = v.length -1; i >= 0 ; i--){
                            var el = create_new_update(v[i]);

                            if (v[i].id >= MAX_ID){
                                $('#verifies').prepend(el);
                                el.fadeIn({
                                    queue : true,
                                    duration : "slow"
                                });   
                            }
                        }
                    }

                    $("#stub-info-element").remove();
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
    <nav class="navbar navbar-default navbar-fixed-top affix">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="#">
                    <!-- <img src="/logo.jpg" height="100%" align="left"/>&nbsp;-->
                    <strong>AGESCI Indaba 2015</strong>
                </a>
            </div>
            <div>
                <ul class="nav navbar-nav">
                    <!-- <li class="active"><a href="#">Home</a></li> -->
                    <li><a href="#" data-toggle="modal" data-target="#modal-howto">Come pubblicare</a></li>
                    <li><a href="annotation/new" >Pubblica opinione</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Trigger the modal with a button -->



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

<!-- Modal -->
<div id="modal-howto" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">Istruzioni per inviare le verifiche</h3>
            </div>
            <div class="modal-body">
                <h4>Questo strumento raccoglie e accorpora le verifiche da fonti differenti per
                    rendere più semplice a tutti inviare il proprio contributo.
                    Per condividere il tuo pensiero puoi:</h4>
                <ul>
                    <li><h4>inviare un <b>SMS</b> al numero <a id="phone-number" href=""></a>,</h4></li>
                    <li><h4>pubblicare un tweet su <b>Twitter</b> inserendo la parola <a id="twitter-tag" href="">#indaba</a>,</h4></li>
                    <li><h4>inviare un<b>Mail</b> : <a id="email" href="mailto:"></a>,</h4></li>
                    <li><h4>inviare un messaggio con <b>Telegram</b> a <a id="telegram" href="">Indaba Bot</a>,</h4></li>
                    <li><h4><b>Chiosco</b> : puoi lasciare un messaggio al chiosco <span id="chiosco"></span>.</h4></li>
                </ul>
                <script type="text/javascript">
                    $("#twitter-tag").attr('href', "http://twitter.com/hashtag/" + TWITTER_HT);
                    $("#twitter-tag").text(TWITTER_HT);
                    
                    $("#phone-number").text(PHONE_NUMBER);
                    $("#phone-number").attr("href","tel:" + PHONE_NUMBER);

                    $("#email").text(MAIL_ADDRESS);
                    $("#email").attr("href","mailto:" + MAIL_ADDRESS);

                    $("#telegram").text(TELEGRAM_BOT);
                    $("#telegram").attr("href","https://telegram.me/" + TELEGRAM_BOT + "?start=web");

                    $("#chiosco").text(POSTO_CHIOSCO);
                </script>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Ok, grazie</button>
            </div>
        </div>

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
