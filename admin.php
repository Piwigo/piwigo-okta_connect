<?php

defined('OKTA_PATH') or die('Hacking attempt!');

global $template, $page, $conf;

include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');

define('OKTA_BASE_URL', get_root_url().'admin.php?page=plugin-okta');

// get current tab
$page['tab'] = isset($_GET['tab']) ? $_GET['tab'] : $page['tab'] = 'config';

//tabsheet
$tabsheet = new tabsheet();
$tabsheet->set_id('okta');

$tabsheet->add('config', l10n('Configuration'), OKTA_ADMIN . '_connect');
$tabsheet->select($page['tab']);
$tabsheet->assign();

$template->assign(
  array(
    'ADMIN_PAGE_TITLE' => 'Okta',
  )
);

// include page
include(OKTA_PATH . 'admin/' . $page['tab'] . '.php');

// send page content
$template->assign_var_from_handle('ADMIN_CONTENT', 'okta');

?>