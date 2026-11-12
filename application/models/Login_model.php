<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login_model extends CI_Model {

    public function getData_Admin($email)
    {
        $query = $this->db->query("
            SELECT 
            *
            FROM admin
            WHERE admin.email = '$email'
            ");
        return $query->row_array();
    }

    public function getData_User($email)
    {
        $query = $this->db->query("
            SELECT 
            *
            FROM user
            WHERE user.email = '$email'
            ");
        return $query->row_array();
    }

    public function getData_UsersByPhone($phone)
    {
        $localPhone = preg_replace('/[^0-9]/', '', (string) $phone);
        $internationalPhone = strpos($localPhone, '0') === 0
            ? '62' . substr($localPhone, 1)
            : $localPhone;

        return $this->db
            ->where_in('no_hp', array($localPhone, $internationalPhone, '+' . $internationalPhone))
            ->order_by('id', 'ASC')
            ->get('user')
            ->result_array();
    }

    public function getStsLogin($id)
    {
        $query = $this->db->query("
            SELECT 
            *
            FROM user
            WHERE user.id = '$id'
            ");
        return $query->row_array();
    }

    public function cekAccountUser($email,$stsLogin)
    {
        $data = array(
        'status_login' => $stsLogin
        );
        $this->db->where('email', $email);
        $this->db->update('user', $data);
    }

    public function cekAccountUserID($id,$stsLogin)
    {
        $data = array(
        'status_login' => $stsLogin
        );
        $this->db->where('id', $id);
        $this->db->update('user', $data);
    }

}
