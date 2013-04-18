<?php

class CodeGenerator {
  private $tree = null;

  public function __construct($tree) {
    $this->tree = $tree;
  }

  public function generate() {
    //for($i = 0; $i < count($this->tree->children); $i++) {
    //}

    $subTree = $this->tree;
    $fn = $this->tree->kind;
    $this->$fn($this->tree);
  }

  private static $IF_STMT = "if(__%EXPR%__){\n\t__%BODY%__\n}\n";
  private function ifK($node) {

    // First generate children
    $expr = $this->exprK($node->children[0]);

    // Generate if stmt first
    $template = $this->template(self::$IF_STMT);
    $compiled = $template->compile(array(
      'expr' => $expr
    ));

    echo $compiled;
  }

  private function exprK($node) {
    $fn = $node->kind;
    $expr = $this->$fn($node);
    return $expr;
  }

  private static $CONST = "__%CONST%__";
  private function constK($node) {
    $template = $this->template(self::$CONST);
    $compiled = $template->compile(array(
      'const' => $node->value()
    ));

    return $compiled;
  }

  private function template($template) {
    return new Templater($template);
  }
}

class Templater {
  private $template = null;

  public function __construct($template) {
    $this->template = $template;
  }

  public function compile($values) {
    foreach($values as $key => $val) {
      $key = strtoupper($key);
      $this->template = str_replace("__%$key%__", $val, $this->template);
    }
    return $this->template;
  }
}

