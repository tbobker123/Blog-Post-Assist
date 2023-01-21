<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\APIkeys;
use App\Models\Queries;
use Codeigniter\Shield\Models\UserModel;
use PhpParser\Node\Stmt\TryCatch;
use PHPUnit\TextUI\CliArguments\Exception;
use Orhanerday\OpenAi\OpenAi;

class ContentEditor extends BaseController
{
    private $apikeys;
    private $queries;

    private $query;

    public function __construct(){
        $this->apikeys = new APIKeys();
        $this->queries = new Queries();
    }
    public function index()
    {
        if (auth()->loggedIn()) {

            if ($this->request->getVar('reportid') and is_numeric($this->request->getVar('reportid'))) {

                try {

                    $reportid = intval($this->request->getVar('reportid'));
                    $loadreport = $this->queries->where('id', $reportid)->where('user_id', auth()->id())->first();
                    
                    if($loadreport == null){
                        $data['message'] = "Report not found.";
                        return view("errors/html/report_not_found", $data);
                    }


                    usort(json_decode($loadreport['results'])->keywords, function ($a, $b) {
                        return ($a->score > $b->score) ? -1 : 1;
                    });

                    $this->query = json_decode($loadreport['results'])->keywords;

                    $data['keywords'] = $this->query;
                    $data['wordcount'] = $loadreport['wordcount'];
                    $data['query'] = str_replace("+", " ", $loadreport['query']);
                    $data['id'] = $loadreport['id'];

                    /*$outline_title = $this->openAIPrompt();

                    if($outline_title !== false){
                        $data['title_outline'] = $outline_title;
                    }*/

                    return view("content-editor", $data);

                } catch (Exception $e){
                    $data['message'] = "An unexpected error just got triggered.";
                    return view("errors/html/report_not_found", $data);
                }
            }
        }
    }
}