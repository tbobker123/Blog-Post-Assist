<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Auth extends BaseController
{
    
    public function __construct(){
        helper(['url', 'form']);
    }

    public function index()
    {
        if(session()->has('loggedInUser')){
            return redirect()->to('/dashboard');
        } else {
            return view('login');
        }
    }

    public function login()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $stored_username = getenv("USERNAME");

        if($stored_username == $username){
            if(password_verify($password, getenv("PASSWORD"))){
                session()->set('loggedInUser', $stored_username);
                return redirect()->to('/dashboard');
            } else {
                session()->setFlashdata('failed', 'Incorrect password provided');
                return redirect()->to('/auth');
            }
        } else {
            session()->setFlashdata('failed', 'Incorrect username provided');
            return redirect()->to('/auth');
        }

        return false;
    }

    public function logout()
    {
        if(session()->has('loggedInUser'))
        {
            session()->remove('loggedInUser');
        }

        return redirect()->to('/auth?access=loggedout')->with('failed','You are logged out');
    }

}
