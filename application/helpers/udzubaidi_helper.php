<?php

    function getDropdownList($table, $columns)
    {
        $UD     =& get_instance();
        $query  = $UD->db->select($columns)->from($table)->get();

        if ($query->num_rows() >= 1) {
            $option1    = ['' => '- Select -'];
            $option2    = array_column($query->result_array(), $columns[1], $columns[0]);
            $options    = $option1 + $option2;

            return $options;
        }

        return $options = ['' => '- Select -'];

    }

    function getCategories()
    {
         $UD     =& get_instance();
         $query  = $UD->db->get('category')->result();
         return $query;
    }

    function getCart()
    {
        $UD     =& get_instance();
        $userID = $UD->session->userdata('id');

        if ($userID) {
            $query  = $UD->db->where('id_user', $userID)->count_all_results('cart');
            return $query;
        }

        return false;
    }

    function hashEncrypt($input)
    {
        $hash   = password_hash($input, PASSWORD_DEFAULT);
        return $hash;
    }

    function hashEncryptVerify($input, $hash)
    {
        if (password_hash($input, $hash)) {
            return true;
        } else {
            return false; 
        }
    }