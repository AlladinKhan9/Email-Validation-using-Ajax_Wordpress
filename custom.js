jQuery(document).on('click', '.submit_contact_us .quform-submit', function (e) {
        var emailField = jQuery('.quform-field-email');
        var email_val = emailField.val();
        emailField.parent().find('.error-message').remove('span');
        $.ajax({
            url: custom_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'contact_us_email_validation',
                nonce: custom_vars.nonce,
                email: email_val
            },
            dataType: 'json',
            success: function (response) {
                if(response.success == false){
                    emailField.after('<span class="error-message" style="color: red; display: block;">' + response.data.message + '</span>');
                }
            },
            error: function () {
                alert('There was an error submitting the form. Please try again.');
            }
        });
});
