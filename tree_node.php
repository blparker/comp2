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
}

class ExpKind {
  const constK = 'constK';
  const opK  = 'opK';
  const idK = 'idK';
}

