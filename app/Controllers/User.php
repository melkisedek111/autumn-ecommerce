<?php

namespace App\Controllers;

use App\Helpers\Validation;
use App\Models\UsersModel;

class User extends BaseController
{
    protected $session;
    protected $requests;
    protected $UsersModel;
    protected $token;
    protected $rules;
    protected $messages;
    protected $rulesAndMessages;
    protected $validate;
    public function __construct()
    {
        $this->UsersModel = new UsersModel;
        $this->requests = \Config\Services::request();
        $this->session = session();
        $this->token = ['name' => csrf_token(), 'value' => csrf_hash()];
        $this->validate = new Validation;
        $this->rules =  [
            'register' => [
                'email' => 'required|valid_email',
                'first_name' => 'required|min_length[3]|max_length[150]|alpha_space',
                'last_name' => 'required|min_length[3]|max_length[150]|alpha_space',
                'password' => 'required|min_length[8]',
                'confirm_password' => 'required|matches[password]'
            ],
        ];
        $this->messages = [
            'register' => [
                'email' => [
                    'required' => 'Email is required!',
                    'valid_email' => 'Email is invalid!'
                ],
                'first_name' => [
                    'required' => 'First name is required',
                    'min_length' => 'First name must be at least 3 characters',
                    'max_length' => 'First name is too long!',
                    'alpha' => 'First name should be letter'
                ],
                'last_name' => [
                    'required' => 'Last name is required',
                    'min_length' => 'Last name must be at least 3 characters',
                    'max_length' => 'Last name is too long!',
                    'alpha' => 'Last name should be letter'
                ],
                'password' => [
                    'required' => 'Password is required!',
                    'min_length' => 'Password should at least 8 characters'
                ],
                'confirm_password' => [
                    'required' => 'Confirm password is required!',
                    'matches' => 'Confirm password doesn\'t match to your password'
                ]
            ]
        ];
        if ($this->requests->getPost()) {
            if (isset($this->requests->getPost()['register'])) {
                $this->rulesAndMessages = $this->validate->getRules($this->rules['register'], $this->messages['register'], $this->requests->getPost());
            } elseif (isset($this->requests->getPost()['login'])) {
                $this->rulesAndMessages = $this->validate->getRules($this->rules['login'], $this->messages['login'], $this->requests->getPost());
            }
        }
    }

    protected function validatePost(array $rules, array $messages, array $post, string $proxy): bool
    {
        $isFormValid = $this->validate($rules, $messages);
        foreach ($this->validator->getErrors() as $name => $errors) {
            $this->session->setFlashdata($proxy.'_error_'.$name, $errors);
        }
        foreach ($post as $name => $_) {
            $this->session->setFlashdata($proxy.'_value_'.$name, $post[$name]);
        }
        return $isFormValid;
    }

    public function login()
    {
        return view('login_view');
    }
    public function register()
    {
        return view('register_view', ['pageTitle' => 'Register']);
    }

    public function process()
    {
        if ($this->requests->getMethod(true) == "POST") {
            /**
             * check if the process post if for register, if yes then code below will run
             */
            if (isset($this->requests->getPost()['register'])) {
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) { // --> this will check if the request is a HTTP request
                    if (!$this->validatePost($this->rulesAndMessages['rules'], $this->rulesAndMessages['messages'], $this->requests->getPost(), "register")) { // --> validatePost function is to validate all input from the form
                        $data['internalValidationError'] = true; // --> This is the indicator that if the ajax process validation has an error, back validation will throw an error
                        $data['internalValidationErrorMessage'] = "Validation Error or Internal Server Error";
                        echo json_encode($data);
                    } else {
                        if ($this->requests->getPost('checkEmail') == true) { // --> indicator for email checking
                            $data['token'] = $this->token; // --> this token is used for HTTP/Ajax request only, to refresh the old CSRF Token
                            $count = count($this->UsersModel->check_email($this->requests->getPost())); // --> email count is more than 0 then email already exists!
                            if ($count == 0) {
                                $data['isEmailExists'] = false;
                                echo json_encode($data);
                            } else {
                                $data['isEmailExists'] = true;
                                echo json_encode($data);
                            }
                        }
                        if ($this->requests->getPost('ajaxRegister') == true) { // --> indicator for register user using from ajax
                            $data['token'] = $this->token; // --> this token is used for HTTP/Ajax request only, to refresh the old CSRF Token
                            if ($this->UsersModel->insert_user(($this->requests->getPost()))) {
                                $this->validate->alert('alertFixedSuccess', 'Registerd Success', 'You have successfully registered');
                                $data['registerSuccess'] = true;
                                echo json_encode($data);
                            }
                        }
                    }
                    
                } else {
                    /**
                     * Non ajax process
                     */
                    unset($_POST['register']);
                    if (!$this->validatePost($this->rulesAndMessages['rules'], $this->rulesAndMessages['messages'], $this->requests->getPost(), "register")) {
                        $count = count($this->UsersModel->check_email($this->requests->getPost()));
                        if ($count != 0) {
                            $this->session->setFlashdata('register_error_email', 'Email already exists!');
                            $this->session->setFlashdata('register_value_email', $this->requests->getPost('email'));
                        }
                        return redirect()->to('/register');
                    } else {
                        if ($this->UsersModel->insert_user(($this->requests->getPost()))) {
                            return redirect()->to('/login');
                        }
                    }
                }
            }
            if (isset($this->requests->getPost()['login'])) {
                unset($_POST['register']);
                if (!$this->validatePost($this->rulesAndMessages['rules'], $this->rulesAndMessages['messages'], $this->requests->getPost(), "register")) {
                    $count = count($this->UsersModel->check_email($this->requests->getPost()));
                    if ($count != 0) {
                        $this->session->setFlashdata('register_error_email', 'Email already exists!');
                        $this->session->setFlashdata('register_value_email', $this->requests->getPost('email'));
                    }
                    return redirect()->to('/register');
                } else {
                    if ($this->UsersModel->insert_user(($this->requests->getPost()))) {
                        return redirect()->to('/login');
                    }
                }
            }
        }
    }
    public function hello()
    {
        echo "Hello World!";
    }
    public function say()
    {
        echo "HI";
    }
    public function say_anything($a)
    {
        echo $a;
    }
    public function danger()
    {
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
