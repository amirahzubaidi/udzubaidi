<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends MY_Controller 
{

    private $id;

    public function __construct()
    {
        parent::__construct();
        $is_login   = $this->session->userdata('is_login');
        $this->id   = $this->session->userdata('id');

        if (!$is_login) {
            redirect(base_url());
            return;
        }

    }
    

    public function index()
    {
        $data['title']      = 'Profile';
        $data['content']    = $this->profile->where('id', $this->id)->first();
        $data['page']       = 'page/profile/index';

        return $this->view($data);
    }

    public function update($id) 
    {
        $data['content'] = $this->profile->where('id', $id)->first();
        if (!$data['content']) {
            $this->session->set_flashdata('warning', 'Maaf , data tidka ditemukan');
            redirect(base_url('index.php/profile'));
        }

        if (!$_POST) {
            $data['input']  = $data['content'];
        } else {
            $data['input']  = (object) $this->input->post(null, true);
            if ($data['input']->password !== '') {
                $data['input']->password = hashEncrypt($data['input']->password);
            } else {
                $data['input']->password = $data['content']->password;
            }
        }

        if (!empty($_FILES) && $_FILES['image']['name'] !== '') {
            $imageName  = url_title($data['input']->name, '-', true) . '-' . date('YmdHis');
            $upload     = $this->profile->uploadImage('image', $imageName);
            if ($upload) {
                if ($data['content']->image !== '') {
                    $this->profile->deleteImage($data['content']->image);
                }
                $data['input']->image   =$upload['file_name'];
            } else {
                redirect(base_url("index.php/profile/update/$id"));
            }
        }

        if (!$this->profile->validate()) {
            // $this->form_validation->MY =& $this; 
            $data['title']          = 'Ubah Data Profile';
            $data['form_action']    = base_url("index.php/profile/update/$id");
            $data['page']           = 'page/profile/form';

            $this->view($data);
            return;
        }

        if ($this->profile->where('id', $id)->update($data['input'])) {
             $this->session->set_flashdata('success', 'Data berhasil disimpan!');
        } else {
            $this->session->set_flashdata('error', 'Oops! Terjadi suatu kesalahan');
        }

        redirect(base_url('index.php/profile'));
    }

    public function unique_email($email)
    {
    $id = $this->input->post('id');

    $this->db->where('email', $email);
    if ($id) {
    $this->db->where('id !=', $id);
    }

    $user = $this->db->get('user')->row();

    if ($user) {
        // Tambahkan baris ini agar error muncul jelas di form
        $this->form_validation->set_message('unique_email', '%s sudah digunakan. Silakan pilih yang lain.');
        return false;
    }

    return true;
    }

}

/* End of file Profile.php */
