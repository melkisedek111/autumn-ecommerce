<?php

namespace App\Helpers;

use App\Controllers\BaseController;
use CodeIgniter\Controller;
class Validate extends Controller
{
    protected $session;
    protected $requests;
    public function __construct()
    {
        $this->requests = \Config\Services::request();
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

    public function validatePost(array $rules, array $messages, array $post, string $proxy): bool
    {
        $isFormValid = $this->validate($rules, $messages);
        foreach ($this->validator->getErrors() as $name => $errors) {
            $this->session->setFlashdata($proxy.'_error_'.$name, $errors);
        }
        foreach ($post as $name => $_) {
            $this->session->setFlashdata($proxy.'_value_'.$name, $post($name));
        }
        return $isFormValid;
    }
}
