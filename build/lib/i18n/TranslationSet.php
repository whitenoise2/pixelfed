<?php

namespace lib\i18n;

require_once 'Directories.php';

/**
 * Tracks a set of translations for a given language
 * code.
 */
class TranslationSet {
  /**
   * Obtain a translation set for a language code
   * 
   * @return TranslationSet Translations for requested language 
   */
  public static function FromLanguageCode(string $code) {
    return new TranslationSet($code);
  }

  /**
   * Export subset of translations for the single page web
   * application for Pixelfed.
   * 
   * Translations are taken from the web PHP source file and
   * pushed out as a Vue.js i18n JSON file.
   */
  public function ExportForSinglePageApp() {
    $dirs = \DIRECTORIES::get();
    $strings = \Lang::get('web', [], $this->lang_code);
    $json = json_encode($strings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    $path = "{$dirs->TranslationsExportSpa}{$this->lang_code}.json";
    file_put_contents($path, $json);

    $pathAlt = "{$dirs->TranslationsExportSpaAlt}{$this->lang_code}.json";
    file_put_contents($pathAlt, $json);
  }

  private function __construct(private string $lang_code) {}
};