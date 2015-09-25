<?php

namespace Indaba\Dashboard\Evaluation;

class Parser {

	public static function parse($text) {

		$result = new \stdClass();
        $result->sessione = '';
        $result->evento = 0;
        $result->punteggio = 0;
        $result->lastCodeChar = 0;

        $text = preg_replace('/[[:^print:]]/', '', $text);

        $text = trim($text);

        if ( strlen($text) < 3 ) {
        	return false;
        }

        $firstChar = mb_substr($text, 0, 1, 'utf-8');
        $firstCharCode = ord($firstChar);
        if ( $firstCharCode > 64 && $firstCharCode < 91 || $firstCharCode > 96 && $firstCharCode < 123 ){
            $result->sessione = $firstChar;
            $code = mb_substr($text, 1, 2, 'utf-8');
            if ( is_numeric($code) ) {
                $result->evento = $code;
            } else {
                echo "analyze : $text -> invalid code : $code \n";
                $result->lastCodeChar = 1;
                return false;
            }
        } else {
            echo "analyze : $text -> invalid firstChar : $firstChar ($firstCharCode) \n";
            $result->lastCodeChar = 0;
            return false;
        }

        if ( strlen($text) == 6 ) {
            
            $evaluation = mb_substr($text,3, 3, 'utf-8');
            $strlen = strlen( $evaluation );
            for( $i = 0; $i <= $strlen; $i++ ) {
                $char = substr( $evaluation, $i, 1 );
                if ( $char == '+' ) {
                    $result->punteggio++;
                }else if ( $char == '-' ) {
                    $result->punteggio--;
                } else {
                	$result->lastCodeChar = 3 + $i;
                	return $result;
                }
            }

            $result->lastCodeChar = strlen($text);
            return $result;
        }

        //analisi carattere per carattere
        $strlen = strlen( $text );
        for( $i = 3; $i <= $strlen; $i++ ) {
            $char = substr( $text, $i, 1 );
            if ( $char == '+' ) {
                $result->punteggio++;
            } else if ( $char == '-' ) {
                $result->punteggio--;
            } else {
            	$result->lastCodeChar = $i;
            	return $result;
            }

        }

        $result->lastCodeChar = strlen($text);
        return $result;

	}


}

?>