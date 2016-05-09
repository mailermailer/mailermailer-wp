<?php
/*
Plugin Name: MailerMailer
Plugin URI: http://wordpress.org/extend/plugins/mailermailer/
Description: The mailermailer plugin allows you to add your own signup form to your site.
Version: 1.2.1
Author: mailermailer
Author URI: http://www.mailermailer.com/api/
*/
/*  Copyright 2016  MailerMailer  (email : support@mailermailer.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
  die;
}

require_once( plugin_dir_path( __FILE__ ) . 'class-mailermailer.php' );

// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
register_activation_hook( __FILE__, array( 'MailerMailer', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'MailerMailer', 'deactivate' ) );

MailerMailer::get_instance();
