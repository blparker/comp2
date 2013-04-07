<?php

class TreeNode {
  public /* TreeNode[] */ $children = array();
  public /* TreeNode */ $sibling;
  public $value;

  public function add_child(TreeNode $child) {
    array_push($this->children, $child);
  }

  public function value($value = null) {
    if($value != null) {
      $this->value = $value;
    }
    else {
      return $this->value;
    }
  }
}

class ExprNode extends TreeNode {
  public function __construct(/*ExpKind*/ $kind) {
    $this->kind = $kind;
  }
}

class StmtNode extends TreeNode {
  public function __construct(/*SmtKind*/ $kind) {
    $this->kind = $kind;
  }
}

class StmtKind {
  const assignK = 'assignK';
  const ifK = 'ifK';
  const elifK = 'elifK';
  const elseK = 'elseK';
  const idK = 'idK';
  const funcK = 'funcK';
  const funcdefK = 'funcdefK';
  const forK = 'forK';
  const whileK = 'whileK';
  const dowhileK = 'dowhileK';
}

class ExpKind {
  const constK = 'constK';
  const opK  = 'opK';
  const idK = 'idK';
  const nullK = 'nullK';
  const strK = 'strK';
  const arglistK = 'arglistK';
  const relopK = 'relopK';
  const addopK = 'addopK';
  const multopK = 'multopK';
  const idlistK = 'idlistK';
}
