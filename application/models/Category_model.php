<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category_model extends MY_Model 
{

    protected $table = 'category';
    protected $perPage = 5;

    public function getDefaultValues()
    {
        return [
            'id'    => '',
            'slug'  => '',
            'title' => ''
        ];
    }

    public function getValidationRules()
    {
        $validationRules = [
            [
                'field'     => 'slug',
                'label'     => 'Slug',
                'rules'     => 'trim|required|callback_unique_slug'
            ],
            [
                'field'     => 'title',
                'label'     => 'Kategori',
                'rules'     => 'trim|required'
            ],
        ];

        return $validationRules;
    }


// public function paginate($page, $perPage = null)
// {
//     $perPage = $perPage ?? $this->perPage; // fallback ke default jika tidak diberikan
//     $page = max(1, (int)$page); // mencegah page < 1
//     $offset = ($page - 1) * $perPage;

//     return $this->db->order_by('id', 'DESC')
//                     ->get('category', $perPage, $offset)
//                     ->result();
// }

public function paginate($page, $perPage = null)
{
    if ($perPage) {
        $this->perPage = $perPage;
    }

    $this->db->limit(
        $this->perPage,
        $this->calculateRealOffset($page)
    );

    return $this; // <<=== PENTING: supaya bisa chaining ->get()
}


}

/* End of file Category_model.php */
