<?php

namespace lib;

require_once 'phing/Task.php';

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

/**
 * Base class for all Pixelfed custom build tasks
 */
abstract class BuildTaskBase extends \Task {
  /**
   * Laravel application instance
   */
  protected $app;
    
  public function __construct() {
    $this->bootstrapLaravelApplication();
  }

  /**
   * Couples the Laravel application lifetime with the task lifetime,
   * ensuring build tasks can use the same paradigm as artisan commands
   */
  private function bootstrapLaravelApplication() {
    $this->app = require Application::inferBasePath() . '/bootstrap/app.php';
    $this->app->make(Kernel::class)->bootstrap();
  }
}
