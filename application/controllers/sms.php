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
      $this->REQUEST_METHODS = array(
        'receive' => 'POST'
      );

      $this->_extract_route();
      $this->_check_request_method();
    }

    public function receive() {
      $sms = $this->_parse_sms('php://input');
      $this->sms->create($sms);

      $message = $this->_parse_message($sms['content']);
      if (!$message['keyword']) {
        $this->sms->send($sms['source'], $this->RESPONSES['INVALID_KEYWORD']);
      } else if ($message['keyword'] == 'ADD') {
        $this->_report_people($message['content'], $sms);
      } else if ($message['keyword'] == 'FIND') {
        $this->_search_people($message['content'], $sms);
      }
    }

    private function _parse_sms($source) {
      $sms = simplexml_load_file($source);
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
      $location_index = strrpos($message, '(');
      $location = substr($message, $location_index + 1, -1);
      $message = substr($message, 0, $location_index);
      $people = explode(',', $message);
      foreach ($people as $person) {
        $person = explode('(', $person);
        $data['person']['name'] = trim($person[0]);
        $data['status'] = array(
          'status' => isset($person[1]) ? substr(trim($person[1]), 0, -1) : 'unknown',
          'reporter' => $meta['source'],
          'location' => $location
        );
        $this->person->create_if_nonexistent($data);
      }
      $this->sms->send($meta['source'], $this->RESPONSES['ADD']['SUCCESS']);
    }

    private function _search_people($message, $meta) {
      $queries = explode(',', $message);
      $response = '';
      foreach ($queries as $query) {
        $people = $this->person->like(array('name' => trim($query)));
        foreach ($people as $person) {
          $statuses = $this->person->retrieve_statuses($person['id']);
          $response .= "Name: " . $person['name'] 
                    . "\nStatus: " . $statuses[0]['status'] 
                    . "\nReported On: " . date('M d, Y', strtotime($statuses[0]['created_at']))
                    . "\nReported By: " . $statuses[0]['reporter'] . "\n\n";
        }
      }
      if ($response) {
        $this->sms->send($meta['source'], trim($response));
      } else {
        $this->sms->send($meta['source'], $this->RESPONSES['FIND']['EMPTY_RESULTS']);
      }
    }

    private function _extract_route() {
      $controller = $this->uri->segment(1);
      $action = $this->uri->segment(2);
      $this->route['controller'] = ($controller) ? $controller : 'sms';
      $this->route['action'] = ($action) ? $action : 'receive';
      $this->load->vars($this->route);
    }

    private function _check_request_method() {
      if (!array_key_exists($this->route['action'], $this->REQUEST_METHODS) || $_SERVER['REQUEST_METHOD'] != $this->REQUEST_METHODS[$this->route['action']]) {
        show_404();
        exit();
      }
    }

  }

?>