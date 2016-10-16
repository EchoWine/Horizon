<?php

namespace WT\Controller\Admin;

use CoreWine\Http\Router;
use CoreWine\Http\Request;
use Auth\Service\Auth;
use WT\Model\Episode;
use WT\Model\Chapter;
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

		$router -> any('admin/calendar/monthly','monthly');
	}
	
	/**
	 * @ANY
	 *
	 * @return Response
	 */
	public function monthly(Request $request){
		$datetime = new \DateTime();
		$month = $request -> query -> get('month',$datetime -> format('m'));
		$year = $request -> query -> get('year',$datetime -> format('Y'));

		# Create a collection of current period divining by weeks
		$datetime = DateTime::createByMonthAndYear($month,$year);
		$collection = $datetime -> createCollectionMonth(true);

		# Retrieve episodes
		$episodes = Episode::where("DATE_FORMAT(aired_at,'%m-%Y')","$month-$year") -> get();

		# Merge episodes with collection of days
		foreach($episodes as $episode){
			$aired_at = new DateTime($episode -> aired_at);
			$collection[$aired_at -> getWeek()][$aired_at -> format('Y-m-d')]['data'][] = $episode;
		}
		
		# Retrieve chapters
		$resources = Chapter::where("DATE_FORMAT(released_at,'%m-%Y')","$month-$year") -> get();

		# Merge episodes with collection of days
		foreach($resources as $resource){
			$released_at = new DateTime($resource -> released_at);
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