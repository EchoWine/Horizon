<?php

namespace WT\Model;

use CoreWine\DataBase\ORM\Model;
use Auth\Model\User;

class ResourceContainerUser extends Model{

    /**
     * Table name
     *
     * @var
     */
    public static $table = 'resource_containers_users';

    /**
     * Set schema fields
     *
     * @param Schema $schema
     */
    public static function fields($schema){

        $schema -> id();
        
        $schema -> toOne(User::class,'user') -> required();

        $schema -> toOne(ResourceContainer::class,'container') -> required();

        
    }
}

?>