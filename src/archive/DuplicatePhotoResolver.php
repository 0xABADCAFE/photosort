<?php
/**
 * @package PhotoSort
 */

namespace PhotoSort\Archive;

class DuplicatePhotoResolver implements \PhotoSort\Utility\IDuplicateResolver {
  public function resolve($oExisting, string $sEPath, $oCurrent, string $sCPath) {
    echo
      "Duplicate image found when indexing archive:\n",
      "\tA:", $this->formatRecord($oExisting, $sEPath), "\n",
      "\tB:", $this->formatRecord($oCurrent, $sCPath), "\n";

    $aOptions = [
      'A' => 'Display A',
      'B' => 'Display B',
      '1' => 'Index A, Ignore B',
      '2' => 'Index B, Ignore A',
      '3' => 'Index A, Delete B',
      '4' => 'Index B, Delete A',
    ];

    $sExistingFile = $sEPath . '/' . $oExisting->n;
    $sCurrentFile  = $sCPath . '/' . $oCurrent->n;

    while(true) {
      $sSelected = $this->promptOption(
        "Please choose a resolution option:",
        $aOptions
      );
      switch ($sSelected) {
        case 'A':
          shell_exec('gopen ' . escapeshellarg($sExistingFile) . ' &');
          break;
        case 'B':
          shell_exec('gopen ' . escapeshellarg($sCurrentFile) . ' &');
          break;
        case '1':
          return $oExisting;
        case '2':
          return $oCurrent;
        case '3':
          return $oExisting;
        case '4':
          return $oCurrent;
        default:
          break;
      }
    }
  }

  private function formatRecord($oRecord, string $sPath) {
    return   $sPath . $oRecord->n .
      ", " . date('d/m/Y H:i:s', $oRecord->m->t) .
      ", " . $oRecord->m->w .
      "x"  . $oRecord->m->h .
      ", " . $oRecord->m->s .
      " bytes";
  }

  private function promptOption(string $sMessage, array $aOptions) {
    $sSelected = null;
    $iAttempts = 0;
    do {
      echo $sMessage, "\n";
      if (0 == ($iAttempts++ % 10)) {
        foreach ($aOptions as $sOption => $sDescription) {
          echo "\t", $sOption, ") ", $sDescription, "\n";
        }
      }
      $sSelected = strtoupper(trim(fgets(STDIN), "\n"));
    } while (!isset($aOptions[$sSelected]));
    return $sSelected;
  }
}
