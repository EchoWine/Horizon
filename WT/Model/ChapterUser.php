<?php

namespace WT\Model;

use CoreWine\DataBase\ORM\Model;
use Auth\Model\User;

class ChapterUser extends Model{

    /**
     * Table name
     *
     * @var
     */
    public static $table = 'chapters_users';

    /**
     * Set schema fields
     *
     * @param Schema $schema
     */
    public static function fields($schema){

        $schema -> id();
        
        $schema -> toOne(User::class,'user') -> required();

        $schema -> integer('consumed');

        $schema -> toOne(ResourceContainer::class,'container') -> required();
        $schema -> toOne(Manga::class,'manga') -> required();
        $schema -> toOne(Chapter::class,'chapter') -> required();

        
    }
}

?>