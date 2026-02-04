<?php

require_once __DIR__ . '/../spl_autoload_register.php';

use App\Database\Connection;
use App\Database\Schema;

Schema::createTables(Connection::getInstance());
Schema::renderTablesAsHtml(Connection::getInstance());
