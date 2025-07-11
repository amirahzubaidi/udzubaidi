<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MY_Controller 
{
    
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');

        $role = $this->session->userdata('role');
        if ($role != 'admin') {
            redirect(base_url('/'));
            return;
        }
    }
    

    public function index($page = null)
    {
        $data['title']      = 'Admin: Pengguna';
        $data['content']    = $this->user->paginate($page)->get();
        $data['total_rows'] = $this->user->count();
        $data['pagination'] = $this->user->makePagination(
            base_url('index.php/user'), 2, $data['total_rows']
        );
        $data['page']       = 'page/user/index';

        $this->view($data);
    }

    public function search($page = null)
    {
    if(isset($_POST['keyword'])) {
        $this->session->set_userdata('keyword', $this->input->post('keyword'));
    } else {
        redirect(base_url('index.php/user'));
    }

    $keyword            = $this->session->userdata('keyword');
    $data['title']      = 'Admin: Pengguna';
    $data['content']    = $this->user
        ->like('name', $keyword)
        ->orLike('email', $keyword)
        ->paginate($page)
        ->get();
    $data['total_rows'] = $this->user->like('name', $keyword)->orLike('email',$keyword)->count();
    $data['pagination'] = $this->user->makePagination(
        base_url('index.php/user/search'), 3, $data['total_rows']
    );
    $data['page']   = 'page/user/index';

    $this->view($data);
    }

    public function reset()
    {   
    $this->session->unset_userdata('keyword');
    redirect(base_url('index.php/user'));
    }


    public function create()
    {
        if (!$_POST) {
            $input  = (object) $this->user->getDefaultValues();
        } else {
            $input  = (object)  $this->input->post(null, true);
            $this->load->library('form_validation');
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');
            $input->password = hashEncrypt($input->password);

        }

        if (!empty($_FILES) && $_FILES['image']['name'] !== '') {
            $imageName  = url_title($input->name, '-', true) . '-' . date('YmdHis');
            $upload     = $this->user->uploadImage('image', $imageName);
            if ($upload) {
                $input->image   = $upload['file_name'];
            } else {
                redirect(base_url('index.php/user/create'));
            }
        }

        //ada tambahan

        $this->form_validation->set_error_delimiters('<small class="text-danger">', '</small>');
        // $this->form_validation->MY =& $this;

        if (!$this->user->validate()) {
            // $this->form_validation->MY =& $this; 
            $data['title']          = 'Tambah Pengguna';
            $data['input']          = $input;
            $data['form_action']    = base_url('index.php/user/create');
            $data['page']           = 'page/user/form';

            $this->view($data);
            return;
        }

        if ($this->user->create($input)) {
             $this->session->set_flashdata('success', 'Data berhasil disimpan!');
        } else {
            $this->session->set_flashdata('error', 'Oops! Terjadi suatu kesalahan');
        }

        redirect(base_url('index.php/user'));
    }

        public function edit($id) 
    {
        $data['content'] = $this->user->where('id', $id)->first();
        if (!$data['content']) {
            $this->session->set_flashdata('warning', 'Maaf , data tidka ditemukan');
            redirect(base_url('index.php/user'));
        }

        if (!$_POST) {
            $data['input']  = $data['content'];
        } else {
            $data['input']  = (object) $this->input->post(null, true);
            if ($data['input']->password !== '') {
                $data[input]->password = hashEncrypt($data['input']->password);
            } else {
                $data[input]->password = $data['content']->password;
            }
        }

        if (!empty($_FILES) && $_FILES['image']['name'] !== '') {
            $imageName  = url_title($data['input']->name, '-', true) . '-' . date('YmdHis');
            $upload     = $this->user->uploadImage('image', $imageName);
            if ($upload) {
                if ($data['content']->image !== '') {
                    $this->user->deleteImage($data['content']->image);
                }
                $data['input']->image   =$upload['file_name'];
            } else {
                redirect(base_url("index.php/user/edit/$id"));
            }
        }

        if (!$this->user->validate()) {
            // $this->form_validation->MY =& $this; 
            $data['title']          = 'Ubah Pengguna';
            $data['form_action']    = base_url("index.php/user/edit/$id");
            $data['page']           = 'page/user/form';

            $this->view($data);
            return;
        }

        if ($this->user->where('id', $id)->update($data['input'])) {
             $this->session->set_flashdata('success', 'Data berhasil disimpan!');
        } else {
            $this->session->set_flashdata('error', 'Oops! Terjadi suatu kesalahan');
        }

        redirect(base_url('index.php/user'));
    }

     public function delete($id) 
    {
        if (!$_POST) {
            redirect(base_url('index.php/user'));
        }

        $user = $this->user->where('id', $id)->first();

        if (!$user) {
            $this->session->set_flashdata('warning', 'Maaf , data tidka ditemukan');
            redirect(base_url('index.php/user'));
        }

        if ($this->user->where('id', $id)->delete()) {
            $this->user->deleteImage($user->image);
            $this->session->set_flashdata('success', 'Data sudah berhasil dihapus!');
        } else {
            $this->session->set_flashdata('error', 'Oops! Tejadi suatu kesalahan!');
        }

        redirect(base_url('index.php/user'));
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

/* End of file User.php */
