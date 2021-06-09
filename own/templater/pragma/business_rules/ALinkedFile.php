<?php


namespace Templater\Pragma;


use Files\iFile;

require_once __DIR__ . '/../../../../modules/files/pragma/business_rules/iFile.php';

abstract class ALinkedFile implements IDocLink, \Files\iFile {

}