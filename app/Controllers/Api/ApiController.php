<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use Orhanerday\OpenAi\OpenAi;
use voku\helper\HtmlDomParser;
use App\Models\Settings;
use App\Models\APIKeys;
use App\Models\SaveBlogPost;

class ApiController extends ResourceController
{
    private $total_word_count = 0;
    private $total_search_results = 0;

    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */

     public function __construct(){
        $this->settings = model('Settings');
        $this->apikeys = model('apikeys');
        $this->blogdrafts = model('SaveBlogPost');
        $this->query = $this->settings->find(0);
     }

    public function index()
    {
        //
    }

    public function serpAPIAccountInfo(){

        //$serpapi_key = getenv("SERPAPI");
        $api_key = $this->apikeys->where('name', 'serpapi')->first();
        $serpapi_key = $api_key['key'];

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://serpapi.com/account?api_key=$serpapi_key");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $headers = [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
                'User-Agent:  Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.0.0 Safari/537.36',
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            // $output contains the output string
            $output = curl_exec($ch);

            $output = json_decode($output);
    
            // close curl resource to free up system resources
            curl_close($ch);    
            
            return  $this->response->setJSON([
                "plan" => $output->plan_name,
                "searches" => $output->searches_per_month,
                "remaining" => $output->plan_searches_left,
                "usage" => $output->this_month_usage,
                "total_remaining" => $output->total_searches_left
            ]);
            
        } catch (\Throwable $th) {
            return $this->response->setJson([
                'error' => 'request not successful',
                'code' => $th
            ]);
        }       
    }

    public function openAIPrompt(){

        $openai_api_key = $this->apikeys->where('name','openai')->first();
        $open_ai = new OpenAi($openai_api_key['key']);


        $prompt = $this->request->getVar("prompt");
        $type = $this->request->getVar("type");

        /**
         * OpenAI default settings
         */
        $engine = 'text-davinci-002';

        try{

             switch($type){
                case "outline":
                    $openAIConfig = json_decode($open_ai->complete([
                        'engine' => $engine,
                        'prompt' => $this->query['openAI_outline'] . $prompt,
                        'temperature' => 0.7,
                        'max_tokens' => 4000,
                        'presence_penalty' => 0.6,
                        'n' => 5,
                     ]));
                    break;
                case "topics":
                    $openAIConfig = json_decode($open_ai->complete([
                        'engine' => $engine,
                        'prompt' => $this->query['openAI_topic'] . $prompt,
                        'temperature' => 0.83,
                        'max_tokens' => 4000,
                        'presence_penalty' => 0.6,
                     ]));
                    break;
                case "section":
                    $openAIConfig = json_decode($open_ai->complete([
                        'engine' => $engine,
                        'prompt' => $this->query['openAI_section'] . $prompt,
                        'temperature' => 0.53,
                        'max_tokens' => 4000,
                        'presence_penalty' => 0,
                     ]));
                    break;
                case "open":
                    $openAIConfig = json_decode($open_ai->complete([
                        'engine' => $engine,
                        'prompt' => $prompt,
                        'temperature' => 0.53,
                        'max_tokens' => 4000,
                        'presence_penalty' => 0,
                     ]));
                    break;                    
             }

             unset($open_ai);

            return $this->response->setJSON([
                'result' => $openAIConfig->choices[0]->text,
                'csrf_hash' => csrf_hash()
            ]);

        } catch(\Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }

    public function fetchSavedReports(){
        $savedReports = new \App\Models\queries();
        try{
            $results = $savedReports->query("SELECT id, query, ROUND(wordcount) as wordcount FROM queries");
            if($results->getNumRows() > 0){
                return $this->response->setJSON($results->resultArray);
            } else {
                return $this->response->setJSON(["Error" => "No saved reports"]);
            }
        } catch(\Exception $e){
            return $this->response->setJSON(array(
                'error' => $e->getMessage()
            ));
        }
    }

    public function deleteSavedReport(){
        $reportid = $this->request->getVar("reportid");
        $savedReports = new \App\Models\queries();
        try{
            $savedReports->query("DELETE FROM queries WHERE id='$reportid'");
            if($savedReports->affectedRows() > 0){
                return $this->response->setJSON(["status" => "deleted", 'csrf_hash' => csrf_hash()]);
            } else {
                return $this->response->setJSON(["status" => "not deleted", 'csrf_hash' => csrf_hash()]);
            }
        } catch(\Exception $e){
            return $this->response->setJSON(array(
                'error' => $e->getMessage(),
                'csrf_hash' => csrf_hash()
            ));
        }      
    }

    public function searchResults(){
        /**
         * Fetch total results to return
         */
        $serp_results = $this->query['serp'];

        /**
         * Load saved report or run new query
         */
        $query = $this->request->getVar("query") ?? false;
        $LoadSavedReport = $this->request->getVar("query_id") ?? false;

        /**
         * Geo location selected to get the results from
         */
        $location = urlencode($this->request->getVar("location"));

        /**
         * Load the SerpAPI key from the database
         */
        $api_key = $this->apikeys->where('name', 'serpapi')->first();
        $serpapi_key = $api_key['key'];


        /**
         * If the query_id param exists then try and load a saved report
         */
        if($LoadSavedReport !== false){
            $fetchPreviousSERPs = $this->serpRetrieve($LoadSavedReport);
            if(is_array($fetchPreviousSERPs)){
                return $this->response->setJSON([
                    'error' => $fetchPreviousSERPs['error'],
                    'csrf_hash' => csrf_hash()
                ]);
            } else {
                $result = json_decode($fetchPreviousSERPs->resultArray[0]['results']);
                $result->csrf_hash = csrf_hash();
                $result->blogposts = $this->fetchSavedBlogPosts($LoadSavedReport);
                return $this->response->setJSON($result);
            }
        }

        /**
         * Try and run a new API request to SerpAPI
         */
        try {

            $query = urlencode($query);
            $url ="https://serpapi.com/search.json?engine=google&device=desktop&q=$query&api_key=$serpapi_key&num=$serp_results&gl=$location";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $headers = [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
                'User-Agent:  Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.0.0 Safari/537.36',
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            // $output contains the output string
            $output = curl_exec($ch);
    
            // close curl resource to free up system resources
            curl_close($ch);   

            $this->writeContentToDisk();
            $process = $this->processSearchResults($output);

            $keywordExtractorProcess = $this->rapidAPIExtractKeywords();

            if($keywordExtractorProcess !== false){
                $keywords = json_decode($keywordExtractorProcess);
            } else {
                $keywords = [
                    'error' => 'false'
                ];
            }
    
            $serp_response = [
                "results" => $process['searchResults'],
                "wordcount" => ($this->total_word_count/$this->total_search_results) * 1.3,
                "relatedquestions" => $process['relatedQuestions'],
                "keywords" => $keywords,
                "csrf_hash" => csrf_hash()
            ];

            $status = $this->serpSave($query, $serp_response);

            if($status !== true){
                return $this->response->setJson([
                    'error' => $status,
                    'csrf_hash' => csrf_hash()
                ]);
            }
            return  $this->response->setJSON($serp_response);
            
        } catch (\Exception $e) {
            return $this->response->setJson([
                'error' => $e->getMessage(),
                'csrf_hash' => csrf_hash()
            ]);
        }

    }

    private function serpRetrieve($query){
        $retrieveSerp = new \App\Models\queries();
        try{
            $results = $retrieveSerp->query("SELECT results FROM queries WHERE id='$query'");
            if($results->getNumRows() > 0){
                return $results;
            } else {
                return false;
            }
        } catch(\Exception $e){
            return array(
                'error' => $e->getMessage()
            );
        }
    }

    private function serpSave($query, $response){
        $data = [
            'query'=> $query,
            'results' => json_encode($response),
            'wordcount' => $response['wordcount'],
            'relatedquestions' => json_encode($response['relatedquestions'])
        ];
        $SaveSERP = new \App\Models\queries();
        try{
            $SaveSERP->insert($data);
            return true;
        }
        catch(\Exception $e){
            return $e->getMessage();
        }
    }

    private function fetchSavedBlogPosts($keyword_id){
        $results = $this->blogdrafts->where("query_id", $keyword_id)->findAll();
        if(count($results) > 0){
            return $results;
        } else {
            return ['error' => 'no blog posts found for keyword ' . $keyword_id];
        }
    }


    public function saveBlogPostDraft(){

        if($this->request->getMethod() == "post")
        {
            // {id: '1', query_id: '32', title: 'Best Courier PHP Scripts on Codecanyon', text: '<h2>What is a courier PHP script</h2>'}

            $post_id = $this->request->getVar('id');

            $data = [
                'query_id' => $this->request->getVar('query_id'),
                'title' => $this->request->getVar('title'),
                'text' => $this->request->getVar('text'),
            ];

            /**
             * If fields are empty then return an error message
             */
            if(empty($data['query_id'])){
                return $this->response->setJSON([
                    'status' => 'No keyword selected for blog post to save',
                    'csrf_hash' => csrf_hash()
                ]);
            }

            /**
             * UPDATE BLOG POST DRAFT
             * If a post_id exists, then we must want to update
             * a already existing draft post.
             */
            if(!empty($post_id))
            {
                if($this->blogdrafts->update($post_id, $data) === true){
                    return $this->response->setJSON([
                        'status' => "successfully updated blog post draft " . $data['title'],
                        'id' => 'updated',
                        'csrf_hash' => csrf_hash()
                    ]);
                }
            } 
            
            /**
             * CREATE A NEW BLOG POST DRAFT
             * If there isn't any post_id, then this must be a new
             * draft post to save in the database
             */
            if(empty($post_id))
            {
                if(is_numeric($this->blogdrafts->insert($data))){
                    return $this->response->setJSON([
                        'status' => "Blog post draft successfully created",
                        'id' => $this->blogdrafts->getInsertID(),
                        'csrf_hash' => csrf_hash()
                    ]);
                } else {
                    return $this->response->setJSON([
                        'status' => "Draft not saved",
                        'csrf_hash' => csrf_hash()
                    ]);                      
                };
            }
            
        }

    }

    public function deleteBlogPostDraft(){
        if($this->request->getMethod() == "post"){
            $post_id = $this->request->getVar("post_id");
            $status = $this->blogdrafts->delete($post_id);
            return $this->response->setJSON([
                "status" => $status,
                "id" => "updated",
                'csrf_hash' => csrf_hash(),
            ]);
        }
    }

    public function rapidAPIExtractKeywords(){

        $api_key = $this->apikeys->where('name','rapidapi')->first();
        $rapidapi_key = $api_key['key'];

        $curl = curl_init();

        $text = file_get_contents(ROOTPATH . 'text.txt');
        $text = str_replace("\n", '', $text);  

        $payload = json_encode( array(
            "language" => "en",
            "deduplication_threshold" => 0.9,
            "deduplication_algo" => "seqm",
            "max_ngram_size" => 3,
            "number_of_keywords" => 40,
            "text" => $text,
        ) );

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://extract-keywords1.p.rapidapi.com/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "X-RapidAPI-Host: extract-keywords1.p.rapidapi.com",
                "X-RapidAPI-Key:" .  $rapidapi_key
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return false;
        } else {
            return $response;
        }
    }

    private function processSearchResults($serpapi){

        $results = json_decode($serpapi);
        $searchResults = $results->organic_results;
        $alsoSearchedFor = $results->inline_people_also_search_for[0]->items ?? [];

        foreach($searchResults as $item){

            $wordCountAndHeadings = $this->getWordCountAndHeading($item->link);
            $this->total_search_results = $this->total_search_results + 1;

            if($wordCountAndHeadings === false){
                $item->wordcount = 0;
                $item->headings = 0;
            } else {
                $item->wordcount = $wordCountAndHeadings['wordcount'];
                $item->headings = $wordCountAndHeadings['headings'];

                $this->total_word_count = $this->total_word_count + $wordCountAndHeadings['wordcount'];
            }

        }

        return [
            "searchResults" => $searchResults,
            "relatedQuestions" => (property_exists($results, 'related_questions')) ? $results->related_questions : ["No related questions"],
            "alsoSearchedFor" => $alsoSearchedFor
        ];
 
    }

    private function getWordCountAndHeading($url){
        
        try {
            $html = HtmlDomParser::file_get_html($url);

            $text = "";
            $headings = Array();
    
            foreach ($html->find('h1,h2,h3,h4,p') as $e) {
                //$text .= " " . $this->strip($e->text, false);
                $text .= " " . $this->strip($e->text,false);

                if($e->nodeName != 'p') $headings[$e->nodeName][] = trim($e->text);
            }

            $wordcount = str_word_count($text);

            $this->writeContentToDisk($text, false);
    
            return [
                "wordcount" => $wordcount,
                "headings"  => $headings
            ];

        } catch (\Throwable $th) {
           return false;
        }

    }

    private function strip($text,$keepLines=true){
        $text = preg_replace('/(<[^>]*) style=("[^"]+"|\'[^\']+\')([^>]*>)/i', '$1$3', $text);
    
        if(!$keepLines)
        {
            $text = str_replace(array("\t","\r","\n"),"",$text);
            $text = preg_replace('/\s+/',' ',$text);
        }
        else
        {
            $text = str_replace('  ',' ',$text);
        }   
        return trim($text);
    }

    private function writeContentToDisk($text ="", $mode=true){
        if($mode === true){
            file_put_contents(ROOTPATH . "text.txt", "");
        }
        $myfile = fopen(ROOTPATH . "text.txt", "a");
        fwrite($myfile, $text);
        fclose($myfile);
    }

    public function fetchLocations(){

        $query = urlencode($this->request->getVar("q"));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://serpapi.com/locations.json?q=$query&limit=5");

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

        // $output contains the output string
        $output = curl_exec($ch);

        // close curl resource to free up system resources
        curl_close($ch); 
        
        return $this->response->setJSON($output);
    }

    /**
     * Add or update a model resource, from "posted" properties
     *
     * @return mixed
     */
    public function update($id = null)
    {
        //
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        //
    }
}
