<?php

namespace Auth\Field\Password;

use CoreWine\DataBase\ORM\Field\Field\Model as FieldModel;

use Auth\Service\Auth;

class Model extends FieldModel{

	/**
	 * Retrieve a value out given a value raw
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function getValueOutByValueRaw($value){
		return null;
	}

	/**
	 * Retrieve a value raw given a value out
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function getValueRawByValueOut($value){
		return !empty($value) ? Auth::getHashPass($value) : null;
	}

	/**
	 * Add the field to query to add an model
	 *
	 * @param Repository $repository
	 *
	 * @return Repository
	 */
	public function addRepository($repository){
		return $repository -> addInsert($this -> getSchema() -> getColumn(),$this -> getValueRaw());
	}

	/**
	 * Add the field to query to edit an model
	 *
	 * @param Repository $repository
	 *
	 * @return Repository
	 */
	public function editRepository($repository){

		if($this -> getValueRaw() == null){
			return $repository;
		}

		return $repository -> addUpdate($this -> getSchema() -> getColumn(),$this -> getValueRaw());
	}

}
?>