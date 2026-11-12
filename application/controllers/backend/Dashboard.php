<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('id_admin')) {
            redirect('login_admin');
        }
    }

    public function index()
    {
        $this->loadBackend('dashboard/index', array());
    }
}
