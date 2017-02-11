<?php
namespace toolbox;

class datatableV2 extends page {

	function __construct(){
		parent::__construct();

		$start = isset($_GET['start']) ? (int)$_GET['start'] : 0;
		$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
		$sort = isset($_GET['sort']) ? (int)$_GET['sort'] : null;
		$sort_dir = isset($_GET['sort_dir']) ? $_GET['sort_dir'] : false;
		$search = isset($_GET['search']) ? $_GET['search'] : false;

		$this->setMainView('elements/datatable/container.php')
		    ->setStart($start)
		    ->setLimit($limit)
			->enableSort()
			->enableSearch()
			->setSearch($search)
		    ->setSort($sort, $sort_dir)
            ->addView('elements/datatable/pagination.php', 'pagination')
            ->addView('elements/datatable/no_data.php', 'no_data')
            ->setTableClass('style4 shrink-width')
            ->setPaginationLink($_SERVER['REQUEST_URI'])
            ->setPaginationClass('style1')
            ->setSortClass('')
            ->set('datatable_id', utils::getRandomString())
            ->setResizable(false)
            ->setContainerClass('')
            ->setPaginationLoader('#' . $this->datatable_id)
            ->setPaginationDestination('#' . $this->datatable_id)
            ->set('post_data', '');

	}

	public $sqlSelect = '';
	function setSelect($sql){
		$this->sqlSelect = 'SELECT SQL_CALC_FOUND_ROWS ' . $sql;
		//$this->sqlSelect = 'SELECT '.$sql;

		return $this;
		return new datatableV2();
	}

	function setShow($sql){
		$this->sqlSelect = 'SHOW ' . $sql;
		//$this->sqlSelect = 'SELECT '.$sql;

		return $this;
		return new datatableV2();
	}

	public $sqlFrom = '';
	function setFrom($sql){
		$this->sqlFrom = 'FROM ' . $sql;

		return $this;
		return new datatableV2();
	}

	public $sqlWhere = '';
	function setWhere($sql){
		$this->sqlWhere = 'WHERE ' . $sql;

		return $this;
		return new datatableV2();
	}

	private $sqlOrder = '';
	public function setOrderBy($sql){
		$this->sqlOrder = 'ORDER BY ' . $sql;

		return $this;
		return new datatableV2();
	}

	public $sqlGroup = '';
	function setGroupBy($sql){
		$this->sqlGroup = 'GROUP BY ' . $sql;

		return $this;
		return new datatableV2();
	}

	public $colDefinitions = array();
	function defineCol($field_name, $display_name, $callbackFunction = null){
		$this->colDefinitions[] = array('field_name' => $field_name, 'display_name' => $display_name, 'callback' => $callbackFunction);

		return $this;
		return new datatableV2();
	}

    function setResizable($bool = true){
        $this->set('resizable', $bool);

        return $this;
        return new datatableV2();
    }

	function getSort($col){
		/*if(!$this->isSortable($col)){
			return false;
		}*/

		$sort_dir = $this->getColSetting($col, 'sort_dir');
		if($sort_dir === null){
			return 'none';
		}

		return $sort_dir;

	}

	private $sort_col, $sort_dir;
	function setSort($col, $dir, $overwrite = false){
	    if($col === null){
	        return $this;
	    }
		//if($this->isSortable($col)){
			if(!in_array($dir, array('desc', 'asc', 'none'))){
				$dir = 'none';
			}

            if($overwrite !== true && $this->getColSetting($col, 'sort_dir') !== null){
                return $this;
            }
            foreach($this->colSettings as $col_name => &$values){
                if($overwrite === false && isset($values['sort_dir'])){
                    return $this;
                }
                unset($values['sort_dir']);
            }

            $this->sort_col = $col;
            $this->sort_dir = $dir;
			$this->setColSetting($this->sort_col, 'sort_dir', $this->sort_dir);
		//}

		return $this;
		return new datatableV2();
	}

	private $search_queries = array();
	function setSearch($col, $value = null){
		if(is_array($col)){
			if(!empty($col)){
				foreach ($col as $key => $value){
					if($value !== ''){
						$this->search_queries[$key] = $value;
					}
				}
			}
		}elseif($col === false || $col === null){

		}elseif($value !== ''){
			$this->search_queries[$col] = $value;
		}

		return $this;
		return new datatableV2();
	}

	function getSearch($col){
		if(isset($this->search_queries[$col])){
			return $this->search_queries[$col];
		}

		return '';
	}

	public $search = false;
	public $search_overrides = array();
	function enableSearch($cols = null, $bool = true){
	    if(is_bool($cols)){
            $this->search = $cols;
	    }elseif($cols === null){
			$this->search = true;
		} elseif(is_array($cols)){
			$this->search = $cols;
		} else {
			if($bool){
				$this->search_overrides[$cols] = true;
			} else {
				$this->search_overrides[$cols] = false;
			}
		}

		return $this;
		return new datatableV2();
	}

	function isSearchable($col){
		if(isset($this->search_overrides[$col])){
			return $this->search_overrides[$col];
		}

		if($this->search === false){
			return false;
		}

		if($this->search !== true && !in_array($col, $this->search)){
			return false;
		}

		return true;
	}


	public $sort = false;
	public $sort_overrides = array();
	function enableSort($cols = null, $bool = true){
		if(is_bool($cols)){
            $this->sort = $cols;
        }elseif($cols === null){
			$this->sort = true;
		} elseif(is_array($cols)){
			$this->sort = $cols;
		} else {
			if($bool){
				$this->sort_overrides[$cols] = true;
			} else {
				$this->sort_overrides[$cols] = false;
			}
		}

		return $this;
		return new datatableV2();
	}

	function isSortable($col){
		if(isset($this->sort_overrides[$col])){
			return $this->sort_overrides[$col];
		}

		if($this->sort === false){
			return false;
		}

		if($this->sort !== true && !in_array($col, $this->sort)){
			return false;
		}

		return true;
	}

	function someSearchable(){
		return ($this->search !== false);
	}

	function getSortLink($col){

		$current_dir = $this->getSort($col);
		if($current_dir === 'none'){
			$next_dir = 'desc';
		} elseif($current_dir === 'desc'){
			$next_dir = 'asc';
		} else {
			$next_dir = 'none';
		}

		return utils::mergeUrlParams($this->pagination_link, array('sort' => $col, 'sort_dir' => $next_dir));
	}

	function getLink(){
		return $this->pagination_link;
	}

	public $colSettings = array();
	function setColSetting($col_name, $setting_name, $value){
		$this->colSettings[$col_name][$setting_name] = $value;

		return $this;
		return new datatableV2();
	}

	function getColSetting($col_name, $setting_name){
		if(!isset($this->colSettings[$col_name])){
			return null;
		}

		if(!isset($this->colSettings[$col_name][$setting_name])){
			return null;
		}

		return $this->colSettings[$col_name][$setting_name];
	}

	public $currentHeader = null;
	public $displayHeader = false;
	public $headerDefinitions = array();
	function defineHeader($field_name, $callbackFunction = null){
		if($callbackFunction === null){
			$callbackFunction = function($even_odd, $value, $key, $row, $dt){ ?>
            <tr class="<?php echo ($even_odd = !$even_odd) ? 'even' : 'odd'; ?>">
            <td class="datatable-row-header" colspan="<?php
            	echo $dt->getColCount(); ?>"><?php
            	echo $value; ?></td>
            </tr>
			<?php
			};
		}

		$this->headerDefinitions[$field_name] = array('callback' => $callbackFunction);
		$this->displayHeader = true;
		return $this;
		return new datatableV2();
	}

	function getCurrentHeader(){
		return $this->currentHeader;
	}

	function setTableClass($class){
		$this->set('table_class', $class);

		return $this;
		return new datatableV2();
	}

	function setContainerClass($class){
		$this->set('container_class', $class);
		return $this;
		return new datatableV2();
	}

	function setPaginationClass($val){
		$this->set('pagination_class', $val);
		return $this;
		return new datatableV2();
	}

	function setSortClass($val){
		$this->set('sort_class', $val);
		return $this;
		return new datatableV2();
	}

	function setSortInline($bool = true){
		if($bool){
			$this->set('sort_class', 'inline');
		}else{
			$this->set('sort_class', '');
		}

		return $this;
		return new datatableV2();
	}

	function setPaginationLink($val){
		$this->set('pagination_link', $val);
		return $this;
		return new datatableV2();
	}

	function setPaginationLoader($val){
		$this->set('pagination_loader', $val);
		return $this;
		return new datatableV2();
	}

	function setPaginationDestination($val){
		$this->set('pagination_destination', $val);
		return $this;
		return new datatableV2();
	}

	private $is_calculated = false;
	function getPageLink($start){
		if(!$this->is_calculated){
			$this->is_calculated = true;
			$this->calculate();
		}
		return utils::mergeUrlParams($this->pagination_link, array('start' => $start, 'limit' => $this->getLimit(), ));
	}

	public $limit = 10;
	function setLimit($limit){
		if($limit === false){
			$this->limit = false;
		} else {
			$this->limit = (int)$limit;
		}
		return $this;
		return new datatableV2();
	}

	public $db = null;
	function setDB($db){
		$this->db = $db;
		return $this;
		return new datatableV2();
	}

	public $filtered_rows = 0;
	function setFiltered($filtered_rows){
		$this->filtered_rows = (int)$filtered_rows;
		return $this;
		return new datatableV2();
	}

	function getFiltered(){
		return $this->filtered_rows;
	}
    function getFieldName($col){
        $internal_field_name = $this->getColSetting($this->getColFieldName($col), 'internal_field_name');

        if($internal_field_name !== null){
            $internal_field_name = utils::removeStringFromBeginning($internal_field_name, '`');
            $internal_field_name = utils::removeStringFromEnd($internal_field_name, '`');

            return '`' . $internal_field_name . '`';
        }
        if(isset($this->colDefinitions[$col]))
            return '`' . $this->colDefinitions[$col]['field_name'] . '`';

        return $col;


    }
	public $query;
	function getNextRow(){
		if(!isset($this->query)){
			$sql_limit = '';
			if($this->limit !== false){
				$sql_limit = ' LIMIT ' . $this->getStart() . ',' . $this->getLimit();
			}

			$search_sql = '';
			if(!empty($this->search_queries)){
				if($this->sqlWhere === ''){
					$search_sql = 'WHERE ';
				} else {
					$search_sql = ' and ';
				}

				foreach ($this->search_queries as $col => $value){
					if(!isset($this->colDefinitions[$col]['field_name']) || !$this->isSearchable($col)){
						continue;
					}
					if($this->getColSetting($this->getColFieldName($col), 'search_type') === 'exact'){
						$search_sql .= $this->getFieldName($col) . ' = ' . db::quote($value) . ' and ';
					}else{
						$search_sql .= $this->getFieldName($col).' like "%' . db::likeEscape($value) . '%" and ';
					}

				}

				$search_sql = utils::removeStringFromEnd($search_sql, ' and ');
			}

			if($this->sort_col !== null && isset($this->colDefinitions[$this->sort_col]['field_name'])){
				if($this->getSort($this->sort_col) != 'none'){
				    $fieldname = $this->getFieldName($this->colDefinitions[$this->sort_col]['field_name']);
					$this->setOrderBy($fieldname . ' ' . $this->getSort($this->sort_col));
				}
			}

			$this->query = db::query($this->sqlSelect . ' ' . $this->sqlFrom . ' ' . $this->sqlWhere . ' ' . $search_sql . ' ' . $this->sqlGroup . ' ' . $this->sqlOrder . $sql_limit, $this->db);
			$this->total_rows = db::query('SELECT FOUND_ROWS() as `total`', $this->db)->fetchRow()->total;
		}

		if($this->total_rows == 0){
			$this->renderViews('no-results');
		}

		return $this->query->fetchRow();
	}

	function setNoResultView($view = null){
		$this->clearViews('no-results');
		if($view === null){
			$view = page::create()->set('notice', 'No results found!')->set('container_style', 'style-1')->addView('elements/notice.php');
		}
		$this->addView($view, 'no-results');

        return $this;
        return new datatableV2();
	}

	function setStmt(data_stmt $stmt){
		$this->query = $stmt;
		$this->total_rows = $stmt->rowCount();
		return $this;
		return new datatableV2();
	}

	function getColDisplayName($col){
		if(isset($this->colDefinitions[$col])){
			return $this->colDefinitions[$col]['display_name'];
		}

		return $col;
	}

	function getColFieldName($col){
		if(isset($this->colDefinitions[$col])){
			return $this->colDefinitions[$col]['field_name'];
		}

		return $col;
	}

	function getColValue($col, $value, $row){
		if(isset($this->colDefinitions[$col]) && isset($this->colDefinitions[$col]['callback'])){
			return $this->colDefinitions[$col]['callback']($value, $row, $this, $col);
		}

		return $value;
	}

	public function getCallback($callback_name){

        $args = func_get_args();

        $args[0] = $this;

		if(isset($this->callbacks[$callback_name])){
		    return call_user_func_array($this->callbacks[$callback_name], $args);
			//return $this->callbacks[$callback_name]($this);
		}

		return false;
	}

    function hasCallback($callback_name){
        if(isset($this->callbacks[$callback_name])){
            return true;
        }

        return false;
    }

	public $callbacks;
	function setCallback($callback_name, \closure $callback){
		$this->callbacks[$callback_name] = $callback;

		return $this;
		return new datatableV2();
	}

	function isColDefined($col){
		if(isset($this->colDefinitions[$col])){
			return true;
		}

		return false;
	}

	function getColCount(){
		return count($this->colDefinitions);
	}

	function isHeader($col){
		if(isset($this->headerDefinitions[$col])){
			return true;
		}

		return false;
	}

	public $renderHeader = true;

	public $total_rows;
	function getTotalRows(){
	    if($this->total_rows === null){
	        $this->getNextRow();
            $this->query->seek(0);
	    }
		return (int)$this->total_rows;
	}

	function getLimit(){
		return $this->limit;
	}

	public $start = 0;
	function setStart($start){
		$this->start = (int)$start;
		return $this;
		return new datatableV2();
	}

	function getStart(){
		return $this->start;
	}

	private function calculate(){
		if(isset($this->limit) && isset($this->total_rows)){
		    if($this->limit === false || $this->limit === 0){
		        $this->total_pages = 0;
                $this->current_page = 0;
		    }else{
                $this->total_pages = ceil($this->getTotalRows() / $this->limit);
                $this->current_page = (int)ceil($this->start / $this->limit) + 1;
		    }

		}
	}

	function getPrevStart(){
		return max($this->start - $this->limit, 0);
	}

	function getLastStart(){
		return $this->total_pages * $this->limit - $this->limit;
	}

	function getFirstStart(){
		return 0;
	}

	function getNextStart(){
		$next = $this->start + $this->limit;
		if($next >= $this->getTotalRows()){
			return $this->start;
		}
		return $next;
	}

	private $pagination_limit = 5;
	function setPaginationLimit($limit){
		$this->pagination_limit = $limit;
		return $this;
		return new datatableV2();
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
		$pages_lr = floor($this->pagination_limit / 2);

		//find end page
		$this->pagination_max = max(min(($this->getCurrentPage() + $pages_lr), $this->getTotalPages()), $this->pagination_limit);

		//find start page
		$this->pagination_page = max(0, ($this->pagination_max - $this->pagination_limit));

	}

	private $current_page = 1;
	private $pagination_page = 0;
	function nextPage(){
		$this->paginationSetup();
		if($this->pagination_page++ * $this->limit < $this->getTotalRows() && $this->pagination_page < $this->pagination_max + 1){
			return true;
		}

		return false;

	}

	function getPageStart(){
		return $this->pagination_page * $this->getLimit() - $this->getLimit();
	}

	function getPageNum(){
		return $this->pagination_page;
	}

	function getCurrentPage(){
		return $this->current_page;
	}

	private $total_pages;
	function getTotalPages(){
        if(!$this->is_calculated){
            $this->is_calculated = true;
            $this->calculate();
        }
		return $this->total_pages;
	}

	/**
	 * Creation factory
	 * (creates NEW instance each time)
	 */
	static function create($name = 'singleton'){
		self::$instances[$name] = new self();
		return new datatableV2();
	}

	/**
	 * Get THE singleton of class instance
	 * (creates it if not exists)
	 */
	public static $instances = array();
	public static function get($name = 'singleton'){
		if(!isset(self::$instances[$name])){
			self::$instances[$name] = new self();
		}
		return self::$instances[$name];
		return new datatableV2();
	}

}
