<?php
namespace toolbox;

class dataset {

    private $domain;
    function __construct(domain $domain){
        $this->domain = $domain;
    }

    private $startTime = 0;
    function setDateStart($timestamp){
        $this->startTime = (int)$timestamp;
    }

    private $endTime = 0;
    function setDateEnd($timestamp){
        $this->endTime = (int)$timestamp;
    }

    function getDateEnd(){
        return $this->endTime;
    }

    function getDateStart(){
        return $this->startTime;
    }

    function getData(){
        $this->is_cached_data = false;
        $sql = 'select b.`continent_name`, b.`country_iso_code`,
            `total_hits` as `non_unique_visits`,
            `unique_hits` as `unique_visits`,
            `latitude`,
            `longitude`,
            b.`city_name`,
            b.`subdivision_1_name`,
            b.`country_name`, `day`,
            max(`last_hit_id`) as `max_hit_id`
             from  hits_aggregated `a`
            left outer join `geoip2-city-locations-en` b using (`geoname_id`)
             where `day` <= date(FROM_UNIXTIME('.db::quote($this->endTime).'))
                    and `day` >= date(FROM_UNIXTIME('.db::quote($this->startTime).'))
                    and `domain_id` = '.$this->domain->getID().'
            group by `geoname_id`
            ORDER BY `day` DESC';

        //utils::vd($sql);
        return db::query($sql);
        return new db_stmt();

    }

    function isComplete(){
        if($this->is_complete === 0){
            return false;
        }

        return true;
    }

    private $is_cached_data;
    private $is_complete = 0;
    private $cache_date;
    private $cache_max_hit_id;
    function getDataCached(){
        $this->is_cached_data = true;
        $sql = 'select * from `hits_cached` where `date_range-start` = '.db::quote($this->startTime).'
                    and `date_range-end` = '.db::quote($this->endTime).'
                    and ( `is_complete` = 1 OR `cache_date` > DATE_SUB(NOW(),INTERVAL 1 HOUR) )
                    and `domain_id` = '.$this->domain->getID().' limit 1';
        //utils::vdd($sql);

        //get cached data
        $query = db::query($sql);
        if($query->rowCount() == 0){
            return false;
        }
        $row = $query->fetchRow();
        $this->is_complete = (int)$row->is_complete;
        $this->cache_date = $row->cache_date;
        $this->cache_max_hit_id = $row->max_hit_id;
        return new data_stmt(unserialize($row->data));

    }

    function isCachedData(){
        return $this->is_cached_data;
    }

    function getCachedDate(){
        return $this->cache_date;
    }

    function getCachedMaxHitId(){
        return $this->cache_max_hit_id;
    }



    function getDataByIP(){

        $sql = 'select `continent_name`, `country_iso_code`, `ip_id`,`visits` AS `non_unique_visits`,
        `hit_date`, `latitude`, `longitude`,
        `city_name`, `subdivision_1_name`, `country_name`
         from (
                SELECT `ip_id`, MAX(`date`) AS `hit_date`, COUNT(`ip_id`) AS visits
                        FROM `hits`
                        WHERE `domain_id` = '.$this->domain->getID().'
                        AND `date` <= FROM_UNIXTIME('.db::quote($this->endTime).')
                            and `date` >= FROM_UNIXTIME('.db::quote($this->startTime).')
                        and `ip_id` in (
                        select * from (
                        SELECT distinct `ip_id`
                        FROM `hits`
                        where `date` <= FROM_UNIXTIME('.db::quote($this->endTime).')
                            and `date` >= FROM_UNIXTIME('.db::quote($this->startTime).')
                            and `domain_id` = '.$this->domain->getID().'
                        ORDER BY `date` DESC LIMIT 15) a
                        )
                        GROUP BY `ip_id`
                        ORDER BY `hit_date` DESC
                        LIMIT 15
                ) a
        LEFT OUTER
        JOIN `ip_info` USING (`ip_id`)
        LEFT OUTER
        JOIN `geoip2-city-locations-en` USING (`geoname_id`)';


        //utils::vd($sql);
        return db::query($sql);

    }

    function getFirstAllTime($start = 0){
        if($start === 0){
            $limit_sql = 'LIMIT 1';
        }else{
            $limit_sql = 'LIMIT '.(int)$start.', 1';
        }
        $sql = '
                select `hit_id`, `domain_id`, `ip_id`, `date` from `hits`
                force index (`domain_id+date`)
                where `domain_id` = '.$this->domain->getID().' ORDER BY `date` DESC '.$limit_sql;
        return db::query($sql)->fetchRow();

    }
    function getLastAllTime(){
        $sql = '
                select `hit_id`, `domain_id`, `ip_id`, `date` from `hits`
                force index (`domain_id+date`)
                where `domain_id` = '.$this->domain->getID().' ORDER BY `date` ASC LIMIT 1';

        return db::query($sql)->fetchRow();

    }


}
