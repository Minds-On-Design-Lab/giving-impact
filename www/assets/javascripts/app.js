$(document).foundation();

(function($) {

    $(function() {
        if( $('[data-action="remove-opp-supporter"]').length == 0 ) {
            return;
        }

        $('[data-action="remove-opp-supporter"]').on('click', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');

            if( !confirm('Are you sure you want to remove this supporter?') ) {
                return;
            }

            var form = $('<form></form>')
                .attr('method', 'post')
                .attr('action', url)
                .append(
                    $('<input />')
                        .attr('type', 'hidden')
                        .attr('name', 's')
                        .val(1)
                );
            $('body').append(form);
            form.submit();
        });
    });

    $(function() {
        if( $('[name="currency"]').length ) {
            $('[name="currency"]').on('change', function(e) {
                var cur = $(this).val().toUpperCase();
                if( !confirm('You selected '+cur+'. Once selected, your currency cannot be changed. Are you sure you want to select this currency?') ) {
                    return;
                }

                $(this).parents('form').first().submit();
            });
        }
    });

    $(function() {
        if( !$('#container-donation-levels').length ) {
            return;
        }
        if( $('input[name="levels"]:checked').val() == 1 ) {
            $('#container-donation-levels').show();
        } else {
            $('#container-donation-levels').hide();
        }

        $('input[name="levels"]').change(function(e) {
            if( $(this).val() == 1 ) {
                $('#container-donation-levels').show();
                return;
            }

            $('#container-donation-levels').hide();
        });
    });

    $(function() {
        if( $('[name="send_receipt"]').length == 0 ) {
            return;
        }

        if( $('input[name="send_receipt"]:checked').val() == 0 ) {
            $('#container-reciept-settings').hide();
        }

        $('[name="send_receipt"]').click(function(e) {
            if( $(this).prop('checked') && $(this).val() == 1 ) {
                $('#container-reciept-settings').show();
                return;
            }
            $('#container-reciept-settings').hide();
        })
    });

    $(function() {
        if( !$('#custom-field-modal').length ) {
            return;
        }

        $('body').on('click', '#customFieldModal .close-reveal-modal', function(e) {
            $('#customFieldModal').remove();
            $('.reveal-modal-bg').remove();
        });

        var modal = $('#custom-field-modal'),
            template = Handlebars.compile($('#custom-field-modal').html()),
            rowTemplate = Handlebars.compile($('#custom-field-row').html()),
            update = function() {
                // grab all the bits and bobs
                var ctx = {
                    'type': $('#customFieldModal').find('[data-field="type"]').val(),
                    'label': $('#customFieldModal').find('[data-field="label"]').val(),
                    'options': $('#customFieldModal').find('[data-field="options"]').val(),
                    'required': $('#customFieldModal').find('[data-field="required"]:checked').val(),
                    'id': $('#customFieldModal').find('[data-field="id"]').val(),
                    'options_formatted': []
                };
                console.log(ctx);
                if( !ctx.id ) {
                    ctx.status = true;
                }

                $(ctx.options.split(/(\n|\r)/)).each(function() {
                    var o = this;
                    if( !o.replace(/(\n|\r)/g, '').length ) {
                        return;
                    }
                    ctx.options_formatted.push({'option': o});
                });

                if( $('#customFieldModal').find('[data-field="required"]').prop('checked') ) {
                    ctx.required = true;
                }

                if( !$('#customFieldModal').data('track-row') ) {
                    $('#custom-fields').append($(rowTemplate(ctx)));
                } else {
                    var row = $('#customFieldModal').data('track-row');

                    row.find('[name="custom_field_types[]"]').val(ctx.type);
                    row.find('[name="custom_field_labels[]"]').val(ctx.label);
                    row.find('[name="custom_field_options[]"]').val(ctx.options);
                    row.find('[name="custom_field_required[]"]').val(ctx.required);

                    row.find('[data-field="label"]').text(ctx.label);
                    row.find('[data-field="type"] span').text(ctx.type);
                    row.find('[data-field="options"]').html(ctx.options.replace(/(\n|\r)/g, '<br>'));
                }
            };

        $('body').on('change', '> #customFieldModal [data-field="type"]', function() {
            if( $(this).val() == 'text' ) {
                $('#container-field-dropdown').hide();
            } else {
                $('#container-field-dropdown').show();
            }
        });
        $('body').on('click', '> #customFieldModal .success', function(e) {
            e.preventDefault();
            update();
            $('#customFieldModal').remove();
            $('.reveal-modal-bg').remove();
        });


        $('[data-action="add-custom-field"]').click(function(e) {
            e.preventDefault();
            var ctx = {
                'label': false,
                'options': false,
                'type': false,
                'required': false
            };

            $('#scratch').append($(template(ctx)));

            $('#customFieldModal').foundation('reveal', 'open');
            $('#container-field-dropdown').hide();

        });

        $('#custom-fields').on('click', '[data-action="hide-custom-field"]', function(e) {
            e.preventDefault();
            var el = $(this).parents('tr').find('[name="custom_field_status[]"]');
            if( el.val() != 0 ) {
                el.val(0);
                $(this).parents('tr').addClass('field-disabled');
                $(this).html('<i class="fa fa-times fa-lg action"></i>');
            } else {
                el.val(1);
                $(this).parents('tr').removeClass('field-disabled');
                $(this).html('<i class="fa fa-check fa-lg action"></i>');
            }
        });

        $('#custom-fields').on('click', '[data-action="edit-custom-field"]', function(e) {
            e.preventDefault();
            var row = $(this).parents('tr');
            var ctx = {
                'label': row.find('[data-field="label"]').text(),
                'options': row.find('[name="custom_field_options[]"]').val(),
                'type': row.find('[data-field="type"] span').text(),
                'required': row.find('[name="custom_field_required[]"]').val(),
                'id': row.find('[name="custom_field_ids[]"]').val()
            };

            $('#scratch').append($(template(ctx)));

            $('#customFieldModal').find('[data-field="type"]').val(ctx.type);
            if( ctx.type == 'text' ) {
                $('#container-field-dropdown').hide();
            }
            if( ctx.required ) {
                $('#custom-field-required').prop('checked', true);;
            }

            $('#customFieldModal').data('track-row', row);

            $('#customFieldModal').foundation('reveal', 'open');
        });
    });

    $(function() {
        $('table').on('click', '[data-action="remove-row"]', function(e) {
            e.preventDefault();
            $(this).parents('tr').remove();
            $(this).parents('tr input[name="level_statuses[]"]').val(0);
        });
        $('table').on('click', '[data-action="add-row"]', function(e) {
            e.preventDefault();

            var source = $('#'+$(this).attr('data-template')).html();
            var template = Handlebars.compile(source);

            $(template()).insertBefore($(this).parents('tr'));
        });
    });

    $(function() {
        if( $('[data-minicolors]').length == 0 ) {
            return;
        }
       // console.log($('[data-minicolors]').miniColors());
        $('[data-minicolors]').minicolors();
    });

    $(function() {
        if( !$('[data-action="remove-image"]').length ) {
            return;
        }

        $('[data-action="remove-image"]').click(function(e) {
            e.preventDefault();

            $(this).parents('form').append($('<input/>')
                .attr('type', 'hidden')
                .attr('value', 1)
                .attr('name', 'remove-image')
            );

            $('#campaign-image-thumb').remove();
            $(this).remove();
        });
    });

    $(function() {
        if( !$('#form-signup').length ) {
            return;
        }

        $('[name="pass"], [name="pass2"]').blur(function() {
            if( $('[name="pass"]').val().length && $('[name="pass2"]').val().length
                && $('[name="pass"]').val() != $('[name="pass2"]').val()
            ) {
                $('[name="pass2"]').addClass('error');
                $('[name="pass2"]').siblings('small').removeClass('hide');
            } else {
                $('[name="pass2"]').removeClass('error');
                $('[name="pass2"]').siblings('small').addClass('hide');
            }
        });
    });

    $(function() {
        if( $('[name="donation_level"]').length === 0 ) {
            return;
        }

        $('[name="donation_level"]').click(function(e) {
            var $this = $(this);

            var tpl = $('.additional_donation_container').clone();
            $('.additional_donation_container').remove();

            if( $this.prop('checked') ) {

                tpl.insertAfter(
                    $($this.parents().get(0))
                );
                tpl.find('[name="additional_donation"]').val('');
            }
        });
    });

    $(function() {
        if( !$('#form-donation-checkout').length ) {
            return;
        }

        $('[name="cc_number"]').formatCardNumber();
        $('[name="cc_cvc"]').formatCardCVC();
        $('[name="cc_exp"]').formatCardExpiry();

        $('#form-donation-checkout').submit(function(e) {
            if( $(this).find('[name="stripe_token"]').length ) {
                return;
            }

            e.preventDefault();

            $(this).find('input[type="submit"]').val('Please wait...').prop('disabled', true);

            var fail = function() {
                e.preventDefault();
                $(this).find('input[type="submit"]').val('Checkout').prop('disabled', false);
            }.bind(this);

            $('small.error').addClass('hide');

            var to_check = [
                'first_name',
                'last_name',
                'street',
                'city',
                // 'state',
                'postal',
                'country',
                'email',
                'cc_number',
                'cc_cvc',
                'cc_exp'
//                'cc_month',
//                'cc_year'
            ];

            if( window._gi_req_state ) {
                to_check.push('state');
            }
            var has_failed = false,
                pattern = /[a-z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/g;

            $(to_check).each(function(i, field) {
                var $field = $('[name="'+field+'"]');
                if( $field.attr('type') == 'text' && !$field.val().length ) {
                    $('[data-field="'+field+'"]').removeClass('hide');
                    has_failed = true;
                    return fail();
                }
                if( $field.attr('type') == 'checkbox' && !$field.prop('checked') ) {
                    $('[data-field="'+field+'"]').removeClass('hide');
                    has_failed = true;
                    return fail();
                }
            });
            if( !$('[name="email"]').val().match(pattern) ) {
                has_failed = true;
                $('[data-field="email"]').removeClass('hide');
                return fail();
            }


            if( moment().isAfter(moment($('[name="cc_exp"]').val(), 'MM / YYYY'), 'month') ) {
                $('[data-field="cc_exp"]').removeClass('hide');
                $('[data-field="cc_exp"]').text('Your expiration date is invalid');
                return fail();
            }

            if( has_failed ) {
                return;
            }

            Stripe.setPublishableKey($('[name="spk"]').val());

            Stripe.createToken({
                number: $('[name="cc_number"]').val(),
                cvc: $('[name="cc_cvc"]').val(),
                //exp_month: $('[name="cc_month"]').val(),
                //exp_year: $('[name="cc_year"]').val(),
                exp_month: $('[name="cc_exp"]').val().substr(0,2),
                exp_year: $('[name="cc_exp"]').val().substr(5,4),
                name: $('#input-firstname').val()+' '+$('#input-lastname').val(),
                address_zip: $('[name="postal"]').val()
            }, function(status, res) {
                if( res.error ) {

                    if (res.error.param == "exp_year") {
                        $('<small class="error">'+res.error.message+'</small>').insertAfter(
                            $('[name="cc_exp"]')
                        );
                    } else if (res.error.param == "exp_month") {
                        $('<small class="error">'+res.error.message+'</small>').insertAfter(
                            $('[name="cc_exp"]')
                        );
                    } else if (res.error.param == "cvc") {
                        $('<small class="error">'+res.error.message+'</small>').insertAfter(
                            $('[name="cc_cvc"]')
                        );
                    } else {
                        $('<small class="error">'+res.error.message+'</small>').insertAfter(
                            $('[name="cc_number"]')
                        );
                    }

                    return fail();
                }

                $('[name="cc_number"], [name="cc_cvc"], [name="cc_month"], '
                    +'[name="cc_year"], [name="cc_name"], [name="cc_zip"]')
                    .remove();

                var tok = res.id;
                $('#form-donation-checkout').append($('<input type="hidden" value="'+tok+'" name="stripe_token" />'));
                $('#form-donation-checkout').submit();
            });

        });

    });

    $(function() {
        if( $('#form-donation-donate').length == 0 ) {
            return;
        }

        $('#form-donation-donate').submit(function(e) {
            $(this).find('input[type="submit"]')
                .prop('disabled', true)
                .val('Please wait...');
        });
    });

    $(function() {
    	if( $('#form-contact').length == 0 ) {
    		return;
    	}

    	$('#form-contact #input_contact').change(function(e) {
    		var checked = $(this).prop('checked'),
    			$this	= $(this);

    		if( checked ) {
    			checked = 1;
    		} else {
    			checked = 0;
    		}

    		$.post(
				$('#form-contact').attr('action'),
				{contact: checked},
				function(resp) {
					$('.contact-notification').remove();

					$('<span></span>')
						.addClass('label success radius contact-notification')
						.text('Contact preferences updated')
						.insertAfter($this.parent());

					setTimeout(function() {
						$('.contact-notification').fadeOut();
					}, 5000);
				}
			);
    	});
    });

    $(function() {
        if( $('[name="frequency"]').length == 0 || $('[name="interval"]').length == 0 ) {
            return;
        }
        if( $('[name="frequency"]').val() == 'onetime' || !$('[name="frequency"]').val() ) {
            $('[name="interval"]').parents('.row').first().css('display', 'none');
        }

        $('[name="frequency"]').on('change', function(e) {
            if( $(this).val() && $(this).val() != 'onetime' ) {
                $('[name="interval"]').parents('.row').first().css('display', 'block');
            } else {
                $('[name="interval"]').parents('.row').first().css('display', 'none');
            }
        });
    });

})(jQuery);

logshare = function(t,u) {
    var url = window.gi_base+'share/'+window.gi_campaign_id+'/log',
        did = false;

    if( $('#input-donation-id').length ) {
        did = $('#input-donation-id').val();
    }

    $.post(
        url,
        {url:encodeURIComponent(u),type:t,donation:did},
        function(data) {
        }
    );

};

extractParamFromUri = function(uri, paramName) {
    if (!uri) {
        return;
    }
    var regex = new RegExp('[\\?&#]' + paramName + '=([^&#]*)');
    var params = regex.exec(uri);
    if (params !== null) {
        return unescape(params[1]);
    }
    return;
};
