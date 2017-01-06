<?php

namespace Admin\View\Form;

class Base{


	/**
	 * Default value
	 *
	 * @var mixed
	 */
	protected $value;

	/**
	 * Default value
	 *
	 * @var mixed
	 */
	protected $label;

	/**
	 * Don't display the field
	 *
	 * @var bool
	 */
	protected $show = true;

	/**
	 * String name
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Return alias
	 *
	 * @return string
	 */
	public static function getStaticAlias(){
		$class = explode("\\",static::class);
		$class = $class[count($class) - 1];
		return lcfirst($class);
	}

	/**
	 * Compare alias
	 *
	 * @param string $alias
	 *
	 * @return bool
	 */
	public static function isAlias($alias){
		return static::getStaticAlias() == $alias;
	}

	/**
	 * Construct
	 *
	 * @param string $name
	 */
	public function __construct($name){
		$this -> name = $name;
	}

	/**
	 * Get name
	 *
	 * @return string
	 */
	public function getName(){
		return $this -> name;
	}

	/**
	 * Get column
	 *
	 * @return string
	 */
	public function getColumn(){
		return $this -> name;
	}

	/**
	 * Compare alias
	 *
	 * @param string $alias
	 *
	 * @return bool
	 */
	public function is($alias){
		if(!is_array($alias))
			$alias = [$alias];

		return in_array(static::getStaticAlias(),$alias);
	}

	/**
	 * Return alias
	 *
	 * @return string
	 */
	public function getAlias(){
		return static::getStaticAlias();
	}

	/**
	 * Set value
	 *
	 * @param mixed $value
	 * 
	 * @return this
	 */
	public function value($value){
		$this -> value = $value;
		return $this;
	}

	/**
	 * Get default value
	 *
	 * @return mixed
	 */
	public function getValue(){
		return $this -> value;
	}

	/**
	 * Get value to string
	 *
	 * @return mixed
	 */
	public function getValueToString(){
		return $this -> value;
	}

	/**
	 * Set label
	 *
	 * @param string $label
	 *
	 * @return this
	 */
	public function label($label){
		$this -> label = $label;
		return $this;
	}

	/**
	 * Get label
	 *
	 * @return string
	 */
	public function getLabel(){
		return $this -> label ? $this -> label : $this -> name;
	}

	/**
	 * Show the element
	 *
	 * @param bool $show;
	 *
	 * @return this
	 */
	public function show($show = true){
		$this -> show = $show;
		return $this;
	}

	/**
	 * Get show
	 *
	 * @return bool
	 */
	public function getShow(){
		return $this -> show;
	}

	/**
	 * Hide the element
	 *
	 * @param bool $hidden
	 *
	 * @return void
	 */
	public function hidden($hidden = true){
		return $this -> show(!$hidden);
	}

	/**
	 * is Hidden?
	 *
	 * @return bool
	 */
	public function isHidden(){
		return !$this -> getShow();
	}

	/**
	 * Get relations
	 *
	 * @return array
	 */
	public function getRelations(){
		return [];
	}
}