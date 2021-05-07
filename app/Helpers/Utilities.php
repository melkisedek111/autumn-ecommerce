<?php

namespace App\Helpers;
use App\Models\UsersModel;
use App\Controllers\BaseController;
class Utilities extends BaseController
{
    protected $session;
    protected $requests;
    protected $UserModel;
    public function __construct()
    {
        $this->UserModel = new UsersModel;
        $this->session = session();
    }
    
    public function alert($class, $head, $message)
    {
        $this->session->setFlashdata('alert', true);
        $this->session->setFlashdata('class', $class);
        $this->session->setFlashdata('head', $head);
        $this->session->setFlashdata('message', $message);
    }

    public function getRules(array $rules, array $messages, $posts): array
    {
        $setRules = [];
        $setMessages = [];
        foreach ($posts as $key => $_) {
            if (@$rules[$key]) {
                $setRules[$key] = @$rules[$key];
            }
            if (@$messages[$key]) {
                $setMessages[$key] = @$messages[$key];
            }
        }
        return ['rules' => $setRules, 'messages' => $setMessages];
    }

    public function isUserLogin(string $user): bool {
        if($this->session->has('user')) {
            if($this->session->get('isLogin')) {
                if($user == 'admin') {
                    if($this->session->get('user')->user_type != 'admin') {
                        if(!$this->session->get('isAdminLogin')) {
                            return false;
                        }
                    } else {
                        return true;
                    }
                }
                if($user == 'normal') {
                    if($this->session->get('user')->user_type != 'normal') {
                        return false;
                    } else {
                        return true;
                    }
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function checkUserAddress(array $posts) {
        $isAddressSet = $this->UserModel->check_user_address($posts);
        return $isAddressSet->address_count ? true : false;
    }
}
