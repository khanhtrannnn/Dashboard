<?php

//defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

    public function index() {
        $this->login();
    }

    public function login() {
        $this->load->view('view_login');
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect('main/login');
    }

    public function members() {
        if ($this->session->userdata('is_logged_in')) {
            $this->load->view('view_members');
        } else {
            redirect('main/restricted');
        }
    }

    public function restricted() {
        $this->load->view('view_restricted');
    }

    public function login_validation() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|xss_clean|min_length[6]|max_length[50]|valid_email|callback_check_user_validation');
        $this->form_validation->set_rules('password', 'Password', 'required|trim|xss_clean|md5|min_length[6]|max_length[50]');

        if ($this->form_validation->run()) {
            $data = array(
                'email' => $this->input->post('email'),
                'is_logged_in' => 1
            );
            $this->session->set_userdata($data);
            redirect('main/members');
        } else {
            $this->load->view('view_login');
        }
    }

    public function check_user_validation() {
        $this->load->model('model_users');

        if ($this->model_users->this_user_can_log_in()) {
            return true;
        } else {
            $this->form_validation->set_message('check_user_validation', 'Incorrect username/password.');
            return false;
        }
    }

    public function signup() {
        $this->load->view('view_signup');
    }

    public function signup_validation() {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[users.email]');
        $this->form_validation->set_rules('password', 'Password', 'required|trim');
        $this->form_validation->set_rules('password_conf', 'Comfirm Password', 'required|trim|matches[password]');

        $this->form_validation->set_message('is_unique', 'The Email you entered already existed');
        if ($this->form_validation->run()) {
            $key = md5(uniqid());

            $config['useragent'] = 'Dashboard';
            $config['protocol'] = 'smtp';
            $config['smtp_host'] = 'ssl://smtp.googlemail.com';
            $config['smtp_user'] = 'ictlab.usth1@gmail.com';
            $config['smtp_pass'] = 'Midnight2';
            $config['smtp_port'] = 465;
            $config['wordwrap'] = TRUE;
            $config['wrapchars'] = 76;
            $config['mailtype'] = 'html';
            $config['charset'] = 'iso-8859-1';
            $config['validate'] = FALSE;
            $config['priority'] = 3;
            $config['newline'] = "\r\n";
            $config['crlf'] = "\r\n";

            $this->load->library('email', array('mailtype' => 'html'));
            $this->load->model('model_users');

            $this->email->initialize($config);

            $this->email->from('me@myweb.com', 'ICT Lab | Email Verification');
            $this->email->to($this->input->post('email'));
            $this->email->subject('Comfirm your account.');
            $message = "<p> Thanks for signing up with ICTLab!\n</p>";
            $message .= "<p>Please click <a href='" . base_url() . "main/register_user/$key'><strong>here</strong></a> to activate your account at ICTLab Website.</p>";
            $this->email->message($message);

            if ($this->model_users->add_temp_user($key)) {
                if ($this->email->send()) {
                    redirect('main/check_mail_to_verify');
                } else
                    echo "The email can't be sent";
            } else
                echo "Problem adding data to database.";
        }else {
            $this->load->view('view_signup');
        }
    }

    public function check_mail_to_verify() {
        $this->load->view('view_check_mail_to_verify');
    }

    public function register_user($key) {
        $this->load->model('model_users');

        if ($this->model_users->is_valid_key($key)) {
            if ($newemail = $this->model_users->add_user($key)) {
                $data = array(
                    'email' => $newemail,
                    'is_logged_in' => 1
                );
                $this->session->set_userdata($data);
                redirect('main/login');
            } else {
                echo "Failed to add user, please try again.";
            }
        } else {
            echo "Invalid key";
        }
    }

    public function forget() {
        if (isset($_GET['info'])) {
            $data['info'] = $_GET['info'];
        }
        if (isset($_GET['error'])) {
            $data['error'] = $_GET['error'];
        }

        $this->load->view('login-forget');
    }

    public function doforget() {
        $this->load->helper('url');
        $email = $_POST['email'];
        $q = $this->db->query("select * from users where email='" . $email . "'");
        if ($q->num_rows() > 0) {
            $r = $q->result();
            $user = $r[0];
            $this->resetpassword($user);
            echo "Success";
        } else {
            echo "Fail";
        }
        
    }

    private function resetpassword($user) {
        $config['useragent'] = 'Dashboard';
        $config['protocol'] = 'smtp';
        $config['smtp_host'] = 'ssl://smtp.googlemail.com';
        $config['smtp_user'] = 'ictlab.usth1@gmail.com';
        $config['smtp_pass'] = 'Midnight2';
        $config['smtp_port'] = 465;
        $config['wordwrap'] = TRUE;
        $config['wrapchars'] = 76;
        $config['mailtype'] = 'html';
        $config['charset'] = 'iso-8859-1';
        $config['validate'] = FALSE;
        $config['priority'] = 3;
        $config['newline'] = "\r\n";
        $config['crlf'] = "\r\n";

        $this->load->library('email', array('mailtype' => 'html'));
        date_default_timezone_set('GMT');
        $this->load->helper('string');
        $this->email->initialize($config);

        $password = random_string('alnum', 16);
        $this->db->where('id', $user->id);
        $this->db->update('users', array('password' => md5($password)));
        $this->email->from('me@myweb.com', 'ICT Lab | Reset Password');
        $this->email->to($this->input->post('email'));
//        $this->email->to($user->email);
        $this->email->subject('Password reset');
        $this->email->message('You have requested the new password, Here is you new password:' . $password);
        $this->email->send();
    }

}
