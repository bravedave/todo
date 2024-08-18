<?php
// file: src/todo/controller.php
namespace todo;

use bravedave\dvc\json;
use strings;

class controller extends \Controller {
  protected function _index() {

    $this->title = config::label;

    // 'aside' => fn() => $this->load('index'),
    $this->renderBS5([
      'main' => fn() => $this->load('matrix')
    ]);
  }

  protected function before() {
    config::todo_checkdatabase();  // add this line
    parent::before();

    $this->viewPath[] = __DIR__ . '/views/';  // location for module specific views
  }

  protected function postHandler() {

    $action = $this->getPost('action');

    if ('todo-add' == $action) {

      $a = [
        'description' => $this->getPost('description')
      ];
      $id = (new dao\todo)->Insert($a);
      json::ack($action)
        ->add('id', $id);
    } elseif ('todo-delete' == $action) {

      if ($id = (int)$this->getPost('id')) {

        (new dao\todo)->delete($id);
        json::ack($action);
      } else {

        json::nak($action);
      }
    } elseif ('todo-get-by-id' == $action) {

      if ($id = (int)$this->getPost('id')) {

        json::ack($action)
          ->add('dto', (new dao\todo)($id));
      } else {

        json::nak($action);
      }
    } elseif ('todo-get-matrix' == $action) {

      json::ack($action)
        ->data((new dao\todo)->getMatrix());
    } elseif ('todo-update' == $action) {

      if ($id = (int)$this->getPost('id')) {

        $a = [
          'description' => $this->getPost('description')
        ];
        (new dao\todo)->UpdateByID($a, $id);
        json::ack($action);
      } else {

        json::nak($action);
      }
    } else {

      parent::postHandler();
    }
  }
}
