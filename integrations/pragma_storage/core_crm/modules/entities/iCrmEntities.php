<?php


namespace PragmaStorage;


interface iCrmEntities
{
    function getEntityForStore (int $pragma_entity_id) : iEntityForStore;
}