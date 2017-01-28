<?php

namespace WT\Controller\Admin;

use CoreWine\Http\Router;
use CoreWine\Http\Request;
use Auth\Service\Auth;
use WT\Model\Episode;
use WT\Model\Chapter;
use WT\Model\EpisodeUser;
use WT\Model\ChapterUser;
use CoreWine\Component\Collection;

use CoreWine\Http\Controller as Controller;
use CoreWine\Component\DateTime;


class CalendarController extends Controller{

	/**
	 * Middleware
	 *
	 * @var Array
	 */
	public $middleware = ['Admin\Middleware\Authenticate'];

	/**
	 * Define your routes
	 *
	 * @param Router $router
	 */
	public function __routes($router){

		$router -> any('admin/wt/calendar/monthly','monthly') -> as('wt:admin.calendar.monthly');
	}
	
	/**
	 * @ANY
	 *
	 * @return Response
	 */
	public function monthly(Request $request){

		if(!\Auth::user() -> permission -> has(\Auth\Model\User::PEX_WT_BASIC))
			return abort(404);
		
		$datetime = new \DateTime();
		$month = $request -> query -> get('month',$datetime -> format('m'));
		$year = $request -> query -> get('year',$datetime -> format('Y'));

		# Create a collection of current period divining by weeks
		try{
			$datetime = DateTime::createByMonthAndYear($month,$year);
		}catch(\Exception $e){
			$datetime = (new DateTime()) -> startMonth();
			$month = $datetime -> getMonth();
			$year = $datetime -> getYear();
		}

		$collection = $datetime -> createCollectionMonth(true);

		# Retrieve episodes
		$resources = Episode::where("DATE_FORMAT(aired_at,'%m-%Y')","$month-$year")
		-> leftJoin('series','episodes.serie_id','series.id')
		-> leftJoin('resource_containers','series.container_id','resource_containers.id')
		-> leftJoin('resource_containers_users','resource_containers_users.container_id','resource_containers.id')
		-> where('resource_containers_users.user_id',Auth::user() -> id)
		-> select('episodes.*')
		-> setIndexResult('id')
		-> get();

		# Merge with consumed
		$ids = $resources -> map(function($value){ return $value -> id; });
		$resources_consumed = EpisodeUser::where('user_id',Auth::user() -> id) -> whereIn('episode_id',$ids -> toArray()) -> get();

		foreach($resources_consumed as $resource_consumed){
			$resources[$resource_consumed -> episode_id] -> consumed = $resource_consumed -> consumed;
		}


		# Merge episodes with collection of days
		foreach($resources as $resource){
			$aired_at = $resource -> aired_at;
			$collection[$aired_at -> getWeek()][$aired_at -> format('Y-m-d')]['data'][] = $resource;
		}
		
		# Retrieve chapters
		$resources = Chapter::where("DATE_FORMAT(released_at,'%m-%Y')","$month-$year")
		-> leftJoin('manga','chapters.manga_id','manga.id')
		-> leftJoin('resource_containers','manga.container_id','resource_containers.id')
		-> leftJoin('resource_containers_users','resource_containers_users.container_id','resource_containers.id')
		-> where('resource_containers_users.user_id',Auth::user() -> id)
		-> select('chapters.*')
		-> setIndexResult('id')
		-> get();

		# Merge with consumed
		$ids = $resources -> map(function($value){ return $value -> id; });
		$resources_consumed = ChapterUser::where('user_id',Auth::user() -> id) -> whereIn('chapter_id',$ids -> toArray()) -> get();

		foreach($resources_consumed as $resource_consumed){
			$resources[$resource_consumed -> chapter_id] -> consumed = $resource_consumed -> consumed;
		}

		# Merge episodes with collection of days
		foreach($resources as $resource){
			$released_at = $resource -> released_at;
			$collection[$released_at -> getWeek()][$released_at -> format('Y-m-d')]['data'][] = $resource;
		}

		return $this -> view('WT/admin/calendar-monthly',[
			'results' => $collection,
			'datetime' => $datetime,
			'today' => (new DateTime()) -> setTime(00,00,00),
		]);
	}
}

?>