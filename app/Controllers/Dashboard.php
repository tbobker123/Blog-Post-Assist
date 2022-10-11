<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    
     /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */

    public function __construct(){
        $this->saveSERP = model('queries');
        $this->apikeys = model('APIKeys');
     }   
    
    public function index()
    {
        $keys = $this->apikeys->findAll();
        
        $data['update_keys'] = false;
        $data['keys'] = $keys;

        foreach($keys as $key){
            if($key['key'] == ""){
                $data['update_keys'] = true;
            }
        }
        $data['tinymce_key'] = $this->apikeys->where('name', 'tinymce')->first()['key'];
        return view('dashboard', $data);
    }
}


