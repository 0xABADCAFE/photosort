<?php
/**
 * @package PhotoSort
 */

namespace PhotoSort\Utility;

abstract class CLIScript {
  const
    /**
     * Parameter types.
     */
    PARAM_TYPE_SWITCH     = 0,
    PARAM_TYPE_STRING     = 1,
    PARAM_TYPE_INTEGER    = 2,
    PARAM_TYPE_FLOAT      = 3,

    /**
     * Parameter requirements.
     */
    PARAM_OPTIONAL       = 0,
    PARAM_VALUE_OPTIONAL = 0,
    PARAM_REQUIRED       = 1,
    PARAM_VALUE_REQUIRED = 2
  ;

  public abstract function main();

  /**
   * Set the expectation for an argument.
   *
   * @param string $sParamName
   * @param enum   $eParamType
   * @param enum   $eParamReqt
   */
  protected function expectParam(string $sParamName, int $eParamType, int $eParamReqt) {
    if (!isset(self::$aAllowedTypes[$eParamType])) {
      throw new \InvalidArgumentException();
    }
    $this->aExpectParams[$sParamName] = (object)[
      'eParamType' => $eParamType,
      'eParamReqt' => $eParamReqt
    ];
  }

  /**
   * Validate and return the provided arguments.
   *
   * @return object[]
   */
  protected function getParams() {
    if (count($this->aExpectParams)>0) {
      return $this->parseGetOpts(getopt('', $this->buildGetOpts()));
    }
    return [];
  }

  /**
   * Prepare the getopt() long options input array.
   *
   * @return string[]
   */
  private function buildGetOpts() {
    $aOptions = [];
    foreach ($this->aExpectParams as $sParamName => $oParamInfo) {
      $sOptFormat  = $sParamName;
      if ($oParamInfo->eParamType != self::PARAM_TYPE_SWITCH) {
        $sOptFormat .= ($oParamInfo->eParamReqt & self::PARAM_VALUE_REQUIRED ? ':' : '::');
      }
      $aOptions[] = $sOptFormat;
    }
    return $aOptions;
  }

  /**
   * Validate the getopt() array return.
   *
   * @param string[] $aReceived
   * @return object[]
   */
  private function parseGetOpts(array $aReceived) {
    $aRsult = [];
    foreach ($this->aExpectParams as $sParamName => $oParamInfo) {
      $oParamState = (object)[
        'bParamProvided' => isset($aReceived[$sParamName])
      ];
      if ($oParamInfo->eParamType != self::PARAM_TYPE_SWITCH) {
        if (
          $oParamInfo->eParamReqt & self::PARAM_REQUIRED &&
          false == $oParamState->bParamProvided
        ) {
          throw new \Exception('Missing required paramter --' . $sParamName);
        }
        if ($oParamState->bParamProvided) {
          if ($oParamInfo->eParamReqt & self::PARAM_VALUE_REQUIRED) {
            $oParamState->mValue = $this->validateParamType($sParamName, $aReceived[$sParamName], $oParamInfo->eParamType);
          }
          else {
            if (false !== $aReceived[$sParamName]) {
              $oParamState->mValue =  $this->validateParamType($sParamName, $aReceived[$sParamName], $oParamInfo->eParamType);
            }
          }
        }
      }
      $aResult[$sParamName] = $oParamState;
    }
    return $aResult;
  }

  /**
   * Validate the type of a given parameter.
   *
   * @param string $sParamNname
   * @param string $sValue
   * @param enum   $eParamType
   * @return mixed
   */
  private function validateParamType(string $sParamNname, $sValue, $eParamType) {
    switch ($eParamType) {
      case self::PARAM_TYPE_INTEGER:
        if (!is_numeric($sValue)) {
          throw new \Exception('Value for --' . $sParamNname . ' must be ' . self::$aAllowedTypes[$eParamType]);
        }
        return (int)$sValue;
      case self::PARAM_TYPE_FLOAT:
        if (!is_numeric($sValue)) {
          throw new \Exception('Value for --' . $sParamNname . ' must be ' . self::$aAllowedTypes[$eParamType]);
        }
        return (float)$sValue;
      default:
        return $sValue;
    }
  }

  private $aExpectParams        = [];
  private static $aAllowedTypes = [
    self::PARAM_TYPE_SWITCH  => 'a switch',
    self::PARAM_TYPE_STRING  => 'a string',
    self::PARAM_TYPE_INTEGER => 'an integer',
    self::PARAM_TYPE_FLOAT   => 'a decimal'
  ];
}
