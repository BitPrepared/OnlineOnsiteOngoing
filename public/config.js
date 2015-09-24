// Lunghezza della parte di testo mostrata
// subito nell'aggiornamento (i.e. tweet).
// Il resto viene troncato e mostrato solo su click dell'utente
var TEXT_PREVIEW_LENGTH = 200;

// Intervallo per il caricamento automatico
// di nuovi contenuti
// (in millisecondi)
var MS_RELOAD_DELAY = 20000;

// L'API endpoint per ricevere i nuovi
// aggiornamenti (l'ID verrà messo se necessario
// direttamente a programma)
var API_RES_POST = '/feed/';

// L'API endpoint per i vecchi aggiornamenti (facendo lo scroll verso il basso).
// (l'ID verrà messo se necessario direttamente
// a programma)
var API_RES_PRE = '/history/';