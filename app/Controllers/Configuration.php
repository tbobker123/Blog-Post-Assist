<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Settings;

class Configuration extends BaseController
{
    
	public function __construct(){

		$this->settings = new Settings();
		$this->apikeys = new APIKeys();
		helper(['url', 'form']);
	}

	public function index()
    {
    	$data['settings'] = $this->settings->findAll();
		$data['apikeys'] = $this->apikeys->findAll();

		//echo "<pre>"; print_r($data['apikeys']); echo "</pre>"; exit;
		return view('configuration', $data);
    }

    public function update(){

		if( $this->request->getMethod() == "post" AND $this->request->getPost('update_configuration') ){

			/**
			 * Settings
			 */
			
			$outline = $this->request->getPost('outline');
			$topic = $this->request->getPost('topic');
			$serp = $this->request->getPost('serp');
			$section = $this->request->getPost('section');


			$settings_update = [
				'openAI_topic'=> $topic,
				'openAI_outline' => $outline,
				'openAI_section' => $section,
				'serp' => $serp,
			];

			$this->settings->update(0, $settings_update);

			/**
			 * API Keys
			 */

			$openai_key = $this->request->getPost('openai-key');
			$openai_id = $this->request->getPost('openai-id');
			$rapidapi_key = $this->request->getPost('rapidapi-key');
			$rapidapi_id = $this->request->getPost('rapidapi-id');
			$serpapi_key = $this->request->getPost('serpapi-key');
			$serpapi_id = $this->request->getPost('serpapi-id');
			$tinymce_key = $this->request->getPost('tinymce-key');
			$tinymce_id = $this->request->getPost('tinymce-id');

			if(isset($openai_key) && !empty($openai_key)){
				$this->apikeys->update($openai_id, [
					'key' => $openai_key
				]);
			}


			if(isset($rapidapi_key) && !empty($rapidapi_key)){
				$this->apikeys->update($rapidapi_id, [
					'key' => $rapidapi_key
				]);
			}


			if(isset($serpapi_key) && !empty($serpapi_key)){
				$this->apikeys->update($serpapi_id, [
					'key' => $serpapi_key
				]);
			}

			if(isset($tinymce_key) && !empty($tinymce_key)){
				$this->apikeys->update($tinymce_id, [
					'key' => $tinymce_key
				]);
			}

			session()->setFlashdata('config', 'Successfully Updated');

			return redirect()->to('/configuration?updated=success');

		}
	}
}
