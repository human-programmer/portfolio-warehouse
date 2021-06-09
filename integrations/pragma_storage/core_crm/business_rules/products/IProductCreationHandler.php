<?php


namespace PragmaStorage;


interface IProductCreationHandler {
	function productCreateEvent(iProduct $product): void;
}