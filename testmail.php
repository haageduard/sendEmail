<?php

  require_once('sendmail.php');

  $sendmail = new klib\SendMail('smtp_host', 'user', 'password');
  $sendmail->setSendEmailPath('./sendEmail.pl');
  $sendmail->setFrom('sender@domain');
  $sendmail->setTo('address@domain');
  $sendmail->setSubject('subject');
  $sendmail->setMessage('question');
  $sendmail->setSSL(true);
  $sendmail->setLogFile('logfile.txt');	
  $sendmail->send();

?>
