<?php

// If uninstall, not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
  exit;
}

/**
* Delete all stored data when plugin is uninstalled.
*
* @return    void
*/
function mailermailer_delete_settings()
{
  delete_option('mailermailer');
  delete_option('mailermailer_api');
  delete_option('mailermailer_refresh');
  delete_option('mm_formfields_struct');
}

mailermailer_delete_settings();