<?php
/**
 * @package PhotoSort
 */

namespace PhotoSort\Archive;

class Indexer implements IIndexer, \PhotoSort\Scanner\IScannedDataConsumer {

  public function __construct(\PhotoSort\Utility\IDuplicateResolver $oResolver, IIndexHasher $oHasher) {
    $this->oResolver = $oResolver;
    $this->oHasher   = $oHasher;
  }

  public function consume(string $sDirectory, array $aRecords) {
    echo $sDirectory, " : ", count($aRecords), "\n";

    $iDirId = 0;

    if (isset($this->kDirs[$sDirectory])) {
      $iDirId = $this->kDirs[$sDirectory];
    } else {
      $iDirId = $this->kDirs[$sDirectory] = ++$this->iNextDirId;
    }

    foreach ($aRecords as $sImage => $oRecord) {
      $sHash = $this->oHasher->hash($oRecord);

      $oCurrent = (object)[
        'p' => $iDirId,
        'n' => $sImage,
        'm' => $oRecord,
      ];

      if (isset($this->kHashed[$sHash])) {
        $aDirs     = array_flip($this->kDirs);
        $oExisting = $this->kHashed[$sHash];
        $oCurrent  = $this->oResolver->resolve(
          $this->kHashed[$sHash],
          $aDirs[$oExisting->p],
          $oCurrent,
          $sDirectory
        );
      }
      $this->kHashed[$sHash] = $oCurrent;
    }
    return $this;
  }

  public function showStats() {
    echo
      "Directories scanned: ", $this->iNextDirId, "\n",
      "Images indexed: ", count($this->kHashed), "\n";
    return $this;
  }

  public function writeIndex(string $sFile) {
    $oData = (object)[
      'd' => array_flip($this->kDirs),
      'h' => $this->kHashed
    ];
    file_put_contents($sFile, serialize($oData));
    return $this;
  }

  private
    $oResolver    = null,
    $oHasher      = null,
    $kHashed      = [],
    $kDirs        = [],
    $iNextDirId = 0
  ;
}

