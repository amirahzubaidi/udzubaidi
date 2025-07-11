<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends MY_Controller 
{
     public function __construct()
    {
        parent::__construct();
        $role = $this->session->userdata('role');
        if ($role != 'admin') {
            redirect(base_url('/'));
            return;
        }

        $this->load->model('Order_model', 'order');
    }

    public function index($page = null)
    {

    $data['title']      = 'Admin: Order';
    $data['content']    = $this->order
                                ->orderBy('date', 'DESC') 
                                ->paginate($page)
                                ->get();
                                
    $data['total_rows'] = $this->order->count();
    $data['pagination'] = $this->order->makePagination(
                                base_url('index.php/order'), 2, $data['total_rows']
                          );
    $data['page']       = 'page/order/index.php';

    $this->view($data);

    }

     public function search($page = null)
    {
    if(isset($_POST['keyword'])) {
        $this->session->set_userdata('keyword', $this->input->post('keyword'));
    } else {
        redirect(base_url('index.php/order'));
    }

    $keyword            = $this->session->userdata('keyword');
    $data['title']      = 'Admin: Order';
    $data['content']    = $this->order->like('invoice', $keyword)
                            ->orderBy('date', 'DESC')
                            // ->orLike('email', $keyword)
                            ->paginate($page)->get();
    $data['total_rows'] = $this->order->like('invoice', $keyword)->count();
    $data['pagination'] = $this->order->makePagination(
        base_url('index.php/order/search'), 3, $data['total_rows']
    );
    $data['page']   = 'page/order/index';

    $this->view($data);
    }

    public function reset()
    {   
    $this->session->unset_userdata('keyword');
    redirect(base_url('index.php/order'));
    }


    // public function detail($id) 
    // {
    //     $data['order']      = $this->order->where('id', $id)->first();
    //     if (!$data['order']) {
    //         $this->session->set_flashdata('warning', 'Data tidak ditemukan.');
    //         redirect(base_url('index.php/order'));
    //     }

    //     //yg 1
    //     // $this->order->table   = 'orders_detail';
    //     // $data['order_detail']   = $this->order->select([
    //     //     'orders_detail.id_orders', 'orders_detail.id_product', 'orders_detail.qty', 
    //     //     'orders_detail.subtotal', 'product.title', 'product.image', 'product.price'
    //     // ])
    //     // ->join('product')
    //     // ->where('orders_detail.id_orders', $id)
    //     // ->get();

    //     $this->db->select([
    //         'orders_detail.id_orders', 'orders_detail.id_product', 'orders_detail.qty', 
    //         'orders_detail.subtotal', 'product.title', 'product.image', 'product.price'
    //     ]);
    //         $this->db->from('orders_detail');
    //         $this->db->join('product', 'orders_detail.id_product = product.id', 'left');
    //         $this->db->where('orders_detail.id_orders', $id);

    //     $data['order_detail'] = $this->db->get()->result();


    //     if ($data['order']->status !== 'waiting') {
    //         $this->order->table = 'orders_confirm';
    //         $data['order_confirm']  = $this->order->where('id_orders', $id)->first();
    //     }

    // $data['page']               = 'page/order/detail';

    // $this->view($data);

    // }

    public function detail($id) 
    {
    $data['order'] = $this->order->where('id', $id)->first();
    if (!$data['order']) {
        $this->session->set_flashdata('warning', 'Data tidak ditemukan.');
        redirect(base_url('index.php/order'));
    }

    // Ambil detail order dengan join ke tabel product
    $this->load->database(); // tambahkan jika belum ada
    $this->db->select([
        'orders_detail.id_orders', 
        'orders_detail.id_product', 
        'orders_detail.qty', 
        'orders_detail.subtotal', 
        'product.title', 
        'product.image', 
        'product.price'
    ]);
    $this->db->from('orders_detail');
    $this->db->join('product', 'orders_detail.id_product = product.id', 'left');
    $this->db->where('orders_detail.id_orders', $id);
    $data['order_detail'] = $this->db->get()->result();

    // Ambil data konfirmasi jika status bukan 'waiting'
    if ($data['order']->status !== 'waiting') {
        $this->order->table = 'orders_confirm';
        $data['order_confirm']  = $this->order->where('id_orders', $id)->first();
    }

    $data['page'] = 'page/order/detail';
    $this->view($data);
    }


    public function update($id) 
    {
        if (!$_POST) {
            $this->session->set_flashdata('error', 'Oops! Terjadi kesalahan!');
            redirect(base_url("index.php/order/detail/$id"));
        }

        if ($this->order->where('id', $id)->update(['status' => $this->input->post('status')])) {
            $this->session->set_flashdata('success', 'Data berhasil diperbaharui.');
        } else {
            $this->session->set_flashdata('error', 'Oops! Terjadi kesalahan!');
        }

        redirect(base_url("index.php/order/detail/$id"));
    }

}

/* End of file Order.php */
