<?php
/**
 * Created by PhpStorm.
 * User: Stefano "Yoghi" Tamagnini
 * Date: 22/09/15 - 00:57
 *
 */

require '../vendor/autoload.php';
require '../config.php';

set_time_limit(0); //unlimited

$attachmentsDir = __DIR__;

//echo 'connection string: {' . $imap_server . ':' . $imap_port . '/imap/' . $imap_protocol . '/debug/novalidate-cert}Elaborated' . "\n";
//$mailbox = new PhpImap\Mailbox('{' . $imap_server . ':' . $imap_port . '/imap/' . $imap_protocol . '/debug/novalidate-cert}Elaborated', $imap_mail, $imap_password, $attachmentsDir);
//$mailbox->createMailbox();
//$mailbox->__destruct();

//echo 'connection string: {' . $imap_server . ':' . $imap_port . '/imap/' . $imap_protocol . '/debug/novalidate-cert}Elaborated' . "\n";
//$mailbox = new PhpImap\Mailbox('{' . $imap_server . ':' . $imap_port . '/imap/' . $imap_protocol . '/debug/novalidate-cert}Elaborated', $imap_mail, $imap_password, $attachmentsDir);
//$mailbox->deleteMailBox(); //creata IO
//$mailbox->__destruct();


echo 'connection string: {' . $imap_server . ':' . $imap_port . '/imap/' . $imap_protocol . '/debug/novalidate-cert}Inbox' . "\n";
$mailbox = new PhpImap\Mailbox('{' . $imap_server . ':' . $imap_port . '/imap/' . $imap_protocol . '/debug/novalidate-cert}Inbox', $imap_mail, $imap_password, $attachmentsDir);
$mails = array();

$mailsIds = $mailbox->searchMailBox('ALL');
if (!$mailsIds) {
    echo('Mailbox is empty'."\n");
} else {
    print_r($mailsIds);

    foreach ($mailsIds as $mailId) {
//        echo $mailId . "\n";
        $mail = $mailbox->getMail($mailId);
//        var_dump($mail);
//        var_dump($mail->getAttachments());

        $id = $mail->id;
        $titolo = $mail->subject;
        $from = $mail->fromAddress;

        $textPlain = $mail->textPlain;
        $textHtml = $mail->textHtml;

        $attachments = $mail->getAttachments();
        foreach ($attachments as $attachment) {
//            PhpImap\IncomingMailAttachment Object
//            (
//                            [id] => 670616196493581800
//                [name] => Screen Shot 2015-06-25 at 23.41.56.png
//                [filePath] => /Users/yoghi/Documents/Workspace/OnlineOnsiteOngoing/script/2_670616196493581800_Screen_Shot_20150625_at_23.41.56.png
//            )
            $filePath = $attachment->filePath;
            $fileName = $attachment->name;

        }

        $mailbox->markMailAsUnread($id);

//        $mailbox->moveMail($id,'Elaborated');

        $mailbox->moveMail($id, 'Inbox.Elaborated');
        $mailbox->moveMail($id, 'Inbox.Failure');


    }
}
foreach ($mailbox->getListingFolders() as $dirs) {
    echo $dirs."\n";
}


//echo 'connection string: {' . $imap_server . ':' . $imap_port . '/imap/' . $imap_protocol . '/debug/novalidate-cert}Inbox' . "\n";
//$mailbox = new PhpImap\Mailbox('{' . $imap_server . ':' . $imap_port . '/imap/' . $imap_protocol . '/debug/novalidate-cert}Inbox', $imap_mail, $imap_password, $attachmentsDir);
//print_r();


echo "END\n";