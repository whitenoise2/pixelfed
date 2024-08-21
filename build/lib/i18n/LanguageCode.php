<?php

namespace lib\i18n;

require_once 'Directories.php';

class LanguageCodes {
  /**
   * By scanning the translation resources folder, builds
   * a list of all currently defined translation languages.
   * 
   * @return array[] Language codes for all defined translations
   */
  public static function GetAllDefined() {
    $langs = array();

    foreach (new \DirectoryIterator(\DIRECTORIES::get()->TranslationsRoot) as $io) {
      $name = $io->getFilename();
      $skip = ['vendor'];
      if($io->isDot() || in_array($name, $skip)) {
        continue;
      }

      if($io->isDir()) {
        array_push($langs, $name);
      }
    }

    return $langs;
  }
};