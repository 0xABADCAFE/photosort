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
      "\t0) Display A\n",
      "\t1) Display B\n",
      "\t2) Index A, Ignore B\n",
      "\t3) Index B, Ignore A\n",
      "\t4) Index A, Delete B\n",
      "\t5) Index B, Delete A\n";

    while(true) {
      $iOption = $this->promptOption(0, 4);
      switch ($iOption) {
        case 0:
          shell_exec('gopen ' . escapeshellarg($sEPath) . ' &');
          break;
        case 1:
          shell_exec('gopen ' . escapeshellarg($sCPath) . ' &');
          break;
        case 2:
          return $oExisting;
        case 3:
          return $oCurrent;
        case 4:
          return $oExisting;
        case 5:
          return $oCurrent;
        default:
          break;
      }
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
