<?php

namespace Admin\View;

use Kernel\Exceptions;

use Admin\View\Form as Form;

class View{

	/**
	 * Schema
	 *
	 * @var ORM\Schema
	 */
	public $schema;

	/**
	 * Fields
	 *
	 * @var array
	 */
	public $fields;

	/**
	 * List all form
	 *
	 * @todo through coning
	 * @var array
	 */
	public static $form_classes = [
		Form\Base::class,
		Form\Date::class,
		Form\DateTime::class,
		Form\Email::class,
		Form\Hidden::class,
		Form\Id::class,
		Form\Number::class,
		Form\Password::class,
		Form\Select::class,
		Form\SelectMultiple::class,
		Form\Text::class,
		Form\Textarea::class,
		Form\TextLong::class,
		Form\ToMany::class,
		Form\ToOne::class,
		Form\Url::class,
	];


	/**
	 * Construct
	 */
	public function __construct($schema){
		$this -> schema = $schema;
	}

	/**
	 * Get schema
	 *
	 * @return ORM\Schema
	 */
	public function getSchema(){
		return $this -> schema;
	}

	/**
	 * Call
	 *
	 * @param string $method
	 * @param array $arguments
	 */
	public function __call($method,$arguments){

		$name = $arguments[0];

		foreach(static::$form_classes as $class){

			if($class::getStaticAlias() == $method){
				$form = new $class($name);
				$builder = new ViewBuilder($this -> getSchema() -> getField($name),$form);

				$this -> fields[$name] = $builder;
				return $form;
			}
		}


		throw new Exceptions\UndefinedMethodException(static::class,$method);
	}

	/**
	 * Get all fields defined in view
	 *
	 * @return array
	 */
	public function getFields(){
		return $this -> fields;
	}

	/**
	 * Get all fields defined in view
	 *
	 * @return array
	 */
	public function isField($field_name){
		foreach($this -> getFields() as $field){
			if($field_name == $field -> getName())
				return true;
		}

		return false;
	}

	/**
	 * Return an array with basic information of relations of all fields
	 *
	 * @return array
	 */
	public function getMinimalRelation(){
		$return = [];
		foreach($this -> getFields() as $field){
			$partials = [];

			if($field -> getForm() -> is('to_one') || $field -> getForm() -> is('to_many')){
				foreach($field -> getRelations() as $n => $relation){
					if($field -> getUrl($n)){
						$partial = [];
						$partial['name'] = $relation -> getName();
						$partial['url'] = $field -> getUrl($n);

						switch($relation -> getType()){
							case "to_one":
								$partial['type'] = 'toOne';
								$partial['column'] = $relation -> getColumn();
							break;
							case "to_many":
								$partial['type'] = 'toMany';
								$partial['column'] = $relation -> getReference();
							break;
						}

						$partials[] = $partial;
					}
				}
			}
			
			if(!empty($partials))
				$return[$field -> getName()] = $partials;
		}
		return $return;
	}
}
?>