<?php
/*
  Plugin Name: Okta connect
  Version: auto
  Description:Connect to Piwigo using Okta SAML
  Plugin URI:
  Author: HWFord
  Author URI: piwigo.com
  Has Settings: true
*/

defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

// Prevent incorrect folder names
if (basename(dirname(__FILE__)) != 'okta_connect') {
  add_event_handler('init', function () {
    global $page;
    $page['errors'][] = 'Okta connect folder name is incorrect. Please rename it to "okta_connect".';
  });
  return;
}

// Plugin constants
define('OKTA_ID', basename(dirname(__FILE__)));
define('OKTA_PATH', PHPWG_PLUGINS_PATH . OKTA_ID . '/');
define('OKTA_ADMIN', get_root_url() . 'admin.php?page=plugin-okta');

require_once PHPWG_ROOT_PATH . 'include/functions_user.inc.php';
require_once OKTA_PATH . 'include/functions.inc.php';

//Php saml tools
define('SAML_TOOLKIT_PATH', OKTA_PATH . 'vendor/php-saml/');
require_once SAML_TOOLKIT_PATH.'_toolkit_loader.php';

use OneLogin\Saml2\Auth;
use OneLogin\Saml2\Utils;
use OneLogin\Saml2\Settings;

//Evnet handlers
add_event_handler('init', 'okta_init');
add_event_handler('blockmanager_apply', 'okta_blockmanager');

/*
 * prepare things to the connexion menu :
 */

function okta_init()
{
  global $conf;

  // Load plugin language
  load_language('plugin.lang', OKTA_PATH);

  // Load plugin config
  $conf['okta_connect'] = safe_unserialize($conf['okta_connect']);

  //we keep a safe noOkta access to the identification page for administration purpose.
  if (isset($_GET['noOKTA'])) {
      pwg_set_session_var('noOKTA', 'noOKTA');
  }

  if (isset($_GET['okta_sso'])) {
      pwg_unset_session_var('noOKTA');
  }

  if (isset($_GET['okta_sso']) && $_GET['okta_sso'] === 'tryLoginOkta') {
    $auth = new Auth(get_saml_settings());
    $auth->login();
  }

  if (isset($_GET['okta_sso']) && $_GET['okta_sso'] === 'tryLoginPiwigo')
  {
    $auth = new Auth(get_saml_settings());

    if (isset($_SESSION) && isset($_SESSION['AuthNRequestID'])) {
    $requestID = $_SESSION['AuthNRequestID'];
    } else {
        $requestID = null;
    }
    
    $auth->processResponse($requestID);
    
    // Uncomment to show what is being sent in the XML
    // echo('<pre>');print_r(htmlspecialchars($auth->getLastResponseXML()));echo('</pre>');
    
    $errors = $auth->getErrors();
    
    //Check for errors, else exit
    if (!empty($errors)) {
      do_error(401, 'There was an error during the authentication process');
    }
    
    if (!$auth->isAuthenticated()) {
      do_error(401, 'Authentication didn\'t succeed, please try again');
    }
    
    $_SESSION['samlUserdata'] = $auth->getAttributes();
    
    //Create Piwigo username from okta first name and last name
    $attributes = $_SESSION['samlUserdata'] ?? [];
    
    $okta_user = [
        'mail_address' => trim($attributes['email'][0] ?? ''),
        'username' => trim(($attributes['firstName'][0] ?? '') . '_' . ($attributes['lastName'][0] ?? '')),
        'status' => trim($attributes['piwigoroles'][0] ?? $attributes['Piwigoroles'][0] ?? 'guest'),
    ];

    //see if user exists with email if exists then get user id if not create user and get id
    $user_id = get_userid_by_email($okta_user['mail_address']);

    //Get user status
    $user_status = 'guest';
    
    if ($okta_user['status'] == $conf['okta_connect']['admin_role_name'])
    {
      $user_status = 'admin';
    }
    else if ($okta_user['status'] == $conf['okta_connect']['user_role_name'])
    {
      $user_status = 'normal';
    }

    if (!$user_id) {
      include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
      $user_id = register_user($okta_user['username'], generate_key(16), $okta_user['mail_address'], false);
      
      // Update user infos with correct status
      single_update(
        USER_INFOS_TABLE,
        array(
          'status' => $user_status,
          ),
        array('user_id' => $user_id)
      );
    }
//     else
//     {
//       //We need to check the user status and make sure it hasn't changed
//       $query = '
// SELECT
//     status
//   FROM '.USER_INFOS_TABLE.'
//   WHERE user_id = '.$user_id.'
// ;';
//       list($existing_status) = pwg_db_fetch_row(pwg_query($query));

//       //Make sure we don't change the role if the user is a 
//       if('webmaster' != $existing_status and $existing_status != $user_status)
//       {
//         single_update(
//           USER_INFOS_TABLE,
//           array(
//             'status' => $user_status,
//             ),
//           array('user_id' => $user_id)
//         );
//       }
//     }

    log_user($user_id, false);

    //If a user goes through their servce provider portal to connect we need to have a redirect defined to avoid php-saml error
    $auth->redirectTo(get_absolute_root_url());
  }
}

/*
 * Get the menu login block
 * Only compatible with bootstrap darkroom
 */
function okta_blockmanager($menu_ref_arr)
{
    global $template, $conf;
    $menu = &$menu_ref_arr[0];

    if ($menu->get_block('mbIdentification') == null) 
    {
        return;
    }

    $okta_connect = safe_unserialize($conf['okta_connect']);

    $template->assign(
      array(
        'OKTA_CONNECT' => $okta_connect,
        'OKTA_LOGIN_URL' => get_root_url().'identification.php?okta_sso=tryLoginOkta',
        'PIWIGO_LOGIN_URL' => get_root_url().'identification.php',
      )
    );

    $template->set_prefilter('menubar', 'okta_add_menubar_buttons_prefilter');
}

/*
 * we want to replace completely the form part in identification_menubar.tpl as Okta becommes the only authentification available
 */
function okta_add_menubar_buttons_prefilter($content, $smarty)
{
    $search = '#(<form[^>]*action="{\$U_LOGIN}".*/form>)#is';
    $replace = file_get_contents(OKTA_PATH . 'template/identification_menubar.tpl');
    return preg_replace($search, $replace, $content);
}
