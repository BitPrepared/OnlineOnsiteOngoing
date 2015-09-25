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
    <script src="/config.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="/lib/base.css">
    <link rel="stylesheet" href="/ooo-vis.css">

</head>
<body>
<div id="content">
    <nav class="navbar navbar-default navbar-fixed-top affix">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="/">
                    <!-- <img src="/logo.jpg" height="100%" align="left"/>&nbsp;-->
                    <strong>AGESCI Indaba 2015</strong>
                </a>
            </div>
            <div>
                <ul class="nav navbar-nav">
                    <!-- <li><a href="index.htm">Home</a></li> -->
                    <li><a href="#" data-toggle="modal" data-target="#modal-howto">Come pubblicare</a></li>
                    <!-- <li class="active"><a href="form.htm">Pubblica opinione</a></li> -->
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid" id="content-container">
        <ul id="verifies" class="list-group">
            <li class="list-group-item update" id="stub-info-element">
                <blockquote>
                    <h2>Invia la tua verifica!</h2>
                    <p>Puoi utilizzare questo form per inserire la tua verifica personale.</p>
                    <footer>BitPrepared</footer>
                </blockquote>
                <form action="/annotation/new" method="post" accept-charset="UTF-8" autocomplete="off">

                    <div class="form-group">
                        <label for="usr">Valutazione:</label>
                        <input name="valutazione" type="text" class="form-control" id="usr" placeholder="A00+++">
                    </div>
                    <div class="form-group">
                        <label for="comment">Commento:</label>
                        <textarea name="commento" class="form-control" rows="5" id="comment"></textarea>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-default">Invia la verifica</button>
                    </div>
                </form>
            </li>
        </ul>
    </div>
</div>
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
</body>
</html>
