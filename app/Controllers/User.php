<?php

namespace App\Controllers;

use App\Helpers\Utilities;
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
    protected $utilities;
    public function __construct()
    {
        $this->UsersModel = new UsersModel;
        $this->requests = \Config\Services::request();
        $this->session = session();
        $this->token = ['name' => csrf_token(), 'value' => csrf_hash()];
        $this->utilities = new Utilities;
        $this->rules =  [
            'register' => [
                'email' => 'required|valid_email',
                'first_name' => 'required|min_length[3]|max_length[150]|alpha_space',
                'last_name' => 'required|min_length[3]|max_length[150]|alpha_space',
                'password' => 'required|min_length[8]',
                'confirm_password' => 'required|matches[password]'
            ],
            'login' => [
                'email' => 'required',
                'password' => 'required'
            ],
            'set_address' => [
                'address' => 'required',
                'contact' => 'required|numeric',
                'city' => 'required|alpha_space',
                'province' => 'required|alpha_space',
                'zipcode' => 'required|numeric',
            ]
        ];
        $this->messages = [
            'set_address' => [
                'address' => [
                    'required' => 'Address is required',
                ],
                'contact' => [
                    'required' => 'Contact is required',
                    'numeric' => 'Contact should be numeric',
                ],
                'city' => [
                    'required' => 'City is required',
                    'alpha' => 'City should be letter'
                ],
                'province' => [
                    'required' => 'Province is required',
                    'alpha' => 'Province should be letter'
                ],
                'zipcode' => [
                    'required' => 'Zipcode is required',
                    'numeric' => 'Zipcode should be numeric',
                ]
            ],
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
                ],
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
            ],
            'login' => [
                'email' => [
                    'required' => 'Email is empty!',
                ],
                'password' => [
                    'required' => 'Password is empty!',
                ],
            ]
        ];
        if ($this->requests->getPost()) {
            if (isset($this->requests->getPost()['register'])) {
                $this->rulesAndMessages = $this->utilities->getRules($this->rules['register'], $this->messages['register'], $this->requests->getPost());
            } elseif (isset($this->requests->getPost()['login'])) {
                $this->rulesAndMessages = $this->utilities->getRules($this->rules['login'], $this->messages['login'], $this->requests->getPost());
            } elseif (isset($this->requests->getPost()['set_address'])) {
                $this->rulesAndMessages = $this->utilities->getRules($this->rules['set_address'], $this->messages['set_address'], $this->requests->getPost());
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
        return view('login_view', ['pageTitle' => 'Login']);
    }
    public function register()
    {
        return view('register_view', ['pageTitle' => 'Register']);
    }
    public function admin_login()
    {
        return view('admin_login_view', ['pageTitle' => 'Admin Login']);
    }
    public function set_address()
    {
        $isAddressSet = $this->utilities->checkUserAddress(['user_id' => $this->session->get('user')->user_id]);
        if($isAddressSet) {
            return redirect()->to('/');
        }
        return view('address_view');
    }
    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('/login');
    }
    public function set_address_process()
    {
        if ($this->requests->getMethod(true) == "POST") {
            if (!$this->validatePost($this->rulesAndMessages['rules'], $this->rulesAndMessages['messages'], $this->requests->getPost(), "set_address")) {
                $data['internalValidationError'] = true; // --> 4
                $data['internalValidationErrorMessage'] = "Validation Error or Internal Server Error";
                echo json_encode($data);
            } else {
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
                    $user = $this->session->get('user');
                    $success = $this->UsersModel->set_user_address($this->requests->getPost(), $user->user_id);
                    if($success) {
                        $data['isAddressSet'] = true;
                    } else {
                        $data['isAddressSet'] = false;
                    }
                    echo json_encode($data);
                }
            }
        }
    }
    public function process()
    {
        $data['token'] = $this->token; // --> this token is used for HTTP/Ajax request only, to refresh the old CSRF Token
        if ($this->requests->getMethod(true) == "POST") {
            /**
             * REGISTER PROCESS
             * 1. check if the process post if for register, if yes then code below will run
             * 2. this will check if the request is a HTTP request
             * 3. validatePost function is to validate all input from the form
             * 4. This is the indicator that if the ajax process validation has an error, back validation will throw an error
             * 5. indicator for email checking
             * 6. email count is more than 0 then email already exists!
             * 7. indicator for register user using from ajax
             */
            if (isset($this->requests->getPost()['register'])) { // --> 1
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) { // --> 2
                    if (!$this->validatePost($this->rulesAndMessages['rules'], $this->rulesAndMessages['messages'], $this->requests->getPost(), "register")) { // --> 3
                        $data['internalValidationError'] = true; // --> 4
                        $data['internalValidationErrorMessage'] = "Validation Error or Internal Server Error";
                        echo json_encode($data);
                    } else {
                        if ($this->requests->getPost('checkEmail') == true) { // --> 5
                            $count = count($this->UsersModel->check_email($this->requests->getPost())); // --> 6
                            if ($count == 0) {
                                $data['isEmailExists'] = false;
                                echo json_encode($data);
                            } else {
                                $data['isEmailExists'] = true;
                                echo json_encode($data);
                            }
                        }
                        if ($this->requests->getPost('ajaxRegister') == true) { // --> 7
                            if ($this->UsersModel->insert_user(($this->requests->getPost()))) {
                                $this->utilities->alert('alertFixedSuccess', 'Registerd Success', 'You have successfully registered');
                                $data['registerSuccess'] = true;
                                echo json_encode($data);
                            }
                        }
                    }
                } else {
                    /**
                     * Non ajax process
                     */
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
            /**
             * LOGIN PROCESS
             * 1. This is the indicator that if the ajax process validation has an error, back validation will throw an error
             * 2. indicator for register user using from ajax
             * 3. process login through model
             * 4. sending response to the ajax/ http request
             * 5. validation process
             * 6. Flash message if email and password does not match when not using ajax
             */
            if (isset($this->requests->getPost()['login'])) {
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
                    if (!$this->validatePost($this->rulesAndMessages['rules'], $this->rulesAndMessages['messages'], $this->requests->getPost(), "login")) { // --> 5
                        $data['internalValidationError'] = true; // --> 1
                        $data['internalValidationErrorMessage'] = "Validation Error or Internal Server Error";
                        echo json_encode($data);
                    } else {
                        if ($this->requests->getPost('ajaxLogin') == true) { // --> 2
                            $user = $this->UsersModel->login_user(($this->requests->getPost())); // --> 3
                            if ($user) {
                                if ($user->user_type == 'normal') {
                                    $this->session->set('user', $user);
                                    $this->session->set('isLogin', true);
                                    $data['loginSuccess'] = true;
                                    echo json_encode($data); // 4
                                } else {
                                    $data['loginSuccess'] = false;
                                    echo json_encode($data); // 4
                                }
                            } else {
                                $data['loginSuccess'] = false;
                                echo json_encode($data); // 4
                            }
                        }
                    }
                } else {
                    if (!$this->validatePost($this->rulesAndMessages['rules'], $this->rulesAndMessages['messages'], $this->requests->getPost(), "login")) { // -> 5
                        return redirect()->to('/login');
                    } else {
                        $user = $this->UsersModel->login_user(($this->requests->getPost()));
                        if ($user) {
                            // do something
                            if ($user->user_type == 'normal') {
                                $this->session->set('user', $user);
                                $this->session->set('isLogin', true);
                                return redirect()->to('/login');
                            }
                            if ($user->user_type == "admin") {
                                $this->session->set('user', $user);
                                $this->session->set('isLogin', true);
                                $this->session->set('isAdminLogin', true);
                                return redirect()->to('/main');
                            }
                        } else {
                            $this->session->setFlashdata('login_error_email', 'Username or password does not matched!'); // --> 6
                            return redirect()->to('/login');
                        }
                    }
                }
            }
        }
    }
}
