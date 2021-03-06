<?php
namespace Api;


use CoreWine\Http\Controller as HttpController;
use Api\Response;
use Api\Exceptions;
use CoreWine\Http\Request;
use Api\Service\Api;
use CoreWine\DataBase\DB;
use Auth\Service\Auth;

abstract class Controller extends HttpController{

	/**
	 * Name of obj in url
	 *
	 * @var string
	 */
	public $url = null;

	/**
	 * ClassName ORM\Model
	 *
	 * @var string
	 */
	public $model = null;

	/**
	 * User data
	 *
	 * @var bool
	 */
	public $user_data = false;

	/**
	 * Defining routes
	 */
	public function __routes($router){

		$url = $this -> url;

		$router -> get("/api/v1/crud/{$url}",'all');
		$router -> post("/api/v1/crud/{$url}",'add');
		$router -> post("/api/v1/crud/{$url}/{id}",'copy');
		$router -> get("/api/v1/crud/{$url}/{id}",'get');
		$router -> put("/api/v1/crud/{$url}/{id}",'edit');
		$router -> delete("/api/v1/crud/{$url}/{id}",'delete');

	}


	/**
	 * Get basic path api 
	 *
	 * @return string
	 */
	public function getApiURL(){

		return Api::url();
	}

	/**
	 * Get api url
	 *
	 * @return string
	 */
	public function getFullApiURL(){

		return Api::url()."{$this -> url}";
	}

	/**
	 * Check
	 */
	public function __boot(){

		if($this -> getUrl() == null)
			throw new Exceptions\UrlNullException(static::class);

		if($this -> getModel() == null)
			throw new Exceptions\ModelNullException(static::class);

		else if(!class_exists($this -> getModel()))
			throw new Exceptions\ModelNotExistsException(static::class,$this -> getModel());
		
	
	}

	/**
	 * Get user data attribute
	 *
	 * @return bool
	 */
	public function getUserData(){
		return $this -> user_data;
	}

	/**
	 * Get model
	 *
	 * @return string ClassName Model
	 */
	public function getModel(){
		return $this -> model;
	}

	/**
	 * Get url
	 *
	 * @return string
	 */
	public function getUrl(){
		return $this -> url;
	}

	/**
	 * Get Schema
	 *
	 * @return ORM\Schema
	 */
	public function getSchema(){
		return $this -> getModel()::schema();
	}

	/**
	 * Get Repository
	 *
	 * @return ORM\Repository
	 */
	public function getRepository($alias = null){
		return $this -> getModel()::repository($alias);
	}

	/**
	 * Get all records
	 *
	 * @return results
	 */
	public function all(Request $request){

		try{
			# Get repository alias _d0
			# This will prevent error with joins between same table
			$repository = $this -> getRepository('_d0');

			if($this -> getUserData())
				$repository = $repository -> where('_d0.user_id',Auth::user() -> id);

			# Request
			$page = $request -> query -> get('page',1);
			$show = $request -> query -> get('show',100);
			$sort = $request -> query -> get('desc','id');
			$sort = $request -> query -> get('asc',$sort);
			$search = $request -> query -> get('search',[]);

			$direction = $sort == $request -> query -> get('desc') ? 'desc' : 'asc';

			if(empty($sort))
				$sort = 'id';

			# SORTING
			if($sort){
				
				$repository = $repository -> sortBy($sort,$direction);

			}else{

				$repository = $repository -> sortBy();
			}

			foreach((array)$search as $field => $params){

				$values = self::getArrayParams($params);

				$repository = $repository -> find($field,$values);
			
			}

			$repository = $repository -> paginate($show,$page);
			$repository = $repository -> select('_d0.*');

			$results = $repository -> get();

			return new Response\ApiAllSuccess([
				'results' => $results -> toArray(),
				'pagination' => $results -> getPagination() -> toArray(),
				'sort' => ['field' => $sort,'direction' => $direction]
			]);

		}catch(\Exception $e){

			# Return exception
			return new Response\ApiException($e);
		}

	}

	/**
	 * Get a records
	 *
	 * @param int $id
	 *
	 * @return results
	 */
	public function get(Request $request,$id){

	
		# Return error if not found
		if(!$model = $this -> getModel()::first($id))
			return new Response\ApiNotFound();

		switch($request -> query -> get('filter')){
			case 'edit':

			break;
			default:

			break;
		}

		return new Response\ApiGetSuccess($model);
	}

	/**
	 * Add a new record
	 *
	 * @return \Api\Response\Response
	 */
	public function add(Request $request){

		try{

			# Create and retrieve a new model
			$model_class = $this -> getModel();
			$model = new $model_class;


			$values = $request -> request -> all();

			if($this -> getUserData())
				$values = array_merge($values,['user_id' => Auth::user() -> id]);

			$model -> fill($values);

			$this -> __insert($model);
			$this -> __save($model);

			$model -> save();


			# Get last validation
			$errors = $this -> getModel()::getLastValidate();

			# Return error if validation failed
			if(!empty($errors))
				return new Response\ApiFieldsInvalid($errors);

			# Return success
			return new Response\ApiAddSuccess($model);

		}catch(\Exception $e){

			# Return exception
			return new Response\ApiException($e);
		}

	}	

	/**
	 * Edit record
	 *
	 * @param int $id
	 *
	 * @return \Api\Response\Response
	 */
	public function edit(Request $request,$id){

		try{

			# Return error if not found
			if(!$model = $this -> getModel()::first($id))
				return new Response\ApiNotFound();

			# Get an "old model"
			$old_model = $model -> getClone();


			$model -> fill($request -> request -> all());
			$model -> save();


			# Get last validation
			$errors = $this -> getModel()::getLastValidate();

			# Return error if validation failed
			if(!empty($errors))
				return new Response\ApiFieldsInvalid($errors);

			return new Response\ApiEditSuccess($model,$old_model);

		}catch(\Exception $e){

			# Return exception
			return new Response\ApiException($e);
		}

	}

	/**
	 * Remove a new record
	 *
	 * @param mixed $id
	 *
	 * @return \Api\Response\Response
	 */
	public function delete(Request $request,$id){

		try{

			$models = [];

			foreach(self::getArrayParams($id) as $id){

				# Return error if not found
				if(!$model = $this -> getModel()::first($id))
					return new Response\ApiNotFound();

				# Delete
				$model -> delete();

				$models[] = $model;

			}
			
			# Return success
			return new Response\ApiDeleteSuccess($models);

		}catch(\Exception $e){

			# Return exception
			return new Response\ApiException($e);
		}


	}

	/**
	 * Copy a new record
	 *
	 * @param mixed $id
	 *
	 * @return \Api\Response\Response
	 */
	public function copy(Request $request,$id){

		try{

			$models = [];

			foreach(self::getArrayParams($id) as $id){

				# Return error if not found
				if(!$from_model = $this -> getModel()::first($id))
					return new Response\ApiNotFound();

				# Copy
				$new_model = $this -> getModel()::copy($from_model);


				if(!$new_model){
					throw new \Exception("Unexpected error: ".json_encode($this -> getModel()::getLastValidate()));
				}

				$models[] = ['from' => $from_model,'new' => $new_model];

			}


			# Return success
			return new Response\ApiCopySuccess($models);

		}catch(\Exception $e){

			# Return exception
			return new Response\ApiException($e);
		}

	}

	public function getArrayParams($params){

		$params = preg_split('|(?<!\\\);|', $params);

		array_walk(
		    $params,
		    function(&$item){
		        $item = str_replace('\;', ';', $item);
		    }
		);

		if(!is_array($params))
			$params = [$params];

		return $params;

	}

	/**
	 * Insert a model
	 *
	 * @param ORM\Model $model
	 */
	public function __insert($model){

	}

	/**
	 * Save a model
	 *
	 * @param ORM\Model $model
	 */
	public function __save($model){
	}

}


?>