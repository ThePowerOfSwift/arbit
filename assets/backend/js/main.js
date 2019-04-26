/*Script by developed StartCheme
Website: https://www.startcheme.com*/
(function($) {

  "use strict";

  var $document = $(document),
    $window = $(window),
    forms = {
      registerForm: $('#registerForm')
    };

  $document.ready(function() {
    var $registerForm = forms.registerForm;
    //register
    $('#u_pwd, #pwd2').on('keyup', function() {
      if ($('#u_pwd').val() == $('#pwd2').val()) {
        $('#message').html('Password matching').css(
          'color', '#81c868');
      } else
        $('#message').html('Password no matching').css(
          'color', '#f05050');
    });
    if (forms.registerForm.length) {
      $registerForm.validate({
        rules: {
          u_email: {
            email: true,
            remote: {
              url: "register/register_email_exists",
              type: "post",
              data: {
                u_email: function() {
                  return $("#u_email").val();
                }
              }
            }
          },
          u_username: {
            required: true
          },
          u_pwd: {
            required: true,
            minlength: 6
          }
        },
        messages: {
          u_email: {
            remote: 'Email is already exists, choose another email'
          }
        }
      });
    }

  });

  jQuery(window).on('load', function(){$ = jQuery;});

})(jQuery);
