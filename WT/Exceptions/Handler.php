<?php

namespace WT\Exceptions;

use Kernel\Exceptions\ExceptionHandler;

use CoreWine\Http\Exceptions\RouteException;

class Handler extends ExceptionHandler{

	public function report($exception){
	}

	public function render($exception){

		if($exception instanceof RouteException){
			return $this -> view("WT/errors/404");
		}
	}
}