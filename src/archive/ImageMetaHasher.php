<?php

namespace PhotoSort\Archive;

class ImageMetaHasher implements IIndexHasher {
  public function hash($oRecord) {
    return sha1(json_encode($oRecord));
  }
}
