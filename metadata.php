<?php

// Load Piwigo core functions because we don't have access to it yet here
define('PHPWG_ROOT_PATH', dirname(__DIR__, 2) . '/');
include_once(PHPWG_ROOT_PATH . 'include/common.inc.php');

require_once OKTA_PATH . 'include/functions.inc.php';

use OneLogin\Saml2\Settings;
use OneLogin\Saml2\Utils;

try {
  $settings = new Settings(get_saml_settings());

  $metadata = $settings->getSPMetadata();
  $errors = $settings->validateMetadata($metadata);

  if (empty($errors)) {
    header('Content-Type: text/xml');
    echo $metadata;
  } else {
    throw new Error(
      'Invalid SP metadata: '.implode(', ', $errors),
      Error::METADATA_SP_INVALID
    );
  }
} catch (Exception $e) {
    echo $e->getMessage();
}