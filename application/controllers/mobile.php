<?php

  class Mobile extends CI_Controller {

    public function __construct() {
      parent::__construct();

      $this->REQUEST_METHODS = array();

      $this->_extract_route();
      $this->_check_request_method();
    }

    public function upload() {
      
    }

    private function _extract_route() {
      $controller = $this->uri->segment(1);
      $action = $this->uri->segment(2);
      $this->route['controller'] = ($controller) ? $controller : 'mobile';
      $this->route['action'] = ($action) ? $action : 'upload';
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