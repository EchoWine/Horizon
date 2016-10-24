<?php

namespace Auth\Field\Password;

use CoreWine\DataBase\ORM\Field\Field\Schema as FieldSchema;

class Schema extends FieldSchema{
	
	/**
	 * Model
	 *
	 * @var string
	 */
	public $__model = 'Auth\Field\Password\Model';

	/**
	 * Name
	 *
	 * @var string
	 */
	public $name = 'password';

	/**
	 * Column
	 *
	 * @var string
	 */
	public $column = 'password';

	/**
	 * Label
	 *
	 * @var string
	 */
	public $label = 'password';

	/**
	 * Regex of field
	 *
	 * @var string
	 */
	public $regex = "/^(.){0,255}$/iU";

	/**
	 * Required
	 *
	 * @var bool
	 */
	public $required = true;

	/**
	 * Max length
	 *
	 * @var int
	 */
	public $max_length = 128;

	/**
	 * Min length
	 *
	 * @var int
	 */
	public $min_length = 1;

	/**
	 * Include field in toArray operations
	 *
	 * @var bool
	 */
	public $enable_to_array = false;
}
?>