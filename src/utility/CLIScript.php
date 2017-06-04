<?php

namespace PhotoSort\Utility;

abstract class CLIScript {
  const
    ARGTYPE_SWITCH  = 0,
    ARGTYPE_STRING  = 1,
    ARGTYPE_INTEGER = 2,

    ARG_OPTIONAL       = 0,
    ARG_VALUE_OPTIONAL = 0,
    ARG_REQUIRED       = 1,
    ARG_VALUE_REQUIRED = 2
  ;

  public abstract function main();

  protected function expectArg($sArgName, $eArgType, $eReqirement) {
    if (!isset(self::$aAllowed[$eArgType])) {
      throw new \InvalidArgumentException();
    }
    $this->aExpect[$sArgName] = (object)[
      'eArgType'    => $eArgType,
      'eReqirement' => $eReqirement
    ];
  }

  protected function getArgs() {
    $aResult = [];
    if (count($this->aExpect)>0) {
      $aResult   = $this->parseGetOpts(getopt('', $this->buildGetOpts()));
    }
    return $aResult;
  }

  private function buildGetOpts() {
    $aOptions = [];
    foreach ($this->aExpect as $sArgName => $oArgInfo) {
      $sOptFormat  = $sArgName;
      if ($oArgInfo->eArgType != self::ARGTYPE_SWITCH) {
        $sOptFormat .= ($oArgInfo->eReqirement & self::ARG_VALUE_REQUIRED ? ':' : '::');
      }
      $aOptions[] = $sOptFormat;
    }
    return $aOptions;
  }

  private function parseGetOpts(array $aReceived) {
    $aRsult = [];
    foreach ($this->aExpect as $sArgName => $oArgInfo) {
      $oArgState = (object)[
        'bArgProvided' => isset($aReceived[$sArgName])
      ];
      if ($oArgInfo->eArgType != self::ARGTYPE_SWITCH) {
        if (
          $oArgInfo->eReqirement & self::ARG_REQUIRED &&
          false == $oArgState->bArgProvided
        ) {
          throw new \Exception('Missing required ' . $sArgName);
        }
        if ($oArgState->bArgProvided) {
          if ($oArgInfo->eReqirement & self::ARG_VALUE_REQUIRED) {
            $oArgState->mValue = $aReceived[$sArgName];
          }
          else {
            if (false !== $aReceived[$sArgName]) {
              $oArgState->mValue = $aReceived[$sArgName];
            }
          }
        }
      }
      $aResult[$sArgName] = $oArgState;
    }
    return $aResult;
  }

  private $aExpect = [];
  private static $aAllowed = [
    self::ARGTYPE_SWITCH  => 'switch',
    self::ARGTYPE_STRING  => 'string',
    self::ARGTYPE_INTEGER => 'integer'
  ];
}
