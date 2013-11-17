<?php

  class Person_Model extends CI_Model {

    public function __construct() {
      parent::__construct();
      $this->load->database();
    }

    public function create($data) {
      $this->db->insert('persons', $data['person']);
      $data['status']['person_id'] = $this->db->insert_id();
      $this->db->insert('person_statuses', $data['status']);
    }

    public function create_if_nonexistent($data) {
      $this->db->like('name', $data['person']['name']);
      if ($this->db->count_all_results('persons') > 0) {
        $person = $this->db->get('persons', 1);
        $person = $person->row_array();
        $data['status']['person_id'] = $person['id'];
        $this->create_status($data['status']);
      } else {
        $this->create($data);
      }
    }

    public function create_status($status) {
      $this->db->insert('person_statuses', $status);
    }

  }

?>