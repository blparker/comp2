<?php

class TreePrinter {
  private $indent = -2;
  private $indentAmt;

  public function print_tree(TreeNode $tree, $indentAmt = 2) {
    $this->indentAmt = $indentAmt;
    $this->indent = (0 - ($this->indentAmt));

    echo "\n\n";
    $this->_print_tree($tree);   
    echo "\n\n\n";
  }

  private function _print_tree($tree) {
    $this->indent += $this->indentAmt;

    while($tree != null) {
      $this->print_spaces();

      if($tree instanceof StmtNode) {
        switch($tree->kind) {
          case StmtKind::assignK:
            echo "assign to: {$tree->value}\n";
            break;
          case StmtKind::funcdefK:
            echo "function def: {$tree->value}\n";
            break;
          default:
            echo "{$tree->kind}\n";
            //echo "Unknown statement node";
            break;
        }
      }
      else if($tree instanceof ExprNode) {
        switch($tree->kind) {
          case ExpKind::constK:
            echo "const: {$tree->value}\n";
            break;
          case ExpKind::opK:
            echo "op: {$tree->value}\n";
            break;
          case ExpKind::idK:
            echo "id: {$tree->value}\n";
            break;
          case ExpKind::addopK:
            echo "addop: {$tree->value}\n";
            break;
          case ExpKind::relopK:
            echo "relop: {$tree->value}\n";
            break;
          case ExpKind::multopK:
            echo "multop: {$tree->value}\n";
            break;
          default:
            //echo "Unknown ExprNode kind\n";
            echo "{$tree->kind}\n";
            break;
        }
      }
      else if($tree instanceof AttrNode) {
        switch($tree->kind) {
          case AttrKind::modifierK:
            echo "modifier: {$tree->value}\n";
            break;
          default:
            echo "{$tree->kind}\n";
            break;
        }
      }
      else {
        echo "Unknown node kind\n";
      }

      for($i = 0; $i < count($tree->children); $i++) {
        $this->_print_tree($tree->children[$i]);
      }

      $tree = $tree->sibling;
    }
    
    $this->indent -= $this->indentAmt;
  }

  private function print_spaces() {
    for($s = 0; $s < $this->indent; $s++) {
      echo ' ';
    }
  }
}
