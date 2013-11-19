<?php

  class Maps extends CI_Controller {

    public function __construct() {
      parent::__construct();
      $this->load->model('person_model', 'person');
    }

    public function index() {
      $this->load->view('maps/index');
    }

    public function search($query) {
      $people = $this->person->search(urldecode($query));
      for ($i = 0; $i < count($people); $i++) {
        $people[$i]['statuses'] = $this->person->retrieve_statuses($people[$i]['id']);
      }
      echo json_encode($people);
    }

  }

?>