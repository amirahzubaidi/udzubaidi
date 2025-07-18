<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product extends MY_Controller 
{
    
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('dropdown');
        $this->load->library('form_validation');

        $role = $this->session->userdata('role');
        if ($role != 'admin') {
            redirect(base_url('/'));
            return;
        }

    }
    
    public function index($page = null)
    {
        $data['title']      = 'Admin: Produk';
        $data['content']    = $this->product->select(
            [
                'product.id', 'product.title AS product_title', 'product.image',
                 'product.price' ,'product.is_available', 
                 'category.title AS category_title'
            ]
        )
        ->join('category')
        ->paginate($page)
        ->get();

        $data['total_rows'] = $this->product->count();
        $data['pagination'] = $this->product->makePagination(
            base_url('index.php/product'), 2, $data['total_rows']
        );
        $data['page']       = 'page/product/index';

        $this->view($data);
    }

    public function search($page = null)
    {
    if(isset($_POST['keyword'])) {
        $this->session->set_userdata('keyword', $this->input->post('keyword'));
    } else {
        redirect(base_url('index.php/product'));
    }

    $keyword            = $this->session->userdata('keyword');
    $data['title']      = 'Admin: Produk';
    $data['content']    = $this->product->select(
            [
                'product.id', 'product.title AS product_title', 'product.image',
                 'product.price' ,'product.is_available', 
                 'category.title AS category_title'
            ]
        )
        ->join('category')
        ->like('product.title', $keyword)
        ->orLike('description', $keyword)
        ->paginate($page)
        ->get();
    $data['total_rows'] = $this->product->like('product.title', $keyword)->orLike('description',$keyword)->count();
    $data['pagination'] = $this->product->makePagination(
        base_url('index.php/product/search'), 3, $data['total_rows']
    );
    $data['page']   = 'page/product/index';

    $this->view($data);
    }

    public function reset()
    {   
    $this->session->unset_userdata('keyword');
    redirect(base_url('index.php/product'));
    }

    public function create()
    {
        if (!$_POST) {
            $input  = (object) $this->product->getDefaultValues();
        } else {
            $input  = (object)  $this->input->post(null, true);
        }

        if (!empty($_FILES) && $_FILES['image']['name'] !== '') {
            $imageName  = url_title($input->title, '-', true) . '-' . date('YmdHis');
            $upload     = $this->product->uploadImage('image', $imageName);
            if ($upload) {
                $input->image   =$upload['file_name'];
            } else {
                redirect(base_url('index.php/product/create'));
            }
        }

        //ada tambahan

        $this->form_validation->set_error_delimiters('<small class="text-danger">', '</small>');
        // $this->form_validation->MY =& $this;

        if (!$this->product->validate()) {
            // $this->form_validation->MY =& $this; 
            $data['title']          = 'Tambah Produk';
            $data['input']          = $input;
            $data['form_action']    = base_url('index.php/product/create');
            $data['page']           = 'page/product/form';

            $this->view($data);
            return;
        }

        if ($this->product->create($input)) {
             $this->session->set_flashdata('success', 'Data berhasil disimpan!');
        } else {
            $this->session->set_flashdata('error', 'Oops! Terjadi suatu kesalahan');
        }

        redirect(base_url('index.php/product'));
    }

    public function edit($id) 
    {
        $data['content'] = $this->product->where('id', $id)->first();
        if (!$data['content']) {
            $this->session->set_flashdata('warning', 'Maaf , data tidka ditemukan');
            redirect(base_url('index.php/product'));
        }

        if (!$_POST) {
            $data['input']  = $data['content'];
        } else {
            $data['input']  = (object) $this->input->post(null, true);
        }

        if (!empty($_FILES) && $_FILES['image']['name'] !== '') {
            $imageName  = url_title($data['input']->title, '-', true) . '-' . date('YmdHis');
            $upload     = $this->product->uploadImage('image', $imageName);
            if ($upload) {
                if ($data['content']->image !== '') {
                    $this->product->deleteImage($data['content']->image);
                }
                $data['input']->image   =$upload['file_name'];
            } else {
                redirect(base_url("index.php/product/edit/$id"));
            }
        }

        if (!$this->product->validate()) {
            // $this->form_validation->MY =& $this; 
            $data['title']          = 'Ubah Produk';
            $data['form_action']    = base_url("index.php/product/edit/$id");
            $data['page']           = 'page/product/form';

            $this->view($data);
            return;
        }

        if ($this->product->where('id', $id)->update($data['input'])) {
             $this->session->set_flashdata('success', 'Data berhasil disimpan!');
        } else {
            $this->session->set_flashdata('error', 'Oops! Terjadi suatu kesalahan');
        }

        redirect(base_url('index.php/product'));
    }

    public function delete($id) 
    {
        if (!$_POST) {
            redirect(base_url('index.php/product'));
        }

        $product = $this->product->where('id', $id)->first();

        if (!$product) {
            $this->session->set_flashdata('warning', 'Maaf , data tidka ditemukan');
            redirect(base_url('index.php/product'));
        }

        if ($this->product->where('id', $id)->delete()) {
            $this->product->deleteImage($product->image);
            $this->session->set_flashdata('success', 'Data sudah berhasil dihapus!');
        } else {
            $this->session->set_flashdata('error', 'Oops! Tejadi suatu kesalahan!');
        }

        redirect(base_url('index.php/product'));
    }

    public function unique_slug($slug)
    {
    $id = $this->input->post('id');

    $this->db->where('slug', $slug);
    if ($id) {
        $this->db->where('id !=', $id);
    }

    $product = $this->db->get('product')->row();

    if ($product) {
        // Tambahkan baris ini agar error muncul jelas di form
        $this->form_validation->set_message('unique_slug', '%s sudah digunakan. Silakan pilih yang lain.');
        return false;
    }

    return true;
    }

}

/* End of file Product.php */
