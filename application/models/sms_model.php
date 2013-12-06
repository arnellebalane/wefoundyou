<?php

  class Sms_Model extends CI_Model {

    public function __construct() {
      parent::__construct();
      $this->load->database();
    }

    public function create($data) {
      $this->db->insert('sms', $data);
    }

    public function send($recepient, $content) {
      // echo '<pre>';
      // print_r($recepient);
      // echo '<br/>';
      // print_r($content);
      // echo '</pre>';

      $this->load->library('xhttp');

      $data['post'] = array(
        'uName' => 'u1vhn7dkm',
        'uPin' => '21737086',
        'MSISDN' => $recepient,
        'messageString' => $content,
        'Display' => '1',
        'udh' => '',
        'mwi' => '',
        'coding' => '0'
      );
      xhttp::fetch('http://iplaypen.globelabs.com.ph:1881/axis2/services/Platform/sendSMS', $data);
    }

  }

?>