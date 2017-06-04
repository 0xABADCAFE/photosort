<?php
/**
 * @package PhotoSort
 */
 
namespace PhotoSort\Utility;

interface IDuplicateResolver {
  public function resolve($oExisting, string $sEPath, $oCurrent, string $sCPath);
}
