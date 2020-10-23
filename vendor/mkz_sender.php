<?php

/*
MIT License

Copyright (c) Markeaze Inc. https://markeaze.com

This file is part of the markeaze-php-tracker library created by Markeaze, adapted for Wordpress requirements.
Original file: https://github.com/markeaze/markeaze-php-tracker/blob/master/mkz_sender.php

Repository: https://github.com/markeaze/markeaze-php-tracker
Documentation: https://github.com/markeaze/markeaze-php-tracker/blob/master/README.md
*/

class MkzSender {

  public function __construct($url) {
    $this->url = (string) $url;
  }

  public function send($data) {
    $opts = array(
      'method' => 'POST',
      'httpversion' => '1.0',
      'blocking' => true,
      'headers' => Array(
        'Content-Type' => 'application/x-www-form-urlencoded; charset=utf-8',
        'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:20.0) Gecko/20100101 Firefox/20.0'
      ),
      'body' => $data
    );
    return wp_remote_post($this->url, $opts)['body'];
  }

}
