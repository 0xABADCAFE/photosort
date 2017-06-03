<?php

namespace PhotoSort\Archive;

class DuplicatePhotoResolver implements \PhotoSort\Utility\IDuplicateResolver {
  public function resolve($oExisting, $sEPath, $oCurrent, $sCPath) {
    echo
      "Duplicate image found when indexing archive:\n",
      "\tA:", $this->formatRecord($oExisting, $sEPath), "\n",
      "\tB:", $this->formatRecord($oCurrent, $sCPath), "\n";
 
    echo
      "Please choose a resolution option:\n",
      "\t1) Index A, Ignore B\n",
      "\t2) Index B, Ignore A\n",
      "\t3) Index A, Delete B\n",
      "\t4) Index B, Delete A\n";
      
    $iOption = $this->promptOption(1, 4);
    
    switch ($iOption) {
      case 1:
        return $oExisting;
      case 2:
        return $oCurrent;
      case 3:
        return $oExisting;
      case 4:
        return $oCurrent;
    }
  }

  private function formatRecord($oRecord, $sPath) {
    return   $sPath .
      "/"  . $oRecord->n .
      ", " . date('d/m/Y H:i:s', $oRecord->m->t) .
      ", " . $oRecord->m->w .
      "x"  . $oRecord->m->h .
      ", " . $oRecord->m->s .
      " bytes";
  }
  
  private function promptOption($iFirst, $iLast) {
    $iOption = 0;
    do {
      echo "\nChoose ", $iFirst, "...", $iLast, ": ",
      $iOption = (int)trim(fgets(STDIN), "\n");
    } while ($iOption < $iFirst || $iOption > $iLast);
    return $iOption;
  }
}
