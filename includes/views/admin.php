<div class="wrap">
  <?php screen_icon(); ?>
  <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
  <div>
    <div id="poststuff" class="mm_admin">
      <div class="postbox" id="aiosp">
        <form method="post" action="options.php">
          <?php settings_fields('mailermailer_api_settings') ?>
          <h3 class="hndle"><span>API Settings</span></h3>
          <div class="inside">
            Please enter your MailerMailer API key to get started
            <table class="form-table">
              <tr valign="top">
                <th scope="row">
                  <label>
                    Your API Key
                  </label>
                </th>
                <td>
                  <input type="text" class="regular-text" name="mailermailer_api[mm_apikey]" id="mm_apikey" value="<?php echo $opts_api['mm_apikey']; ?>"/>
                  <p class="description"><a href="http://www.mailermailer.com/api/getting-started/index.rwp">Request an API Key</a></p>
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <input type="submit" name="Submit" value="Save Changes" class="button button-primary" />
                </td>
              </tr>    
            </table>
          </div>  
        </form>
      </div>

      <?php if ($connected) { ?>

      <div class="postbox" id="aiosp">
        <form method="post" action="options.php">
          <?php settings_fields('mailermailer_captcha_settings') ?>
          <h3 class="hndle"><span>CAPTCHA Settings</span></h3>
          <div class="inside">
            Please enter your CAPTCHA keys
            <table class="form-table">
              <tr valign="top">
                <th scope="row">
                  <label>
                    Your Site Key
                  </label>
                </th>
                <td>
                  <input type="text" class="regular-text" name="mailermailer_captcha_keys[mm_public_captcha_key]" id="mm_apikey" value="<?php echo $captcha_keys['mm_public_captcha_key']; ?>"/>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row">
                  <label>
                    Your Private Key
                  </label>
                </th>
                <td>
                  <input type="text" class="regular-text" name="mailermailer_captcha_keys[mm_private_captcha_key]" id="mm_apikey" value="<?php echo $captcha_keys['mm_private_captcha_key']; ?>"/>
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <input type="submit" name="Submit" value="Save Changes" class="button button-primary" />
                </td>
              </tr>    
            </table>
            <p class="description"><a href="https://developers.google.com/recaptcha/docs/start">Get CAPTCHA keys</a></p>
          </div>  
        </form>
      </div>      
    
      <div class="postbox" id="aiosp">
        <form method="post" action="options.php">
          <?php settings_fields( 'mailermailer_options_group' ); ?>
          <h3 class="hndle"><span>Form Settings</span></h3>
          <div class="inside">
            <table class="form-table">
              <tr valign="top">
                <th scope="row">
                  Display "Powered by MailerMailer"?
                </th>
                <td>
                  <input type="checkbox" name="mailermailer[mm_powered_by_tagline]" value="yes" <?php checked($opts['mm_powered_by_tagline'] == 'yes') ?> />
                </td>
              </tr>
              <tr valign="top">
                <th scope="row">
                  <label>
                    Title
                  </label>
                </th>
                <td>
                  <input type="text" class="regular-text" name="mailermailer[mm_user_form_title]" value="<?php echo $opts['mm_user_form_title']; ?>" />
                  <p class="description">Displayed at the top of your form</p>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row">
                  <label>
                    Background Color
                  </label>  
                </th>
                <td>
                  #<input type="text" class="small-text" name="mailermailer[mm_background_color]" value="<?php echo $opts['mm_background_color']; ?>" />
                  <p class="description">Do not include the initial #</p>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row">
                  <label>
                    Text Color
                  </label>  
                </th>
                <td>
                  #<input type="text" class="small-text" name="mailermailer[mm_text_color]" value="<?php echo $opts['mm_text_color']; ?>" />
                  <p class="description">Do not include the initial #</p>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row">
                  <label>
                    Border Color
                  </label>  
                </th>
                <td>
                  #<input type="text" class="small-text" name="mailermailer[mm_border_color]" value="<?php echo $opts['mm_border_color']; ?>" />
                  <p class="description">Do not include the initial #</p>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row">
                <label>
                  Border Width
                </label>  
              </th>
              <td>
                <input type="text" class="small-text" name="mailermailer[mm_border_width]" value="<?php echo $opts['mm_border_width']; ?>" />px
                <p class="description">Do not include the trailing "px"</p>
              </td>
            </tr>
            <tr>
              <td colspan="2">
                <input type="submit" class="button button-primary" name="Submit" value="Save Changes" class="button"/>
              </td>
            </tr>
          </table>
        </div>
      </form>
    </div>
    
    <div class="form-section">
      <form method="post" action="options.php">
        <?php settings_fields( 'mailermailer_form_refresh' ); ?>
        <p class="description">Have you made changes to your form on MailerMailer?&nbsp;
          <!-- <input type="hidden" name="mm_request" value="mm_update_form"/> -->
          <input type="submit" name="Submit" value="Refresh Form" class="button"/>
        </p>  
      </form>
    </div>
    <?php } ?>
  </div>
</div>
