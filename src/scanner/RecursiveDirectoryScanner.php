<?php
/**
 * @package PhotoSort
 */

namespace PhotoSort\Scanner;

/**
 * RecursiveDirectoryScanner
 *
 */
class RecursiveDirectoryScanner implements IScanner {

  public function __construct() {

  }

  public function addDirectoryExclusion(string $sDirExclusion) {
    $this->kDirExclusions[$sDirExclusion] = 1;
    return $this;
  }

  public function addFileVisitor(IFileVisitor $oVisitor) {
    $this->aFileVisitors[] = $oVisitor;
    return $this;
  }

  public function addDirectoryVisitor(IDirectoryVisitor $oVisitor) {
    $this->aDirVisitors[] = $oVisitor;
    return $this;
  }

  public function scan(string $sDirectory) {
    $this->enter($sDirectory);
  }

  private function enter(string $sDirectory) {
    foreach ($this->aDirVisitors as $oVisitor) {
      $oVisitor->visitDirectory($sDirectory);
    }
    $kSubdirs    = [];
    $oCurrentDir = dir($sDirectory);
    $sDirectory  = rtrim($sDirectory, '/');
    while (false !== ($sItem = $oCurrentDir->read())) {
      if (isset($this->kDirExclusions[$sItem])) {
        continue;
      }
      $sFullPath = $sDirectory . '/' . $sItem;
      if (is_link($sFullPath)) {
        continue;
      }
      if (is_dir($sFullPath)) {
        $kSubdirs[$sFullPath] = 1;
        continue;
      }

      foreach ($this->aFileVisitors as $oVisitor) {
        $oVisitor->visitFile($sDirectory, $sItem);
      }
    }
    $oCurrentDir->close();
    foreach ($this->aDirVisitors as $oVisitor) {
      $oVisitor->leaveDirectory();
    }
    if (count($kSubdirs)>0) {
      ksort($kSubdirs);
      foreach ($kSubdirs as $sSubDirectory => $dummy) {
        $this->enter($sSubDirectory);
      }
    }
  }

  private
    $kDirExclusions = [
      '.'  => 1,
      '..' => 1
    ],
    $aDirVisitors   = [],
    $aFileVisitors  = []
  ;
}
