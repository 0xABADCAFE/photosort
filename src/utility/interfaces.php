<?php

namespace PhotoSort\Utility;

interface IDuplicateResolver {
  public function resolve($oExisting, $sEPath, $oCurrent, $oCPath);
}
