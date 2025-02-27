<?php
defined('BASEPATH') or exit('No direct script access allowed');





class Invoice extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'language', 'timezone_helper']);
        $this->load->model(['Invoice_model', 'Order_model']);
        $this->session->set_flashdata('authorize_flag', "");
    }
    public function test()
    {
        echo get_invoice_html(277);
    }

    public function index()
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->data['main_page'] = VIEW . 'invoice';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Invoice Management |' . $settings['app_name'];
            $this->data['meta_description'] = 'eRestro | Invoice Management';
            $is_add_ons = [];
            if (isset($_GET['edit_id']) && !empty($_GET['edit_id'])) {
                $res = $this->Order_model->get_order_details(['o.id' => $_GET['edit_id']], true);
                // print_r($res);
                // echo "<pre>";
                // print_r($res);
                if (!empty($res)) {
                    $items = [];
                    $promo_code = [];
                    if (!empty($res[0]['promo_code'])) {
                        $promo_code = fetch_details(['promo_code' => trim($res[0]['promo_code'])], 'promo_codes');
                    }
                    foreach ($res as $row) {
                        $temp['product_id'] = $row['product_id'];
                        if (isset($row['add_ons']) && !empty($row['add_ons']) && $row['add_ons'] != "[]") {
                            $is_add_ons[] = true;
                        } else {
                            $is_add_ons[] = false;
                        }
                        // echo "<pre>";
                        // print_r($row);
                        // die;
                        $temp['order_id']  = $_GET['edit_id'];
                        $temp['add_ons'] = $row['add_ons'];
                        $temp['sub_total'] = $row['sub_total'];
                        $temp['branch_id'] = $row['branch_id'];
                        $temp['product_variant_id'] = $row['product_variant_id'];
                        $temp['pname'] = $row['pname'];
                        $temp['quantity'] = $row['quantity'];
                        $temp['discounted_price'] = $row['discounted_price'];
                        $temp['tax_percent'] = $row['tax_percent'];
                        $temp['tax_amount'] = $row['tax_amount'];
                        $temp['price'] = $row['price'];
                        $temp['rider'] = $row['rider'];
                        $temp['active_status'] = $row['active_status'];
                        array_push($items, $temp);
                    }
                    $this->data['order_detls'] = $res;
                    $this->data['is_add_ons'] = $is_add_ons;
                    $this->data['items'] = $items;

                    $this->data['promo_code'] = $promo_code;
                    $this->data['settings'] = $settings;
                    if (!isset($_SESSION['branch_id'])) {

                        redirect('admin/branch', 'refresh');
                    } else {

                        $this->load->view('admin/template', $this->data);
                    }
                } else {
                    redirect('admin/orders/', 'refresh');
                }
            } else {
                redirect('admin/orders/', 'refresh');
            }
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function thermal_invoice()
    {

        $settings = get_settings('system_settings', true);
        $is_add_ons = [];
        if (isset($_GET['edit_id']) && !empty($_GET['edit_id'])) {
            $res = $this->Order_model->get_order_details(['o.id' => $_GET['edit_id']], true);
            if (!empty($res)) {
                $items = [];
                $promo_code = [];
                if (!empty($res[0]['promo_code'])) {
                    $promo_code = fetch_details(['promo_code' => trim($res[0]['promo_code'])], 'promo_codes');
                }
                foreach ($res as $row) {

                    $temp['product_id'] = $row['product_id'];
                    if (isset($row['add_ons']) && !empty($row['add_ons']) && $row['add_ons'] != "[]") {
                        $is_add_ons[] = true;
                    } else {
                        $is_add_ons[] = false;
                    }
                    $temp['order_id']  = $_GET['edit_id'];
                    $temp['add_ons'] = $row['add_ons'];
                    $temp['sub_total'] = $row['sub_total'];
                    $temp['partner_id'] = $row['partner_id'];
                    $temp['product_variant_id'] = $row['product_variant_id'];
                    $temp['pname'] = $row['pname'];
                    $temp['quantity'] = $row['quantity'];
                    $temp['discounted_price'] = $row['discounted_price'];
                    $temp['tax_percent'] = $row['tax_percent'];
                    $temp['tax_amount'] = $row['tax_amount'];
                    $temp['price'] = $row['price'];
                    $temp['rider'] = $row['rider'];
                    $temp['active_status'] = $row['active_status'];
                    array_push($items, $temp);
                }
                $this->data['order_detls'] = $res;
                $this->data['is_add_ons'] = $is_add_ons;
                $this->data['items'] = $items;
                $this->data['promo_code'] = $promo_code;
                $this->data['settings'] = $settings;
                $this->load->view('thermal_invoice', $this->data);
            } else {
                redirect('admin/orders/', 'refresh');
            }
        }
    }
}
