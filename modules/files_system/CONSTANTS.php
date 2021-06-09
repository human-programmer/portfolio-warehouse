<?php

namespace FilesSystem;

require_once __DIR__ . '/../../lib/services/CONSTANTS.php';

const FAILED_TO_SAVE_FILE = 1001;
const FILE_NOT_FOUND = 1002;
const TYPE_FILE_IS_FILE = 0;
const TYPE_FILE_IS_DIR = 1;
const DAY_DIR_FORMAT = 'Y.m.d';//никогда не менять