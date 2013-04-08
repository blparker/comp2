<?php

require_once('tree_node.php');
require_once('token.php');
require_once('tokentype.php');

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
  *   statement = if_stmt | for_stmt | while_stmt | do_while_stmt | assign_stmt | function_call | arith_stmt
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
    else if(($t = $this->while_stmt()) != null) {
      $this->pln("While Stmt");
    }
    else if(($t = $this->do_while_stmt()) != null) {
      $this->pln('Do while stmt');
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
  *   while_stmt = 'while' expr NEWLINE inner_block
  */
  private function while_stmt() {
    /* TreeNode */ $t = null;

    if($this->token_type() == TokenType::_WHILE) {
      $this->match(TokenType::_WHILE);

      $t = new StmtNode(StmtKind::whileK);

      $e = $this->expr();
      if($e == null) $this->error();

      $t->add_child($e);
    }

    return $t;
  }

  /*
  *   do_while_stmt = 'do' NEWLINE inner_block 'while' expr NEWLINE
  */
  private function do_while_stmt() {
    /* TreeNode */ $t = null;

    if($this->token_type() == TokenType::_DO) {
      // 'do'
      $this->match(TokenType::_DO);

      $t = new StmtNode(StmtKind::dowhileK);

      // NEWLINE
      $this->match(TokenType::NL);

      // inner_block
      $block = $this->inner_block();
      if($block != null) {
        $t->add_child($block);
      }

      // 'while'
      $this->match(TokenType::_WHILE);

      // expr
      $e = $this->expr();
      if($e == null) $this->error();

      $t->add_child($e);

      // NEWLINE
      $this->match($this->_or(TokenType::NL, TokenType::EOF));
    }

    return $t;
  }

  /*
  *   expanded_for = 'for' IDENTIFIER '=' expr ',' expr, [ ',' expr ] NEWLINE inner_block
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
  *   func_def_stmt  = IDENTIFIER '=' [ func_args_list ]  '->' NEWLINE inner_block
  */
  private function func_def_stmt() {
    /* TreeNode */ $t = null;

    // A function definition must begin with an identifier,
    // followed by an equal sign ('='), followed by a left
    // paren ('(')
    if($this->token_type() == TokenType::ID &&
       $this->look_ahead(1)->type == TokenType::EQ &&
       $this->look_ahead(2)->type == TokenType::LP) {

      $t = new StmtNode(StmtKind::funcdefK);
      $t->value($this->token_value());

      $this->match(TokenType::ID);
      $this->match(TokenType::EQ);

      if($this->token_type() == TokenType::LP) {
        $args = $this->func_args_list();

        if($args != null) {
          $t->add_child($args);
        }
      }
      else if($this->token_type() != TokenType::FUNCG) {
        $this->error();
      }

      $this->match(TokenType::FUNCG);
      $this->match(TokenType::NL);

      $block = $this->inner_block();

      if($block != null) {
        $t->add_child($block);
      }

      // inner_block
    }

    return $t;
  }

  /*
  *   func_args_list = '(' [ id_list ] ')'
  */
  private function func_args_list() {
    /* TreeNode */ $t = null;

    if($this->token_type() == TokenType::LP) {

      // '('
      $this->match(TokenType::LP);

      // id_list
      if($this->token_type() == TokenType::ID) {
        $t = new ExprNode(ExpKind::idlistK);

        // IDENTIFIER
        while($this->token_type() == TokenType::ID) {
          $i = new ExprNode(ExpKind::idK);
          $i->value($this->token_value());

          // IDENTIFIER
          $this->match(TokenType::ID);

          $t->add_child($i);

          // COMMA
          if($this->token_type() == TokenType::COMMA) {
            $this->match(TokenType::COMMA);
            continue;
          }
          else if($this->token_type() != TokenType::RP) {
            // If we don't have a comma, and the next token isn't the closing right paren, throw an error
            $this->error();
          }
        }
      }
      else if($this->token_type() == TokenType::RP) {
        // Don't do anything, we match it below
      }
      else {
        $this->error();
      }

      // ')'
      $this->match(TokenType::RP);
    }

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
    
    if(($t = $this->expr_addop()) != null) {
    }

    if($this->token_type() == TokenType::RELOP) {
      $r = new ExprNode(ExpKind::relopK);
      $r->value($this->token_value());

      $this->match(TokenType::RELOP);

      $q = $this->expr_addop();

      if($q == null) $this->error();

      $r->add_child($t);
      $r->add_child($q);

      $t = $r;
    }

    return $t;
  }

  private function expr_addop() {
    /* TreeNode */ $t = null;

    if(($t = $this->expr_term()) != null) {

      while($this->token_type() == TokenType::ADDOP) {
        if($this->token_type() == TokenType::ADDOP) {
          $r = new ExprNode(ExpKind::addopK);
          $r->value($this->token_value());

          $this->match(TokenType::ADDOP);

          $q = $this->expr_term();

          if($q == null) $this->error();

          $r->add_child($t);
          $r->add_child($q);

          $t = $r;
        }
      }
    }

    return $t;
  }

  private function expr_term() {
    /* TreeNode */ $t = null;

    if(($t = $this->expr_factor()) != null) {
      while($this->token_type() == TokenType::MULTOP) {
        if($this->token_type() == TokenType::MULTOP) {
          $r = new ExprNode(ExpKind::multopK);
          $r->value($this->token_value());

          $this->match(TokenType::MULTOP);

          $q = $this->expr_factor();

          if($q == null) $this->error();

          $r->add_child($t);
          $r->add_child($q);

          $t = $r;
        }
      }
    }

    return $t;
  }

  private function expr_factor() {
    /* TreeNode */ $t = null;
    /* TokenType */ $tokenType = $this->token_type();

    if($tokenType == TokenType::NUL) {
      $t = new ExprNode(ExpKind::nullK);

      $this->match(TokenType::NUL);
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
          $exprs = new ExprNode(ExpKind::arglistK);
        }
        $exprs->add_child($e);
      }

      if($this->token_type() == TokenType::COMMA) {
        $this->match(TokenType::COMMA);
        continue;
      }
      else if($this->token_type() == TokenType::NL ||
              $this->token_type() == TokenType::RP ||
              $this->token_type() == TokenType::EOF) {
        break;
      }
      else {
        $this->error();
      }
    }

    return $exprs;
  }


  /****************
  * Utility Methods
  ****************/
  private function match(/* TokenType */ $tokenType) {
    if(is_array($tokenType)) {
      if($this->token_type() == $tokenType[0] ||
         $this->token_type() == $tokenType[1]) {
        ++$this->idx;
      }
      else {
        throw new Exception("Match failed. Expected: '$tokenType'; Actual: '{$this->token_type()}' @ idx {$this->idx}");
      }
    }
    else {
      if($this->token_type() == $tokenType) {
        ++$this->idx;
      }
      else {
        throw new Exception("Match failed. Expected: '$tokenType'; Actual: '{$this->token_type()}' @ idx {$this->idx}");
      }
    }
  }

  private function _or($tokenType1, $tokenType2) {
    return array(
      $tokenType1, $tokenType2
    );
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

