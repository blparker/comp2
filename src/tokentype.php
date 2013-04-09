<?php

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
  const _WHILE = 'while';
  const _DO = 'do';
  const RELOP = 'relop';
  const ADDOP = 'addop';
  const MULTOP = 'multop';
  const FUNCG = '->';
  const _SWITCH = 'switch';
  const _CASE = 'case';
  const _DEFAULT = 'default';
}

