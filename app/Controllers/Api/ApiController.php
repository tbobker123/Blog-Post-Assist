<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use Orhanerday\OpenAi\OpenAi;
use voku\helper\HtmlDomParser;

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
                'prompt' => "Create a detailed, interesting and informative blog post outline about $prompt",
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
                'prompt' => "Create 15 blog topics or blog post titles about $prompt",
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
                'prompt' => "Expand the blog section in to a detailed professional , witty and clever explanation:  $prompt",
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

    public function searchResults(){

        $query = urlencode($this->request->getVar("query"));

        $query_url = file_get_contents("https://serpapi.com/search.json?engine=google&q=$query&api_key=344d53341bdd80e5bb74bb1b41da3cb2f2b93ff78d90a0fca6e1345e77b0c12d&num=25&gl=uk");
        
        $process = $this->processSearchResults($query_url);
        
        return  $this->response->setJSON([
            "results" => $process,
            "wordcount" => ($this->total_word_count/$this->total_search_results) * 1.3
        ]);
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

        return $searchResults;
 
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
