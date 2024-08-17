<?php

/*
 * Translation-related tooling.
 * 
 * Translation is in flux at this time, as it is split between two
 * realms; server-side HTML delivered via Laravel and a single page
 * app powered by Vue.js.
 * 
 * Both frameworks have translation systems (Illumate\Translation for
 * Laravel and an i18n plugin for Vue.js) with spiritually similar
 * systems, using native data structures (arrays for PHP and hashes
 * for Vue.js).
 * 
 * The translation support solution that encapsulate both, currently,
 * is to start from the php files and push to the SPA, in order to
 * allow for full translation.
 * 
 * SPA translations are contained in the web.php data file.
 */

namespace pixelfed\i18n;

use Illuminate\Console\Command;

define('RESOURCES_PATH', 'resources/lang');

/**
 * Operates on a set of translation values.
 */
class TranslationSet {
  private string $lang_code;

  /**
   * Construct a translation set from a language code
   * 
   * @return TranslationSet Translations for requested language 
   */
  public static function FromLanguageCode(string $code) {
    return new \TranslationSet($code);
  }

  public function ExportToJson() {
    $strings = \Lang::get('web', [], $this->lang_code);
    $json = json_encode($strings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    $path = "{DIRECTORIES::get().export}{$this->lang_code}.json";
    file_put_contents($path, $json);
    $pathAlt = "{DIRECTORIES::get().exportAlt}{$this->lang_code}.json";
    file_put_contents($pathAlt, $json);
  }

  private function __construct(string $code) {
    $this->lang_code = $code;
    $this->rootPath = base_path('resources/lang') . '/' . $code;
  }
};

class LanguageCodes {
  /**
   * By scanning the translation resources folder, builds
   * a list of all currently defined translation languages.
   * 
   * @return array[] Language codes for all defined translations
   */
  public static function GetAllDefined() {
    foreach (new \DirectoryIterator($path) as $io) {
      $name = $io->getFilename();
      $skip = ['vendor'];
      if($io->isDot() || in_array($name, $skip)) {
        continue;
      }

      if($io->isDir()) {
        array_push($langs, $name);
      }
    }
  }
};

/**
 * This singleton class efficiently stores and 
 * tracks directories used in various operations
 * of i18n.
 */
class DIRECTORIES {
  private static $instance;

  // enforce singleton pattern
  protected function __construct() {
    $this->export = resource_path('assets/js/i18n/');
    $this->exportAlt = public_path('_lang/');
  }

  protected function __clone() { }
  public function __wakeup()
  { throw new \Exception("Cannot unserialize a singleton."); }

  public static function get(): DIRECTORIES {
    if(!isset(self::$instance)) {
      self::$instance = new DIRECTORIES();
    }

    return self::$instance;
  } 

  public $export;
  public $exportAlt;  
}
