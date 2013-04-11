<?php

/*
*   class_prop = access_modifier [ 'static' ] IDENTIFIER [ '=' static_scalar ]
*              | [ access_modifier ] 'static' IDENTIFIER [ '=' static_scalar ]
*              | [ access_modifier ] [ 'static' ] IDENTIFIER '=' static_scalar
*/
private function class_prop_alt() {
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
    $static = new AttrNode(AttrKind::staticK);
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

    $scalar = $this->scalar();

    if($scalar != null) {
      $assign = new StmtNode(StmtKind::assignK);
      $assign->add_child($id);
      $assign->add_child($scalar);

      $prop = new StmtNode(StmtKind::classpropK);

      if($modifier != null) $prop->add_child($modifier);
      if($static != null) $prop->add_child($static);

      $prop->add_child($assign);
    }
    else {
      $this->backtrack($backtrack);
    }
  }
  else if($this->token_type() == TokenType::NL {
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
*   class_prop = access_modifier [ 'static' ] IDENTIFIER [ '=' static_scalar ]
*              | [ access_modifier ] 'static' IDENTIFIER [ '=' static_scalar ]
*              | [ access_modifier ] [ 'static' ] IDENTIFIER '=' static_scalar
*/
private function class_prop_alt() {
  /* TreeNode */ $prop = null;

  if($this->token_type() == TokenType::ID &&
     $this->token_type() == TokenType::EQ) {

    $id = new ExprNode(ExpKind::idK);
    $id->value($this->token_value();
    $this->match(TokenType::ID);
    $this->match(TokenType::EQ);

    $scalar = $this->scalar();

    if($scalar != null) {
      $assign = new StmtNode(StmtKind::assignK);
      $assign->add_child($id);
      $assign->add_child($scalar);

      $prop = new StmtNode(StmtKind::classpropK);
      $prop->add_child($assign);

      return $prop;
    }
    else {
      // Backtrack
      $this->backtrack(2);
      return null;
    }
  }

  /* AttrNode */ $modifier = null;
  /* AttrNode */ $static   = null;

  if($this->token_type() == TokenType::MODIFIER) {
    $modifier = new AttrNode(AttrKind::modifierK);
    $modifier->value($this->token_value());
  }
  
  if($this-token_type() == TokenType::_STATIC) {
    $static = new AttrNode(AttrKind::staticK);
    $static->value($this->token_value());
  }

  $id = new ExprNode(ExpKind::idK);
  $id->value($this->token_value();
  $this->match(TokenType::ID);

  if($this->token_type() == TokenType::EQ) {
    $assign = new StmtNode(StmtKind::assignK);
    $this->match(TokenType::EQ);

    $assign->add_child($id);

    $scalar = $this->scalar();
  }
  else if($this->token_type() != TokenType::NL) {
    $this->error();
  }

  /*if($this->token_type() == TokenType::MODIFIER) {
    $this->match(TokenType::MODIFIER);
    $this->match(TokenType::_STATIC);
    $this->match(TokenType::ID);
    $this->match(TokenType::EQ);
    $this->static_scalar();
  }
  else if($this->token_type() == TokenType::_STATIC) {
    $this->match(TokenType::_STATIC);
    $this->match(TokenType::ID);
    $this->match(TokenType::EQ);
    $this->static_scalar();
  }
  else if($this->token_type() == TokenType::ID) {
    $this->match(TokenType::ID);
    $this->match(TokenType::EQ);
    $this->static_scalar();
  }*/

  return $prop;
}


/*
  *   class_prop = access_modifier [ 'static' ] IDENTIFIER [ '=' static_scalar ]
  *              | [ access_modifier ] 'static' IDENTIFIER [ '=' static_scalar ]
  *              | [ access_modifier ] [ 'static' ] IDENTIFIER '=' static_scalar
  */
  private function class_prop_alt() {
    /* TreeNode */ $prop = null;

    if($this->token_type() == TokenType::MODIFIER) {
      $prop = new StmtKind(StmtKind::classpropK);

      $mod = new AttrNode(AttrKind::modifierK);
      $mod->value($this->token_value());
      $this->match(TokenType::MODIFIER);
      $prop->add_child($prop);

      if($this->token_type() == TokenType::_STATIC) {
        $static = new AttrNode(AttrKind::staticK);
        $this->match(TokenType::_STATIC);
        $prop->add_child($static);
      }

      // IDENTIFIER
      $id = new ExpKind(ExpKind::idK);
      $id->value($this->token_value());
      $this->match(TokenType::ID);

      if($this->token_type() == TokenType::EQ) {
        $assignNode = new StmtNode(StmtKind::assignK);
        $assignNode->add_child($id);

        $this->match(TokenType::EQ);
        $scalar = $this->static_scalar();
        $assignNode->add_child($scalar);

        $prop->add_child($assignNode);
      }
      else {
        $prop->add_child($id);
      }
    }
    else {
      if($this->token_type() == TokenType::_STATIC) {
        if($this->look_ahead(1)->type == TokenType::_STATIC && $this->look_ahead(2)->type == TokenType::ID) {
          $prop = new StmtKind(StmtKind::classpropK);

          $this->match(TokenType::_STATIC);
          $this->match(TokenType::ID);

          if($this->token_type() == TokenType::EQ) {
            $this->match(TokenType::EQ);
            $scalar = $this->static_scalar();
          }
        }
      }
      else if($this->token_type() == TokenType::ID) {
        $this->match(TokenType::ID);
        $this->match(TokenType::EQ);

        $scalar = $this->static_scalar();
      }
    }

    return $prop;
  }


  /*
  *   class_prop = access_modifier [ 'static' ] IDENTIFIER [ '=' static_scalar ]
  *              | [ access_modifier ] 'static' IDENTIFIER [ '=' static_scalar ]
  *              | [ access_modifier ] [ 'static' ] IDENTIFIER '=' static_scalar
  */
  private function class_prop() {
    /* TreeNode */ $prop = null;
    /* TreeNode */ $modifier = null;
    /* TreeNode */ $static = null;
    /* TreeNode */ $id = null;
    /* TreeNode */ $val = null;

    if($this->token_type() == TokenType::MODIFIER) {
      $modifier = new AttrNode(AttrKind::modifierK);
      $modifier->value($this->token_value());
      $this->match(TokenType::MODIFIER);
    }

    if($this->token_type() == TokenType::_STATIC) {
      $static = new AttrNode(AttrKind::staticK);
      $this->match(TokenType::_STATIC);
    }

    // IDENTIFIER
    $id = new ExprNode(ExpKind::idK);
    $id->value($this->token_value());
    $this->match(TokenType::ID);

    /* TreeNode */ $assign = null;

    if($this->token_type() == TokenType::EQ) {
      $assign = new StmtNode(StmtKind::assignK);
      $assign->add_child($id);

      $this->match(TokenType::EQ);
      $val = $this->static_scalar();

      if($val != null) {
        $assign->add_child($val);
      }
    }
    else {
      if($modifier == null && $static == null) {
        $this->error();
      }
    }

    $prop = new StmtNode(StmtKind::classpropK);

    if($modifier != null) {
      $prop->add_child($modifier);
    }

    $prop->add_child($id);

    return $prop;
  }

