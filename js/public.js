/*
Submit form
*/
;(function($){
  $(function($) {

    $('#mm_is_javascript').val('yes');

    $('#mm_signup_form').ajaxForm({
      url: mailermailer_params.mm_url, 
      type: 'POST', 
      dataType: 'json',
      beforeSubmit: mm_beforeForm,
      success: mm_success
    });
  });
  
  function mm_beforeForm(){
    // Disable the submit button
    $('#mm_signup_submit').attr("disabled","disabled");
  }
  
  function mm_success(data){
    // enable the submit button
    $('#mm_signup_submit').removeAttr("disabled");

    // gather data
    var missing = data.missing;
    var message = data.message;
    var member = data.member;
    var captcha_container = 'mailermailer_captcha_container';
    var check_message = new RegExp('class="mm_display_success"', 'i');

    // display message
    $('#mm_msg').html(message);

    // highlight missing fields
    for (var field in member) {
      if (member.hasOwnProperty(field)) {
        var fieldname = 'mm_'+field;
        var checkboxes = $('#'+fieldname+'_chbx');
        
        if (missing.hasOwnProperty(fieldname)) {
          if (checkboxes.length) {
            checkboxes.addClass("checkbox-warning");
          } else {
            $('#'+fieldname).addClass("warning");
            if (fieldname == 'mm_user_state') {
              $('#'+fieldname+'_other').addClass("warning");
            }
          }
        } else {
          if (checkboxes.length) {
            checkboxes.removeClass("checkbox-warning");
          } else {
            $('#'+fieldname).removeClass("warning");
            if (fieldname == 'mm_user_state') {
              $('#'+fieldname+'_other').removeClass("warning");
            }
          }
        }
      } 
    }// end for loop

    if (check_message.test(message)) {
      $('#mm_signup_form').each(function(){
        this.reset();
      });
      $('#mm_is_javascript').val('yes');
    }

    if ($.trim($('#'+captcha_container).html()).length) {
      // reset captcha
      grecaptcha.reset(); 
      // highlight captcha if missing
      if (missing.hasOwnProperty(captcha_container)) {
        $('#'+captcha_container).addClass("captcha-warning");
      } else {
        $('#'+captcha_container).removeClass("captcha-warning");        
      }
    }

  }
})(jQuery);