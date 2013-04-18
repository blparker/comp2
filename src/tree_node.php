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

class AttrNode extends TreeNode {
  public function __construct(/*SmtKind*/ $kind) {
    $this->kind = $kind;
  }
}

class StmtKind {
  const assignK = 'assignK';
  const ifK = 'ifK';
  const elifK = 'elifK';
  const elseK = 'elseK';
  const funcK = 'funcK';
  const funcdefK = 'funcdefK';
  const forK = 'forK';
  const whileK = 'whileK';
  const dowhileK = 'dowhileK';
  const switchK = 'switchK';
  const caseK = 'caseK';
  const defaultK = 'defaultK';
  const classK = 'classK';
  const classpropK = 'classpropK';
  const classconstK = 'classconstK';
  const classmethodK = 'classmethodK';
  const interfaceK = 'interfaceK';
  const compoundidK = 'compoundidK';
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
  const arrayK = 'arrayK';
}

class AttrKind {
  const classentryK = 'classentryk';
  const modifierK = 'modifierK';
  const extendsK = 'extendsK';
  const implementsK = 'implementsK';
  const accesstypeK = 'accesstypeK';
  const selectorK = 'selectorK';
}

