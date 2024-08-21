<?php

/**
 * Singleton that stores important directories
 * task-wide.
 */

class DIRECTORIES {
  private static $instance;
 
  /// Root folder for translationn source files
  public string $TranslationsRoot;

  /// Export folder for single page web application
  public string $TranslationsExportSpa;    
  /// Export folder for single page web application (alternate path)
  public string $TranslationsExportSpaAlt;

  
  public function __construct() {
    $this->TranslationsRoot         = resource_path('lang/');
    $this->TranslationsExportSpa    = resource_path('assets/js/i18n/');
    $this->TranslationsExportSpaAlt = public_path('_lang/');
  }

  protected function __clone() { }
  public function __wakeup()
  { throw new \Exception("Cannot unserialize a singleton."); }

  /**
   * Get the unique instance of the singleton.
   * 
   * Only available after completion of BuildTaskBase constructor
   * due to dependency on Laravel application initilization.
   */
  public static function get(): DIRECTORIES {
    if(!isset(self::$instance)) {
      self::$instance = new DIRECTORIES();
    }

    return self::$instance;
  } 
}
