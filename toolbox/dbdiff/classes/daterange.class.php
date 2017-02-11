<?php
namespace toolbox;
class daterange {

    private $menu;
    private $init_time;
    function __construct(){
        $this->init_time = time();

        if(date('l') === 'Monday'){
            $week_start = 'this monday';
            $week_end = 'next monday';
        }else{
            $week_start = 'last monday';
            $week_end = 'this monday';
        }


        $this->menu = menu::create('daterange')
            ->addItem('today')
            ->setDisplayName('Today')
            ->setMeta('start_date', strtotime('today'))
            ->setMeta('end_date', strtotime('tomorrow')-1)
            ->addItem('week')
            ->setDisplayName('This Week')
            ->setMeta('start_date', strtotime($week_start))
            ->setMeta('end_date', strtotime($week_end) - 1)
            ->addItem('month')
            ->setDisplayName('This Month')
            ->setMeta('start_date', strtotime('first day of -0 month midnight'))
            ->setMeta('end_date', strtotime('first day of next month midnight') - 1)
            //->addItem('last_3_months')
            //->setDisplayName('Last 3 Months')
            //->setMeta('start_date', strtotime('first day of -3 months midnight'))
            ->addItem('last_month')
            ->setDisplayName('<span class="badge badge-dropdown">archive</span>Last Month')
            ->setMeta('start_date', strtotime('first day of -1 month midnight'))
            ->setMeta('end_date', strtotime('first day of this month midnight') - 1)
            ->addItem('archives')
            ->setDisplayName('See All Archives')
            ->setMeta('tab_trigger', '#archives')
            ->setMeta('start_date', 0);
            //->addItem('year')
            //->setDisplayName('This Year')
            //->setMeta('start_date', strtotime('first day of January'))
            //->setMeta('end_date', strtotime('first day of January next year')-1);
            //->addItem('all_time')
            //->setDisplayName('All Time')
            //->setMeta('start_date', 1)
            //->setMeta('end_date', 2147483647);//lets hope this time never comes
            /*->addItem('custom')
            ->setDisplayName('Custom Range')
            ->setMeta('toggle_id', 'custom_range')
            ->setMeta('start_date', 0);

            $this->parseDateValue('today');*/
    }

    function getInitTime(){
        return $this->init_time;
    }

    function getStartDate(){
        return $this->start_date;
    }

    function getEndDate(){
        return $this->end_date;
    }

    private $start_date;
    function setStartDate($start_date){
        $this->start_date = $start_date;
    }

    private $end_date;
    function setEndDate($end_date){
        $this->end_date = $end_date;
    }

    private $maxStartDate;
    function setMaxStartDate($maxStartDate){
        $this->maxStartDate = $maxStartDate;
    }
    private $maxEndDate;
    function setMaxEndDate($maxEndDate){
        $this->maxEndDate = $maxEndDate;
    }

    function getMaxEndDate(){
        return $this->maxEndDate;
    }
    private $dateDropEndDate;
    function setDateDropStartDate($dateDropEndDate){
        $this->dateDropEndDate = $dateDropEndDate;
    }

    function getMaxStartDate(){
        return $this->maxStartDate;
    }


    private $range_type;
    function parseDateValue($item_name){
        //reset active incase set before
        $this->menu->setActive(false);

        //if a predefined range
        while($item = $this->menu->getNextItem()){
            if($item === $item_name){
                $this->menu->setActive($item);
                $this->range_type = $item;
                $this->setStartDate($this->menu->getMeta('start_date', $item));
                $this->setEndDate($this->menu->getMeta('end_date', $item));
                $this->menu->getNextItem(true);
                return;
            }
        }

        //if a custom range
        $settings = explode('|', $item_name);
        if(isset($settings[0]) && isset($settings[1]) && isset($settings[2])){
            $settings[1] = (int)$settings[1];
            $settings[2] = (int)$settings[2];
            $this->range_type = $settings[0];
            if($settings[0] == 'month'){
                if(
                    //timestamp must start at beginning of month
                    strtotime(date('Y-m-d', $settings[1]).' 00:00:00') == $settings[1]
                    //timestamp must end at last second of month
                    && strtotime(date('Y-m', $settings[2]).'-01 00:00:00 +1 month -1 second') == $settings[2]
                ){//range is valid
                    $this->setStartDate($settings[1]);
                    $this->setEndDate($settings[2]);
                    return;
                }
            }elseif($settings[0] == 'week'){
                if(
                    //timestamp must start at beginning of week
                    strtotime(date('o\WW', $settings[1])) == $settings[1]
                    //timestamp must end at last second of week
                    && strtotime(date('o\WW', $settings[1]).' +1 week -1 second') == $settings[2]
                ){//range is valid
                    $this->setStartDate($settings[1]);
                    $this->setEndDate($settings[2]);
                    return;
                }
            }elseif($settings[0] == 'day'){
                if(
                    //timestamp must start at beginning of day
                    strtotime(date('Y-m-d', $settings[1]).' 00:00:00') == $settings[1]
                    //timestamp must end at last second of day
                    && strtotime(date('Y-m-d', $settings[1]).' 00:00:00 + 1 day - 1 second') == $settings[2]
                ){//range is valid
                    $this->setStartDate($settings[1]);
                    $this->setEndDate($settings[2]);
                    return;
                }
            }else{
                $this->range_type = 'today';
            }

        }


        //otherwise, set default range
        $this->menu->setActive('today');
        $this->range_type = 'today';
        $this->setStartDate($this->menu->getMeta('start_date', 'today'));
        $this->setEndDate($this->menu->getMeta('end_date', 'today'));



        return $this;
        return new daterange();
    }

    function getRangeType(){
        return $this->range_type;
    }


    function getdateID(){
        return $this->menu->getActive();
    }


    function getDateDisplayName(){
        if($this->getdateID() === null){
            echo formatter::dateTypeDisplay($this->range_type, $this->getStartDate(), $this->getEndDate());
        }
        return $this->menu->getMeta('display_name', $this->getdateID());
    }


    function getNextItem(){
        $item_name = $this->menu->getNextItem();
        if($item_name === false){
            return false;
        }

        return $this->menu->getItem($item_name);

    }

    function getMenu(){
        return $this->menu;
        return new menu();
    }

    /**
     * Creation factory
     * (creates NEW instance each time)
     */
    static function create(){
        return new self();
        return new daterange();
    }


    /**
     * Get THE singleton of class instance
     * (creates it if not exists)
     */
    private static $instances;
    public static function get($name = 'singleton'){

        if (!isset(self::$instances[$name])) {
            self::$instances[$name] = new self();
        }

        return self::$instances[$name];
        return new daterange();
    }
}
