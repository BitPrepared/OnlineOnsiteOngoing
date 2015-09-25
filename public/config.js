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

// L'API endpoint per ottenere le immagini allegate alle verfifiche.
// Viene utilizzata (nel file index.htm) così:
// API_RES_IMGS + id + "/" + maxWidth
var API_RES_IMGS = "/resources/";

// L'Hash tag da cui vengono prese le verifiche su Twitter
// Il contenuto di questa variabile vieme mostrato nelle istruzioni
// sulla pubblicazione via twitter (nel modale dentro index.html)
var TWITTER_TAG = "indaba";

// Lo stesso per il telefono e gli sms
var PHONE_NUMBER = 1 408 555 5555;