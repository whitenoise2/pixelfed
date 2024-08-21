<?php

require 'BuildTaskBase.php';

require 'i18n/LanguageCode.php';
require 'i18n/TranslationSet.php';


use lib\BuildTaskBase;
use lib\i18n\LanguageCodes;
use lib\i18n\TranslationSet;


class GenerateTask extends BuildTaskBase {
  public function __construct() { parent::__construct(); }
  
  public function init() {}

  public function main() {
    foreach(LanguageCodes::GetAllDefined() as $lang) {
      TranslationSet::FromLanguageCode($lang)->ExportForSinglePageApp();
    }
  }
};
