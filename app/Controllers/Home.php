<?php

namespace App\Controllers;

use Codeigniter\Shield\Models\UserModel;

class Home extends BaseController
{
    public function index()
    {

        if(auth()->loggedIn())
        {
            $user = new UserModel();
            $username = $user->find(auth()->id())->username;
            $data['username'] = $username;
            $data['login_register'] = false;
        } else {
            $data['username'] = 'Guest';
            $data['login_register'] = true;
        }

        return view('homepage', $data);
    }

}