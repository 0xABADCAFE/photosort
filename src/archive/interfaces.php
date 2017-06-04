<?php
/**
 * @package PhotoSort
 */

namespace PhotoSort\Archive;

interface IIndexer {
  public function writeIndex(string $sFile);
}

interface IIndexHasher {
  public function hash($oRecord);
}
