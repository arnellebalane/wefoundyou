<?php

  class Maps extends CI_Controller {

    public function __construct() {
      parent::__construct();
      $this->load->model('person_model', 'person');

      $this->REQUEST_METHODS = array(
        'index' => 'GET',
        'search' => 'GET'
      );

      $this->_extract_route();
      $this->_check_request_method();
    }

    public function index() {
      $this->load->view('maps/index');
    }

    public function search($query = '') {
      $people = $this->person->search(urldecode($query));
      for ($i = 0; $i < count($people); $i++) {
        $people[$i]['statuses'] = $this->person->retrieve_statuses($people[$i]['id']);
      }
      echo json_encode($people);
    }

    private function _extract_route() {
      $controller = $this->uri->segment(1);
      $action = $this->uri->segment(2);
      $this->route['controller'] = ($controller) ? $controller : 'maps';
      $this->route['action'] = ($action) ? $action : 'index';
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