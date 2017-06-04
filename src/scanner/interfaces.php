<?php
/**
 * @package PhotoSort
 */

namespace PhotoSort\Scanner;

/**
 * IDirectoryVisitor
 *
 * Customisable behaviour for entering and leaving directories.
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
 * Invoked for each (image) file encountered.
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
 * Scans a directory, invoking any registered IFileVisitor for each file within and any
 * regustered IDirectoryVisitor on entry and exit of the directory.
 */
interface IScanner {
  /**
   * Attach an IFileVisitor that will be called for every file this Scanner cares about.
   * @param IFileVisitor $oVisitor
   */
  public function addFileVisitor(IFileVisitor $oVisitor);

  /**
   * Attach an IDirectoryVisitor that will be called for every directory this Scanner scans.
   * @param IDirectoryVisitor $oVisitor
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

