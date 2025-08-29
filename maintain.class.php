<?php
defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

class okta_connect_maintain extends PluginMaintain
{
  private $default_conf = array(

  );

  function __construct($plugin_id)
  {
    parent::__construct($plugin_id);
  }

  /**
   * Plugin install
   */
  function install($plugin_version, &$errors = array())
  {
    global $conf;

    if (!isset($conf['okta_connect'])) {
        $default_config = array(
            'idp_entity_id' => '',
            'sso_url' => '',
            'x509cert' => '',
            'altaccess' => false,
            'admin_role_name' => '',
            'user_role_name' => '',
        );
        conf_update_param('okta_connect', serialize($default_config));
    }
  }

  /**
   * Plugin activate
   */
  function activate($plugin_version, &$errors = array())
  {
  }

  /**
   * Plugin deactivate
   */
  function deactivate()
  {
  }

  /**
   * Plugin update
   */
  function update($old_version, $new_version, &$errors = array())
  {
    $this->install($new_version, $errors);
  }

  /**
   * Plugin uninstallation
   */
  function uninstall()
  {
    // delete configuration
     pwg_query("DELETE FROM " . CONFIG_TABLE . " WHERE param = 'okta_connect';");
  }

}
