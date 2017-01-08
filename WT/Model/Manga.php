<?php

namespace WT\Model;

use CoreWine\DataBase\ORM\Model;

use WT\Model\Queue\Chapter as QueueChapter;

class Manga extends Model implements Resource{

	/**
	 * Table name
	 *
	 * @var
	 */
	public static $table = 'manga';

	/**
	 * Set schema fields
	 *
	 * @param Schema $schema
	 */
	public static function fields($schema){

		$schema -> id();
		
		$schema -> string('name');

		$schema -> string('type');

		$schema -> string('genres');

		$schema -> text('overview');

		$schema -> text('status');

		$schema -> file('poster');

		$schema -> file('banner');

		$schema -> toOne(ResourceContainer::class,'container');

		$schema -> toMany('volumes',Volume::class,'manga');

		$schema -> toMany('chapters',Chapter::class,'manga');
		
		$schema -> updated_at();

	}

	/**
	 * Return a complete array of this model (usefull in api)
	 *
	 * @return array
	 */
	public function toArrayComplete(){

		$res = parent::toArray();

		foreach(Chapter::where('manga_id',$this -> id) -> get() as $chapter){
			$chapters[] = $chapter -> toArray();
		}
		
		return array_merge($res,[
			'type' => 'manga',
			'poster' => $this -> poster() -> thumb(540,780),
			'chapters' => $chapters,
			'container' => $this -> container -> toArray()
		]);
	}

	/**
	 * Return a complete array of this model
	 *
	 * @return array
	 */
	public function toArray(){

		$res = parent::toArray();
		
		return array_merge($res,[
			'type' => 'manga',
			'poster' => $this -> poster() -> thumb(540,780),
			'new' => \Auth::user() ? ChapterUser::where(['user_id' => \Auth::user() -> id,'manga_id' => $this -> id,'consumed' => 0]) -> count() : null
		]);
	}

	/**
	 * Fill this entity using a generic response from database api
	 *
	 * @param object $response
	 * @param Container $container
	 */
	public function fillFromDatabaseApi($response,$container){

		$this -> container = $container;

		$this -> name = $response -> name;
		$this -> type = $response -> type;
		$this -> overview = $response -> overview;
		$this -> status = $response -> status;

		if($response -> poster)
			$this -> poster() -> setByUrl($response -> poster);

		if($response -> banner)
			$this -> banner() -> setByUrl($response -> banner);

		$this -> save();

		$volumes_ids = [];
		$chapters_ids = [];


		$users = $this -> container -> users;

		foreach($response -> chapters as $r_chapter){

			
			$volume = Volume::firstOrCreate([
				'number' => $r_chapter -> season,
				'manga_id' => $this -> id,
				'number' => $r_chapter -> volume,
			]);


			$chapter = Chapter::firstOrNew([
				'number' => $r_chapter -> number,
				'manga_id' => $this -> id,
			]);	

			$new = !$chapter -> id;

			$chapter -> save();

			# Download only if new
			if($new){
				QueueChapter::create([
					'chapter_id' => $chapter -> id,
				]);
			}
			
			$volumes_ids[] = $volume -> id;
			$chapters_ids[] = $chapter -> id;

			$chapter -> volume_n = $r_chapter -> volume;
			$chapter -> volume = $volume;
			$chapter -> name = $r_chapter -> name;
			$chapter -> scan = $r_chapter -> scan;
			$chapter -> released_at = new \DateTime($r_chapter -> released_at);

			$chapter -> save();


			# Create if doesn't exists for every user that have access to this resource
			foreach($users as $user){
				$eu = ChapterUser::firstOrCreate([
					'container_id' => $this -> container_id,
					'manga_id' => $this -> id,
					'chapter_id' => $chapter -> id,
					'user_id' => $user -> id
				]);
			}

		}

		Volume::where('manga_id',$this -> id) -> whereNotIn('id',$volumes_ids) -> delete();
		Chapter::where('manga_id',$this -> id) -> whereNotIn('id',$chapters_ids) -> delete();

		# Remove all queue chapters that have been deleted
		QueueChapter::leftJoin('chapters','chapters.id','queue_chapters.chapter_id') -> whereNull('chapters.id') -> delete('queue_chapters.*');
	
	}

	/**
	 * Update consumed chapters
	 *
	 * @param user $user
	 * @param array $ids
	 *
	 * @return void
	 */
	public function updateConsumed($user,$ids){

		foreach($this -> chapters as $chapter){

			$eu = ChapterUser::firstOrCreate([
				'container_id' => $this -> container_id,
				'manga_id' => $this -> id,
				'chapter_id' => $chapter -> id,
				'user_id' => $user -> id
			]);

			$eu -> consumed = in_array($chapter -> id,$ids) ? 1 : 0;
			$eu -> save();

		}
	}
}

?>