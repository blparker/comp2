<?php

class Scanner {
  private $input;
  private $idx = 0;
  private $ch;
  private $tokens = array();

  public function __construct($input) {
    $this->input = $input;
  }

  public function scan() {
    $len = strlen($this->input);

    for($i = 0; $i < $len; $i++) {
      echo "{$this->input[$i]}\n";
    }
    
    return null;
  }

}

$input = 
"foo = 'bar'";

$s = new Scanner($input);
$tokens = $s->scan();

