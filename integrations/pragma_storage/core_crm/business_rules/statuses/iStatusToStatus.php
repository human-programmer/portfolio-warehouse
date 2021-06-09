<?php


namespace PragmaStorage;


interface iStatusToStatus
{
    function getExportStatus (int $pipeline_id, int $entity_status_id) : iStatus;

    function setExportStatusLinks (array $links) : bool;

    public function getLinks () : array;
}