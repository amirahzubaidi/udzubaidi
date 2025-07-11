<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function getDropdownList($table, $columns)
{
    $CI =& get_instance(); // Memanggil instance CodeIgniter
    $query = $CI->db->select($columns)->from($table)->get();

    if ($query->num_rows() >= 1) {
        $options = ['' => '- Pilih -'];
        foreach ($query->result() as $row) {
            $options[$row->{$columns[0]}] = $row->{$columns[1]};
        }
        return $options;
    }

    return ['' => '- Tidak ada data -'];
}
