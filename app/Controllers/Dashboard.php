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
        //$this->savedSERPs = $this->saveSERP->findAll();
     }   
    
    public function index()
    {
        $data['tinymce'] = getenv("TINYMCE");
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


