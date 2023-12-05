<?php

/**
 * Plugin Name: Work Service Database
 * Author: JaminHood
 * Author URI: https://github.com/robicse11127
 * Version: 1.0.0
 * Description: Work Service Database
 * Text-Domain: work-service-database
 */

if (!defined('ABSPATH')) exit(); # No direct access allowed.

/**
 * Define Plugins Contants
 */
define('WSD_PATH', trailingslashit(plugin_dir_path(__FILE__)));
define('WSD_URL', trailingslashit(plugins_url('/', __FILE__)));

/**
 * WS main class
 */
if (!class_exists('WorkServiceDatabase')) {
  class WorkServiceDatabase
  {
    public function __construct()
    {
      require_once WSD_PATH . 'classes/class-ws-settings.php';
      require_once WSD_PATH . 'classes/class-ws-db.php';
      require_once WSD_PATH . 'classes/class-ws-rest-api.php';
      // require_once WSD_PATH . 'classes/table/class-ws-bookings.php';
      // require_once WSD_PATH . 'classes/table/class-ws-category.php';
      // require_once WSD_PATH . 'classes/table/class-ws-chat.php';
      // require_once WSD_PATH . 'classes/table/class-ws-profile.php';
      // require_once WSD_PATH . 'classes/table/class-ws-services.php';
      $this->init();
    }

    /**
     * Initialize Plugin
     */
    public function init(): void
    {
      new WorkServiceSettings;
      new WorkServiceDB;
      # Registering Plugin
      register_activation_hook(__FILE__, [$this, 'ws_activate']);
      register_deactivation_hook(__FILE__, [$this, 'ws_deactivate']);

      // WorkServiceDB::set_chat(array(
      //   // 'serviceID' => 1,
      //   'chatName' => 'Customer Support',
      // ));

      // WorkServiceDB::update_chat(1);
    }

    /**
     * Activation Script
     */
    public function ws_activate(): void
    {
      WorkServiceSettings::on_activation();
    }

    /**
     * Deactivation Script
     */
    public function ws_deactivate(): void
    {
      WorkServiceSettings::on_deactivation();
      flush_rewrite_rules(true);
    }
  }

  new WorkServiceDatabase;
}
