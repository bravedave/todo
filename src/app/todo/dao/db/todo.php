<?php
// file: src/todo/dao/db/todo.php

namespace todo\dao\db;

use bravedave;

$dbc = bravedave\dvc\sys::dbCheck('todo');

/**
 * note:
 *  id, autoincrement primary key is added to all tables - no need to specify
 *  field types are MySQL and are converted to SQLite equivalents as required
 */

$dbc->defineField('created', 'datetime');
$dbc->defineField('updated', 'datetime');

$dbc->defineField('description', 'text');

$dbc->check();  // actually do the work, check that table and fields exist
