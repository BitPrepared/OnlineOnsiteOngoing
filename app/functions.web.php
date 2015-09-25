<?php

use Indaba\Dashboard\Annotation as Annotation;
use Indaba\Dashboard\Evaluation as Evaluation;
use Indaba\Dashboard\Source as Source;
use Indaba\Dashboard\Evaluation\Parser as Parser;

function insertAnnotation($req,$connection_name) {

	 $author = $req->getIp();

    //$allPostVars = $req->post();
    //print_r($allPostVars);
    //Array ( [sezione] => A [evento] => 1 [valutazione] => A10++ [commento] => Prova )

    $commento = $req->post('commento');

     $annotation = new Annotation(array(
         'author' => $author,
         'source' => Source::WEB,
         'sourceId' => '',
         'text' => $commento,
         'textHtml' => $commento,
         'hashtags' => array()
     ));

     $annotation->setConnection($connection_name);
     $annotation->save();

    $sezione = $req->post('sezione');
    $evento = $req->post('evento');
    $valutazione = $req->post('valutazione');

    if ( null != $sezione && null != $evento && is_numeric($evento) ) {
        $eventoNum = intval($evento);
        if ($eventoNum < 0) $eventoNum = 0;
        if ($eventoNum < 10) {
            $valutazione = $sezione . '0'.$eventoNum . $valutazione;
        } else {
            $valutazione = $sezione . $eventoNum . $valutazione;
        }
    }

    $result = Parser::parse($valutazione);

     if ($result != false){
         $evaluation = new Evaluation(array(
             'annotation_id' => $annotation->id,
             'sessione' => $result->sessione,
             'evento' => $result->evento,
             'punteggio' => $result->punteggio
         ));
         $evaluation->setConnection($connection_name);
         $evaluation->save();
     }

}
