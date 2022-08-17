<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Settings;

class Configuration extends BaseController
{
    
	public function __construct(){

		$this->settings = model('Settings');
		helper(['url', 'form']);
	}

	public function index()
    {
    	$data['settings'] = $this->settings->findAll();
		return view('configuration', $data);
    }

    public function update(){

		if( $this->request->getMethod() == "post" AND $this->request->getPost('update_configuration') ){
			
			$outline = $this->request->getPost('outline');
			$topic = $this->request->getPost('topic');
			$serp = $this->request->getPost('serp');
			$section = $this->request->getPost('section');

			$data = [
				'openAI_topic'=> $topic,
				'openAI_outline' => $outline,
				'openAI_section' => $section,
				'serp' => $serp,
			];

			$this->settings->update(0, $data);

			session()->setFlashdata('config', 'Successfully Updated');

			return redirect()->to('/configuration?updated=success');

		}
	}
}
