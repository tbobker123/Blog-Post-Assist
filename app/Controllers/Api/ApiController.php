<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use Orhanerday\OpenAi\OpenAi;
use voku\helper\HtmlDomParser;
use App\Models\Settings;

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
        $this->query = $this->settings->find(0);
     }

    public function index()
    {
        //
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function outline($prompt = "live streaming")
    {
        $open_ai = new OpenAi(getenv("OPENAI"));

        $prompt = $this->request->getVar("prompt");

        try{
            $complete = json_decode($open_ai->complete([
                'engine' => 'text-davinci-002',
                'prompt' => $this->query['openAI_outline'] . $prompt,
                'temperature' => 0.7,
                'max_tokens' => 4000,
                'frequency_penalty' => 0,
                'presence_penalty' => 0.6,
             ]));
             
             unset($open_ai);

            return $this->response->setJSON([
                'result' => $complete->choices[0]->text
            ]);

        } catch(Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

    }

    /**
     * Return a new resource object, with default properties
     *
     * @return mixed
     */
    public function topics($prompt = 'live streaming')
    {
        $open_ai = new OpenAi(getenv("OPENAI"));

        $prompt = $this->request->getVar("prompt");

        try{
            $complete = json_decode($open_ai->complete([
                'engine' => 'text-davinci-002',
                'prompt' => $this->query['openAI_topic'] . $prompt,
                'temperature' => 0.83,
                'max_tokens' => 4000,
                'frequency_penalty' => 0,
                'presence_penalty' => 0.6,
             ]));

             //$outline = explode("\n", trim($complete->choices[0]->text));

             unset($open_ai);

            return $this->response->setJSON([
                'result' => $complete->choices[0]->text
            ]);


        } catch(Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }

    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return mixed
     */
    public function section($prompt = 'live streaming')
    {
        $open_ai = new OpenAi(getenv("OPENAI"));

        $prompt = $this->request->getVar("prompt");

        try{
            $complete = json_decode($open_ai->complete([
                'engine' => 'text-davinci-002',
                'prompt' => $this->query['openAI_section'] . $prompt,
                'temperature' => 0.53,
                'max_tokens' => 4000,
                'frequency_penalty' => 0,
                'presence_penalty' => 0,
             ]));

             $outline = $complete->choices[0]->text;

             unset($open_ai);

            return $this->response->setJSON([
                'result' => $outline
            ]);

        } catch(Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }

    public function searchResults()
    {
        $serp_results = $this->query['serp'];
        $query = urlencode($this->request->getVar("query"));
        $location = urlencode($this->request->getVar("location"));
        $serpapi_key = getenv("SERPAPI");

        $url ="https://serpapi.com/search.json?engine=google&device=desktop&q=$query&api_key=$serpapi_key&num=$serp_results&gl=$location";
    
        try {
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
            
            $process = $this->processSearchResults($output);
    
            $serp_response = [
                "results" => $process['searchResults'],
                "wordcount" => ($this->total_word_count/$this->total_search_results) * 1.3,
                "relatedquestions" => $process['relatedQuestions']
            ];

            //echo $url;
            
            return  $this->response->setJSON($serp_response);
            
        } catch (\Throwable $th) {
            return $this->response([
                'error' => 'request not successful',
                'code' => $th
            ]);
        }

    }

    private function processSearchResults($serpapi){

        $results = json_decode($serpapi);
        $searchResults = $results->organic_results;

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
            "relatedQuestions" => (property_exists($results, 'related_questions')) ? $results->related_questions : ["No related questions"]
        ];
 
    }

    private function serpSave($query, $response){

    }

    private function getWordCountAndHeading($url)
    {
        try {

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);

            //return the transfer as a string
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

            // $output contains the output string
            $output = curl_exec($ch);

            // close curl resource to free up system resources
            curl_close($ch); 

            $html = HtmlDomParser::str_get_html($output);

            $text = "";
            $headings = Array();
    
            foreach ($html->find('h1,h2,h3,h4,p') as $e) {
                $text .= $e->textContent;
                if($e->nodeName != 'p') $headings[$e->nodeName][] = trim($e->textContent);
            }

            $wordcount = str_word_count($text);
    
            return [
                "wordcount" => $wordcount,
                "headings"  => $headings
            ];

        } catch (\Throwable $th) {
           return false;
        }

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
