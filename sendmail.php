<?php

  /** 
  ** Author: Eduard Haag
  ** Project: klib framework
  ** Title: Sendmail wrapper above sendEmail
  **/


namespace klib {

  class SendMail {

    private $from = ''; // Отправить
    private $to = ''; // Получатель
    private $cc = ''; // Копия
    private $bcc = ''; // Скрытая копия
    private $host = ''; // Сервер отправки
    private $login = ''; // Логин
    private $pass = ''; // Пароль
    private $subj = ''; // Тема письма
    private $reply = ''; // Адрес для ответа
    private $msg = ''; // Текст сообщения    
    private $files = array(); // Файлы для вложения
    private $log_file = ''; // Лог
    private $log_file2 = ''; // Вспомогательный лог
    private $flag = ''; // Флаг
    private $rdn = ''; // Уведомление о прочтении
    private $dsn = ''; // Уведомление о доставке
    private $charset = 'utf-8'; // Кодировка
    private $cmd = '';
    private $res = '';
    private $ssl = false; // использовать ли SSL
    private $ssl_opt = '-o tls=no'; // SSL шифрование
    private $sendEmailPath = '/usr/lib/sendEmail.pl';

    // Конструктор
    function __construct($host, $login, $pass) {
      $this->host = $host;
      $this->login = $login;
      $this->pass = $pass;
    }

    // Задать путь sendEmail.pl
    public function setSendEmailPath($path) {
      $this->sendEmailPath = $path;
    }

    // Добавить файл
    public function addFile($file) {
      $this->files[] = $file;
    }

    public function setFrom($from) {
      $this->from = $from;      
    }

    public function setTo($to) {
      $this->to = $to;
    }

    public function setCC($cc) {
      $this->cc = $cc;
    }

    public function setBCC($bcc) {
      $this->bcc = $bcc;
    }

    public function setSubject($subj) {
      $this->subj = str_replace('"', '\"', $subj);
    }

    public function setMessage($msg) {
      $this->msg = str_replace('"', '\"', $msg);
    }

    public function setLogFile($log_file) {
      $this->log_file = $log_file;
    }

    public function setLogFile2($log_file2) {
      $this->log_file2 = $log_file2;
    }

    public function setReply($reply) {
      $this->reply = $reply;
    }

    public function setDSN($dsn) {
      $this->dsn = $dsn;
    }

    public function setRDN($rdn) {
      $this->rdn = $rdn;
    }

    public function setFlag($flag) {
      $this->flag = $flag;
    }  

    public function setCharset($charset) {
      $this->charset = $charset;
    }

    public function setSSL($value) {
      if ($value == true) {
        $this->ssl = '-o tls=yes';
      } else {
        $this->ssl = '-o tls=no';
      }
    }

    // Отправка 
    public function send() {

      $res = '';

      if (($this->from != '') &&
        ($this->to != '') &&
        ($this->login != '') &&
        ($this->pass != '') ) {

        $cmd =
          $this->sendEmailPath .
          ' -f ' . $this->from .
          ' -t ' . $this->to;

        if ($this->cc) {
          $cmd .=
            ' -cc ' . $this->cc;
        }

        if ($this->bcc) {
          $cmd .=
            ' -bcc ' . $this->bcc;
        }

        $cmd .=          
          ' -s ' . $this->host .
          ' -xu ' . $this->login .
          ' -xp ' . $this->pass .          
          ' -o message-charset=' . $this->charset .' ';

        $cmd .= $this->ssl . ' ';

        if ($this->rdn) {
          $cmd .=
            ' -o message-header="Disposition-Notification-To: ' . $this->from . '"' .
            ' -o message-header="Return-Receipt-To: ' . $this->from . '"' .
            ' -o message-header="Return-Path: ' . $this->from . '"' .
            ' -o message-header="X-Return-Path: ' . $this->from . '"' .
            ' -o message-header="X-Confirm-Reading-To: ' . $this->from . '"';
        }

        if ($this->dsn) {          
          $cmd .= ' -dsn';
        }

        if ($this->flag) {
          $cmd .= ' -o message-header="X-Custom-Flag: ' . $this->flag . '"';
        }

        if ($this->reply) {
          $cmd .= ' -o reply-to=' . $this->reply;
        }        

        $cmd .=
          ' -u "' . $this->subj . '"'.
          ' -m "' . $this->msg . '"';        
        
        if (count($this->files) > 0) {
          $cmd .= ' -a';
          foreach($this->files as $file) {
            $cmd .= ' "' . $file . '"';
          }
        }

        if ($this->log_file) {
          $cmd .= ' -l "' . $this->log_file . '"';
        };

        if ($this->log_file2) {
          write_log($this->log_file2, $cmd);
          write_log($this->log_file2, 'Файлов во вложении: ' . count($this->files));
        }

        $this->cmd = $cmd;        

        $res = shell_exec($cmd);
        $this->res = $res;

      }
      
      return (substr_count($res, 'successfully!') > 0);

    }

  }

}
?>
