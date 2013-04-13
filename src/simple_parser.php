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
       $this->look_ahead(2)->type == TokenType::LP &&
       ($this->look_ahead(3)->type == TokenType::ID ||
       $this->look_ahead(3)->type == TokenType::RP)) {

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
  *
  */
  private function switch_stmt() {
    /* TreeNode */ $t = null;

    if($this->token_type() == TokenType::_SWITCH) {
      $this->match(TokenType::_SWITCH);

      $t = new StmtNode(StmtKind::switchK);

      $expr = $this->expr();
      if($expr == null) $this->error();

      $t->add_child($expr);

      $this->match(TokenType::NL);
      $this->match(TokenType::INDENT);

      while($this->token_type() == TokenType::_CASE) {
        $case = $this->switch_case_stmt();

        if($case == null) $this->error();

        $t->add_child($case);
      }

      if($this->token_type() == TokenType::_DEFAULT) {
        $d = new StmtNode(StmtKind::defaultK);
        $this->match(TokenType::_DEFAULT);

        $this->match(TokenType::NL);
        $b = $this->inner_block();

        if($b != null) {
          $d->add_child($b);
        }

        $t->add_child($b);
      }

      //$this->match(TokenType::NL);
      $this->match(TokenType::DEDENT);
    }

    return $t;
  }

  /*
  *
  */
  private function switch_case_stmt() {
    /* TreeNode */ $c = null;

    if($this->token_type() == TokenType::_CASE) {
        $c = new StmtNode(StmtKind::caseK);
        $this->match(TokenType::_CASE);

        $e = $this->expr();
        if($e == null) $this->error();

        $c->add_child($e);

        // NL
        $this->match(TokenType::NL);

        $b = $this->inner_block();
        if($b != null) {
          $c->add_child($b);
        }

        // NL
        $this->match(TokenType::NL);
    }

    return $c;
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

  /*
  *   class_def_stmt = [ class_entry ] 'class' IDENTIFIER [ class_def_rest ] NEWLINE class_body
  */
  private function class_def_stmt() {
    /* TreeNode */ $c = null;
    /* TreeNode */ $entry = null;

    // class_entry ('abstract' | 'final')
    if($this->token_type() == TokenType::_ABSTRACT ||
       $this->token_type() == TokenType::_FINAL) {

      $entry = new AttrNode(AttrKind::classentryK);
      $entry->value($this->token_value());

      $this->match(array(TokenType::_ABSTRACT, TokenType::_FINAL));
    }

    // 'class'
    $this->match(TokenType::_CLASS);
    $c = new StmtNode(StmtKind::classK);

    if($entry != null) $c->add_child($entry);

    $name = new ExprNode(ExpKind::idK);
    $name->value($this->token_value());
    $c->add_child($name);

    // IDENTIFIER
    $this->match(TokenType::ID);

    if($this->token_type() == TokenType::_EXTENDS) {
      $this->match(TokenType::_EXTENDS);
      
      $extends = new AttrNode(AttrKind::extendsK);

      $i = new ExprNode(ExpKind::idK);
      $i->value($this->token_value());
      $this->match(TokenType::ID);

      $extends->add_child($i);
      $c->add_child($extends);
    }

    if($this->token_type() == TokenType::_IMPLEMENTS) {
      $this->match(TokenType::_IMPLEMENTS);
      
      $implements = new AttrNode(AttrKind::implementsK);

      $ids = new ExprNode(ExpKind::idlistK);
      while($this->token_type() == TokenType::ID) {
        $i = new ExprNode(ExpKind::idK);
        $i->value($this->token_value());
        $ids->add_child($i);
        $this->match(TokenType::ID);

        if($this->token_type() == TokenType::COMMA) {
          $this->match(TokenType::COMMA);
          continue;
        }
        else {
          break;
        }
      }

      $implements->add_child($ids);
      $c->add_child($implements);
    }

    // Are we at the end of the token stream after declaring the class?
    if($this->token_type() == TokenType::EOF) {
      return $c;
    }

    // Have to account for an arbitrary number of newlines
    $this->match(TokenType::NL);
    $this->match(TokenType::INDENT);

    // Class property
    /* TreeNode */ $p = null;

    while($this->token_type() != TokenType::EOF && $this->token_type() != TokenType::DEDENT) {

      if($this->token_type() == TokenType::NL) {
        $this->match(TokenType::NL);
        continue;
      }

      if(($p = $this->class_method()) != null) {
        $this->pln('Class Method');
        $c->add_child($p);
      }
      else if(($p = $this->class_prop()) != null) {
        $c->add_child($p);
      }
      else if(($p = $this->class_const()) != null) {
        $c->add_child($p);
      }
      else {
        $this->error();
      }

    }
    return $c;
  }

  /*
  *   class_method = [ access_modifier ] { access_level } function_def_stmt
  */
  private function class_method() {
    /* TreeNode */ $m = null;
    /* TreeNode */ $modifier = null;
    /* TreeNode */ $access = null;
    $backtrack = 0;

    if($this->token_type() == TokenType::MODIFIER) {
      // access_modifier
      $modifier = new AttrNode(AttrKind::modifierK);
      $modifier->value($this->token_value());
      $this->match(TokenType::MODIFIER);
      $backtrack++;
    }

    while($this->token_type() == TokenType::ACCESS) {
      // access_level
      $access = new AttrNode(AttrKind::accessK);
      $access->value($this->token_value());
      $this->match(TokenType::ACCESS);
      $backtrack++;
    }

    $funcDef = $this->func_def_stmt();

    if($funcDef != null) {
      $m = new StmtNode(StmtKind::classmethodK);
      if($modifier != null) $m->add_child($modifier);
      if($access != null) $m->add_child($access);
      $m->add_child($funcDef);
    }
    else {
      $this->backtrack($backtrack);
    }

    return $m;
  }

  /*
  *   class_prop = access_modifier [ 'static' ] IDENTIFIER [ '=' static_scalar ]
  *              | [ access_modifier ] 'static' IDENTIFIER [ '=' static_scalar ]
  *              | [ access_modifier ] [ 'static' ] IDENTIFIER '=' static_scalar
  */
  private function class_prop() {
    /* TreeNode */ $prop = null;
    /* AttrNode */ $modifier = null;
    /* AttrNode */ $static = null;
    /* StmtNode */ $id = null;
    /* StmtNode */ $scalar = null;
    $backtrack = 0;

    if($this->token_type() == TokenType::MODIFIER) {
      $modifier = new AttrNode(AttrKind::modifierK);
      $modifier->value($this->token_value());
      $this->match(TokenType::MODIFIER);
      ++$backtrack;
    }

    if($this->token_type() == TokenType::_STATIC) {
      $static = new AttrNode(AttrKind::accesstypeK);
      $static->value($this->token_value());
      $this->match(TokenType::_STATIC);
      ++$backtrack;
    }

    // If our next token isn't an identifier, we're not doing the right production
    if($this->token_type() != TokenType::ID) {
      $this->backtrack($backtrack);
      return null;
    }

    $id = new ExprNode(ExpKind::idK);
    $id->value($this->token_value());
    $this->match(TokenType::ID);
    ++$backtrack;

    if($this->token_type() == TokenType::EQ) {
      $this->match(TokenType::EQ);
      ++$backtrack;

      $scalar = $this->static_scalar();

      if($scalar != null) {
        $assign = new StmtNode(StmtKind::assignK);
        $assign->add_child($id);
        $assign->add_child($scalar);

        $prop = new StmtNode(StmtKind::classpropK);

        if($modifier != null) $prop->add_child($modifier);
        if($static != null) $prop->add_child($static);

        $prop->add_child($assign);

        if($this->token_type() == TokenType::NL) {
          $this->match(TokenType::NL);
        }
        else {
          $this->error();
        }
      }
      else {
        $this->backtrack($backtrack);
      }
    }
    else if($this->token_type() == TokenType::NL) {
      if($modifier != null || $static != null) {
        $prop = new StmtNode(StmtKind::classpropK);

        if($modifier != null) $prop->add_child($modifier);
        if($static != null) $prop->add_child($static);

        $prop->add_child($id);
        
        // Match the newline here?
        $this->match(TokenType::NL);
      }
      else {
        // Invalid class property (not enough info)
        $this->error();
      }
    }

    return $prop;
  }

  /*
  *   class_const = 'const' IDENTIFIER '=' simple_type { ',' IDENTIFIER '=' simple_type }
  */
  private function class_const() {
    /* TreeNode */ $c = null;

    if($this->token_type() == TokenType::_CONST) {
      // 'const'
      $this->match(TokenType::_CONST);
      $c = new StmtNode(StmtKind::classconstK);

      // IDENTIFIER
      $id = new StmtNode(StmtKind::idK);
      $id->value($this->token_value());
      $this->match(TokenType::ID);

      // '='
      $this->match(TokenType::EQ);

      // simple_type
      $type = $this->simple_type();
            
      if($type == null) $this->error();

      $assign = new StmtNode(StmtKind::assignK);
      $assign->add_child($id);
      $assign->add_child($type);
      $c->add_child($assign);

      while($this->token_type() == TokenType::COMMA) {
        $this->match(TokenType::COMMA);
        $type = $this->simple_type();
      }

      if($this->token_type() == TokenType::NL) {
        $this->match(TokenType::NL);
      }
      else {
        $this->error();
      }
    }

    return $c;
  }

  /*
  *   static_scalar = simple_type | array_decl | static_class_reference
  */
  private function static_scalar() {
    /* TreeNode */ $scalar = null;

    if(($scalar = $this->simple_type()) != null) {
    }
    else if(($scalar = $this->array_decl()) != null) {
    }
    else if(($scalar = $this->static_class_reference()) != null) {
    }

    return $scalar;
  }

  /*
  *   interface_def_stmt = 'interface' IDENTIFIER [ 'extends' id_list ] NEWLINE interface_body
  */
  private function interface_def_stmt() {
    /* TreeNode */ $i = null;

    if($this->token_type() == TokenType::_INTERFACE) {
      // 'interface'
      $this->match(TokenType::_INTERFACE);

      $i = new StmtNode(StmtKind::interfaceK);

      $id = new StmtNode(StmtKind::idK);
      $id->value($this->token_value());
      $this->match(TokenType::ID);
      $i->add_child($id);

      if($this->token_type() == TokenType::_EXTENDS) {
        $this->match(TokenType::_EXTENDS);

        $ids = $this->id_list();
        if($ids == null) $this->error();

        $i->add_child($ids);
      }

      $this->match(TokenType::NL);
      $this->match(TokenType::INDENT);


      $this->match(TokenType::DEDENT);
    }

    return $i;
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
    $t = debug_backtrace();
    //print_r($t);
    $caller = array_shift($t);
    //echo "\n### Error ###\n{$caller['class']}->{$caller['function']}, line {$caller['line']}\n";
    throw new Exception('Unexpected token: "'. $this->tokens[$this->idx]->value .'"; parse error line: '. $caller['line']);
  }

  private function pln($str) {
    $t = debug_backtrace();
    //array_shift($t);
    $caller = array_shift($t);
    echo "{$caller['class']}->{$caller['function']}, line {$caller['line']} - {$str}\n";
  }

  private function is_type($type) {
    return $this->token_type() == $type;
  }

  private function backtrack($howMuch) {
    $this->idx -= $howMuch;
  }

}

