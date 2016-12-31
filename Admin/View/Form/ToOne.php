<?php

namespace Admin\View\Form;

class ToOne extends Base{


	/**
	 * Relations
	 *
	 * @var array
	 */
	public $relations = [];

	/**
	 * urls
	 *
	 * @var array
	 */
	public $urls = [];


	/**
	 * Construct
	 */
	/*public function __construct($schema,$arguments){
		

		$this -> relations[] = $schema;
		$this -> label($this -> getName());	

		if($this -> getSchema() -> getType() == "to_one" || $this -> getSchema() -> getType() == "to_many"){
			if(isset($arguments[0]))
				$this -> urls[] = $arguments[0];
		}
	}*/

	/**
	 * Call
	 *
	 * @param string $method
	 * @param array $arguments
	 */
	public function __call($method,$arguments){
		$last_relation = $this -> getLastRelation();

		if($last_relation -> getType() == "to_one" || $last_relation -> getType() == "to_many"){
				
			if($last_relation -> getRelation()::schema() -> isField($method)){

				$field = $last_relation -> getRelation()::schema() -> getField($method);
				//$this -> schema = $field;
				$this -> relations[] = $field;

				$this -> label($this -> getName());	

				if($field -> getType() == "to_one" || $field -> getType() == "to_many"){
					if(isset($arguments[0]))
						$this -> urls[] = $arguments[0];
				}

				return $this;
			}
		}

		$this -> label($this -> getName());	
		
		throw new Exceptions\UndefinedMethodException(static::class,$method);
	}

	/**
	 * Get relations
	 *
	 * @return array
	 */
	public function getRelations(){
		return $this -> relations;
	}

	public function getUrl($n){
		return isset($this -> urls[$n]) ? $this -> urls[$n] : null;
	}

	/**
	 * Get relations
	 *
	 * @return array
	 */
	public function getLastRelation(){
		return $this -> relations[count($this -> relations) - 1];
	}

	/**
	 * Get relations
	 *
	 * @return array
	 */
	public function getLastColumnRelation(){
		return $this -> relations[count($this -> relations) - 2];
	}

	public function countRelations(){
		return count($this -> relations);
	}

	public function getName(){
		return implode(".",array_map(function($item){ return $item -> getName(); },$this -> getRelations()));
	}

	public function getColumn(){
		return $this -> getSchema() -> getColumn();
	}
}