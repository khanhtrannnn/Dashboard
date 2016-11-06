<?php

class new_password extends CI_Controller {

    public function index() {
        $this->load->view('view_login');
    }

    public function forget() {
        if (isset($_GET['info'])) {
            $data['info'] = $_GET['info'];
        }
        if (isset($_GET['error'])) {
            $data['error'] = $_GET['error'];
        }

        $this->load->view('login-forget', $data);
    }

}
