<?php namespace toolbox;
class widgetHelper {

    private $page;
    function __construct(){
        $this->page = page::create()
            ->set('widget_id', utils::getRandomString())
            ->set('class', '');
    }

    private $unique_ids = array();
    function set($name, $value, $use_as_unique_id = true){
        $this->page->set($name, $value);

        if($use_as_unique_id){
            $this->unique_ids[] = $name;
        }

        $this->page->set('unique_ids', $this->unique_ids);

        return $this;
        return new widgetHelper();
    }

    private $view_hook = 'content-narrow';
    function setHook($view_hook){
        $this->view_hook = $view_hook;

        return $this;
        return new widgetHelper();
    }

	function setWidgetUrl($path = '/widget'){
		$widget_vars = get_object_vars($this->page);
		unset($widget_vars['page_modified_time']);
		$widget_vars['widget_content'] = $widget_content;
		$widget_vars['widget_template'] = $widget_template;
		$this->page->set('widget_url', $path.'?'.http_build_query($widget_vars));
	}

    function getWidgetUrl(){
        if(!isset($this->page->widget_url)){
            throw new toolboxException('Widget url not set!', 1);
        }

        return $this->page->widget_url;
    }

    private $rendering = false;
    function add($widget_content, $widget_template = 'minimal.php', $render_now = null){
        if(is_callable($widget_content)){
            $widget_id = '';
        }else{
            $widget_id = $widget_content;
            $widget_content = 'widget/content/'.$widget_content;
        }

		if(
			$render_now === null
            && utils::isAjax()
			&& !isset($this->page->widget_url)
			//&& isset($_REQUEST['widget_content'])
			//&& isset($_REQUEST['widget_template'])
			//&& $widget_id === $_REQUEST['widget_content']
			//&& $widget_template === $_REQUEST['widget_template']
			&& $this->unique_ids_match()
		){
			$render_now = true;
		}elseif(
            $render_now === null
            && utils::isAjax()
            && isset($_REQUEST['widget'])
            && 'widget/content/'.$_REQUEST['widget'] === $widget_content
        ){
            $render_now = true;
        }elseif(is_string($render_now)){
        	$this->set('widget_unique_id', $render_now);
			if(isset($_REQUEST['widget_unique_id'])
			&& $_REQUEST['widget_unique_id'] === $render_now){
				$render_now = true;
			}
        }

		if(!isset($this->page->widget_url)){
            $widget_vars = get_object_vars($this->page);
            unset($widget_vars['page_modified_time']);
            $widget_vars['widget_content'] = $widget_id;
            $widget_vars['widget_template'] = $widget_template;
			$this->page->set('widget_url',
				utils::mergeUrlParams($_SERVER['REQUEST_URI'], $widget_vars));
		}

        page::get()
            ->addView(
                $this->page
                    ->setMainView('widget/template/'.$widget_template)
                    ->addView($widget_content, 'widget_content'),
                $this->view_hook
            );

		if($render_now === true){
			$this->page->renderViews();
            //signal the shutdown function to not report
            //any exceptions as we already handled them if execution got to this point
            //(either they were caught, or our exception handler already did)
            toolbox::$error_handled = true;
            exit;
		}

        return $this;
        return new widgetHelper();

    }

    private function unique_ids_match(){
        foreach ($this->unique_ids as $key => $name) {
            if(!isset($_REQUEST[$name]) || $_REQUEST[$name] != $this->page->{$name}){
                return false;
            }
        }

        return true;
    }

    static function create(){
        return new self();
        return new widgetHelper();
    }
}
