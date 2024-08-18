<?php
// file: src/todo/dao/dto/todo.php
namespace todo\dao\dto;

use bravedave\dvc\dto;

class todo extends dto {
  public $id = 0;
  public $created = '';
  public $updated = '';

  public $description = '';

  public $user_id = 0;
}
