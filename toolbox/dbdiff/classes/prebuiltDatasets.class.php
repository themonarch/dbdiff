<?php namespace toolbox;
class prebuiltDatasets {
	
	static function alexa_rank($domain_id){
		return datasetBuilder::create()
		            ->set('title', 'Rank')
		            ->set('select', '*')
		            ->setArray('fields', 'type')
		            ->setArrayMeta('display_name', 'type of stat')
		            ->set('main_field', 'type')
		            ->set('date_field', 'date')
		            ->set('value_field', 'count')
		            ->set('value_field', 'count')
		            ->setArray('fields', 'count')
		            ->setArrayMeta('display_name', 'count')
		            ->setArray('fields', 'date')
		            ->setArrayMeta('display_name', 'Date')
		            ->set('table', "stats")
		            ->set('where', "`type` = 'domains_24hrs_rolling'");
	}
	
	static function alexa_traffic($domain_id){
		return datasetBuilder::create()
		            ->set('title', 'Traffic')
		            ->set('select', '*')
		            ->setArray('fields', 'type')
		            ->setArrayMeta('display_name', 'type of stat')
		            ->set('main_field', 'type')
		            ->set('date_field', 'date')
		            ->set('value_field', 'count')
		            ->set('value_field', 'count')
		            ->setArray('fields', 'count')
		            ->setArrayMeta('display_name', 'count')
		            ->setArray('fields', 'date')
		            ->setArrayMeta('display_name', 'Date')
		            ->set('table', "stats")
		            ->set('where', "`type` = 'users_24hrs_rolling'");
	}
	
}
