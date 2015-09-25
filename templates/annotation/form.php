<html>
    <head>
    	<title>Indaba Verifica Web</title>
    	<style>

    		input[type=text], input[type=url], input[type=email], input[type=password], input[type=tel] {
			  -webkit-appearance: none; -moz-appearance: none;
			  display: block;
			  margin: 0;
			  width: 100%; height: 40px;
			  line-height: 40px; font-size: 17px;
			  border: 1px solid #bbb;
			}

			textarea {
				width: 100%;
			}

			button[type=submit] {
			 -webkit-appearance: none; -moz-appearance: none;
			 display: block;
			 margin: 1.5em 0;
			 font-size: 1em; line-height: 2.5em;
			 color: #333;
			 font-weight: bold;
			 height: 2.5em; width: 100%;
			 background: #fdfdfd; background: -moz-linear-gradient(top, #fdfdfd 0%, #bebebe 100%); background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#fdfdfd), color-stop(100%,#bebebe)); background: -webkit-linear-gradient(top, #fdfdfd 0%,#bebebe 100%); background: -o-linear-gradient(top, #fdfdfd 0%,#bebebe 100%); background: -ms-linear-gradient(top, #fdfdfd 0%,#bebebe 100%); background: linear-gradient(to bottom, #fdfdfd 0%,#bebebe 100%);
			 border: 1px solid #bbb;
			 -webkit-border-radius: 10px; -moz-border-radius: 10px; border-radius: 10px;
			}

    	</style>
    </head>
    <body>

    <form action="/annotation/new" method="post" accept-charset="UTF-8" autocomplete="off">
<!--    	<label for="sezione">Sezione:</label> -->
<!--    	<select name="sezione">-->
<!--		  <option value="A">A</option>-->
<!--		  <option value="B">B</option>-->
<!--		</select>-->
<!--		<br/>-->
<!--		<label for="evento">Evento</label>-->
<!--    	<select name="evento">-->
<!--		  <option value="1">1</option>-->
<!--		  <option value="2">2</option>-->
<!--		</select>-->
<!--		<br/>-->
		<label for="valutazione">Valutazione</label>
    	<input type="text" name="valutazione" autocorrect="off" autocapitalize="off" placeholder="A00+++" /> 
    	<br/>
		<label for="commento">Commento</label>
		<br/>
    	<textarea name="commento" rows="8"></textarea>
        <br/>
        <input type="submit" value="Invia"/>
    </form>

    </body>
</html>