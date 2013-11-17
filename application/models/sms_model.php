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
      echo '<pre>';
      print_r($recepient);
      echo '<br/>';
      print_r($content);
      echo '</pre>';
    }

  }

?>