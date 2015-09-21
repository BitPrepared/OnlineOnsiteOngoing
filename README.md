# OnlineOnsiteOngoing
Strumenti di verifica pre, durante e post evento


### Guida

{{{
cd public 
php -S 0.0.0.0:8080
}}}

cosi attivate il server web 


### Dependencies

php extension:
 * ext-imap


### History

* https://apps.twitter.com/ -> registrata nuova app

 
 
### PhpBrew install command 

phpbrew -d install 5.5.24 +mbstring +bcmath +imap +ctype +pdo +mysql +pcntl +posix +readline +json +intl +cgi +sqlite +openssl +zip +gd +ipc +bz2 +mcrypt +cli +dom +filter +pcre +inifile +fileinfo +mhash +zlib -- --with-curl=/usr/local/
phpbrew use php-5.5.24
phpbrew extension install imap -- --with-imap=/usr/local/opt/imap-uw/ --with-imap-ssl --with-kerberos
