/* +--------------------------------+ */
/* |                                | */
/* | forestPHP V0.1.1               | */
/* |                                | */
/* +--------------------------------+ */

/*
 * + Description +
 * standard js file of fphp framework
 *
 * + Version Log +
 * Version	Developer	Date		Comment
 * 0.1.0 alpha	renatus		2019-08-04	first build
 * 0.1.1 alpha	renatus		2019-08-14	added functionality for navigation and modal-call
 */

$(function(){
	$('.dropdown .fphp_menu_dropdown').on("click", function(p_o_event){
		if ($(this).find('span').hasClass('glyphicon-menu-down')) {
			$(this).find('span').removeClass('glyphicon-menu-down');
			$(this).find('span').addClass('glyphicon-menu-up');
		} else if ($(this).find('span').hasClass('glyphicon-menu-up')) {
			$(this).find('span').removeClass('glyphicon-menu-up');
			$(this).find('span').addClass('glyphicon-menu-down');
		}
	});
	
	$('.dropdown-submenu .fphp_menu_dropdown').on("click", function(p_o_event){
		$(this).next('ul').toggle();
		
		$(this).parent().siblings().each(function() {
			$(this).find('ul').css("display", "none");
			
			if ($(this).find('ul').parent().find('span').hasClass('glyphicon-menu-up')) {
				$(this).find('ul').parent().find('span').removeClass('glyphicon-menu-up');
				$(this).find('ul').parent().find('span').addClass('glyphicon-menu-down');
			}
		});
		
		p_o_event.stopPropagation();
		p_o_event.preventDefault();
	});
	
	$('.modal-call').each(function() {
		if ($(this).data('modal-call') !== undefined) {
			$(this).on('click', function() {
				$($(this).data('modal-call')).modal();
			});
		}
	});
	
	var b_isCtrl = false;
	var b_isShift = false;
	
	$(document).on('keyup', function(p_o_event) {
		if(p_o_event.which == 17) {
			b_isCtrl = false;
		}
		
		if(p_o_event.which == 16) {
			b_isShift = false;
		}
	});
	
	$(document).on('keydown', function(p_o_event) {
		if(p_o_event.which == 17) {
			b_isCtrl = true; 
		}
		
		if(p_o_event.which == 16) {
			b_isShift = true; 
		}
		
		if(p_o_event.which == 33 && b_isShift) {
			if ($('#a-button-limit-view-left').length > 0) {
				window.location.replace($('#a-button-limit-view-left').attr('href'));
			}
		}
		
		if(p_o_event.which == 34 && b_isShift) {
			if ($('#a-button-limit-view-right').length > 0) {
				window.location.replace($('#a-button-limit-view-right').attr('href'));
			}
		}
	});
});