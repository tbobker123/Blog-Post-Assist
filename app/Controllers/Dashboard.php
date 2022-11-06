<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Queries;
use App\Models\APIKeys;
use Codeigniter\Shield\Models\UserModel;

class Dashboard extends BaseController
{
    private $apikeys;
    private $saveSERP;
    
     /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */

    public function __construct(){
        $this->saveSERP = new Queries();
        $this->apikeys = new APIKeys();
     }   
    
    public function index()
    {
        $keys = $this->apikeys->where('user_id', auth()->id())->findAll();
        
        $data['update_keys'] = false;
        $data['keys'] = $keys;

        foreach($keys as $key){
            if($key['key'] == ""){
                $data['update_keys'] = true;
            }
        }
        $data['tinymce_key'] = $this->apikeys->where('name', 'tinymce')->first()['key'];

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


        return view('dashboard', $data);
    }

    public function listSavedQueries(){
        
    }
}


