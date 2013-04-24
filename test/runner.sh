#!/bin/sh

for f in *_test.php
do
  echo "Processing $f"
  php $f
done
