<?php
namespace toolbox;

class datatable extends page {

    public $oddeven;
    public $pagination_object;
    function __construct(){
        $this->pagination_object = pagination::create();
        $this->setNoResultView();
        $this->setContainerClass('');
        $this->setPaginationStyle('style1');
        $this->addView(function($tpl){


        while($row = $tpl->getNextRow()){
            if($tpl->renderHeader){
                $tpl->renderHeader = false;

                echo '<div class="datatable '.$tpl->container_class.'">';
                echo '<table class="'.$tpl->class.'">';
                    echo '<thead><tr>';
                    foreach($tpl->colDefinitions as $key => $definitions){
                        //if(!$tpl->isColDefined($key)) continue;
                        echo '<th class="'.$tpl->getColSetting($key, 'class')
                            .'"><span class="padding">'.$tpl->getColDisplayName($key).'</span></th>';
                    }
                    echo '</tr></thead>';
                echo '<tbody>';
            }

            if($tpl->oddeven === 'odd'){
                $tpl->oddeven = 'even';
            }else{
                $tpl->oddeven = 'odd';
            }

            //we need to start with the header first if one was
            //defined or changed since last display
            if($tpl->displayHeader){
                //loop fields until we find the header
                foreach($row as $key => $value){
                    if($tpl->isHeader($key) && $tpl->currentHeader !== $value){
                        echo '<tr class="'.$tpl->oddeven.'">';
                        echo '<td class="datatable-row-header" colspan="'.$tpl->getColCount().'">'
                            .$tpl->getHeaderValue($key, $value, $row).'</td>';
                        echo '</tr>';
                        $tpl->currentHeader = $value;
                        break;
                    }
                }
            }



            echo '<tr class="'.$tpl->oddeven.'">';
            foreach($tpl->colDefinitions as $key => $definitions){
                echo '<td class="'.$tpl->getColSetting($key, 'class').'">';
                echo $tpl->getColValue($key,
                        isset($row->{$definitions['field_name']}) ? $row->{$definitions['field_name']} : null, $row);
                echo '</td>';
            }
            echo '</tr>';

            $tpl->getCallback('postRow', $row);
        }

        $tpl->getCallback('lastRow', null);
        if($tpl->getTotalRows() !== 0){
            echo '</tbody>';
            echo '</table>';
            $tpl->renderViews('pagination');
        }else{
            echo '<div class="datatable '.$tpl->container_class.'">';
        }

        $tpl->renderViews('footer');
        echo '</div>';
        });
    }

    public $sqlSelect = '';
    function setSelect($sql){
        $this->sqlSelect = 'SELECT SQL_CALC_FOUND_ROWS '.$sql;
        //$this->sqlSelect = 'SELECT '.$sql;

        return $this;
        return new datatable();
    }
    function setShow($sql){
        $this->sqlSelect = 'SHOW '.$sql;
        //$this->sqlSelect = 'SELECT '.$sql;

        return $this;
        return new datatable();
    }

    public $sqlFrom = '';
    function setFrom($sql){
        $this->sqlFrom = 'FROM '.$sql;

        return $this;
        return new datatable();
    }
    public $sqlWhere = '';
    function setWhere($sql){
        $this->sqlWhere = 'WHERE '.$sql;

        return $this;
        return new datatable();
    }
    public $sqlOrder = '';
    function setOrderBy($sql){
        $this->sqlOrder = 'ORDER BY '.$sql;

        return $this;
        return new datatable();
    }
    public $sqlGroup = '';
    function setGroupBy($sql){
        $this->sqlGroup = 'GROUP BY '.$sql;

        return $this;
        return new datatable();
    }

    public $colDefinitions = array();
    function defineCol($field_name, $display_name, $callbackFunction = null){
        $this->colDefinitions[] = array(
            'field_name' => $field_name,
            'display_name' => $display_name,
            'callback' => $callbackFunction
        );

        return $this;
        return new datatable();
    }

    public $colSettings = array();
    function setColSetting($col_name, $setting_name, $value){
        $this->colSettings[$col_name][$setting_name] = $value;

        return $this;
        return new datatable();
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
        $this->headerDefinitions[$field_name] = array(
            'callback' => $callbackFunction
        );
        $this->displayHeader = true;
        return $this;
        return new datatable();
    }

    public $class = 'table style1';
    function setClass($class){
        $this->class = $class;
        return $this;
        return new datatable();
    }

    function setContainerClass($class){
        $this->set('container_class', $class);
        return $this;
        return new datatable();
    }

    function setPaginationStyle($val){
        $this->set('pagination_style', $val);
        return $this;
        return new datatable();
    }

    function setPaginationLimit($val){

        return $this;
        return new datatable();
    }

    public $limit = 10;
    function setLimit($limit){
    	if($limit === false){
    		$this->limit = false;
    	}else{
        	$this->limit = (int)$limit;
		}
        return $this;
        return new datatable();
    }
    public $db = null;
    function setDB($db){
        $this->db = $db;
        return $this;
        return new datatable();
    }

    public $filtered_rows = 0;
    function setFiltered($filtered_rows){
        $this->filtered_rows = (int)$filtered_rows;
        return $this;
        return new datatable();
    }

    function getFiltered(){
        return $this->filtered_rows;
    }

    public $query;
    function getNextRow(){
        if(!isset($this->query)){
        	$sql_limit = '';
			if($this->limit !== false){
				$sql_limit = ' LIMIT '.$this->getStart().','.$this->getLimit();
			}
            $this->query = db::query($this->sqlSelect.' '.$this->sqlFrom.' '.$this->sqlWhere
                .' '.$this->sqlOrder
                .' '.$this->sqlGroup
                .$sql_limit, $this->db);
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
            $view = page::create()
                ->set('notice', 'No results found!')
				->set('container_style', 'style-1')
                ->addView('elements/notice.php');
        }
        $this->addView($view, 'no-results');
    }

    function setStmt(data_stmt $stmt){
        $this->query = $stmt;
        $this->total_rows = $stmt->rowCount();
        return $this;
        return new datatable();
    }

    function getColDisplayName($col){
        if(isset($this->colDefinitions[$col])){
            return $this->colDefinitions[$col]['display_name'];
        }

        return $col;
    }

    function getHeaderValue($col, $value, $row){
        if(isset($this->headerDefinitions[$col]) && isset($this->headerDefinitions[$col]['callback'])){
            return $this->headerDefinitions[$col]['callback']($value, $row, $this);
        }

        return $value;
    }

    function getColValue($col, $value, $row){
        if(isset($this->colDefinitions[$col]) && isset($this->colDefinitions[$col]['callback'])){
            return $this->colDefinitions[$col]['callback']($value, $row, $this);
        }

        return $value;
    }

    public function getCallback($callback_name, $row){
        if(isset($this->callbacks[$callback_name])){
            return $this->callbacks[$callback_name]($row, $this);
        }

        return false;
    }

    public $callbacks;
    function setCallback($callback_name, \closure $callback){
        $this->callbacks[$callback_name] = $callback;

        return $this;
        return new datatable();
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
        return (int)$this->total_rows;
    }

    function getLimit(){
        return $this->limit;
    }
    public $start = 0;
    function setStart($start){
        $this->start = (int)$start;
        return $this;
        return new datatable();
    }

    function getStart(){
        return $this->start;
    }


    function setPaginationDestination($path){
        $this->set('pagination_destination', $path);
        return $this;
        return new datatable();
    }

    function getPagination(){
        return $this->pagination_object;
        return new pagination();
    }

    function renderPagination($output_destination, $loader_destination = null, $post_data = ''){
        $this
        ->set('output_destination', $output_destination)
        ->set('loader_destination', $loader_destination)
        ->set('post_data', $post_data)
        ->addView(function(){
        $this->post_data = utils::htmlEncode($this->post_data);
        if($this->loader_destination === null){
            $this->loader_destination = $this->output_destination;
        }

        if($this->getTotalRows() == 0 || $this->getLimit() >= $this->getTotalRows()){
            return;
        }

        $pagination = $this->getPagination()
            ->setTotal($this->getTotalRows())
            ->setLimit($this->getLimit())
            ->setStart($this->getStart());
        ?>
        <div class="datatable-info datatable-section pagination <?php echo $this->pagination_style; ?>">
            <a data-ajax="true" data-ajax_replace="true" data-output_destination="<?php
                echo $this->output_destination; ?>" data-show_loader="<?php echo $this->loader_destination; ?>"
                data-post="<?php echo $this->post_data; ?>"
                href="<?php if(isset($this->pagination_destination))
                    echo utils::mergeUrlParams($this->pagination_destination,
                        array(
                            'start' => $pagination->getFirstStart(),
                            'limit' => $pagination->getLimit(),
                        )); ?>" class="pagination-button pagination-first <?php
                echo $pagination->getFirstClass();
            ?> btn btn-small btn-link"><i class="icon-angle-double-left"></i></a>
            <a data-ajax="true" data-ajax_replace="true" data-output_destination="<?php echo
                $this->output_destination; ?>" data-show_loader="<?php echo $this->loader_destination; ?>"
                data-post="<?php echo $this->post_data; ?>"
                 href="<?php if(isset($this->pagination_destination))
                    echo utils::mergeUrlParams($this->pagination_destination,
                        array(
                            'start' => $pagination->getPrevStart(),
                            'limit' => $pagination->getLimit(),
                        )); ?>" class="pagination-button pagination-prev <?php
                echo $pagination->getPrevClass();
            ?> btn btn-small btn-link"><i class="icon-angle-left"></i></a>
            <?php while($pagination->nextPage()){  ?>
                <a data-ajax="true" data-ajax_replace="true" data-output_destination="<?php
                    echo $this->output_destination; ?>" data-show_loader="<?php echo $this->loader_destination; ?>"
                 data-post="<?php echo $this->post_data; ?>"
                 href="<?php if(isset($this->pagination_destination))
                    echo utils::mergeUrlParams($this->pagination_destination,
                        array(
                            'start' => $pagination->getPageStart(),
                            'limit' => $pagination->getLimit(),
                        )); ?>" class="pagination-button <?php
                        echo $pagination->getPageClass(); ?> btn btn-small btn-link"><?php
                        echo $pagination->getPageNum(); ?></a>
            <?php } ?>
            <a data-ajax="true" data-ajax_replace="true" data-output_destination="<?php
                echo $this->output_destination; ?>" data-show_loader="<?php echo $this->loader_destination; ?>"
                data-post="<?php echo $this->post_data; ?>"
                 href="<?php if(isset($this->pagination_destination))
                    echo utils::mergeUrlParams($this->pagination_destination,
                        array(
                            'start' => $pagination->getNextStart(),
                            'limit' => $pagination->getLimit(),
                        )); ?>" class="pagination-button pagination-next <?php
                echo $pagination->getNextClass();
            ?> btn btn-small btn-link"><i class="icon-angle-right"></i></a>
            <a data-ajax="true" data-ajax_replace="true" data-output_destination="<?php echo
                $this->output_destination; ?>" data-show_loader="<?php echo $this->loader_destination; ?>"
                data-post="<?php echo $this->post_data; ?>"
                 href="<?php if(isset($this->pagination_destination))
                    echo utils::mergeUrlParams($this->pagination_destination,
                        array(
                            'start' => $pagination->getLastStart(),
                            'limit' => $pagination->getLimit(),
                        )); ?>" class="pagination-button pagination-last <?php
                echo $pagination->getLastClass();
            ?> btn btn-small btn-link"><i class="icon-angle-double-right"></i></a>
        </div>
        <?php
        }, 'pagination');

        return $this;
        return new datatable();
    }




    /**
     * Creation factory
     * (creates NEW instance each time)
     */
    static function create($name = 'singleton'){
        self::$instances[$name] = new self();
        return new datatable();
    }

    /**
     * Get THE singleton of class instance
     * (creates it if not exists)
     */
    public static $instances = array();
    public static function get($name = 'singleton'){
        if (!isset(self::$instances[$name])) {
            self::$instances[$name] = new self();
        }
        return self::$instances[$name];
        return new datatable();
    }
}
