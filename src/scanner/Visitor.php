<?php
/**
 * @package PhotoSort
 */

namespace PhotoSort\Scanner;

class Visitor implements IFileVisitor, IDirectoryVisitor {

  public function __construct(IScannedDataConsumer $oDataConsumer) {
    $this->oDataConsumer = $oDataConsumer;
  }

  public function visitDirectory(string $sDirName) {
    $this->sCurrentDir    = $sDirName;
    $this->aFilesExamined = [];
  }

  public function leaveDirectory() {
    if (
      null !== $this->sCurrentDir &&
      0 < count($this->aFilesExamined)
    ) {
      $this->oDataConsumer->consume($this->sCurrentDir, $this->aFilesExamined);
    }
  }

  public function visitFile(string $sDirName, string $sFileName) {

    $sFullPath  = $sDirName . '/'.  $sFileName;
    $aExpected  = $this->getExpectedTypes($sFileName);
    $iExifType  = exif_imagetype($sFullPath);

    if (!isset($aExpected[$iExifType])) {
      return;
    }

    $aExif = @exif_read_data($sFullPath, 'ANY_TAG', true, false);
    if (false !== $aExif) {
      $this->aFilesExamined[$sFileName] = $this->reduceExif($aExif);
    } else {
      echo "\tCouldn't extract EXIF data from $sFullPath...\n";
    }
  }

  private function reduceExif(array $aExif) {
/*
    [FILE] => Array
        (
            [FileName] => DSC_3981.jpg
            [FileDateTime] => 1380822315
            [FileSize] => 12922120
            [FileType] => 2
            [MimeType] => image/jpeg
            [SectionsFound] => ANY_TAG, IFD0, THUMBNAIL, EXIF, GPS, INTEROP
        )

    [COMPUTED] => Array
        (
            [html] => width="3680" height="5520"
            [Height] => 5520
            [Width] => 3680
            [IsColor] => 1
            [ByteOrderMotorola] => 1
            [ApertureFNumber] => f/2.8
            [UserComment] =>
            [UserCommentEncoding] => ASCII
            [Thumbnail.FileType] => 2
            [Thumbnail.MimeType] => image/jpeg
        )

    [IFD0] => Array
        (
            [ImageWidth] => 3680
            [ImageLength] => 5520
            [BitsPerSample] => Array
                (
                    [0] => 8
                    [1] => 8
                    [2] => 8
                )

            [PhotometricInterpretation] => 2
            [Make] => NIKON CORPORATION
            [Model] => NIKON D800
            [Orientation] => 1
            [SamplesPerPixel] => 3
            [XResolution] => 3000000/10000
            [YResolution] => 3000000/10000
            [ResolutionUnit] => 2
            [Software] => Adobe Photoshop CS6 (Windows)
            [DateTime] => 2013:10:03 18:45:14
            [YCbCrPositioning] => 2
            [Exif_IFD_Pointer] => 308
            [GPS_IFD_Pointer] => 1016
        )
    [THUMBNAIL] => Array
        (
            [Compression] => 6
            [XResolution] => 72/1
            [YResolution] => 72/1
            [ResolutionUnit] => 2
            [JPEGInterchangeFormat] => 1130
            [JPEGInterchangeFormatLength] => 7849
        )
    [EXIF] => Array
        (
            [ExposureTime] => 10/1250
            [FNumber] => 28/10
            [ExposureProgram] => 3
            [ISOSpeedRatings] => 100
            [UndefinedTag:0x8830] => 2
            [ExifVersion] => 0230
            [DateTimeOriginal] => 2013:08:29 16:42:48
            [DateTimeDigitized] => 2013:08:29 16:42:48
            [ComponentsConfiguration] =>
            [CompressedBitsPerPixel] => 4/1
            [ShutterSpeedValue] => 6965784/1000000
            [ApertureValue] => 2970854/1000000
            [ExposureBiasValue] => 4/6
            [MaxApertureValue] => 30/10
            [MeteringMode] => 5
            [LightSource] => 0
            [Flash] => 16
            [FocalLength] => 520/10
            [UserComment] => ASCII
            [SubSecTime] => 20
            [SubSecTimeOriginal] => 20
            [SubSecTimeDigitized] => 20
            [FlashPixVersion] => 0100
            [ColorSpace] => 1
            [ExifImageWidth] => 3680
            [ExifImageLength] => 5520
            [InteroperabilityOffset] => 984
            [SensingMethod] => 2
            [FileSource] =>
            [SceneType] =>
            [CFAPattern] =>
            [CustomRendered] => 0
            [ExposureMode] => 0
            [WhiteBalance] => 0
            [DigitalZoomRatio] => 1/1
            [FocalLengthIn35mmFilm] => 52
            [SceneCaptureType] => 0
            [GainControl] => 0
            [Contrast] => 0
            [Saturation] => 0
            [Sharpness] => 0
            [SubjectDistanceRange] => 0
        )
    [GPS] => Array
        (
            [GPSVersion] =>
        )

    [INTEROP] => Array
        (
            [InterOperabilityIndex] => R98
            [InterOperabilityVersion] => 0100
        )

*/
    $oResult = (object)[
      't' => (int)$aExif['FILE']['FileDateTime'],
      's' => (int)$aExif['FILE']['FileSize'],
      'w' => (int)$aExif['COMPUTED']['Width'],
      'h' => (int)$aExif['COMPUTED']['Height'],
    ];
    if (isset($aExif['EXIF']['Orientation'])) {
      $oResult->o = $aExif['EXIF']['Orientation'];
    }
    if (isset($aExif['EXIF']['DateTimeOriginal'])) {
      $oResult->t = (int)strtotime($aExif['EXIF']['DateTimeOriginal']);
    }
    return $oResult;
  }

  private function getExpectedTypes(string $sFileName) {
    $aResult = [];
    $iDot = strrpos($sFileName, '.');
    if (false !== $iDot) {
      $sExt = strtolower(substr($sFileName, $iDot));
      if (isset($this->aExtensions[$sExt])) {
        $aResult = $this->aExtensions[$sExt];
      }
    }
    return $aResult;
  }

  private $sCurrentDir  = null;

  private $aExtensions = [
    '.jpeg' => [IMAGETYPE_JPEG => 1],
    '.jpg'  => [IMAGETYPE_JPEG => 1]
  ];
}
