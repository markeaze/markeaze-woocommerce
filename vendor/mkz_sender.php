<?php

/*
MIT License

Copyright (c) Markeaze Inc. https://markeaze.com
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
