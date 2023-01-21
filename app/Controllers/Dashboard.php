<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Queries;
use App\Models\APIkeys;
use Codeigniter\Shield\Models\UserModel;

class Dashboard extends BaseController
{
    private $apikeys;
    private $saveSERP;
    private $queries;
    
     /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */

    public function __construct()
    {
        $this->saveSERP = new Queries();
        $this->apikeys = new APIkeys();
        $this->queries = new Queries();
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
            $data['saved_reports'] = $this->fetchSavedReports();
            
            if($this->request->getVar('reportid') and is_numeric($this->request->getVar('reportid')))
            {
                $reportid = intval($this->request->getVar('reportid'));
                $fetchSavedSerpReport = $this->serpRetrieve($reportid);

                if($fetchSavedSerpReport === false)
                {
                    $data['message'] = "Not sure how you got here. The report is not found.";
                    return view("errors/html/report_not_found", $data);
                } 
                else 
                {
                    //$data['serp_report'] = $fetchSavedSerpReport['results'];

                    $data['serp_query'] =  $fetchSavedSerpReport['query'];
                    $data['wordcount'] = $fetchSavedSerpReport['wordcount'];
                    $data['serp_report_php'] = json_decode($fetchSavedSerpReport['results'])->results;
                    $data['report_id'] = $reportid;

                    usort(json_decode($fetchSavedSerpReport['results'])->keywords, function ($a, $b) {
                        return ($a->score > $b->score) ? -1 : 1;
                    });
                    $data['keywords'] = json_decode($fetchSavedSerpReport['results'])->keywords;
                    $data['top_10_titles'] = [];
                    foreach($data['serp_report_php'] as $result){
                        if($result->position < 10){
                            $data['top_10_titles'][] = [
                                "position" => $result->position,
                                "domain" => parse_url($result->link)['host'],
                                "title" => $result->title,
                                "wordcount" => $result->wordcount,
                                "link" => $result->link
                            ];
                        }
                    }
                    $data['related_questions'] = json_decode($fetchSavedSerpReport['results'])->relatedquestions;
                    $data['highlighted_keywords'] = [];
                    foreach($data['serp_report_php'] as $highlighted){
                        if(isset($highlighted->related_results)){
                            foreach($highlighted->related_results as $related){
                                $data['highlighted_keywords'][] = $related->snippet_highlighted_words;
                            }
                        }
                    }
                    $data['highlighted_keywords'] = array_reduce($data['highlighted_keywords'], 'array_merge', array());
                }

                return view('report', $data);
                
            } else {

                return view('list-reports', $data);
            }

        } else {
            $data['username'] = 'Guest';
            $data['login_register'] = true;

            return redirect()->to("/");
        }
    }

    public function delete()
    {
        if (auth()->loggedIn()) 
        {
            $user = new UserModel();
            $username = $user->find(auth()->id())->username;

            if ($this->request->getVar('reportid') and is_numeric($this->request->getVar('reportid'))) 
            {
                $reportid = intval($this->request->getVar('reportid'));
               
                $remove = $this->queries->where('id', $reportid)->where('user_id', auth()->id())->delete();

                if($remove){
                    return redirect()->to('/report?deleted=true');
                } else {
                    return redirect()->to('/report?deleted=false');
                }


            } else {
                return redirect()->to('/report?deleted=false');
            }

        } 
        return redirect()->to('/report');
    }

   private function serpRetrieve($query){
        try{
            $results = $this->queries->where('id', $query)->where('user_id', auth()->id())->first();

            if($results !== NULL AND count($results) > 0 ){
                return $results;
            } else {
                return false;
            }
        } catch(\Exception $e){
            return false;
        }
    }

    private function fetchSavedReports(){
        try{
            $results = $this->queries->query("SELECT id, query, ROUND(wordcount) as wordcount FROM queries WHERE user_id =" . auth()->id())->getResultArray();

            if(count($results) > 0){
                return $results;
            } else {
                return ["Error" => "No saved reports"];
            }
        } catch(\Exception $e){
            return ["Error" => "unexpected error occured"];
        }
    }
}


