<?php


namespace Services\General;


interface iRemovalInspector {
	function allowedToDeleteEntity (int $pragma_Entity_id) : bool;
	function allowedToDeleteField (int $pragma_field_id) : bool;
}