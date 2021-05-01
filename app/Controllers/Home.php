<?php

namespace App\Controllers;

use App\Helpers\Utilities;
use App\Models\UsersModel;

class Home extends BaseController
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
            ]
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

    public function index()
    {
        return view('home_view');
    }  
}
