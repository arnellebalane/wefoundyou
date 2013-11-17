<?php

  class Sms extends CI_Controller {

    public function __construct() {
      parent::__construct();
      $this->load->model('sms_model', 'sms');
      $this->load->model('person_model', 'person');

      $this->KEYWORDS = array('ADD', 'FIND');
      $this->RESPONSES = array(
        'INVALID_KEYWORD' => 'The keyword that you used is invalid. To report a person, send ADD name(status) to 23737910. To search for a person, send FIND name to 23737910.',
        'ADD' => array(
          'SUCCESS' => 'The people you reported have been to our database. Thank you for the information.',
          'FAILED' => 'Something went wrong while saving your report. Please try again.'
        ),
        'FIND' => array(
          'EMPTY_RESULTS' => 'No reports found regarding the person that you are looking for. You will be notified once information about that person gets reported.'
        )
      );
    }

    public function receive() {
      $sms = $this->_parse_sms(base_url() . 'assets/sms.xml');
      $this->sms->create($sms);

      $message = $this->_parse_message($sms['content']);
      if (!$message['keyword']) {
        $this->sms->send($sms['source'], $this->RESPONSES['INVALID_KEYWORD']);
      } else if ($message['keyword'] == 'ADD') {
        $this->_report_people($message['content'], $sms);
      } else if ($message['keyword'] == 'FIND') {
        $this->_search_people($message['content']);
      }
    }

    private function _parse_sms($source) {
      $sms = simplexml_load_file(base_url() . 'assets/sms.xml');
      $nodes = $sms->xpath('/message/param');
      $sms = array();
      foreach ($nodes as $node) {
        $node = (array) $node;
        $sms[$node['name']] = $node['value'];
      }
      $message['message_id'] = $sms['id'];
      $message['target'] = $sms['target'];
      $message['source'] = $sms['source'];
      $message['content'] = $sms['msg'];
      return $message;
    }

    private function _parse_message($message) {
      $exploded = explode(' ', $message);
      $message = array();
      if (in_array(trim($exploded[0]), $this->KEYWORDS)) {
        $message['keyword'] = trim($exploded[0]);
        unset($exploded[0]);
        $message['content'] = trim(implode(' ', $exploded));
      } else {
        $message['keyword'] = false;
        $message['content'] = implode(' ', $exploded);
      }
      return $message;
    }

    private function _report_people($message, $meta) {
      $people = explode(',', $message);
      foreach ($people as $person) {
        $person = explode('(', $person);
        $data['person']['name'] = trim($person[0]);
        $data['status'] = array(
          'status' => isset($person[1]) ? trim(substr($person[1], 0, -1)) : 'unknown',
          'reporter' => $meta['source']
        );
        $this->person->create_if_nonexistent($data);
      }
    }

    private function _search_people($message) {
      // 
    }

  }

?>