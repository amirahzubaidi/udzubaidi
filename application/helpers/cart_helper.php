<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function getCart()
{
    $ci =& get_instance();

    // Pastikan user sudah login
    if (! $ci->session->userdata('is_login')) {
        return 0;
    }

    // Ambil id user dari session
    $id_user = $ci->session->userdata('id');

    // Load model cart jika belum
    $ci->load->model('cart_model', 'cart');

    // Hitung jumlah qty barang di cart user tersebut
    $ci->cart->table = 'cart';
    $items = $ci->cart->select('SUM(qty) AS total')->where('id_user', $id_user)->first();

    return $items ? $items->total : 0;
}
