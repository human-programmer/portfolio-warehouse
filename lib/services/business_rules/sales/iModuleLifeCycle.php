<?php


namespace Services;


use Services\General\iNode;

interface iModuleLifeCycle {
	function installEvent(iNode $node): void;
	function activationEvent(iNode $node): void;
	function crmDisableEvent(iNode $node): void;
	function fatalErrorEvent(iNode $node, string $url): void;
}