/* +--------------------------------+ */
/* |                                | */
/* | forestPHP V0.6.0               | */
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
 * 0.1.2 alpha	renatus		2019-08-25	added functionality for list view
 * 0.1.5 alpha	renatus		2019-10-07	added functionality for moveUp and moveDown
 * 0.5.0 beta 	renatus		2019-11-25	added functionality for checkin and checkout
 * 0.6.0 beta 	renatus		2019-12-10	added timeout submit button functionality
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
	
	$('.select-modal-call-add-column').each(function() {
		if ($(this).data('columns') !== undefined) {
			$(this).on('change', function() {
				var s_columns = '';
				
				$('option:selected', this).each(function() {
					s_columns += $(this).val() + ';';
				});
				
				$(this).data('columns', s_columns);
				
				//console.log($(this).data('columns'));
			});
		}
	});
	
	$('.button-modal-call-add-column').each(function() {
		$(this).on('click', function() {
			//console.log($('form#' + $(this).data('form_id')).find('.select-modal-call-add-column').data('columns'));
			if ($('form#' + $(this).data('form_id')).find('.select-modal-call-add-column').data('columns') != '') {
				var s_columns = $('form#' + $(this).data('form_id')).find('.select-modal-call-add-column').data('columns');
				
				if (s_columns != '') {
					var s_hiddenColumns = '';
					var s_hiddenColumnsVal = '';
					var a_columns = s_columns.split(';');
					a_columns.pop();
					
					for (let i = 0; i < a_columns.length; i++) {
						s_hiddenColumns += '-' + a_columns[i];
						
						if (i < (a_columns.length - 1)) {
							s_hiddenColumns += '&'
						}
						
						s_hiddenColumnsVal += '-add';
						
						if (i < (a_columns.length - 1)) {
							s_hiddenColumnsVal += '&'
						}
					}
					
					//console.log(s_hiddenColumns);
					//console.log(s_hiddenColumnsVal);
					
					var o_form = $('form#' + $(this).data('form_id'));
					o_form.attr('action', o_form.attr('action').replace('-hiddencolumnsval', s_hiddenColumnsVal).replace('-hiddencolumns', s_hiddenColumns));
					
					//console.log(o_form.attr('action'));
					
					window.location.replace(o_form.attr('action'));
				}
			}
		});
	});
	
	$('.a-button-edit-record').each(function() {
		$(this).on('click', function(p_o_event) {
			var s_href= $(this).attr('href');
			var s_uniqueSelect = $(this).attr('id').replace('Edit', '');
			
			if ($('tbody#' + s_uniqueSelect + 'ListView').data('fphp_uuids') != '') {
				var s_uuids = $('tbody#' + s_uniqueSelect + 'ListView').data('fphp_uuids');
				var s_key = '';
				var a_uuids = s_uuids.split(';');
				a_uuids.pop();
				s_key = a_uuids.join('~');
				s_href = s_href.replace('inserteditkey', s_key);
			}
			
			$(this).attr('href', s_href);
		});
	});
	
	$('.a-button-delete-record').each(function() {
		$(this).on('click', function(p_o_event) {
			var s_href= $(this).attr('href');
			var s_uniqueSelect = $(this).attr('id').replace('Delete', '');
			
			if ($('tbody#' + s_uniqueSelect + 'ListView').data('fphp_uuids') != '') {
				var s_uuids = $('tbody#' + s_uniqueSelect + 'ListView').data('fphp_uuids');
				var s_key = '';
				var a_uuids = s_uuids.split(';');
				a_uuids.pop();
				s_key = a_uuids.join('~');
				s_href = s_href.replace('insertdeletekey', s_key);
			}
			
			$(this).attr('href', s_href);
		});
	});
	
	$('.a-button-view-record').each(function() {
		$(this).on('click', function(p_o_event) {
			var s_href= $(this).attr('href');
			var s_uniqueSelect = $(this).attr('id').replace('View', '');
			
			if ($('tbody#' + s_uniqueSelect + 'ListView').data('fphp_uuids') != '') {
				var s_uuids = $('tbody#' + s_uniqueSelect + 'ListView').data('fphp_uuids');
				var s_key = '';
				var a_uuids = s_uuids.split(';');
				a_uuids.pop();
				s_key = a_uuids.join('~');
				s_href = s_href.replace('insertviewkey', s_key);
			}
			
			$(this).attr('href', s_href);
		});
	});
	
	$('.a-button-moveUp-record').each(function() {
		$(this).on('click', function(p_o_event) {
			var s_href= $(this).attr('href');
			var s_uniqueSelect = $(this).attr('id').replace('MoveUp', '');
			
			if ($('tbody#' + s_uniqueSelect + 'ListView').data('fphp_uuids') != '') {
				var s_uuids = $('tbody#' + s_uniqueSelect + 'ListView').data('fphp_uuids');
				var s_key = '';
				var a_uuids = s_uuids.split(';');
				a_uuids.pop();
				s_key = a_uuids.join('~');
				s_href = s_href.replace('inserteditkey', s_key);
			}
			
			$(this).attr('href', s_href);
		});
	});
	
	$('.a-button-moveDown-record').each(function() {
		$(this).on('click', function(p_o_event) {
			var s_href= $(this).attr('href');
			var s_uniqueSelect = $(this).attr('id').replace('MoveDown', '');
			
			if ($('tbody#' + s_uniqueSelect + 'ListView').data('fphp_uuids') != '') {
				var s_uuids = $('tbody#' + s_uniqueSelect + 'ListView').data('fphp_uuids');
				var s_key = '';
				var a_uuids = s_uuids.split(';');
				a_uuids.pop();
				s_key = a_uuids.join('~');
				s_href = s_href.replace('inserteditkey', s_key);
			}
			
			$(this).attr('href', s_href);
		});
	});
	
	$('.a-button-checkout-record').each(function() {
		$(this).on('click', function(p_o_event) {
			var s_href= $(this).attr('href');
			var s_uniqueSelect = $(this).attr('id').replace('Checkout', '');
			
			if ($('tbody#' + s_uniqueSelect + 'ListView').data('fphp_uuids') != '') {
				var s_uuids = $('tbody#' + s_uniqueSelect + 'ListView').data('fphp_uuids');
				var s_key = '';
				var a_uuids = s_uuids.split(';');
				a_uuids.pop();
				s_key = a_uuids.join('~');
				s_href = s_href.replace('insertcheckoutkey', s_key);
			}
			
			$(this).attr('href', s_href);
		});
	});
	
	$('.a-button-checkin-record').each(function() {
		$(this).on('click', function(p_o_event) {
			var s_href= $(this).attr('href');
			var s_uniqueSelect = $(this).attr('id').replace('Checkin', '');
			
			if ($('tbody#' + s_uniqueSelect + 'ListView').data('fphp_uuids') != '') {
				var s_uuids = $('tbody#' + s_uniqueSelect + 'ListView').data('fphp_uuids');
				var s_key = '';
				var a_uuids = s_uuids.split(';');
				a_uuids.pop();
				s_key = a_uuids.join('~');
				s_href = s_href.replace('insertcheckinkey', s_key);
			}
			
			$(this).attr('href', s_href);
		});
	});
	
	$('.filter-panel .dropdown-menu').find('a').click(function(p_o_event) {
		p_o_event.preventDefault();
		
		if (!$(this).parent().hasClass("disabled")) {
			var s_newFilterColumn = $(this).attr('href').replace('#', '');
			var s_filterDropDownbutton = $(this).text();
			$('.filter-panel span#filterDropDownButton').text(s_filterDropDownbutton);
			$('.input-group #newFilterColumn').val(s_newFilterColumn);
		}
	});
	
	$('.filter-terms').find('a').click(function(p_o_event) {
		p_o_event.preventDefault();
		var s_deleteFilterColumn = $(this).attr('href').replace('#', '');
		$('.input-group #deleteFilterColumn').val(s_deleteFilterColumn);
		$('.input-group #filterSubmit').click();
	});
	
	$('.table-selectable > tbody').selectable({
		filter: 'tr',
		cancel: 'a,span',
		start: function(p_o_event, p_o_ui) {
			var s_uniqueSelect = $(p_o_event.target).attr('id');
			var s_uuid_container = $('tbody#' + s_uniqueSelect).data('fphp_uuids');
			
			//console.log('starting - delete fphp_uuids');
			
			if (s_uuid_container !== undefined) {
				//console.log('fphp_uuids before: ' + $('tbody#' + s_uniqueSelect).data('fphp_uuids'));
				$('tbody#' + s_uniqueSelect).data('fphp_uuids', '');
			}	
		},
		selected: function(p_o_event, p_o_ui) {
			if ($(p_o_ui.selected).hasClass('save-selected')) {
				$(p_o_ui.selected).removeClass('save-selected');
				$(p_o_ui.selected).removeClass('ui-selected');
			} else {
				$(p_o_ui.selected).addClass('save-selected');
				$(p_o_ui.selected).addClass('ui-selected');
				
				var s_data_container = $(p_o_ui.selected).data('fphp_uuid');
				var a_data_container = s_data_container.split(';');
				var s_uniqueSelect = a_data_container[0];
				var s_uuid = a_data_container[1];
				
				var s_uuid_container = $('tbody#' + s_uniqueSelect + 'ListView').data('fphp_uuids');
				
				//console.log('add: ' + s_uuid);
				
				if (s_uuid_container !== undefined) {
					$('tbody#' + s_uniqueSelect + 'ListView').data('fphp_uuids', $('tbody#' + s_uniqueSelect + 'ListView').data('fphp_uuids') + s_uuid + ';');
				} else {
					$('tbody#' + s_uniqueSelect + 'ListView').attr('data-fphp_uuids', s_uuid + ';');
				}		
				
				//console.log('selected: ' + $('tbody#' + s_uniqueSelect + 'ListView').data('fphp_uuids'));
			}
		},
		unselected: function(p_o_event, p_o_ui) {
			if ($(p_o_ui.unselected).hasClass('save-selected')) {
				$(p_o_ui.unselected).addClass('save-selected');
				$(p_o_ui.unselected).addClass('ui-selected');
			} else {
				$(p_o_ui.unselected).removeClass('ui-selected');
				$(p_o_ui.unselected).removeClass('save-selected');
			}
			
			var s_data_container = $(p_o_ui.unselected).data('fphp_uuid');
			var a_data_container = s_data_container.split(';');
			var s_uniqueSelect = a_data_container[0];
			var s_uuid = a_data_container[1];
			
			var s_uuid_container = $('tbody#' + s_uniqueSelect + 'ListView').data('fphp_uuids');
			
			//console.log('add: ' + s_uuid);
				
			if (s_uuid_container !== undefined) {
				$('tbody#' + s_uniqueSelect + 'ListView').data('fphp_uuids', $('tbody#' + s_uniqueSelect + 'ListView').data('fphp_uuids') + s_uuid + ';');
			} else {
				$('tbody#' + s_uniqueSelect + 'ListView').attr('data-fphp_uuids', s_uuid + ';');
			}		
			
			//console.log('unselected: ' + $('tbody#' + s_uniqueSelect + 'ListView').data('fphp_uuids'));
		},
		unselecting: function (p_o_event, p_o_ui) {
			if ($(p_o_ui.unselecting).hasClass('save-selected')) {
				$(p_o_ui.unselecting).addClass('ui-selected');
				$(p_o_ui.unselecting).addClass('save-selected');
			} else {
				$(p_o_ui.unselecting).removeClass('ui-selected');
				$(p_o_ui.unselecting).removeClass('save-selected');
			}
		},
		stop: function (p_o_event, p_o_ui) {
			var s_uniqueSelect = $(p_o_event.target).attr('id');
			var s_uuid_container = $('tbody#' + s_uniqueSelect).data('fphp_uuids');
			
			//console.log('stop: ' + s_uuid_container);
			
			s_uniqueSelect = s_uniqueSelect.replace('ListView', '');
			
			if (s_uuid_container != '') {
				$('a#' + s_uniqueSelect + 'Edit').removeClass('disabled');
				$('a#' + s_uniqueSelect + 'Delete').removeClass('disabled');
				
				var a_uuid_container = s_uuid_container.split(';');
				a_uuid_container.pop();
				
				if (a_uuid_container.length == 1) {
					$('a#' + s_uniqueSelect + 'View').removeClass('disabled');
					$('a#' + s_uniqueSelect + 'MoveUp').removeClass('disabled');
					$('a#' + s_uniqueSelect + 'MoveDown').removeClass('disabled');
					$('a#' + s_uniqueSelect + 'Checkout').removeClass('disabled');
					$('a#' + s_uniqueSelect + 'Checkin').removeClass('disabled');
				} else {
					$('a#' + s_uniqueSelect + 'View').addClass('disabled');
					$('a#' + s_uniqueSelect + 'MoveUp').addClass('disabled');
					$('a#' + s_uniqueSelect + 'MoveDown').addClass('disabled');
				}
			} else {
				$('a#' + s_uniqueSelect + 'Edit').addClass('disabled');
				$('a#' + s_uniqueSelect + 'Delete').addClass('disabled');
				$('a#' + s_uniqueSelect + 'View').addClass('disabled');
				$('a#' + s_uniqueSelect + 'MoveUp').addClass('disabled');
				$('a#' + s_uniqueSelect + 'MoveDown').addClass('disabled');
				$('a#' + s_uniqueSelect + 'Checkout').addClass('disabled');
				$('a#' + s_uniqueSelect + 'Checkin').addClass('disabled');
			}
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
	
	setTimeout(function() {
        $("button[name^='sys_fphp_SubmitStandard']").attr('disabled', false);
    }, 3000);
});