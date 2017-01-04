<?php

namespace WT\Model;

use CoreWine\DataBase\ORM\Model;
use Auth\Model\User;

class EpisodeUser extends Model{

    /**
     * Table name
     *
     * @var
     */
    public static $table = 'episodes_users';

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
        $schema -> toOne(Serie::class,'serie') -> required();
        $schema -> toOne(Episode::class,'episode') -> required();

        
    }
}

?>