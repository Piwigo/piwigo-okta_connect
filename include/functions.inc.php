<?php

/**
 * Function containing saml settings.
 */
function get_saml_settings()
{
  global $conf;

  $config = safe_unserialize($conf['okta_connect'] ?? '');

  //We do str_replace here because this functions can be called directly from the plugin files.
  //Therfore the root url will include the plugins path and 
  //sometimes this function is called from the main so doesn't include the plugin path.
  //To make sure we always get what we need we check if the plugins path is included and remove it, 
  //to avoid having the plugins path duplicated in the url
  $root_url = str_replace('plugins/okta_connect/','', get_absolute_root_url());

  return [
    'strict' => true,
    'debug' => false,
    'sp' => [
      'entityId' => $root_url.'plugins/okta_connect/metadata.php',
      'assertionConsumerService' => [
          'url' => get_absolute_root_url().'identification.php?okta_sso=tryLoginPiwigo',
          'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
      ],
      'x509cert' => '',
      'privateKey' => '',
    ],
    'idp' => [
      'entityId' => $config['idp_entity_id'] ?? '',
      'singleSignOnService' => [
          'url' => $config['sso_url'] ?? '',
          'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
      ],
      'x509cert' => $config['x509cert'] ?? '',
    ],
  ];
}
