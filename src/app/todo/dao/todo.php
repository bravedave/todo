<?php
// file : src/todo/dao/todo.php
namespace todo\dao;

use bravedave\dvc\{dao, dtoSet};
use currentUser;

class todo extends dao {
  protected $_db_name = 'todo';
  protected $template = dto\todo::class;

  function getMatrix(): array {

    $sql = 'SELECT * FROM `todo` WHERE `user_id` = ' . currentUser::id();
    return (new dtoSet)($sql);
  }

  public function Insert($a) {
    $a['created'] = $a['updated'] = self::dbTimeStamp();
    $a['user_id'] = currentUser::id();
    return parent::Insert($a);
  }

  public function UpdateByID($a, $id) {
    $a['updated'] = self::dbTimeStamp();
    return parent::UpdateByID($a, $id);
  }
}
