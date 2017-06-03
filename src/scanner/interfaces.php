<?php

namespace PhotoSort\Scanner;

/**
 * IDirectoryVisitor
 * 
 * Costomisable behaviour for entering and leaving directories.
 */
interface IDirectoryVisitor {
  /**
   * Visits a Directory.
   *
   * @param string $sDirName  The full path to the directory containing the file.
   */
  public function visitDirectory(string $sDirName);
  
  /**
   * Leaves the most recently entered Directory.
   */
  public function leaveDirectory();
}


/**
 * IFileVisitor
 *
 */
interface IFileVisitor {
  /**
   * Visits a file.
   *
   * @param string $sDirName  The full path to the directory containing the file.
   * @param string $sFileName The file name.
   */
  public function visitFile(string $sDirName, string $sFileName);
}

/**
 * IDirectoryScanner
 *
 */
interface IScanner {
  /**
   * Attach a FileVisitor that will be called for every file this Scanner cares about.
   * @param FileVisitor $oVisitor
   */ 
  public function addFileVisitor(IFileVisitor $oVisitor);

  /**
   * Attach a Directory that will be called for every directory this Scanner scans.
   * @param FileVisitor $oVisitor
   */
  public function addDirectoryVisitor(IDirectoryVisitor $oVisitor);

  /**
   * Scan the named path.
   * @param string $sPath
   */
  public function scan(string $sPath);
}

interface IScannedDataConsumer {
  public function consume(string $Directory, array $aData);
}

interface IDuplicatePhotoResolver {
  public function resolve($oExisting, $sEPath, $oCurrent, $oCPath);
}
