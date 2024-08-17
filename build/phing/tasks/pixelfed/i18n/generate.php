<?php

namespace pixelfed\i18n;

require_once 'phing/Task.php';
require_once 'i18n.php';

use Task;

class generate extends Task {
  public function __construct() {}
  
  public function main() {
    foreach(LanguageCodes::GetAllDefined() as $lang) {
      TranslationSet.FromLanguageCode($lang).ExportToJson();
    }
  }
};
