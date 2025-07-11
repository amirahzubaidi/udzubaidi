<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller 
{

    public function index($page = null)
    {
        
        $page = $this->uri->segment(3) ?? 1;
        $perPage = 10; // jumlah produk per halaman

        $data['title']      = 'Homepage';
        $data['content']    = $this->home->select(
            [
                'product.id', 'product.title AS product_title', 
                'product.description', 'product.image',
                'product.price' ,'product.is_available', 
                'category.title AS category_title', 'category.slug AS category_slug'
            ]
        )
        ->join('category')
        ->where('product.is_available', 1)
        ->paginate($page, $perPage)
        ->get();

       

        $data['total_rows'] = $this->home->where('product.is_available', 1)->count();
        $data['pagination'] = $this->home->makePagination(
            base_url('index.php/home/index'), 2, $data['total_rows']
        );

         // Tambahkan ini untuk menghindari error getCategories()
        $data['categories'] = $this->db->get('category')->result();

        $data['category'] = 'Semua Kategori';
        $data['page']   = 'page/home/index';
        $data['content'] = $data['content'];
        $this->view($data);   
    }

}

/* End of file Home.php */
