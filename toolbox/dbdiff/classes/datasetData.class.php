<?php
namespace toolbox;
class datasetData {
    private $db_id = null;
	private $sql_parts = array(
							'where' => '',
							'from' => '',
							'select' => '',
							'group_by' => ''
						);

	private $metric_datasets = array();
	private $metric_dataset_main;
	function addDataset(datasetBuilder $metric_dataset){
		if($this->metric_dataset_main == null){
			$this->metric_dataset_main = $metric_dataset;
		}

		//if metric is a combined metric
		if($metric_dataset->get('combinedDatasets') !== null){
			foreach($metric_dataset->get('combinedDatasets') as $dataset){
				$this->addDataset($dataset);
			}
		}else{
			$key = count($this->metric_datasets);
			$this->metric_datasets[$key] = $metric_dataset;
			$this->sql_parts[$key]['from'] = $metric_dataset->get('table');
			$this->sql_parts[$key]['select'] = $metric_dataset->get('select');
			$this->sql_parts[$key]['where'] = $metric_dataset->get('where');
			$this->sql_parts[$key]['limit'] = $metric_dataset->get('limit');
			$this->sql_parts[$key]['order_by'] = $metric_dataset->get('order_by');
			$this->sql_parts[$key]['group_by'] = $metric_dataset->get('group_by');
		}

		return $this;
		return new datasetData();
	}

    function setDbId($db_id){
    	$this->db_id = $db_id;

		return $this;
		return new datasetData();
	}

    function getDbId(){
    	return $this->db_id;

	}

    function setGroupBy($group_by){
    	$this->sql_parts['group_by'] = 'GROUP BY '.$group_by;
		return $this;
		return new datasetData();
	}

    function getDataset($property = null, $key = null){
        return $this->metric_dataset_main;
        return new datasetBuilder();
    }


	/**
	 * Get next row of data from query with
	 * metric formatting rules applied
	 */
	function getNextRowWithFormatting($formatters_override = null){
		return $this->applyMetricFormatting($this->getNextSqlRowData(), $formatters_override);
	}

	function applyMetricFormatting($row, $formatters_override = null){
		if($row === null){
			return null;
		}
		if($row->unformatted_data !== null){
			//already formatted
			return $row;
		}
        $row->unformatted_data = clone $row;

		//loop fields
		foreach($row as $key => &$value){
		    if($field === 'unformatted_data'){
		        continue;
		    }
			$value = $this->applyFieldFormatting($key, $value, $formatters_override);
		}

		return $row;

	}

	public function applyFieldFormatting($field, $value, $formatting_type = null){
		$formatted_keys = array();

		if(
			$formatting_type !== null
			&& ($this->getDataset()->getColumnFormatting($field, $formatting_type) !== null)
		){
			self::applyFormatters($this->getDataset()->getColumnFormatting($field, $formatting_type), $value);
		}else{
			//check if field has formatting rules
			while($extra_formatter = $this->getExtraFormatters()){
				if(!in_array($field, $formatted_keys)
				    && ($this->getDataset()->getColumnFormatting($field, $extra_formatter) !== null)
                ){
					$formatted_keys[] = $field;
					self::applyFormatters($this->getDataset()->getColumnFormatting($field, $extra_formatter), $value);
				}
			}
		}

		return $value;
	}

	private static function applyFormatters($formatters, &$value, $_this = null){
		if(is_null($value)){
			return null;
		}

	    if(is_array($formatters)){
			foreach($formatters as $format){
			    if(is_callable($format)){
			    	if($_this !== null){
                    	$value = call_user_func($format, $value, $_this);
					}else{
                    	$value = call_user_func($format, $value);
					}
                }else{
                    $value = formatter::$format($value);
                }
			}
        }elseif(is_callable($formatters)){
	    	if($_this !== null){
            	$value = call_user_func($formatters, $value, $_this);
			}else{
            	$value = call_user_func($formatters, $value);
			}
        }else{
            $value = formatter::$formatters($value);
        }

		return $value;
	}

	private $row_split_data = null;
	function getNextSqlRowData(){
		if(//if row formatting set and not yet applied to current row, apply here
			($row_splitter = $this->getDataset()->get('RowSplitter'))
			&& $this->row_split_data === null
		){
			//return the next split data
			try{
				$this->row_split_data = self::applyFormatters($row_splitter, $this->getSqlData()->fetchRow(), $this);
			}catch(emptyRow $e){
				return $this->getNextSqlRowData();
			}
			if(is_null($this->row_split_data)){
				return null;
			}
			return current($this->row_split_data);

		}elseif($this->row_split_data !== null){//row already split
			//return the next split data
			if($next_row = next($this->row_split_data)){
				return $next_row;
			}else{//no more split rows
				$this->row_split_data = null;
				return $this->getNextSqlRowData();
			}

		}else{//no splitting rows

			return $this->getSqlData()->fetchRow();
		}
	}

    private function addMetricToExportList(){
        page::get('export-dropdown')
            ->addView(
                page::create()
                    ->set('metric_object', $this)
                    ->addView('widgets/export/metric-object.php'),
                'metrics');
    }


	private $sql_data;
	/**
	 * Get sql query for fetching data
	 */
	function getSqlData($limit = false){
		if($this->sql_data === null){
		    $this->addMetricToExportList();
			$sql_final = $this->generateSqlAllTime(null, $this->getFilterSqlString(), null, $limit);

			$this->sql_data = db::query($sql_final, $this->db_id);

		}

		return $this->sql_data;
	}

	public function getFilterSqlString(){
		$filters_where = '';
		foreach($this->filters as $field => $value){
			$filters_where .= '`'.$field.'` = '.db::quote($value) . ' AND ';
		}
		$filters_where = utils::removeStringFromEnd(
									$filters_where,
									' AND '
								);
		return $filters_where;
	}

	private function generateSqlAllTime(
		$select_override = null/*override value for select sql string*/,
		$and_where = null,/*add to where sql using and*/
		$order_by = null,/*add to where sql using and*/
		$limit = null
	){
		$sqls = array();
		foreach($this->metric_datasets as $key => $dataset){
		    $order_by_sql = '';
		    $group_by_sql = '';
			if($select_override === null){
				$select_sql = $this->sql_parts[$key]['select'];
			}else{
				$select_sql = $select_override;
			}

			$where_sql = $this->sql_parts[$key]['where'];

			if($and_where != ''){
				$where_sql .= $and_where . ' AND ';
			}

			if($this->sql_parts[$key]['order_by'] != ''){
				$order_by_sql = 'ORDER BY '.$this->sql_parts[$key]['order_by'];
			}elseif($order_by != ''){
				$order_by_sql = 'ORDER BY '.$order_by;
			}

            if($this->sql_parts[$key]['group_by'] != ''){
                $group_by_sql = 'GROUP BY '.$this->sql_parts[$key]['group_by'];
            }

			$limit_sql = '';
			if($this->sql_parts[$key]['limit'] != ''){
				$limit_sql = 'LIMIT '.$this->sql_parts[$key]['limit'];
			}
			if(trim($where_sql) === ''){
				$where_sql = '1';
			}
			$sqls[] = 'SELECT '.$select_sql
								.' FROM '
								. $this->sql_parts[$key]['from']
								.' WHERE '
									. utils::removeStringFromEnd(
										$where_sql,
										' AND '
									)
                                .' ' . $this->sql_parts['group_by']
                                .' ' . $group_by_sql
                                .' ' . $order_by_sql
                                .' ' . $limit_sql;
		}
		$sql_final = '';
		if(count($sqls) > 1){
			foreach($sqls as $key => $sql){
				$sql_final .= '('.$sql.') UNION ';
			}
			$sql_final = utils::removeStringFromEnd($sql_final, ' UNION ');
		}else{
			$sql_final = $sqls[0];
		}

		return $sql_final;
	}

	public function getFiltersUrlString(){
		if(empty($this->filters)){
			return '';
		}
		return '&'.http_build_query(array('filters' => $this->filters));

	}

	/**
	 * Combine data to by month values
	 */
	function setCombineMonthly(){
		//extend daterange to full months
	}

	/**
	 * Don't combine data, just show values as stored in database.
	 */
	function setCombineNone(){

	}

	function getNewestDataPoint(){

	}

	function getOldestDatapoint(){

	}

	function getNewestDataPointAllTime(){

	}

	function getOldestDatapointAllTime(){

	}

	function getDatapointByPosition(){

	}

	/**
	 * get the next datapoint in the daterange
	 */
	function getNextDatapoint($reset = false){

	}


	function getDelta(){

	}



	private $sql_generated = false;
	private function prepareSQL(){

		//sql for first datapoint all time

		//sql for last datapoint all time

		//sql for all data in date range

		$sql_generated = true;

	}



	function getAvailableFieldValues($field, $where = null){
		$fieldValues = array();
		$sql = $this->generateSqlAllTime('distinct `'.$field.'`', $where, '`'.$field.'` DESC');
		$query = db::query($sql, $this->db_id);
		while($row = $query->fetchRow()){
			$fieldValues[] = $row->$field;
		}

		return $fieldValues;

	}




	private $filters = array();
	function addFilter($field, $value){
		$field = str_replace('`', '', $field);
		$this->filters[$field] = $value;
				return $this;
		return new datasetData();
	}





    /**
     * Creation factory
     * (creates NEW instance each time)
     */
    static function create(){
        return new self();
		return new datasetData();
    }

    /**
     * Get THE singleton of class instance
     * (creates it if not exists)
     */
    private static $instance;
    public static function get(){
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
        return new datasetData();
    }

}

class datasetDataException extends toolboxException{};