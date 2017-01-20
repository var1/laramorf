<?php

namespace Morphy;

class Morphy extends phpMorphy{
	
	protected static $instance;
	
	public static function instance($lang='rus'){
		
		if (!isset(static::$instance[$lang])){
			// Create descriptor for dictionary located in $dir directory with russian language
			$dict_bundle = new phpMorphy_FilesBundle(config('morphy.dict_path',resource_path().'/dicts'), $lang);
			
			$opts = array(
						'storage' => PHPMORPHY_STORAGE_FILE,
						'with_gramtab' => config('morphy.with_gramtab',false),
						'predict_by_suffix' => config('morphy.predict_by_suffix',true), 
						'predict_by_db' => config('morphy.predict_by_db',true)
					);
			
			// Create phpMorphy instance
			static::$instance[$lang] = new static($dict_bundle, $opts);
		}
		
		return static::$instance[$lang];
		
		
	}
	
	 protected function invoke($method, $word, $type) {
		$word=\Illuminate\Support\Str::upper($word);
		return parent::invoke($method, $word, $type);
	 }
	
		


	
	
}

