<?php

/**
 * Adds mailermailer_Widget widget.
 */
class mailermailer_Widget extends WP_Widget
{

  /**
   * Register widget with WordPress.
   */
  function __construct() {
    parent::__construct(
      'mailermailer_widget',
      'MailerMailer Widget',
      array( 'description' => 'Display your MailerMailer signup form' )
    );
  }

  /**
   * Widget.
   *
   *
   * @param array    $args        Widget arguments.
   * @param array    $instance    Saved values from database.
   */
  public function widget( $args, $instance )
  {
    mailermailer_signup_form(array_merge($args, $instance));
  }
  
  /**
   * Place link to plugin settings in widget area.
   *
   * @return    string    Return value taken from parent class.
   */
  public function form()
  {
    echo '<a href="' . admin_url( 'options-general.php?page=mailermailer_settings' ) . '">Configure Settings</a>';
    return 'noform';
  }

}

/**
 * Display widget.
 *
 * @return    void
 */
function mailermailer_signup_form($args = array())
{
  extract($args);

  $opts = get_option('mailermailer');
  $title = $opts['mm_user_form_title'];
  $mm_obj = MailerMailer::get_instance();
  $custom_css = $mm_obj->custom_css();
  
  echo $before_widget;
  
  echo $before_title . $title . $after_title;

?>
  <div id="mm_signup_form_content">
    <form method= "post" action="#mm_signup_form" id="mm_signup_form" style="<?php echo $custom_css; ?>">
      <input type= "hidden" name= "mm_request" value= "mm_form_submit" />
      <input type= "hidden" id="mm_is_javascript" name="mm_is_javascript" value= "no"/>
      <div id="mm_msg"><?php echo MailerMailer::messages(); ?></div>
      <?php mailermailer_display_signup_form(); ?>
      <div><em>*</em> denotes a required field</div>
      <input type="submit" name="mm_signup_submit" id="mm_signup_submit" value="Subscribe" class="button" />
    </form>
    <?php if ($opts['mm_powered_by_tagline'] == 'yes') {
      echo 'powered by <a href="http://www.mailermailer.com/index.rwp">MailerMailer</a>';
    } ?>
  </div> <?php

  echo $after_widget;
}

/**
 * Construct signup form based on our formfields.
 *
 * @return    void
 */
function mailermailer_display_signup_form()
{
  
  $formfields = get_option('mm_formfields_struct');
   
  // go through all the form fields 
  foreach ($formfields as $field) {

    if ($field['visible']) { // display only visible fields
    
      $name = 'mm_' . $field['fieldname'];

      // Display name of input needed and * if required
      echo '<label>' . $field['description'];
      if ($field['required']) {
          echo ' <em>*</em>';
      }
      echo '</label>';
      
      // render open_text fields
      if ($field['type'] == 'open_text') {
        $maxlength = $field['attributes']['length'];
        echo '<div><input type="text" name="' . $name . '" id="' . $name . '" maxlength="' . $maxlength . '"/></div>';
      }

      // render dropdown menus for states
      if ($field['type'] == 'state') {
        echo mailermailer_get_states();
        echo '<div><input id="' . $name . '_other" type="text" name="' . $name . '_other" maxlength="20"/></div>';
      }
      
      // render dropdown menus for countries
      if ($field['type'] == 'country') {
        echo mailermailer_get_countries();
      }
      
      // render select_types fields
      if ($field['type'] == 'select') {
        
        // Sort elements in the order specified by the user
        ksort($field['choices']);
        
        // if multiple choices can be selected
        if ($field['attributes']['select_type'] == 'multi') {
          
          echo '<div id="' . $name . '_chbx">';

          foreach ($field['choices'] as $key => $value) { 
            echo '<div><input id="' . $name . '" type="checkbox" name="' . $name . '_' . $key . '" value="' . $key . '"/>&nbsp;' . $value . '</div>';
          }

          echo '</div>';

        // if only once choice can be selected
        } else {
          echo '<div><select id="' . $name . '" name = "' . $name . '">';
          echo '<option value="--" selected="selected">--</option>';
          foreach($field['choices'] as $key => $value){
            echo '<option value= "' . $key . '" >' . $value . '</option>';
          }
          echo '</select></div>';
        }
      }
      
      if(isset($field['formtip'])) {
        echo '<div class="formtip">' . $field['formtip'] . '</div>';
      }
    }
  }
}

/**
 * Create shortcode.
 *
 * @param     array     $atts    An associative array of attributes, or an empty string if no attributes are given.
 * @return    string             The signup form to be displayed.
 */
function mailermailer_shortcode($atts)
{
  ob_start();
  mailermailer_signup_form();
  $output_string = ob_get_contents();
  ob_end_clean();
  return $output_string;
}
add_shortcode('mailermailer_form', 'mailermailer_shortcode');

/**
 * List of countries.
 *
 * @return    string    List of countries.
 */
function mailermailer_get_countries()
{
return '<div><select id="mm_user_country" name="mm_user_country">
  <option value="--" selected="selected">--</option>
  <option value="us" >United States</option>
  <option value="ca" >Canada</option>
  <option value="af" >Afghanistan</option>
  <option value="ax" >Aland Islands</option>
  <option value="al" >Albania</option>
  <option value="dz" >Algeria</option>
  <option value="as" >American Samoa</option>
  <option value="ad" >Andorra</option>
  <option value="ao" >Angola</option>
  <option value="ai" >Anguilla</option>
  <option value="aq" >Antarctica</option>
  <option value="ag" >Antigua and Barbuda</option>
  <option value="ar" >Argentina</option>
  <option value="am" >Armenia</option>
  <option value="aw" >Aruba</option>
  <option value="au" >Australia</option>
  <option value="at" >Austria</option>
  <option value="az" >Azerbaijan</option>
  <option value="bs" >Bahamas</option>
  <option value="bh" >Bahrain</option>
  <option value="bd" >Bangladesh</option>
  <option value="bb" >Barbados</option>
  <option value="by" >Belarus</option>
  <option value="be" >Belgium</option>
  <option value="bz" >Belize</option>
  <option value="bj" >Benin</option>
  <option value="bm" >Bermuda</option>
  <option value="bt" >Bhutan</option>
  <option value="bo" >Bolivia, Plurinational State of</option>
  <option value="ba" >Bosnia and Herzegovina</option>
  <option value="bw" >Botswana</option>
  <option value="bv" >Bouvet Island</option>
  <option value="br" >Brazil</option>
  <option value="io" >British Indian Ocean Territory</option>
  <option value="bn" >Brunei Darussalam</option>
  <option value="bg" >Bulgaria</option>
  <option value="bf" >Burkina Faso</option>
  <option value="bi" >Burundi</option>
  <option value="kh" >Cambodia</option>
  <option value="cm" >Cameroon</option>
  <option value="cv" >Cape Verde</option>
  <option value="ky" >Cayman Islands</option>
  <option value="cf" >Central African Republic</option>
  <option value="td" >Chad</option>
  <option value="cl" >Chile</option>
  <option value="cn" >China</option>
  <option value="cx" >Christmas Island</option>
  <option value="cc" >Cocos (Keeling) Islands</option>
  <option value="co" >Colombia</option>
  <option value="km" >Comoros</option>
  <option value="cg" >Congo</option>
  <option value="cd" >Congo, The Democratic Republic of the</option>
  <option value="ck" >Cook Islands</option>
  <option value="cr" >Costa Rica</option>
  <option value="ci" >Cote d\'Ivoire (Ivory Coast)</option>
  <option value="hr" >Croatia</option>
  <option value="cu" >Cuba</option>
  <option value="cy" >Cyprus</option>
  <option value="cz" >Czech Republic</option>
  <option value="dk" >Denmark</option>
  <option value="dj" >Djibouti</option>
  <option value="dm" >Dominica</option>
  <option value="do" >Dominican Republic</option>
  <option value="ec" >Ecuador</option>
  <option value="eg" >Egypt</option>
  <option value="sv" >El Salvador</option>
  <option value="gq" >Equatorial Guinea</option>
  <option value="er" >Eritrea</option>
  <option value="ee" >Estonia</option>
  <option value="et" >Ethiopia</option>
  <option value="fk" >Falkland Islands (Malvinas)</option>
  <option value="fo" >Faroe Islands</option>
  <option value="fj" >Fiji</option>
  <option value="fi" >Finland</option>
  <option value="fr" >France</option>
  <option value="gf" >French Guiana</option>
  <option value="pf" >French Polynesia</option>
  <option value="tf" >French Southern Territories</option>
  <option value="ga" >Gabon</option>
  <option value="gm" >Gambia</option>
  <option value="ge" >Georgia</option>
  <option value="de" >Germany</option>
  <option value="gh" >Ghana</option>
  <option value="gi" >Gibraltar</option>
  <option value="gr" >Greece</option>
  <option value="gl" >Greenland</option>
  <option value="gd" >Grenada</option>
  <option value="gp" >Guadeloupe</option>
  <option value="gu" >Guam</option>
  <option value="gt" >Guatemala</option>
  <option value="gg" >Guernsey</option>
  <option value="gn" >Guinea</option>
  <option value="gw" >Guinea-Bissau</option>
  <option value="gy" >Guyana</option>
  <option value="ht" >Haiti</option>
  <option value="hm" >Heard Island and Mcdonald Islands</option>
  <option value="va" >Holy See (Vatican City State)</option>
  <option value="hn" >Honduras</option>
  <option value="hk" >Hong Kong</option>
  <option value="hu" >Hungary</option>
  <option value="is" >Iceland</option>
  <option value="in" >India</option>
  <option value="id" >Indonesia</option>
  <option value="ir" >Iran, Islamic Republic of</option>
  <option value="iq" >Iraq</option>
  <option value="ie" >Ireland</option>
  <option value="im" >Isle of Man</option>
  <option value="il" >Israel</option>
  <option value="it" >Italy</option>
  <option value="jm" >Jamaica</option>
  <option value="jp" >Japan</option>
  <option value="je" >Jersey</option>
  <option value="jo" >Jordan</option>
  <option value="kz" >Kazakhstan</option>
  <option value="ke" >Kenya</option>
  <option value="ki" >Kiribati</option>
  <option value="kp" >Korea, Democratic People\'s Republic of</option>
  <option value="kr" >Korea, Republic of</option>
  <option value="kw" >Kuwait</option>
  <option value="kg" >Kyrgyzstan</option>
  <option value="la" >Lao People\'s Democratic Republic</option>
  <option value="lv" >Latvia</option>
  <option value="lb" >Lebanon</option>
  <option value="ls" >Lesotho</option>
  <option value="lr" >Liberia</option>
  <option value="ly" >Libyan Arab Jamahiriya</option>
  <option value="li" >Liechtenstein</option>
  <option value="lt" >Lithuania</option>
  <option value="lu" >Luxembourg</option>
  <option value="mo" >Macao</option>
  <option value="mk" >Macedonia, The Former Yugoslav Republic of</option>
  <option value="mg" >Madagascar</option>
  <option value="mw" >Malawi</option>
  <option value="my" >Malaysia</option>
  <option value="mv" >Maldives</option>
  <option value="ml" >Mali</option>
  <option value="mt" >Malta</option>
  <option value="mh" >Marshall Islands</option>
  <option value="mq" >Martinique</option>
  <option value="mr" >Mauritania</option>
  <option value="mu" >Mauritius</option>
  <option value="yt" >Mayotte</option>
  <option value="mx" >Mexico</option>
  <option value="fm" >Micronesia, Federated States of</option>
  <option value="md" >Moldova, Republic of</option>
  <option value="mc" >Monaco</option>
  <option value="mn" >Mongolia</option>
  <option value="me" >Montenegro</option>
  <option value="ms" >Montserrat</option>
  <option value="ma" >Morocco</option>
  <option value="mz" >Mozambique</option>
  <option value="mm" >Myanmar</option>
  <option value="na" >Namibia</option>
  <option value="nr" >Nauru</option>
  <option value="np" >Nepal</option>
  <option value="nl" >Netherlands</option>
  <option value="an" >Netherlands Antilles</option>
  <option value="nc" >New Caledonia</option>
  <option value="nz" >New Zealand</option>
  <option value="ni" >Nicaragua</option>
  <option value="ne" >Niger</option>
  <option value="ng" >Nigeria</option>
  <option value="nu" >Niue</option>
  <option value="nf" >Norfolk Island</option>
  <option value="mp" >Northern Mariana Islands</option>
  <option value="no" >Norway</option>
  <option value="om" >Oman</option>
  <option value="pk" >Pakistan</option>
  <option value="pw" >Palau</option>
  <option value="ps" >Palestinian Territory, Occupied</option>
  <option value="pa" >Panama</option>
  <option value="pg" >Papua New Guinea</option>
  <option value="py" >Paraguay</option>
  <option value="pe" >Peru</option>
  <option value="ph" >Philippines</option>
  <option value="pn" >Pitcairn</option>
  <option value="pl" >Poland</option>
  <option value="pt" >Portugal</option>
  <option value="pr" >Puerto Rico</option>
  <option value="qa" >Qatar</option>
  <option value="re" >Reunion</option>
  <option value="ro" >Romania</option>
  <option value="ru" >Russian Federation</option>
  <option value="rw" >Rwanda</option>
  <option value="bl" >Saint Barthelemy</option>
  <option value="sh" >Saint Helena</option>
  <option value="kn" >Saint Kitts and Nevis</option>
  <option value="lc" >Saint Lucia</option>
  <option value="mf" >Saint Martin</option>
  <option value="pm" >Saint Pierre and Miquelon</option>
  <option value="vc" >Saint Vincent and the Grenadines</option>
  <option value="ws" >Samoa</option>
  <option value="sm" >San Marino</option>
  <option value="st" >Sao Tome and Principe</option>
  <option value="sa" >Saudi Arabia</option>
  <option value="sn" >Senegal</option>
  <option value="rs" >Serbia</option>
  <option value="sc" >Seychelles</option>
  <option value="sl" >Sierra Leone</option>
  <option value="sg" >Singapore</option>
  <option value="sk" >Slovakia</option>
  <option value="si" >Slovenia</option>
  <option value="sb" >Solomon Islands</option>
  <option value="so" >Somalia</option>
  <option value="za" >South Africa</option>
  <option value="gs" >South Georgia and the South Sandwich Islands</option>
  <option value="es" >Spain</option>
  <option value="lk" >Sri Lanka</option>
  <option value="sd" >Sudan</option>
  <option value="sr" >Suriname</option>
  <option value="sj" >Svalbard and Jan Mayen</option>
  <option value="sz" >Swaziland</option>
  <option value="se" >Sweden</option>
  <option value="ch" >Switzerland</option>
  <option value="sy" >Syrian Arab Republic</option>
  <option value="tw" >Taiwan, Province of China</option>
  <option value="tj" >Tajikistan</option>
  <option value="tz" >Tanzania, United Republic of</option>
  <option value="th" >Thailand</option>
  <option value="tl" >Timor-leste</option>
  <option value="tg" >Togo</option>
  <option value="tk" >Tokelau</option>
  <option value="to" >Tonga</option>
  <option value="tt" >Trinidad and Tobago</option>
  <option value="tn" >Tunisia</option>
  <option value="tr" >Turkey</option>
  <option value="tm" >Turkmenistan</option>
  <option value="tc" >Turks and Caicos Islands</option>
  <option value="tv" >Tuvalu</option>
  <option value="ug" >Uganda</option>
  <option value="ua" >Ukraine</option>
  <option value="ae" >United Arab Emirates</option>
  <option value="gb" >United Kingdom</option>
  <option value="um" >United States Minor Outlying Islands</option>
  <option value="uy" >Uruguay</option>
  <option value="uz" >Uzbekistan</option>
  <option value="vu" >Vanuatu</option>
  <option value="ve" >Venezuela</option>
  <option value="vn" >Viet Nam</option>
  <option value="vg" >Virgin Islands, British</option>
  <option value="vi" >Virgin Islands, U.S.</option>
  <option value="wf" >Wallis and Futuna</option>
  <option value="eh" >Western Sahara</option>
  <option value="ye" >Yemen</option>
  <option value="zm" >Zambia</option>
  <option value="zw" >Zimbabwe</option>
  </select></div>';
}

/**
 * List of U.S. states.
 *
 * @return    string    List of of U.S. states.
 */
function mailermailer_get_states()
{
return '<div><select id="mm_user_state" name="mm_user_state">
  <option value="--" selected="selected">--</option>
  <option value="al" >Alabama</option>
  <option value="ak" >Alaska</option>
  <option value="az" >Arizona</option>
  <option value="ar" >Arkansas</option>
  <option value="ca" >California</option>
  <option value="co" >Colorado</option>
  <option value="ct" >Connecticut</option>
  <option value="de" >Delaware</option>
  <option value="dc" >District of Columbia</option>
  <option value="fl" >Florida</option>
  <option value="ga" >Georgia</option>
  <option value="hi" >Hawaii</option>
  <option value="id" >Idaho</option>
  <option value="il" >Illinois</option>
  <option value="in" >Indiana</option>
  <option value="ia" >Iowa</option>
  <option value="ks" >Kansas</option>
  <option value="ky" >Kentucky</option>
  <option value="la" >Louisiana</option>
  <option value="me" >Maine</option>
  <option value="md" >Maryland</option>
  <option value="ma" >Massachusetts</option>
  <option value="mi" >Michigan</option>
  <option value="mn" >Minnesota</option>
  <option value="ms" >Mississippi</option>
  <option value="mo" >Missouri</option>
  <option value="mt" >Montana</option>
  <option value="ne" >Nebraska</option>
  <option value="nv" >Nevada</option>
  <option value="nh" >New Hampshire</option>
  <option value="nj" >New Jersey</option>
  <option value="nm" >New Mexico</option>
  <option value="ny" >New York</option>
  <option value="nc" >North Carolina</option>
  <option value="nd" >North Dakota</option>
  <option value="oh" >Ohio</option>
  <option value="ok" >Oklahoma</option>
  <option value="or" >Oregon</option>
  <option value="pa" >Pennsylvania</option>
  <option value="ri" >Rhode Island</option>
  <option value="sc" >South Carolina</option>
  <option value="sd" >South Dakota</option>
  <option value="tn" >Tennessee</option>
  <option value="tx" >Texas</option>
  <option value="ut" >Utah</option>
  <option value="vt" >Vermont</option>
  <option value="va" >Virginia</option>
  <option value="wa" >Washington</option>
  <option value="wv" >West Virginia</option>
  <option value="wi" >Wisconsin</option>
  <option value="wy" >Wyoming</option>
  <option value="--">--</option>
  <option value="ab" >Alberta</option>
  <option value="bc" >British Columbia</option>
  <option value="mb" >Manitoba</option>
  <option value="nt" >N.W. Territories</option>
  <option value="nb" >New Brunswick</option>
  <option value="nl" >Newfoundland and Labrador</option>
  <option value="ns" >Nova Scotia</option>
  <option value="nu" >Nunavet</option>
  <option value="on" >Ontario</option>
  <option value="pe" >Prince Edward Island</option>
  <option value="qc" >Quebec</option>
  <option value="sk" >Saskatchewan</option>
  <option value="yt" >Yukon</option>
  <option value="--">--</option>
  <option value="Other" >Other --&gt;</option>
  </select></div>';
}

?>
