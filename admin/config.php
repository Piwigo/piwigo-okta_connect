<?php
defined('OKTA_PATH') or die('Hacking attempt!');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+

check_status(ACCESS_ADMINISTRATOR);

// +-----------------------------------------------------------------------+
// |Actions                                                                |
// +-----------------------------------------------------------------------

global $template, $page;

if (!empty($_POST) and isset($_POST['save_okta_connect'])) 
{
  $config = array(
      'idp_entity_id' => isset($_POST['idp_entity_id']) ? pwg_db_real_escape_string($_POST['idp_entity_id']) : '',
      'sso_url' => isset($_POST['sso_url']) ? pwg_db_real_escape_string($_POST['sso_url']) : '',
      'x509cert' => isset($_POST['x509cert']) ? pwg_db_real_escape_string($_POST['x509cert']) : '',
      'admin_role_name' => isset($_POST['admin_role_name']) ? pwg_db_real_escape_string(trim($_POST['admin_role_name'])) : '',
      'user_role_name' => isset($_POST['user_role_name']) ? pwg_db_real_escape_string(trim($_POST['user_role_name'])) : '',
      'altaccess' => isset($_POST['altaccess']) ? true : false,
  );

  conf_update_param('okta_connect', serialize($config));
    
  $page['infos'][] = l10n('Information data registered in database');

}

$okta_connect = safe_unserialize($conf['okta_connect']);
// echo('<pre>');print_r($okta_connect);echo('</pre>');
$template->assign('OKTA_CONNECT' , $okta_connect);

// +-----------------------------------------------------------------------+
// | template init                                                         |
// +-----------------------------------------------------------------------+

$template->set_filenames(
  array(
    'plugin_admin_content' => dirname(__FILE__) . '/admin/admin.tpl'
  )
);

$template->set_filename('okta', realpath(OKTA_PATH . 'admin/template/config.tpl'));