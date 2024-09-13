/**
 * main javascript file for all client interaction with the fPHP-Framework
 *
 * @category    forestPHP Framework
 * @author      Rene Arentz <rene.arentz@forestany.net>
 * @copyright   (c) 2024 forestPHP Framework
 * @license     https://www.gnu.org/licenses/gpl-3.0.de.html GNU General Public License 3
 * @license     https://opensource.org/licenses/MIT MIT License
 * @version     1.1.0 stable
 * @link        https://forestany.net
 * @object-id   0x2 00001
 * @since       File available since Release 0.1.0 alpha
 * @deprecated  -
 *
 * @version log Version			Developer	Date		Comment
 * 				0.1.0 alpha		renea		2019-08-04	first build
 * 				0.1.1 alpha		renea		2019-08-14	added functionality for navigation and modal-call
 * 				0.1.2 alpha		renea		2019-08-25	added functionality for list view
 * 				0.1.5 alpha		renea		2019-10-07	added functionality for moveUp and moveDown
 * 				0.5.0 beta 		renea		2019-11-25	added functionality for checkin and checkout
 * 				0.6.0 beta 		renea		2019-12-10	added timeout submit button functionality
 * 				0.8.0 beta 		renea		2020-01-10	added functionality for fphp_flex
 * 				0.9.0 beta 		renea		2020-01-27	changes for bootstrap 4 and navigation sidebar + curtain
 * 				1.1.0 stable	renea		2024-02-10	nav pills for static page
 * 				1.1.0 stable	renea		2024-02-05	added slide calender view, activate current month if available or first slide
 * 				1.1.0 stable	renea		2024-02-10	changes for bootstrap 5
 * 				1.1.0 stable	renea		2024-02-11	string undo obfuscation method
 * 				1.1.0 stable	renea		2024-07-12	cookie consent modal and data protection content
 */
$(function(){
	/* ************************************** */
	/* *********** COOKIE CONSENT *********** */
	/* ************************************** */
	/* get data protection content for cookie consent static modal */
	var o_findDataProtectionContent = $('#DataProtectionModal').find('div#contentDataProtectionModal');
	
	if (o_findDataProtectionContent.length) {
		$('div#cookieConsentContentDataProtection').html(o_findDataProtectionContent.html());
		/* remove all links, so that we stay in static modal */
		$('div#cookieConsentContentDataProtection > a').each(function () {
			$(this).attr('href', '#').removeAttr('data-bs-toggle').removeAttr('data-bs-target');
		});
	}

	/* request to agree to cookie consent */
	$("button#cookieConsentYes").on('click', function(e) {
		/* send ajax request to consent to cookie data */
		$.get("./cookieconsent.php");
	});

	/* ********************************** */
	/* *********** NAVIGATION *********** */
	/* ********************************** */
	/* toggle caret icon in fphp navbar */
	$('.dropdown a.dropdown-menu-item').on('click', function(e) {
		/* toggle caret icon */
		if ($(this).find('span').hasClass('bi-caret-down')) {
			$(this).find('span').removeClass('bi-caret-down');
			$(this).find('span').addClass('bi-caret-up');
		} else if ($(this).find('span').hasClass('bi-caret-up')) {
			$(this).find('span').removeClass('bi-caret-up');
			$(this).find('span').addClass('bi-caret-down');
		}
		
		/* reset caret icon on dropdown on same level in fphp navbar */
		$(this).parent().siblings().each(function() {
			if ($(this).find('a.dropdown-menu-item').find('span').hasClass('bi-caret-up')) {
				$(this).find('a.dropdown-menu-item').find('span').removeClass('bi-caret-up');
				$(this).find('a.dropdown-menu-item').find('span').addClass('bi-caret-down');
			}
		});
	});
	
	/* toggle submenu in fphp navbar */
	$('.dropdown-menu a.dropdown-submenu-item').on('click', function(e) {
		/* close submenu of other dropdown if it is shown */
		if (!$(this).parent().next().hasClass('show')) {
			$(this).parents('.dropdown-menu').first().find('.show').removeClass('show');
		}
		
		/* query current submenu and toggle show state */
		var $subMenu = $(this).parent().next('.dropdown-menu');
		$subMenu.toggleClass('show');

		$(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
			$('.dropdown-submenu .show').removeClass('show');
		});
	
		/* toggle caret icon */
		if ($(this).find('span').hasClass('bi-caret-down')) {
			$(this).find('span').removeClass('bi-caret-down');
			$(this).find('span').addClass('bi-caret-up');
		} else if ($(this).find('span').hasClass('bi-caret-up')) {
			$(this).find('span').removeClass('bi-caret-up');
			$(this).find('span').addClass('bi-caret-down');
		}
		
		/* reset caret icon on dropdown on same level in fphp navbar */
		$(this).parent().parent().siblings().each(function() {
			if ($(this).find('a.dropdown-submenu-item').find('span').hasClass('bi-caret-up')) {
				$(this).find('a.dropdown-submenu-item').find('span').removeClass('bi-caret-up');
				$(this).find('a.dropdown-submenu-item').find('span').addClass('bi-caret-down');
			}
		});
		
		/* show submenu box far right in the browser menu, left of the box of our dropdown */
		if (($(window).width() - $(this).offset().left) < 350) {
			$subMenu.css('right', '100%').css('left', 'auto');
		} else {
			$subMenu.css('right', '').css('left', '100%');
		}

		return false;
	});

	/* ********************************** */
	/* ************** MODAL ************* */
	/* ********************************** */	
	/* help function to call modal, e.g. if you press a button */
	$('.modal-call').each(function() {
		if ($(this).data('modal-call') !== undefined) {
			$(this).on('click', function() {
				$($(this).data('modal-call')).modal('show');
			});
		}
	});
	
	/* standard timeout function for all standard submit buttons in forestPHP */
	setTimeout(function() {
        $("button[name^='sys_fphp_SubmitStandard']").attr('disabled', false);
    }, 1000);
	
	/* ********************************** */
	/* ******* forestPHP-ListView ******* */
	/* ********************************** */
	$('.select-modal-call-add-column').each(function() {
		/* gather columns we want to add to our list view */
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
	
	/* execute add columns request, by taking the gathered columns data nad create a valid url string as form action */
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
	
	/* execute edit request with selected records from list view */
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
	
	/* execute delete request with selected records from list view */
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
	
	/* execute view request with selected record from list view */
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
	
	/* execute moveUp request with selected record from list view */
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
	
	/* execute moveDown request with selected record from list view */
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
	
	/* execute checkout request with selected records from list view */
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
	
	/* execute checkin request with selected records from list view */
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
	
	/* exchange filter dropdown title with selected filter term of dropdown for further search action */
	$('.filter-panel .dropdown-menu').find('a').click(function(p_o_event) {
		p_o_event.preventDefault();
		
		if (!$(this).parent().hasClass("disabled")) {
			var s_newFilterColumn = $(this).attr('href').replace('#', '');
			var s_filterDropDownbutton = $(this).text();
			$('.filter-panel span#filterDropDownButton').text(s_filterDropDownbutton);
			$('.input-group #newFilterColumn').val(s_newFilterColumn);
		}
	});
	
	/* add filter term column to hidden element and execute submit to delete filter term from list view */
	$('.filter-terms').find('a').click(function(p_o_event) {
		p_o_event.preventDefault();
		var s_deleteFilterColumn = $(this).attr('href').replace('#', '');
		$('.input-group #deleteFilterColumn').val(s_deleteFilterColumn);
		$('.input-group #filterSubmit').click();
	});
	
	/* jquery ui selectable feature, to selecet multiple records in a list view */
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
		selected: function(p_o_event, p_o_ui) { /* add record uuid to uuid container for later use */
			if ($(p_o_ui.selected).hasClass('save-selected')) {
				$(p_o_ui.selected).removeClass('save-selected');
				$(p_o_ui.selected).removeClass('ui-selected');
				$(p_o_ui.selected).removeClass('table-secondary');
			} else {
				$(p_o_ui.selected).addClass('save-selected');
				$(p_o_ui.selected).addClass('ui-selected');
				$(p_o_ui.selected).addClass('table-secondary');
				
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
		unselected: function(p_o_event, p_o_ui) { /* remove record uuid from uuid container for later use */
			if ($(p_o_ui.unselected).hasClass('save-selected')) {
				$(p_o_ui.unselected).addClass('save-selected');
				$(p_o_ui.unselected).addClass('ui-selected');
				$(p_o_ui.unselected).addClass('table-secondary');
			} else {
				$(p_o_ui.unselected).removeClass('ui-selected');
				$(p_o_ui.unselected).removeClass('save-selected');
				$(p_o_ui.unselected).removeClass('table-secondary');
			}
			
			var s_data_container = $(p_o_ui.unselected).data('fphp_uuid');
			var a_data_container = s_data_container.split(';');
			var s_uniqueSelect = a_data_container[0];
			var s_uuid = a_data_container[1];
			
			var s_uuid_container = $('tbody#' + s_uniqueSelect + 'ListView').data('fphp_uuids');
			
			//console.log('remove: ' + s_uuid);
				
			if (s_uuid_container !== undefined) {
				$('tbody#' + s_uniqueSelect + 'ListView').data('fphp_uuids', $('tbody#' + s_uniqueSelect + 'ListView').data('fphp_uuids') + s_uuid + ';');
			} else {
				$('tbody#' + s_uniqueSelect + 'ListView').attr('data-fphp_uuids', s_uuid + ';');
			}		
			
			//console.log('unselected: ' + $('tbody#' + s_uniqueSelect + 'ListView').data('fphp_uuids'));
		},
		unselecting: function (p_o_event, p_o_ui) { /* handle selection classes */
			if ($(p_o_ui.unselecting).hasClass('save-selected')) {
				$(p_o_ui.unselecting).addClass('ui-selected');
				$(p_o_ui.unselecting).addClass('save-selected');
				$(p_o_ui.unselecting).addClass('table-secondary');
			} else {
				$(p_o_ui.unselecting).removeClass('ui-selected');
				$(p_o_ui.unselecting).removeClass('save-selected');
				$(p_o_ui.unselecting).removeClass('table-secondary');
			}
		},
		stop: function (p_o_event, p_o_ui) { /* update actions of list view based on record selection */
			var s_uniqueSelect = $(p_o_event.target).attr('id');
			var s_uuid_container = $('tbody#' + s_uniqueSelect).data('fphp_uuids');
			
			//console.log('stop: ' + s_uuid_container);
			
			s_uniqueSelect = s_uniqueSelect.replace('ListView', '');
			
			if (s_uuid_container != '') {
				/* if at least one record is selected, activate edit and delete action */
				$('a#' + s_uniqueSelect + 'Edit').removeClass('disabled');
				$('a#' + s_uniqueSelect + 'Delete').removeClass('disabled');
				
				var a_uuid_container = s_uuid_container.split(';');
				a_uuid_container.pop();
				
				if (a_uuid_container.length == 1) {
					/* following actions are activated if one record is selected */
					$('a#' + s_uniqueSelect + 'View').removeClass('disabled');
					$('a#' + s_uniqueSelect + 'MoveUp').removeClass('disabled');
					$('a#' + s_uniqueSelect + 'MoveDown').removeClass('disabled');
					$('a#' + s_uniqueSelect + 'Checkout').removeClass('disabled');
					$('a#' + s_uniqueSelect + 'Checkin').removeClass('disabled');
				} else {
					/* following actions are deactivated if more than one record are selected */
					$('a#' + s_uniqueSelect + 'View').addClass('disabled');
					$('a#' + s_uniqueSelect + 'MoveUp').addClass('disabled');
					$('a#' + s_uniqueSelect + 'MoveDown').addClass('disabled');
				}
			} else {
				/* following actions are deactivated by standard if no record is selected */
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
	
	/* ********************************** */
	/* ********* forestPHP-View ********* */
	/* ********************************** */
	var b_isCtrl = false;
	var b_isShift = false;
	
	/* handle keyboard paging in view mode */
	$(document).on('keyup', function(p_o_event) {
		if(p_o_event.which == 17) {
			b_isCtrl = false;
		}
		
		if(p_o_event.which == 16) {
			b_isShift = false;
		}
	});
	
	/* if you press SHIFT + PAGEDOWN you will increase page in view mode */
	/* if you press SHIFT + PAGEUP you will decrease page in view mode */
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
	
	
	/* ********************************** */
	/* ********* forestPHP-Flex ********* */
	/* ********************************** */
	var fphp_flexMinWidthInt = 30;
	var fphp_flexMinHeightInt = 30;
	var fphp_flexMinWidth = '30';
	var fphp_flexMinHeight = '30';
	
	/* handle draggable and resizable actions in fPHP-Flex Container */
	$("div[class$='_fphpFlex']")
		.draggable({
			start: function( event, ui ) { $(this).css('z-index', '2'); },
			containment: "parent",
			snap: true,
			grid: [ 10, 10 ],
			opacity: 0.35
		})
		
		.draggable({
			stop: function( event, ui ) {
				$(this).css('z-index', '1');
				
				if (parseInt($(this).css('top')) < 0) { $(this).css('top', '0'); }
				if (parseInt($(this).css('left')) < 0) { $(this).css('left', '0'); }
				
				//$("div[id='" + $(this).attr('class').split(' ')[0] + "-properties']").text($(this).attr('class').split(' ')[0].split('_')[0] + ' - top: ' + $(this).css('top') + '; left: ' + $(this).css('left') + '; width: ' + $(this).css('width') + '; height: ' + $(this).css('height') + ';');
				fphp_AjaxUpdateFlex($("div[id='fphpFlexContainer']").data('flexurl'), $(this).data('flexuuid'), parseInt($(this).css('top'), 10), parseInt($(this).css('left'), 10), parseInt($(this).css('width'), 10), parseInt($(this).css('height'), 10));
			}
		})

		.resizable({
			stop: function( event, ui ) {
				if (parseInt($(this).css('width')) < fphp_flexMinWidthInt) { $(this).css('width', fphp_flexMinWidth); }
				if (parseInt($(this).css('height')) < fphp_flexMinHeightInt) { $(this).css('height', fphp_flexMinHeight); }
				
				//$("div[id='" + $(this).attr('class').split(' ')[0] + "-properties']").text($(this).attr('class').split(' ')[0].split('_')[0] + ' - top: ' + $(this).css('top') + '; left: ' + $(this).css('left') + '; width: ' + $(this).css('width') + '; height: ' + $(this).css('height') + ';');
				fphp_AjaxUpdateFlex($("div[id='fphpFlexContainer']").data('flexurl'), $(this).data('flexuuid'), parseInt($(this).css('top'), 10), parseInt($(this).css('left'), 10), parseInt($(this).css('width'), 10), parseInt($(this).css('height'), 10));
			}
		});
	
	$("div[class='fphpFlexContainer']")
		.resizable({
			stop: function( event, ui ) {
				if (parseInt($(this).css('width')) < fphp_flexMinWidthInt) { $(this).css('width', fphp_flexMinWidth); }
				if (parseInt($(this).css('height')) < fphp_flexMinHeightInt) { $(this).css('height', fphp_flexMinHeight); }
				
				$("div[id='" + $(this).attr('class').split(' ')[0] + "General']").text('fphp-Flex-Container - width: ' + $(this).css('width') + '; height: ' + $(this).css('height') + ';');
				fphp_AjaxUpdateFlex($("div[id='fphpFlexContainer']").data('flexurl'), $(this).data('flexuuid'), 0, 0, parseInt($(this).css('width'), 10), parseInt($(this).css('height'), 10));
			}
		});
	
	/* save postion and size data into database for flex-elements with ajax request */
	function fphp_AjaxUpdateFlex(p_s_url, p_s_uuid, p_i_top, p_i_left, p_i_width, p_i_height) {
		var o_formData = new FormData();    
		o_formData.append('sys_fphp_flex_UUID', p_s_uuid);
		o_formData.append('sys_fphp_flex_Top', p_i_top);
		o_formData.append('sys_fphp_flex_Left', p_i_left);
		o_formData.append('sys_fphp_flex_Width', p_i_width);
		o_formData.append('sys_fphp_flex_Height', p_i_height);
		
		$.ajax({
			xhr: function() {
				var xhr = new window.XMLHttpRequest();
				return xhr;
			},
			url: p_s_url,
			method: 'POST',
			data: o_formData,
			contentType: false,
			processData: false, 
			dataType: 'text',
			error: function(xhr){
				alert('An error occured: ' + xhr.status + ' - ' + xhr.statusText);
			},
			success: function(result) {
				if (result.indexOf('ERR-1') !== -1) {
					alert('Error: [ERR-1] Communication error.');
				} else if (result.indexOf('ERR-2') !== -1) {
					alert('Error: [ERR-2] POST data incomplete. Missing primary key.');
				} else if (result.indexOf('ERR-3') !== -1) {
					alert('Error: [ERR-3] Cannot find record.');
				} else if (result.indexOf('ERR-4') !== -1) {
					alert('Error: [ERR-4] POST data incomplete. Missing values.');
				} else if (result.indexOf('ERR-5') !== -1) {
					alert('Error: [ERR-5] Cannot update record.');
				}
			}
		});
	}

	/* *************************************** */
	/* ******* forestPHP-SlideCalender ******* */
	/* *************************************** */
	var year_month = new Date().getFullYear() + '_' + ('0' + (new Date().getMonth()+1)).slice(-2);
	
	if($('div#carousel_'+ year_month).length){
		$('div#carousel_'+ year_month).addClass('active');
	}else{
		$(".carousel-item:first").addClass('active');
	}
});

/* ********************************** */
/* * NAVIGATION SIDEBAR+FULL SCREEN * */
/* ********************************** */
var fphp_navsidebarState = 0;

/* toggle fphp navigation sidebar */
function fphp_toggleNavsidebar() {
	if (fphp_navsidebarState == 0) {
		document.getElementById("fphp_navsidebarId").style.width = "300px";
		document.getElementById("fphp_navoverlayId").style.display = "block";
		fphp_navsidebarState = 1;
	} else {
		document.getElementById("fphp_navsidebarId").style.width = "0";
		document.getElementById("fphp_navoverlayId").style.display = "none";
		fphp_navsidebarState = 0;
	}
}

var fphp_navfullscreenState = 0;

/* toggle fphp navigation full screen */
function fphp_toggleNavfullscreen(i_mode) {
	if (i_mode == 1) {
		/* no slide */
		if (fphp_navfullscreenState == 0) {
			document.getElementById("fphp_navfullscreenId").style.display = "block";
			fphp_navfullscreenState = 1;
		} else {
			document.getElementById("fphp_navfullscreenId").style.display = "none";
			fphp_navfullscreenState = 0;
		}
	} else if (i_mode == 10) {
		/* slide from left */
		if (fphp_navfullscreenState == 0) {
			document.getElementById("fphp_navfullscreenId").style.width = "100%";
			fphp_navfullscreenState = 1;
		} else {
			document.getElementById("fphp_navfullscreenId").style.width = "0";
			fphp_navfullscreenState = 0;
		}
	} else if (i_mode == 100) {
		/* slide from top */
		if (fphp_navfullscreenState == 0) {
			document.getElementById("fphp_navfullscreenId").style.height = "100%";
			document.getElementById("fphp_navfullscreenId").style.marginTop = "55px";
			fphp_navfullscreenState = 1;
		} else {
			document.getElementById("fphp_navfullscreenId").style.height = "0";
			document.getElementById("fphp_navfullscreenId").style.marginTop = "0";
			fphp_navfullscreenState = 0;
		}
	}
}

/* ********************** */
/* * STRING OBFUSCATION * */
/* ********************** */
function fphp_undoObfuscatedString(s_string, s_domId) {
	var s_foo = '';
	var s_htmlEntityNumber = '';

	for (var i = 0; i < s_string.length; i++) {
		if ( (s_string[i] === '&') || (s_string[i] === '#')  ) {
			/* ignore '&' and '#', although we have the start of a new html entity number here */
			s_htmlEntityNumber = '';
		} else if (s_string[i] === ';') {
			/* convert html entity number to character and add it to return value */
			s_foo += String.fromCharCode(parseInt(s_htmlEntityNumber));
		} else {
			/* gather html entity number */
			s_htmlEntityNumber += s_string[i];
		}
	}

	/* overwrite href attribute of a element with id parameter */
	$('button#' + s_domId).click(function() {
		window.location = s_foo;
	});
}