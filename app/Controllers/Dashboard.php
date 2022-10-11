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

        //echo "<pre>"; print_r($data); echo "</pre>"; exit;
        return view('dashboard', $data);
    }

    public function saveSERPResults(){

		if( $this->request->getMethod() == "post" AND $this->request->getPost('save_serps') ){
			
			$query = $this->request->getPost('query');
			$results = $this->request->getPost('results');
			$relatedquestions = $this->request->getPost('relatedquestions');
			$wordcount = $this->request->getPost('wordcount');

			$data = [
				'query'=> $query,
				'results' => $results,
				'relatedquestions' => $relatedquestions,
				'wordcount' => $wordcount,
			];

			$this->saveSERP->update($data);

			return $this->response([
                'query' => $query,
                'saved' => 'true'
            ]);

		} else {
            return $this->response([
                'error' => 'failure'
            ]);
        }
	}
}


