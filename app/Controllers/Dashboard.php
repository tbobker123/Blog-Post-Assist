<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Queries;
use App\Models\APIKeys;

class Dashboard extends BaseController
{
    
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


