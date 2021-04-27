<?php

namespace App\Controllers;

use App\Models\TablesModel;

class Main extends BaseController
{
    protected $session;
    protected $requests;
    protected $rules;
    protected $messages;
    protected $rulesAndMessages;
    protected $TablesModel;
    protected $token;
    public function __construct()
    {
        $this->TablesModel = new TablesModel;
        $this->requests = \Config\Services::request();
        $this->session = session();
        $this->token = ['name' => csrf_token(), 'value' => csrf_hash()];
    }

    public function index() {
        echo "I am main class11";
    }
    public function hello() {
        echo "Hello World!";
    }
    public function say() {
        echo "HI";
    }
    public function say_anything($a) {
        echo $a;
    }
    public function danger() {
        return redirect()->to('/');
    }

    // public function index()
    // {
    //     return view('table_view');
    // }

    // public function getTotalRows() {
    //     $totalRows = $this->TablesModel->getTotalRows();
    //     $itemPerPageArray = [];
    //     for($i = 0; $i < $totalRows[0]->totalRows; $i++) {
    //         if($i % 10 === 0) {
    //             $itemPerPageArray[] = $i;
    //         }
    //     }
    //     $data['data'] = ['itemPerPageArray' => $itemPerPageArray, 'recordCount' => $totalRows[0]->totalRows];
    //     $data['token'] = $this->token;
    //     echo json_encode($data);
    // }

    // public function getLeads() {
    //     $data['data'] = $this->TablesModel->getFilterLeads($this->requests->getPost());
    //     $data['token'] = $this->token;
    //     echo json_encode($data);
    // }
    // public function getLeadsByName() {
    //     $data['data'] = $this->TablesModel->getLeadsByName($this->requests->getPost());
    //     $data['token'] = $this->token;
    //     echo json_encode($data);
    // }
    // public function getLeadsByDate() {
    //     $data['data'] = $this->TablesModel->getLeadsByDate($this->requests->getPost());
    //     $data['token'] = $this->token;
    //     echo json_encode($data);
    // }

}
