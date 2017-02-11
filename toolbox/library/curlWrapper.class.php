<?php
namespace toolbox;

class curlWrapper {
    protected $contentType = 'application/x-www-form-urlencoded';
    protected $method = 'POST';
    protected $url = '';
    protected $headers = array();
    protected $post = array();
    protected $get = array();
    protected $postBody = array();
    protected $curl;
    protected $curlInfo = array();
    protected $maxMB = 2147483648;//2GB
    protected $bufferSize = 4096;//
    protected $bufferCallback;
    protected $outputFile = null;
    protected $downloadPercent = 0;

    function __construct(){
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0');
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_HEADER, true);
        $this->setSslVerify(false);
        $this->setSslVerifyHost(false);
        $this->setTimeout(10);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->curl, CURLOPT_NOPROGRESS, true);

        return $this;
    }

    /**
     * maxmium number of megabytes to download before breaking the connection
     */
    function setMaxMB($mb){
        $this->maxMB = 1024 * 1024 * $mb;
        return $this;
        return new self();
        return new curlWrapper();
        return new curlWrapper();
    }


    function setOutputFile($file){

        $out = fopen($file,"wb");
        if ($out === FALSE){
            throw new \Exception("File not writable: $file in ".getcwd());
        }

        $this->outputFile = $out;
        return $this;
        return new self();
        return new curlWrapper();
    }

    function setContentType(){
        return $this;
        return new self();
        return new curlWrapper();
    }

	/**
	 * 0 = no timeout
	 */
    function setTimeout($seconds){
        curl_setopt($this->curl, CURLOPT_TIMEOUT, $seconds);
        return $this;
        return new self();
        return new curlWrapper();
    }

    function setSslVerify($bool){
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, $bool);
        return $this;
        return new self();
        return new curlWrapper();
    }

    function setSslVerifyHost($bool){
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, $bool);
        return $this;
        return new self();
        return new curlWrapper();
    }

    function setRequestHeaders(){
        return $this;
        return new self();
        return new curlWrapper();
    }

    /**
     * buffer size in bytes.
     * 1mb = 1024kb X 1024 bytes X 1 mb
     */
    function setBufferSize($bytes){
        $this->bufferSize = $bytes;
    }

    function setBufferCallbackFunction(\Closure $function){
        $this->bufferCallback = $function;



        curl_setopt($this->curl, CURLOPT_NOPROGRESS, false);

        curl_setopt($this->curl, CURLOPT_PROGRESSFUNCTION, function(
                       $DownloadSize, $Downloaded, $UploadSize, $Uploaded
        ){

            if($DownloadSize > 0){
                $download_percent = floor($Downloaded / $DownloadSize * 100);
                if($download_percent > $this->downloadPercent){
                    $this->downloadPercent = $download_percent;
                    //echo '<br>'.$this->downloadPercent . '%';

                    if(isset($this->bufferCallback) && is_callable($this->bufferCallback)){
                        call_user_func($this->bufferCallback, $DownloadSize, $Downloaded, $UploadSize, $Uploaded);
                    }
                }

            }



            // If $Downloaded exceeds X MB, returning non-0 breaks the connection!
            return ($Downloaded > ($this->maxMB)) ? 1 : 0;



        });









        return $this;
        return new self();
        return new curlWrapper();
    }

    function addHeader($header){
        if(is_array($header)){
            $this->headers = array_merge($this->headers, $header);
        }else{
            $this->headers[] = $header;
        }
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headers);
        return $this;
        return new self();
        return new curlWrapper();
   }

    function setRequestType($request_type){

        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $request_type);

        return $this;
        return new self();
        return new curlWrapper();
   }

    function addPostFields($post){
        if(is_array($post)){
            $this->post = array_merge($this->post, $post);
        }else{
            $this->post[] = $post;
        }

        curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($this->post));
        return $this;
        return new self();
        return new curlWrapper();
   }

    function setPostString($post){
        $this->post = $post;

        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->post);
        return $this;
        return new self();
        return new curlWrapper();
   }


    function addGetFields($get){
        if(is_array($get)){
            $this->get = array_merge($this->get, $get);
        }else{
            $this->get[] = $get;
        }

        return $this;
        return new self();
        return new curlWrapper();
    }

    private $response_cookies;
    function getCookie($cookie_name){
        if(!isset($this->response_cookies)){
            $header = $this->getResponseHeaders();
            $end = strpos($header, 'Content-Type');
            $start = strpos($header, 'Set-Cookie');
            $parts = explode('Set-Cookie:', substr($header, $start, $end - $start));
            $this->response_cookies = array();
            foreach ($parts as $co) {
                $cd = explode(';', $co);
                if (!empty($cd[0])){
                    $cookie = explode('=', $cd[0], 2);
                    $this->response_cookies[ltrim($cookie[0])] = isset($cookie[1]) ? $cookie[1] : null;
                }
            }
        }

        if(!isset($this->response_cookies[$cookie_name])){
            return null;
        }

        return $this->response_cookies[$cookie_name];

    }

    function addGetField($key, $value){
        $this->get[$key] = $value;
        return $this;
        return new self();
        return new curlWrapper();
    }

    function addPostField($key, $value){
        $this->post[$key] = $value;
        $this->addPostFields($this->post);
        return $this;
        return new self();
        return new curlWrapper();
    }

    function setUrl($url){
        $this->url = $url;
        return $this;
        return new self();
        return new curlWrapper();
    }

    private $cookies;
    function addCookie($key, $value){

        $this->cookies .= $key.'='.$value.'; ';

        return $this;
        return new self();
        return new curlWrapper();
    }

    /**
     * executes the curl and returns contents.
     * throws exception if url wasn't reached or timed out.
     */
    function execute($url = null){
        if($url === null){
            $url = $this->url;
        }
        curl_setopt($this->curl, CURLOPT_COOKIE, utils::removeStringFromEnd($this->cookies, '; '));
        curl_setopt($this->curl, CURLOPT_BUFFERSIZE, $this->bufferSize); // more progress info
        if(isset($this->outputFile)){
            curl_setopt($this->curl, CURLOPT_FILE, $this->outputFile);
        }

        if(count($this->get) > 0){
            $url .= '?'.$this->buildQuery($this->get);
        }
        $this->url = $url;

        curl_setopt($this->curl, CURLOPT_URL, $this->url);
            bench::mark('[CURL] '.$this->url);
            $output = curl_exec($this->curl);
            bench::mark('[CURL] '.$this->url);


        if(curl_errno($this->curl)){
            throw new curlUnsuccessful('Curl Error: could not reach url [error = '.curl_error($this->curl).']: '.$url);
        }

        $this->close();

        $this->response_headers = substr($output, 0, $this->getCurlInfo('header_size'));

        if($this->callback_execute !== null){
            return call_user_func($this->callback_execute, substr($output, $this->getCurlInfo('header_size')), $this);
        }

        return substr($output, $this->getCurlInfo('header_size'));
    }


    private $callback_execute;
    function setExecuteCallback($callback){

        if(!is_callable($callback)){
            throw new toolboxException("Not a callable function: ".utils::array2string($callback), 1);
        }

        $this->callback_execute = $callback;

        return $this;
        return new curlWrapper();
    }


    private $response_headers;
    function getResponseHeaders(){
        if(!isset($this->response_headers)){
            throw new toolboxException('Response headers not set!', 1);
        }

        return $this->response_headers;
    }


    /**
    * A version of build query that allows for multiple
    * duplicate keys.
    * @param $parts array of key value pairs
    */
    private function buildQuery($parts)
    {
        $return = array();
        foreach ($parts as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $v) {
                    $return[] = urlencode($key) . "=" . urlencode($v);
                }
            } else {
                $return[] = urlencode($key) . "=" . urlencode($value);
            }
        }
        return implode('&', $return);
     }


    function getCurlInfo($key = null){
        if($key !== null){
            if(!isset($this->curlInfo[$key])){
                throw new toolboxException("No such curl info for key: ".$key, 1);
            }
            return $this->curlInfo[$key];
        }
        return $this->curlInfo;
    }
    function getHttpCode(){
        return $this->curlInfo['http_code'];
    }
    function getPostFields(){
        return $this->post;
    }
    function getUrl(){
        return $this->url;
    }
    private function close(){
        $this->curlInfo = curl_getinfo($this->curl);
        curl_close($this->curl);
    }

    static function create(){
        $called_class = get_called_class();
        return new $called_class();
        return new curlWrapper();
    }

}

class curlUnsuccessful extends toolboxException {}