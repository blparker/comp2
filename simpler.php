<?php

/*

statement   = assign_stmt
            | arith_stmt
            | function_call
assign_stmt = 'var' IDENTIFIER [ '=' expr ]
            | IDENTIFIER '=' expr
expr        = null
            | simple_type
            | compound_type
            | function
            | function_call
simple_type = true | false | NUMBER | STRING | IDENTIFIER
compound_type = array_decl

*/

