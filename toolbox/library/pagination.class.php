<?php
namespace toolbox;
/**
 * DEPRECATED, DO NOT EXTEND! USE DATATABE V2 INSTEAD!
 */
class pagination {

    function __construct(){

    }

    private $total;
    function setTotal($total){
        $this->total = $total;
        $this->calculate();
        return $this;
        return new pagination();
    }

    private $limit;
    function setLimit($limit){
        $this->limit = $limit;
        $this->calculate();
        return $this;
        return new pagination();
    }

    private $pagination_limit = 5;
    function setPaginationLimit($limit){
        $this->pagination_limit = $limit;
        $this->calculate();
        return $this;
        return new pagination();
    }

    private $start = 0;
    function setStart($start){
        $this->start = $start;
        $this->calculate();
        return $this;
        return new pagination();
    }

    private function calculate(){
        if(isset($this->limit) && isset($this->total)){
            $this->total_pages = ceil($this->total/$this->limit);
            $this->current_page = ceil($this->start/$this->limit)+1;
        }

    }

    function getLimit(){
        return $this->limit;
    }

    function getPrevStart(){
        return max($this->start - $this->limit, 0);
    }

    function getPrevClass(){
        return 'getPrevClass';
    }

    function getFirstStart(){
        return 0;
    }

    function getFirstClass(){
        return 'getFirstClass';
    }

    function getLastStart(){
        return $this->total_pages*$this->limit - $this->limit;
    }

    function getLastClass(){
        return 'getLastClass';
    }

    function getNextStart(){
        $next = $this->start + $this->limit;
        if($next >= $this->total){
            return $this->start;
        }
        return $next;
    }

    function getNextClass(){
        return 'getNextClass';
    }

	private $pagination_max = false;
	/**
	 * calculate page to start with
	 */
	private function paginationSetup(){
		if($this->pagination_max !== false){
			return true;
		}

		//get number of pages to add left and right
		$pages_lr = floor($this->pagination_limit/2);

		//find end page
		$this->pagination_max = max(min(($this->getCurrentPage() + $pages_lr), $this->getTotalPages()), $this->pagination_limit);

		//find start page
		$this->pagination_page = max(0, ($this->pagination_max - $this->pagination_limit));


	}

    private $current_page = 1;
    private $pagination_page = 0;
    function nextPage(){
    	$this->paginationSetup();
        if($this->pagination_page++ * $this->limit < $this->total && $this->pagination_page < $this->pagination_max+1){
            return true;
        }

        return false;

    }

    function getPageStart(){
        return $this->pagination_page * $this->getLimit() - $this->getLimit();
    }

    function getPageClass(){
        if($this->getPageNum() == $this->getCurrentPage()){
            return 'active';
        }

        return '';
    }

    function getPageNum(){
        return $this->pagination_page;
    }

    function getCurrentPage(){
        return $this->current_page;
    }

    private $total_pages;
    function getTotalPages(){
        return $this->total_pages;
    }



    /**
     * Creation factory
     * (creates NEW instance each time)
     */
    static function create($name = 'singleton'){
        self::$instances[$name] = new self();
        return new pagination();
    }

    /**
     * Get THE singleton of class instance
     * (creates it if not exists)
     */
    private static $instances = array();
    public static function get($name = 'singleton'){
        if (!isset(self::$instances[$name])) {
            self::$instances[$name] = new self();
        }
        return self::$instances[$name];
        return new pagination();
    }
}
