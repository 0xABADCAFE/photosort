<?php
/**
 * @package PhotoSort
 */

namespace PhotoSort\Scanner;

class IndexedBackupScanner implements \PhotoSort\Scanner\IScannedDataConsumer {

  const DUPEDIR_NAME = '.duplicates';

  public function __construct(\PhotoSort\Archive\IIndexHasher $oHasher, string $sIndexPath) {
    $this->oIndex  = unserialize(file_get_contents($sIndexPath));
    $this->oHasher = $oHasher;
  }

  public function consume(string $sDirectory, array $aData) {
    echo $sDirectory, ": ", count($aData), "\n";
    foreach ($aData as $sImage => $oRecord) {
      $sHash = $this->oHasher->hash($oRecord);
      if (isset($this->oIndex->h[$sHash])) {
        $oExisting = $this->oIndex->h[$sHash];
        $sPath     = $this->oIndex->d[$oExisting->p];
        echo "Found duplicate:\n\t", $sDirectory, $sImage, "\n\t", $sPath, $oExisting->n, "\n";
        $this->trash($sDirectory, $sImage);
        $this->iDuplicates++;
      } else {
        $this->iNewImages++;
      }
    }
    $this->debug();
  }

  public function debug() {
    echo "Duplicates found: ", $this->iDuplicates, ", New Images: ", $this->iNewImages, "\n";
  }

  private function trash(string $sDirectory, $sImage) {
    $sDupeDir = $sDirectory . "/" . self::DUPEDIR_NAME;
    if (!file_exists($sDupeDir)) {
      if (false === mkdir($sDupeDir)) {
        throw new Exception("Unable to create " . self::DUPEDIR_NAME . " directory in " . $sDirectory);
      }
    } else if (!is_writable($sDupeDir)) {
      throw new Exception($sDupeDir . " is not writeable");
    }
    rename($sDirectory . "/" . $sImage, $sDupeDir . "/" . $sImage);
  }

  private
    $oIndex       = null,
    $oHasher      = null,
    $iDuplicates  = 0,
    $iNewImages   = 0
  ;
}
