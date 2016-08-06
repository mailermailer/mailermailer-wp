<?php

// Include API
if (!class_exists('MAILAPI_Client')) {
require_once('includes/mailermailer-api-php/MAILAPI_Client.php');
}

// Include API
if (!class_exists('MAILAPI_Error')) {
require_once('includes/mailermailer-api-php/MAILAPI_Error.php');
}

// Includes the widget code
include_once('includes/mailermailer_widget.php');

// Register widget
function mailermailer_register_widget()
{
  register_widget('mailermailer_Widget');
}
add_action('widgets_init', 'mailermailer_register_widget');

// MailerMailer class.
class MailerMailer
{ 

  // Plugin version, used for cache-busting of style and script file references.
  protected $version = '1.2.3';

  // Unique identifier for your plugin.
  protected $plugin_slug = 'mailermailer';

  // Instance of this class.
  protected static $instance = null;

  // Slug of the plugin screen.
  protected $plugin_screen_hook_suffix = null;

  // Define errors for user feedback
  protected $user_errors = array(
    '112'   => 'A status error has occurred.',
    '301'   => 'Missing required data.',
    '302'   => 'Invalid data detected. Check that your email address is properly formatted.',
    '304'   => 'Duplicate data detected. Your email address is already signed up for this list.',
    '10002' => 'Disallowed email address.',
    '11003' => 'Malformed or invalid API key.',
    '12001' => 'The owner of this account has been suspended.',
    '12002' => 'The owner of this account has been termianted.',
  );

  /**
  * Initialize the plugin by setting localization, filters, and administration functions.
  *
  * @return    void
  */
  private function __construct()
  {
    // Load admin style sheet and JavaScript.
    add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );

    // Load public-facing style sheet and JavaScript.
    add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
    add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

    add_filter("plugin_action_links", array($this, 'add_settings_link'), 10, 2);
    add_action('admin_init', array($this, 'register_settings'));
    add_action('admin_menu', array($this, 'add_settings_page'));

    add_action('init', array($this, 'request_hanlder'));

    $this->set_default_values();
  }

  /**
  * Add the options page under settings.
  *
  * @return    void
  */
  public function add_settings_page()
  {
    $this->plugin_screen_hook_suffix = add_options_page('MailerMailer Settings', 'MailerMailer Settings', 'manage_options', 'mailermailer_settings', array($this, 'display_plugin_admin_page'));
  }

  /**
  * Add settings link to MailerMailer plugin row under the 'Plugins' page.
  *
  * @param     array    $links    Links to be displayed on the plugin activation page.
  * @return    array              Links.
  */
  public function add_settings_link( $links, $file )
  {
    if ($file == plugin_basename( dirname(__FILE__).'/mailermailer.php' )) {
      $settings_link = '<a href="' . admin_url( 'options-general.php?page=mailermailer_settings' ) . '">Settings</a>';
      array_unshift($links, $settings_link);
    }
    return $links;
  }

  /**
  * Register settings.
  *
  * @return    void
  */
  public function register_settings()
  {
    register_setting( 'mailermailer_api_settings', 'mailermailer_api', array($this, 'register_apikey'));
    register_setting( 'mailermailer_options_group', 'mailermailer', array($this, 'sanitize_preferences'));
    register_setting( 'mailermailer_form_refresh', 'mailermailer_refresh', array($this, 'refresh_form'));
    register_setting( 'mailermailer_captcha_settings', 'mailermailer_captcha_keys', array($this, 'register_captcha_keys'));
  }

  /**
  * Register API key.
  *
  * @return    string    User input.
  */
  public function register_apikey($input)
  {
    if (!empty($input['mm_apikey'])) {
      $mailapi = new MAILAPI_Client($input['mm_apikey']);
      $ping = $mailapi->ping();

      if (MAILAPI_Error::isError($ping)) {
        add_settings_error('mailermailer_api', 'invalid-api-key-e', $this->errors($ping));
      } else {
        add_settings_error('mailermailer_api', 'invalid-api-key-s', 'API key verified.', 'updated');
        $this->get_formfields($input['mm_apikey'], 'register');
      }
    }
    return $input;
  }

  /**
  * Sanitize our Form Settings before saving them.
  *
  * @return    string    User input.
  */
  public function sanitize_preferences($input)
  {
    foreach ($input as $key => $value) {
      if (preg_match('/color/', $key)) {
        $input[ $key ] = str_replace('#', '', $value);
      } elseif (preg_match('/width/', $key)) {
        $input[ $key ] = str_replace('px', '', $value);
      }
    }

    if (!array_key_exists('mm_powered_by_tagline', $input)) {
      $input['mm_powered_by_tagline'] = 'no';
    }
    return $input;
  }

  /**
  * Handle signup form refresh.
  *
  * @return    string    User input.
  */
  public function refresh_form($input)
  {
    $opts_api = get_option('mailermailer_api');
    $this->get_formfields($opts_api['mm_apikey'], 'refresh');
    return $input;
  }

  /**
  * Register CAPTCHA keys
  *
  * @return    string    User input.
  */
  public function register_captcha_keys($input)
  {
    return $input;
  } 

  /**
  * Retrieves the signup form from MailerMailer. Displays appropriate
  * message based on success or failure.
  *
  * @return    void
  */
  public function get_formfields($api_key, $action)
  {
    $mailapi = new MAILAPI_Client($api_key);
    $formfields = $mailapi->getFormFields();

    if (MAILAPI_Error::isError($formfields)) {
      add_settings_error('mailermailer_api',  'signup-form-e' . $action, $this->errors($formfields));
    } else {
      add_settings_error('mailermailer_api', 'singup-form-s' . $action, 'Signup form retrieved.', 'updated');
      update_option('mm_formfields_struct', $formfields);
    }
  }

  /**
  * Handles requests for the the widget signup form.
  *
  * @return    void
  */
  public function request_hanlder()
  {
    if (isset($_POST['mm_request'])) {
      switch($_POST['mm_request']) {
        case 'mm_form_submit':
          $results = $this->submit_form();
          switch ($_POST['mm_is_javascript']) {
            case 'yes':
              echo json_encode($results); // send data to JS

              exit;
            case 'no':
              $missing_fields = '<span class="mm_display_error">';
              foreach ($results['missing'] as $key => $value) {
                $missing_fields .= "- " . $value . "<br>";
              }
              $missing_fields .= '</span>';
              MailerMailer::messages($results['message'] . $missing_fields);
              break;
          }
          break;
      }
    }
  }

  /**
  * Signup a member based on the $_POST arguments recieved.
  * Display success or error message based on the result.
  *
  * Sets success/error message.
  *
  * @return    void
  */
  public function submit_form()
  {
    $formfields = get_option('mm_formfields_struct');
    $member = array();
    $missing = array();
    $message = '';
    $captcha_keys = get_option('mailermailer_captcha_keys');
    $recaptcha_enabled = !empty($captcha_keys['mm_public_captcha_key']) && !empty($captcha_keys['mm_private_captcha_key']);

    $opts_api = get_option('mailermailer_api');

    // validate recaptcha
    if ($recaptcha_enabled) {
      $valid = $this->is_recaptcha_valid($_POST['g-recaptcha-response']);
      if (!$valid) {
        $missing[ 'mailermailer_captcha_container' ] = 'reCAPTCHA';
      }
    }

    // traverse through formfields and retrieve POST data
    foreach ($formfields as $field) {
      $name = 'mm_' . $field['fieldname'];
      $user_input = array();
      if ($field['type'] == 'select') { // select
        if ($field['attributes']['select_type'] == 'multi') {
          foreach ($field['choices'] as $key => $value) {
            if (isset($_POST[ $name . '_' . $key ])) {
              array_push($user_input, $_POST[ $name . '_' . $key ]);
            }
          }
        } else {
          if (isset($_POST[ $name ])) { // select_type single
            array_push($user_input, $_POST[ $name ]);
          }
        }
      } elseif ($field['type'] == 'state') { // state
        if (isset($_POST[ $name ])) {
          $user_input = (preg_match("/Other/i", $_POST[ $name ])) ? $_POST[ $name . '_other' ] : $_POST[ $name ];
        }
      } else {
        if (isset($_POST[$name ])) { // open_text and country
          $user_input = $_POST[ $name ];
        }
      }

      if ($field['required'] && (empty($user_input) || $user_input == "--" || $user_input[0] == "--")) {
        $missing[ $name ] = $field['description'];
      }
      $member[ $field['fieldname'] ] = $user_input;
    }

    if (!empty($missing)) {
      // If we encounter missing fields no need to call API
      $message = '<span class="mm_display_error">Required data is missing.</span>';
    } else {
      $mailapi = new MAILAPI_Client($opts_api['mm_apikey']);

      $added = $mailapi->addMember($member);

      if (MAILAPI_Error::isError($added)) {
        $message = '<span class="mm_display_error">' . $this->errors($added) . '</span>';
      } else {
        $message = '<span class="mm_display_success">Please check your e-mail for a confirmation message.</span>';
      }
    }

    return array(
      'message' => $message,
      'missing' => $missing,
      'member' => $member
    );
  }

  /**
  * Check if the recieved recaptcha response is valid.
  *
  * @return    boolean    If the recaptcha has been verified returns true, false otherwise.
  */
  public function is_recaptcha_valid($recaptcha_response)
  {
    $captcha_keys = get_option('mailermailer_captcha_keys');

    $args = array(
      'body' => array(
        'secret' => $captcha_keys['mm_private_captcha_key'], 
        'response' => $recaptcha_response
        )
      );
    $req = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', $args );
    $resp = json_decode(wp_remote_retrieve_body($req), true);

    return isset( $resp['success'] ) && !!$resp['success'];
  }

  /**
  * Set plugin default values.
  *
  * @return    void
  */
  public function set_default_values()
  {
    $opts = (array) get_option('mailermailer');
    $opts_api = (array) get_option('mailermailer_api');
    $captcha_keys = (array) get_option('mailermailer_captcha_keys');

    // store default values if they don't exist
    if (!$opts['mm_user_form_title']) {
      $opts['mm_user_form_title'] = 'List Sign Up';
    }

    if (!$opts['mm_powered_by_tagline']) {
      $opts['mm_powered_by_tagline'] = 'yes';
    }

    if (!$opts['mm_background_color']) {
      $opts['mm_background_color'] = 'F9F9F9';
    }

    if (!$opts['mm_border_color']) {
      $opts['mm_border_color'] = 'DFDFDF';
    }

    if (!$opts['mm_border_width']) {
      $opts['mm_border_width'] =  '1';
    }

    if (!$opts['mm_text_color']) {
      $opts['mm_text_color'] = '000000';
    }
    
    if (!$opts_api['mm_apikey']) {
      $opts_api['mm_apikey'] = '';
    }

    if (!$captcha_keys['mm_public_captcha_key']) {
      $captcha_keys['mm_public_captcha_key'] = '';
    }

    if (!$captcha_keys['mm_private_captcha_key']) {
      $captcha_keys['mm_private_captcha_key'] = '';
    }

    update_option('mailermailer', $opts);
    update_option('mailermailer_api', $opts_api);
    update_option('mailermailer_refresh', array('refresh' => true));
    update_option('mailermailer_captcha_keys', $captcha_keys);
  }

  /** 
  * Returns custom styles for signup form.
  *
  * @return    string    CSS for signup form.
  */
  public function custom_css()
  {
    $opts = (array) get_option('mailermailer');
    $custom_css = 'border-width: ' . $opts['mm_border_width'] . 'px;';
    $custom_css .= 'border-style: '. ($opts['mm_border_width'] == 0 ? 'none' : 'solid') . ';';
    $custom_css .= 'border-color: #' . $opts['mm_border_color'] . ';';
    $custom_css .= 'color: #' . $opts['mm_text_color'] . ';';
    $custom_css .= 'background-color: #'. $opts['mm_background_color'] . ';';
    return $custom_css;
  }

  /**
  * List of error codes that map to user friendly error messages.
  *
  * @param     int       $mailapi_error    Error code.
  * @return    string                      Error message.
  */
  public function errors($mailapi_error)
  {
    $error_message = "An error occurred.";

    // display errors related to user feedback
    if (array_key_exists($mailapi_error->getErrorCode(), $this->user_errors)) {
      $error_message = $this->user_errors[ $mailapi_error->getErrorCode() ];
    } else {
      // log all other errors 
      $log_error =  "\n\nmailermailer plugin error: \n";
      $log_error .= "Error Code: " . $mailapi_error->getErrorCode() . "\n";
      $log_error .= "Error Message: " . $mailapi_error->getErrorMessage() . "\n\n";
      error_log($log_error);
    }
    return $error_message;
  }

  /**
  * Displays the settings page in the admin panel.
  *
  * @return    void
  */
  public function display_plugin_admin_page()
  {
    $opts = (array) get_option('mailermailer');
    $opts_api = (array) get_option('mailermailer_api');
    $captcha_keys = (array) get_option('mailermailer_captcha_keys');
    $connected = $this->is_connected($opts_api['mm_apikey']);
    include_once( 'includes/views/admin.php' );
  }

  /**
  * Checks for a connection with MailerMailer.
  *
  * @return    boolean     True if we are connected,false otherwise.
  */
  public function is_connected($api_key)
  {
    $mailapi = new MAILAPI_Client($api_key);
    $ping = $mailapi->ping();

    return (!MAILAPI_Error::isError($ping));
  }

  /**
  * Return an instance of this class.
  *
  * @return    object    A single instance of this class.
  */
  public static function get_instance()
  {

    // If the single instance hasn't been set, set it now.
    if ( null == self::$instance ) {
      self::$instance = new self;
    }

    return self::$instance;
  }

  /**
  * Set or get success and error messages.
  *
  * @param     string    $message    Success or error message to be displayed.
  * @return    string                Returns success or error message when function is called without parameters.
  */
  public static function messages($message = null)
  {
    global $mailermailer_msg;

    if (!is_array($mailermailer_msg)) {
      $mailermailer_msg = array();
    }

    if (is_null($message)) {
      return implode('', $mailermailer_msg);
    }

    $mailermailer_msg[] = $message;
  }

  /**
  * Fired when the plugin is activated.
  *
  * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
  */
  public static function activate( $network_wide )
  {
    // activate
  }

  /**
  * Fired when the plugin is deactivated.
  *
  * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
  */
  public static function deactivate( $network_wide )
  {
    // deactivate
  }

  /**
  * Register and enqueue admin-specific style sheet.
  *
  * @return    null    Return early if no settings page is registered.
  */
  public function enqueue_admin_styles()
  {

    if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
      return;
    }

    $screen = get_current_screen();
    if ( $screen->id == $this->plugin_screen_hook_suffix ) {
      wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'css/admin.css', __FILE__ ), array(), $this->version );
    }
  }

  /**
  * Register and enqueue public-facing style sheet.
  *
  * @return    void
  */
  public function enqueue_styles()
  {
    wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'css/public.css', __FILE__ ), array(), $this->version );
  }

  /**
  * Register and enqueues public-facing JavaScript files.
  *
  * @return    void
  */
  public function enqueue_scripts()
  {
    $captcha_keys = (array) get_option('mailermailer_captcha_keys');

    wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'js/public.js', __FILE__ ), array( 'jquery', 'jquery-form' ), $this->version );
    wp_localize_script( $this->plugin_slug . '-plugin-script', 'mailermailer_params', array( 'mm_url' => trailingslashit(home_url()), 'mm_pub_key' => $captcha_keys['mm_public_captcha_key'] ));
  }

}
