/**
 * javascript library for fphp_validation module with jQuery Validate
 *
 * @category    forestPHP Framework
 * @author      Rene Arentz <rene.arentz@forestany.net>
 * @copyright   (c) 2024 forestPHP Framework
 * @license     https://www.gnu.org/licenses/gpl-3.0.de.html GNU General Public License 3
 * @license     https://opensource.org/licenses/MIT MIT License
 * @version     1.1.0 stable
 * @link        https://forestany.net
 * @object-id   0x2 00002
 * @since       File available since Release 0.1.3 alpha
 * @deprecated  -
 *
 * @version log Version			Developer	Date		Comment
 * 				0.1.3 alpha		renea		2019-08-15	added to framework
 * 				0.9.0 beta		renea		2020-01-27	changes for bootstrap 4
 * 				1.1.0 stable	renea		2024-08-10	changes for bootstrap 5
 */
$.fn.ReplaceCommaWithDot = function() {
	return this.each(function() {
		$(this).keyup(function(p_e_event) {
			$(this).val($(this).val().replace(/,/g,'.'));
		});
	});
};

function fphp_apply_data_validator(p_o_options) {
	var o_validator = $(p_o_options.s_formId).validate({
		ignore: 'div',
		
		invalidHandler: function(e, validator) {
			if(validator.errorList.length){
				let s_tabId = $(validator.errorList[0].element).closest(".tab-pane").attr('id');
				$('.nav-tabs a[href="#' + s_tabId + '"]').tab('show');
			}
		},
		
		showErrors: function(errorMap, errorList) {
			this.defaultShowErrors();
			
			var b_tooltip = false;

			// create new tooltips for invalid elements
			$.each(errorList, function (index, error) {
				var $element = $(error.element);

				var s_message = '';
				
				if ($element.data('valmessage')) {
					s_message = $element.data('valmessage')
				} else if ($element.data('valmessage') == undefined) {
					s_message = error.message;
				}
				
				s_message = '<div class="invalid-tooltip">' + s_message + '</div>';
				
				if (!b_tooltip) {
					$element.nextAll('.invalid-tooltip').remove();
					$element.after(s_message);
					
					b_tooltip = true;
				}
			});
		},
		
		errorPlacement: function(error, element) {
		},
		
		highlight: function(element) {
			$(element).removeClass('is-valid');
			$(element).addClass('is-invalid');
			
			$(element).parent().siblings().each(function() {
				$(this).find("input[name='" + $(element).attr('name') + "']").removeClass('is-valid');
				$(this).find("input[name='" + $(element).attr('name') + "']").addClass('is-invalid');
			});
		},
		
		unhighlight: function(element) {
			$(element).removeClass('is-invalid');
			$(element).addClass('is-valid');
			
			$(element).parent().siblings().each(function() {
				$(this).find("input[name='" + $(element).attr('name') + "']").removeClass('is-invalid');
				$(this).find("input[name='" + $(element).attr('name') + "']").addClass('is-valid');
			});
		}
	});
	
	$.extend($.validator.messages, {
		required: p_o_options.s_requiredDefaultMessage/*,
		remote: 'Please fix this field.',
		email: 'Please enter a valid email address.',
		url: 'Please enter a valid URL.',
		date: 'Please enter a valid date.',
		dateISO: 'Please enter a valid date (ISO).',
		number: 'Please enter a valid number.',
		digits: 'Please enter only digits.',
		creditcard: 'Please enter a valid credit card number.',
		equalTo: 'Please enter the same value again.',
		accept: 'Please enter a value with a valid extension.',
		maxlength: jQuery.validator.format('Please enter no more than {0} characters.'),
		minlength: jQuery.validator.format('Please enter at least {0} characters.'),
		rangelength: jQuery.validator.format('Please enter a value between {0} and {1} characters long.'),
		range: jQuery.validator.format('Please enter a value between {0} and {1}.'),
		max: jQuery.validator.format('Please enter a value less than or equal to {0}.'),
		min: jQuery.validator.format('Please enter a value greater than or equal to {0}.')*/
	});
	
	$.validator.addMethod('fphp_dateISO', function(value, element) {
		// allow any non-whitespace characters as the host part
		return this.optional( element ) || /^(\d){4}-((0[1-9])|(1[0-2]))-((0[1-9])|(1[0-9])|(2[0-9])|(3[0-1]))$/.test( value );
	});
	
	$.validator.addMethod('fphp_month', function(value, element) {
		// allow any non-whitespace characters as the host part
		return this.optional( element ) || /^(\d){4}-((0[1-9])|(1[0-2]))$/.test( value );
	});
	
	$.validator.addMethod('fphp_week', function(value, element) {
		// allow any non-whitespace characters as the host part
		return this.optional( element ) || /^(\d){4}-W((0[1-9])|([1-4][0-9])|(5[0-3]))$/.test( value );
	});
	
	$.validator.addMethod('fphp_dateDMYpoint', function(value, element) {
		// allow any non-whitespace characters as the host part
		return this.optional( element ) || /^((0[1-9])|(1[0-9])|(2[0-9])|(3[0-1]))\.((0[1-9])|(1[0-2]))\.(\d){4}$/.test( value );
	});

	$.validator.addMethod('fphp_dateDMYslash', function(value, element) {
		// allow any non-whitespace characters as the host part
		return this.optional( element ) || /^((0[1-9])|(1[0-9])|(2[0-9])|(3[0-1]))\/((0[1-9])|(1[0-2]))\/(\d){4}$/.test( value );
	});

	$.validator.addMethod('fphp_dateMDYslash', function(value, element) {
		// allow any non-whitespace characters as the host part
		return this.optional( element ) || /^((0[1-9])|(1[0-2]))\/((0[1-9])|(1[0-9])|(2[0-9])|(3[0-1]))\/(\d){4}$/.test( value );
	});

	$.validator.addMethod('fphp_time', function(value, element) {
		// allow any non-whitespace characters as the host part
		return this.optional( element ) || /^(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9])(:([0-5][0-9])){0,1}$/.test( value );
	});

	$.validator.addMethod('fphp_datetime', function(value, element) {
		// allow any non-whitespace characters as the host part
		return this.optional( element ) || /^((0[1-9])|(1[0-9])|(2[0-9])|(3[0-1]))\.((0[1-9])|(1[0-2]))\.(\d){4}\s(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/.test( value );
	});

	$.validator.addMethod('fphp_datetimeISO', function(value, element) {
		// allow any non-whitespace characters as the host part
		return this.optional( element ) || /^(\d){4}-((0[1-9])|(1[0-2]))-((0[1-9])|(1[0-9])|(2[0-9])|(3[0-1]))(\s|T)(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9])(:([0-5][0-9])){0,1}$/.test( value );
	});

	$.validator.addMethod('fphp_dateinterval', function(value, element) {
		// allow any non-whitespace characters as the host part
		var regex1 = /^(P(((\d)+Y(\d)+M((\d)+(W|D))?)|((\d)+(Y|M)(\d)+(W|D))|((\d)+(Y|M|W|D)))T(((\d)+H(\d)+M(\d)+S)|((\d)+H(\d)+(M|S))|((\d)+M(\d)+S)|((\d)+(H|M|S))))$/;
		var regex2 = /^(PT(((\d)+H(\d)+M(\d)+S)|((\d)+H(\d)+(M|S))|((\d)+M(\d)+S)|((\d)+(H|M|S))))$/;
		var regex3 = /^(P(((\d)+Y(\d)+M((\d)+(W|D))?)|((\d)+(Y|M)(\d)+(W|D))|((\d)+(Y|M|W|D))))$/;
		
		return this.optional( element ) || regex1.test( value ) || regex2.test( value ) || regex3.test( value );
	});
	
	$.validator.addMethod('fphp_password', function(value, element) {
		return this.optional( element ) || (
			/^[A-Za-z0-9\d=!\-@._*?#§$%&'~:;,]*$/.test(value) // consists of only these
			&& /[=!\-@._*?#§$%&'~:;,]/.test(value) // has a special character
			&& /[a-z]/.test(value) // has a lowercase letter
			&& /[A-Z]/.test(value) // has a uppercase letter
			&& /\d/.test(value) // has a digit
		)
	});

	$.validator.addMethod('fphp_username', function(value, element) {
		// allow any non-whitespace characters as the host part
		return this.optional( element ) || /^[a-zA-Z0-9_\-]*$/.test( value );
	});
	
	$.validator.addMethod('fphp_onlyletters', function(value, element) {
		// allow any non-whitespace characters
		return this.optional( element ) || /^[a-zA-Z]*$/.test( value );
	});

	for (let i = 0; i < p_o_options.a_rules.length; i++) {
		let o_rule = p_o_options.a_rules[i];
		
		switch (o_rule.s_rule) {
			case 'required':
				if (o_rule.s_ruleParam02 == 'fphpByName') {
					$('input[name^=' + o_rule.s_formElementId.substr(1) + ']').rules('add', { [o_rule.s_rule] : o_rule.s_ruleParam01 });
				} else {
					$(o_rule.s_formElementId).rules('add', { [o_rule.s_rule] : o_rule.s_ruleParam01 });
				}
			break;
			case 'minlength':
			case 'maxlength':
			case 'min':
			case 'max':
			case 'email':
			case 'url':
			case 'digits':
			case 'equalTo':
			case 'fphp_month':
			case 'fphp_week':
			case 'fphp_dateISO':
			case 'dateISO':
			case 'fphp_dateDMYpoint':
			case 'fphp_dateDMYslash':
			case 'fphp_dateMDYslash':
			case 'fphp_datetime':
			case 'fphp_datetimeISO':
			case 'fphp_dateinterval':
			case 'fphp_password':
			case 'fphp_username':
			case 'fphp_onlyletters':
				if (o_rule.s_ruleAutoRequired == true) {
					$(o_rule.s_formElementId).rules('add', { required : true });
				}
				
				$(o_rule.s_formElementId).rules('add', { [o_rule.s_rule] : o_rule.s_ruleParam01 });
			break;
			case 'fphp_time':
				if (o_rule.s_ruleAutoRequired == true) {
					$(o_rule.s_formElementId).rules('add', { required : true });
				}
				
				$(o_rule.s_formElementId).rules('add', { [o_rule.s_rule] : o_rule.s_ruleParam01 });
				$(o_rule.s_formElementId).rules('add', { step : false });
			break;
			case 'number':
				$(o_rule.s_formElementId).ReplaceCommaWithDot();
				
				if (o_rule.s_ruleAutoRequired == true) {
					$(o_rule.s_formElementId).rules('add', { required : true });
				}
				
				$(o_rule.s_formElementId).rules('add', { [o_rule.s_rule] : o_rule.s_ruleParam01 });
			break;
			case 'range':
			case 'rangelength':
				if (o_rule.s_ruleAutoRequired == true) {
					$(o_rule.s_formElementId).rules('add', { required : true });
				}
				
				$(o_rule.s_formElementId).rules('add', { [o_rule.s_rule] : [o_rule.s_ruleParam01, o_rule.s_ruleParam02] });
			break;
			case 'remote':
				if (o_rule.s_ruleAutoRequired == true) {
					$(o_rule.s_formElementId).rules('add', { required : true });
				}
				
				$(o_rule.s_formElementId).rules('add', { [o_rule.s_rule] : {
					url: 'fphp_data_validator.php',
					type: 'post',
					dataType: 'json',
					data: {
						'fphp_validate_command': o_rule.s_ruleParam01,
						'fphp_validate_value': function() {
							return $(o_rule.s_formElementId).val();
						}
					}
				}});
			break;
		}
	}
}

$(function(){
	if ($('div.fphp_data_validator').length) {
		var i_cnt = 0;
		var o_JSON_data_validator = [];
		
		$('div.fphp_data_validator').each(function() {
			try {
				o_JSON_data_validator[i_cnt] = JSON.parse($(this).text());
			} catch (error) {
				if (error instanceof SyntaxError) {
					alert('There was a syntax error. Please correct it and try again: ' + error.message);
					return;
				} else {
					alert(error.message);
					return;
				}
			}
			
			$(this).replaceWith(null);
			
			fphp_apply_data_validator(o_JSON_data_validator[i_cnt]);
		});
	}
});
