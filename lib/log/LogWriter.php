<?php

interface LogWriter
{
    function add(string $message, $params = null);

    function set_container(string $container_name);

    function send_error(Exception $e, string $message = null);

    function save_log ();

    function setPrefix(string $prefix): void;
}