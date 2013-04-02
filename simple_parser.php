<?php

require_once('tree_node.php');

class Parser {
  private $tokens;
  private $idx = 0;

  public function __construct($tokens) {
    $this->tokens = $tokens;
  }

  public function parse() {
    /* TreeNode */ $t = $this->program();

    if(!$this->at_end()) {
      $this->error();
    }

    return $t;
  }

  /****************
  * Private API
  ****************/
  /*
  *   program = block
  */
  private function program() {
    /* TreeNode */ $t = $this->block();

    return $t;
  }
  
  /*
  *   block = statement { NEWLINE statement }
  */
  private function block() {
    /* TreeNode */ $t = $this->statement();
    /* TreeNode */ $p = $t;

    while($this->token_type() != TokenType::EOF && $this->token_type() != TokenType::DEDENT) {
      $this->match(TokenType::NL);
      /* TreeNode */ $q = $this->statement();

      if($q != null) {
        if($t == null) {
          $t = $p = $q;
        }
        else {
          $p->sibling = $q;
          $p = $q;
        }
      }
    }

    return $t;
  }

  /*
  * inner_block = INDENT block DEDENT
  */
  private function inner_block() {
    $this->match(TokenType::INDENT);
    $t = $this->block();
    $this->match(TokenType::DEDENT);

    return $t;
  }

  /*
  *   statement = if_stmt | for_stmt | assign_stmt | function_call | arith_stmt
  */
  private function statement() {
    /* TreeNode */ $t = null;

    //$tokenType = $this->token_type();

    //if($tokenType == TokenType::_IF) {
    if(($t = $this->if_stmt()) != null) {
      $this->pln("IF STMT");
    }
    else if(($t = $this->for_stmt()) != null) {
      $this->pln("For Stmt");
    }
    else if(($t = $this->assign_stmt()) != null) {
      $this->pln("Assign Stmt");
    }
    else if(($t = $this->function_call()) != null) {
      $this->pln("FUNCTION CALL");
    }
    else if(($t = $this->arith_stmt()) != null) {
      $this->pln("Arith Stmt");
    }
    /*else {
      debug_print_backtrace();
      echo "\nCurrent Token: {$this->token_type()}\n";
      die("NOT SUPPOSED TO BE HERE\n");
    }*/

    return $t;
  }


  /****************
  * Statements
  ****************/
  /*
  *   if_stmt = 'if' expr NEWLINE inner_block { 'else if' expr NEWLINE inner_block } [ 'else' NEWLINE inner_block ]
  */
  private function if_stmt() {
    $tokenType = $this->token_type();
    /* TreeNode */ $t = null;

    if($tokenType == TokenType::_IF) {
      $t = new StmtNode(StmtKind::ifK);
      $this->match(TokenType::_IF);

      $expr = $this->expr();

      if($expr == null) $this->error();

      $t->add_child($expr);
      $this->match(TokenType::NL);

      $block = $this->inner_block();

      if($block == null) $this->error();

      $t->add_child($block);

      /*if($this->token_type() == TokenType::ELIF) {
        while($this->token_type() == TokenType::ELIF) {
          $s = new StmtNode(StmtKind::elifK);
          $this->match(TokenType::ELIF);
        }
      }*/

      if($this->token_type() == TokenType::ELS) {
        $this->match(TokenType::ELS);
        $this->match(TokenType::NL);
        $e = $this->inner_block();

        if($e == null) $this->error();

        $t->add_child($e);
      }
    }

    return $t;
  }

  /*
  *   for_stmt = expanded_for | condensed_for
  */
  private function for_stmt() {
    /* TreeNode */ $t = null;

    if($this->token_type() == TokenType::_FOR) {
      $this->match(TokenType::_FOR);

      $t = new StmtNode(StmtKind::forK);
      
      // Expanded for
      if($this->token_type() == TokenType::ID &&
         $this->look_ahead(1)->type == TokenType::EQ) {

        $i = new ExprNode(ExpKind::idK);
        $i->value = $this->token_value();
        $t->add_child($i);

        // IDENTIFIER
        $this->match(TokenType::ID);

        // '='
        $this->match(TokenType::EQ);

        // expr
        $e = $this->expr();
        if($e == null) $this->error();

        $i->add_child($e);

        // ','
        $this->match(TokenType::COMMA);

        // expr
        $e = $this->expr();
        if($e == null) $this->error();

        $t->add_child($e);

        if($this->token_type() == TokenType::COMMA) {
          $this->match(TokenType::COMMA);

          $e = $this->expr();
          if($e == null) $this->error();

          $t->add_child($e);
        }
      }
      else {
        while($this->token_type() == TokenType::ID) {
          $i = new ExprNode(ExpKind::idK);
          $i->value = $this->token_value();
          $t->add_child($i);

          // IDENTIFIER
          $this->match(TokenType::ID);

          if($this->token_type() == TokenType::IN) {
            $this->match(TokenType::IN);
            break;
          }

          $this->match(TokenType::COMMA);
        }

        // expr_list
        $args = $this->expr_list();
        if($args != null) {
          $t->add_child($args);
        }
      }

      // NEWLINE
      $this->match(TokenType::NL);

      $i = $this->inner_block();

      if($i != null) {
        $t->add_child($i);
      }

    }

    return $t;
  }

  /*
  *   expanded_for  = 'for' IDENTIFIER '=' expr ',' expr, [ ',' expr ] NEWLINE inner_block
  */
  private function expanded_for() {
    /* TreeNode */ $t = null;

    return $t;
  }

  /*
  *   condensed_for = 'for' identifier_list 'in' expr_list NEWLINE inner_block
  */
  private function condensed_for() {
    /* TreeNode */ $t = null;

    return $t;
  }

  /*
  *   assign_stmt = 'var' IDENTIFIER [ '=' expr ] | IDENTIFIER '=' expr
  */
  private function assign_stmt() {
    /* TreeNode */ $t = null;
    $tokenType = $this->token_type();

    if($tokenType == TokenType::ID &&
       $this->look_ahead(1)->type == TokenType::EQ) {

      $t = new StmtNode(StmtKind::assignK);
      $t->value($this->token_value());

      $this->match(TokenType::ID);
      $this->match(TokenType::EQ);

      $expr = $this->expr();
      $t->add_child($expr);
    }

    return $t;
  }

  /*
  *   function_call = IDENTIFIER '(' [ expr_list ] ')' | IDENTIFIER expr_list
  */
  private function function_call() {
    /* TreeNode */ $t = null;
    /* TokenType */ $tokenType = $this->token_type();

    if($tokenType == TokenType::ID) {

      $t = new StmtNode(StmtKind::funcK);
      $t->value = $this->token_value();

      $this->match(TokenType::ID);

      if($this->token_type() == TokenType::LP) {  // With parens
        $this->match(TokenType::LP);
        $args = $this->expr_list();
        if($args != null) {
          $t->add_child($args);
        }
        $this->match(TokenType::RP);
      }
      else {  // Without parens
        $args = $this->expr_list();
        if($args == null) {
          // Need to backtrack
          --$this->idx;
          return null;
        }
        else {
          $t->add_child($args);
        }
      }
    }

    return $t;
  }

  // arith_stmt = 
  public function arith_stmt() {
    return null;
  }
  /***************
  * End Statements
  ***************/


  /*
  *   expr = null | function_call | simple_type
  */
  private function expr() {
    /* TreeNode */ $t = null;
    /* TokenType */ $tokenType = $this->token_type();

    if($tokenType == TokenType::NUL) {
      $t = new ExprNode(ExpKind::nullK);
    }
    else if(($t = $this->function_call()) != null) {
    }
    else if(($t = $this->simple_type()) != null) {
    }

    return $t;
  }

  /*
  *   simple_type = true | false | NUMBER | STRING | IDENTIFIER
  */
  private function simple_type() {
    /* TreeNode */ $t = null;
    /* TokenType */ $tokenType = $this->token_type();

    switch($tokenType) {
      case TokenType::TRUE:
        $t = new ExprNode(ExpKind::constK);
        $t->value($this->token_value());
        $this->match(TokenType::TRUE);
        break;
      case TokenType::FALSE:
        $t = new ExprNode(ExpKind::constK);
        $t->value($this->token_value());
        $this->match(TokenType::FALSE);
        break;
      case TokenType::NUM:
        $t = new ExprNode(ExpKind::constK);
        $t->value($this->token_value());
        $this->match(TokenType::NUM);
        break;
      case TokenType::STR:
        $t = new ExprNode(ExpKind::constK);
        $t->value($this->token_value());
        $this->match(TokenType::STR);
        break;
      case TokenType::ID:
        $t = new ExprNode(ExpKind::idK);
        $t->value($this->token_value());
        $this->match(TokenType::ID);
        break;
    }

    return $t;
  }

  /*
  *   expr_list = { expr ',' } expr
  */
  private function expr_list() {
    $exprs = null;

    while(($e = $this->expr()) != null) {

      if($e != null) {
        if($exprs == null) {
          $exprs = new ExprNode(ExpKind::argK);
        }
        $exprs->add_child($e);
      }

      //if($this->token_type() == TokenType::NL) {
        //$this->match(TokenType::NL);
        //break;
      //}

      if($this->token_type() == TokenType::COMMA) {
        $this->match(TokenType::COMMA);
        continue;
      }

      if($this->token_type() == TokenType::NL) {
        break;
      }
    }

    return $exprs;
  }


  /****************
  * Utility Methods
  ****************/
  private function match(/* TokenType */ $tokenType) {
    if($this->token_type() == $tokenType) {
      ++$this->idx;
    }
    else {
      throw new Exception("Match failed. Expected: '$tokenType'; Actual: '{$this->token_type()}' @ idx {$this->idx}");
    }
  }

  private function token_type() {
    if($this->idx < count($this->tokens)) {
      return $this->tokens[$this->idx]->type;
    }
  }

  private function token_value() {
    return $this->tokens[$this->idx]->value;
  }

  public function at_end() {
    return $this->token_type() == TokenType::EOF;
  }

  public function look_ahead($howMany) {
    if(($howMany + $this->idx) < count($this->tokens)) {
      return $this->tokens[$this->idx + $howMany];
    }

    return null;
  }

  private function error() {
    throw new Exception('Unexpected token: "'. $this->tokens[$this->idx]->value .'"');
  }

  private function pln($str) {
    $t = debug_backtrace();
    //array_shift($t);
    $caller = array_shift($t);
    echo "{$caller['class']}->{$caller['function']}, line {$caller['line']} - {$str}\n";
  }

}


class TokenType {
  const LTE = '<=';
  const LT  = '<';
  const GT  = '>';
  const GTE = '>=';
  const EQC = '==';
  const NE  = '!=';
  const ADD = '+';
  const SUB = '-';
  const MUL = '*';
  const DIV = '/';
  const LP  = '(';
  const RP  = ')';
  const NUM = 'number';
  const EOF = 'eof';
  const NL = 'nl';
  const ID = 'id';
  const EQ = '=';
  const _IF = 'if';
  const TRUE = 'true';
  const FALSE = 'false';
  const NUL = 'null';
  const STR = 'string';
  const ELIF = 'else if';
  const ELS = 'else';
  const INDENT = 'indent';
  const DEDENT = 'dedent';
  const COMMA = 'comma';
  const _FOR = 'for';
  const IN = 'in';
}

class Token {
  public $type;
  public $value;
  public function __construct($type, $value = null) {
    $this->type = $type;
    $this->value = $value;
  }
}

/*$tokens = array(
  new Token(TokenType::_IF, 'if'),
  new Token(TokenType::TRUE, 'true'),
  new Token(TokenType::NL, ''),
  new Token(TokenType::INDENT, ''),
  new Token(TokenType::ID, 'foo'),
  new Token(TokenType::EQ, '='),
  new Token(TokenType::STR, 'bar'),
  new Token(TokenType::NL, ''),
  new Token(TokenType::ID, 'echo'),
  new Token(TokenType::STR, 'got a bar!'),
  new Token(TokenType::NL, ''),
  new Token(TokenType::DEDENT, ''),
  new Token(TokenType::ELS, 'else'),
  new Token(TokenType::NL, ''),
  new Token(TokenType::INDENT, ''),
  new Token(TokenType::ID, 'biz'),
  new Token(TokenType::EQ, '='),
  new Token(TokenType::STR, 'baz'),
  new Token(TokenType::NL, ''),
  new Token(TokenType::DEDENT, ''),
  new Token(TokenType::EOF, 'eof')
);*/
/*$tokens = array(
  new Token(TokenType::_FOR,    'for'),
  new Token(TokenType::ID,      'foo'),
  new Token(TokenType::EQ,      '='),
  new Token(TokenType::NUM,     '1'),
  new Token(TokenType::COMMA,   ','),
  new Token(TokenType::NUM,     '5'),
  new Token(TokenType::COMMA,   ','),
  new Token(TokenType::NUM,     '1'),
  new Token(TokenType::NL,      'nl'),
  new Token(TokenType::INDENT,  'indent'),
  new Token(TokenType::ID,      'echo'),
  new Token(TokenType::ID,      'foo'),
  new Token(TokenType::NL,      'nl'),
  new Token(TokenType::DEDENT,  'dedent'),
  new Token(TokenType::EOF,     'eof')
);*/
$tokens = array(
  new Token(TokenType::_FOR,  'for'),
  new Token(TokenType::ID,    'foo'),
  new Token(TokenType::COMMA, ','),
  new Token(TokenType::ID,    'bar'),
  new Token(TokenType::IN,    'in'),
  new Token(TokenType::ID,    'biz'),
  new Token(TokenType::LP,    '('),
  new Token(TokenType::RP,    ')'),
  new Token(TokenType::NL,    'nl'),
  new Token(TokenType::INDENT,'indent'),
  new Token(TokenType::ID,    'echo'),
  new Token(TokenType::ID,    'foo'),
  new Token(TokenType::NL,    'nl'),
  new Token(TokenType::DEDENT,'dedent'),
  new Token(TokenType::EOF,   'eof')
);

$parser = new Parser($tokens);
$t = $parser->parse();

//print_r($t);

require_once('tree_printer.php');

$tp = new TreePrinter();
$tp->print_tree($t);


