$(function() {
	$.fn.fphp_richtext = function(p_o_options) {
		var a_escapeMap = {
			'&': '&amp;',
			'<': '&lt;',
			'>': '&gt;',
			'"': '&aquota;',
			"'": '&#039;'
		};
		
		fphp_updateRichTextToolbar = function(p_o_settings) {
			if (p_o_settings.s_highlightToolbarBtn) {
				$(p_o_settings.s_toolbarSelector).find('a[data-' + p_o_settings.s_dataCommand + ']').each(function () {
					var s_command = $(this).data(p_o_settings.s_dataCommand);
					
					if (document.queryCommandState(s_command)) {
						$(this).addClass(p_o_settings.s_highlightToolbarBtn);
					} else {
						$(this).removeClass(p_o_settings.s_highlightToolbarBtn);
					}
				});
			}
		};
		
		fphp_execRichTextCommand = function (p_o_settings, p_s_commands, p_s_value) {
			var a_commands = p_s_commands.split(' ');
			var s_command = a_commands.shift();
			var args = a_commands.join(' ') + (p_s_value || '');
			var b_commandCreateLink = false;
			var b_commandFrontColor = false;
			var b_commandBackgroundColor = false;
			var b_commandIncreaseFontSize = false;
			var b_commandDecreaseFontSize = false;
			var b_commandUndoRedo = false;
			
			if (a_commands.length > 1) {
				for (let i = 0; i < a_commands.length; i++) {
					if (a_commands[i] == 'createlink') {
						b_commandCreateLink = true;
					} else if (a_commands[i] == 'forecolor') {
						b_commandFrontColor = true;
					} else if (a_commands[i] == 'backcolor') {
						b_commandBackgroundColor = true;
					} else if (a_commands[i] == 'increasefontsize') {
						b_commandIncreaseFontSize = true;
					} else if (a_commands[i] == 'decreasefontsize') {
						b_commandDecreaseFontSize = true;
					} else if ( (a_commands[i] == 'undo') || (a_commands[i] == 'redo') ) {
						b_commandUndoRedo = true;
					}
				}
			} else {
				if (s_command == 'createlink') {
					b_commandCreateLink = true;
				} else if (s_command == 'forecolor') {
					b_commandFrontColor = true;
				} else if (s_command == 'backcolor') {
					b_commandBackgroundColor = true;
				} else if (s_command == 'increasefontsize') {
					b_commandIncreaseFontSize = true;
				} else if (s_command == 'decreasefontsize') {
					b_commandDecreaseFontSize = true;
				} else if ( (s_command == 'undo') || (s_command == 'redo') ) {
					b_commandUndoRedo = true;
				}
			}
			
			if ( (b_commandCreateLink) && (p_o_settings.b_createLink) ) {
				var s_url = prompt(p_o_settings.s_createLinkQuestion, p_o_settings.s_createLinkValue);
				
				if (s_url) {
					document.execCommand(s_command, 0, s_url);
				}
			} else if (b_commandFrontColor) {
				$(p_o_settings.s_toolbarSelector + ' input#' + p_o_settings.s_toolbarId + 'fontColor')[0].click();
			} else if (b_commandBackgroundColor) {
				$(p_o_settings.s_toolbarSelector + ' input#' + p_o_settings.s_toolbarId + 'backgroundColor')[0].click();
			} else if (b_commandIncreaseFontSize) {
				var o_fontSizeValue = document.queryCommandValue('fontsize');
				
				if (o_fontSizeValue) {
					if (parseInt(o_fontSizeValue) < 7) {
						document.execCommand('fontsize', 0, parseInt(o_fontSizeValue) + 1);
					}
				} else {
					document.execCommand('fontsize', 0, 3);
				}
			} else if (b_commandDecreaseFontSize) {
				var o_fontSizeValue = document.queryCommandValue('fontsize');
				
				if (o_fontSizeValue) {
					if (parseInt(o_fontSizeValue) > 1) {
						document.execCommand('fontsize', 0, parseInt(o_fontSizeValue) - 1);
					}
				} else {
					document.execCommand('fontsize', 0, 1);
				}
			} else if ( (b_commandUndoRedo) && (p_o_settings.b_undoAndredo) ) {
				document.execCommand(s_command, 0, args);
			} else {
				document.execCommand(s_command, 0, args);
			}
			
			fphp_updateRichTextToolbar(p_o_settings);
		};
		
		$(p_o_options.s_toolbarSelector).find('a[data-' + p_o_options.s_dataCommand + ']').click(function() {
			$('#' + p_o_options.s_id).focus();
			fphp_execRichTextCommand(p_o_options, $(this).data(p_o_options.s_dataCommand));
		});
		
		$(p_o_options.s_toolbarSelector).find('a[title]').tooltip( { container:'body' } );
				
		this.attr('contenteditable', !p_o_options.b_disabled)
			.on({
				'mouseup keyup': function() {
					fphp_updateRichTextToolbar(p_o_options);
					$('#' + p_o_options.s_hiddenId).val($(this).html().replace(/(<br>|\s|<div><br><\/div>|&nbsp;)*$/, '').replace(/[&<>"']/g, function(m) { return a_escapeMap[m]; }));
				},
				'focus': function() {
					$('#' + p_o_options.s_hiddenId).val($(this).html().replace(/(<br>|\s|<div><br><\/div>|&nbsp;)*$/, '').replace(/[&<>"']/g, function(m) { return a_escapeMap[m]; }));
				}
			});
		
		$(window).on('touchend', function(p_e_event) {
			//var b_isInside = (this.is(p_e_event.target) || this.has(p_e_event.target).length > 0);
			var b_isInside = ($(window).is(p_e_event.target) || $(window).has(p_e_event.target).length > 0);
			var o_currentRange = null;
			
			if (window.getSelection().getRangeAt && window.getSelection().rangeCount) {
				o_currentRange = window.getSelection().getRangeAt(0);
			}
				
			var b_clear = o_currentRange && (o_currentRange.startContainer === o_currentRange.endContainer && o_currentRange.startOffset === o_currentRange.endOffset);
			
			if (!b_clear || b_isInside) {
				fphp_updateRichTextToolbar(p_o_options);
				$('#' + p_o_options.s_hiddenId).val($(this).html().replace(/(<br>|\s|<div><br><\/div>|&nbsp;)*$/, '').replace(/[&<>"']/g, function(m) { return a_escapeMap[m]; }));
			}
		});
		
		this.on('paste', function(p_e_event) {
			if (p_e_event.originalEvent.clipboardData == false) {
				if (typeof(p_o_callback) == 'function') {
					p_o_callback(undefined);
				}
			};

			var a_items = p_e_event.originalEvent.clipboardData.items;

			if (a_items == undefined) {
				if (typeof(p_o_callback) == 'function') {
					p_o_callback(undefined);
				}
			};
			
			for (let i = 0; i < a_items.length; i++) {
				if (a_items[i].kind != 'string') {
					p_e_event.preventDefault();
					p_e_event.stopPropagation();
				}
			}
		});
		
		this.on('dragover dragenter', function(p_e_event) {
			p_e_event.preventDefault();
			p_e_event.stopPropagation();
		});
		
		this.on('dragleave', function(p_e_event) {
			p_e_event.preventDefault();  
			p_e_event.stopPropagation();
		});
		
		this.on('drop', function(p_e_event) {
			if (p_o_options.b_dropImage) {
				$('#' + p_o_options.s_id).focus();
				
				const a_fileArray = p_e_event.originalEvent.dataTransfer.files;
				
				for (let i = 0; i < a_fileArray.length; i++) {
					if (a_fileArray[i].name.lastIndexOf('.') > 3) {
						if (a_fileArray[i].type.indexOf('image') >= 0) {
							
							var o_loadIdentification = $.Deferred();
							var o_fileReader = new FileReader();
							
							o_fileReader.onload = function (p_e_fileReaderEvent) {
								o_loadIdentification.resolve(p_e_fileReaderEvent.target.result);
							};
							
							o_fileReader.onerror = o_loadIdentification.reject;
							o_fileReader.onprogress = o_loadIdentification.notify;
							o_fileReader.readAsDataURL(a_fileArray[i]);
							
							o_loadIdentification.promise().done(function (s_dataUrl) {
								var i_width = parseInt(p_o_options.i_imagesWidth);
								var i_height = parseInt(p_o_options.i_imagesHeight);
								
								if (p_o_options.b_askImageSize) {
									i_width = parseInt(prompt(p_o_options.s_imageWidthQuestion, p_o_options.i_imagesWidth));
									i_height = parseInt(prompt(p_o_options.s_imageHeightQuestion, p_o_options.i_imagesHeight));
								}
								
								var s_imageHTML = '<img src="' + s_dataUrl + '" width="' + i_width + '" height="' + i_height + '">';
								fphp_execRichTextCommand(p_o_options, 'insertHTML', s_imageHTML);
							});
						}
					}
				}
			}
			
			p_e_event.preventDefault();
			p_e_event.stopPropagation();
		});
		
		$(p_o_options.s_toolbarSelector + ' input#' + p_o_options.s_toolbarId + 'fontColor').on('change', function() {
			document.execCommand('foreColor', 0, $(p_o_options.s_toolbarSelector + ' input#' + p_o_options.s_toolbarId + 'fontColor').val());
		});
		
		$(p_o_options.s_toolbarSelector + ' input#' + p_o_options.s_toolbarId + 'backgroundColor').on('change', function() {
			document.execCommand('backColor', 0, $(p_o_options.s_toolbarSelector + ' input#' + p_o_options.s_toolbarId + 'backgroundColor').val());
		});
		
		if ( (p_o_options.s_value != undefined) && (p_o_options.s_value.length > 0) ) {
			$(this).html(p_o_options.s_value.replace(/&aquota;/g, '"').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&amp;/g, '&'));
			$('#' + p_o_options.s_hiddenId).val(p_o_options.s_value);
		}
		
		return this;
	};
	
	$.fn.fphp_richtextGetSource = function() {
		return $(this).html().replace(/(<br>|\s|<div><br><\/div>|&nbsp;)*$/, '');
	};
	
	if (!$('div#fphp_richtext').length) {
		return;
	}
	
	var i_cnt = 0;
	var o_JSON_richtext = [];
	
	$('div#fphp_richtext').each(function() {
		try {
			var str = $(this).text().replace(/(\r\n|\n|\r)/gm,"");
			o_JSON_richtext[i_cnt] = JSON.parse(str);
		} catch (error) {
			if (error instanceof SyntaxError) {
				alert('There was a syntax error. Please correct it and try again: ' + error.message);
				return;
			} else {
				alert(error.message);
				return;
			}
		}
		
		o_settings = o_JSON_richtext[i_cnt];
		
		var s_richtextHTML = `
			<div class="fphp_richtext">
				`;
				
				if (!o_settings.b_disabled) {
					s_richtextHTML += `<div class="btn-toolbar" data-toolbarId="` + o_settings.s_toolbarId + `">
						<div class="btn-group">
							<a class="btn btn-default" role="button" data-` + o_settings.s_dataCommand + `="bold" title="` + o_settings.s_bTitle + `">` + o_settings.s_bButton + `</a>
							<a class="btn btn-default" data-` + o_settings.s_dataCommand + `="italic" title="` + o_settings.s_iTitle + `">` + o_settings.s_iButton + `</a>
							<a class="btn btn-default" data-` + o_settings.s_dataCommand + `="underline" title="` + o_settings.s_uTitle + `">` + o_settings.s_uButton + `</a>
							<a class="btn btn-default" data-` + o_settings.s_dataCommand + `="strikethrough" title="` + o_settings.s_sTitle + `">` + o_settings.s_sButton + `</a>
						</div>
						
						<div class="btn-group">
							<a class="btn btn-default" data-` + o_settings.s_dataCommand + `="increasefontsize" title="` + o_settings.s_incFontTitle + `">` + o_settings.s_incFontButton + `</a>
							<a class="btn btn-default" data-` + o_settings.s_dataCommand + `="decreasefontsize" title="` + o_settings.s_decFontTitle + `">` + o_settings.s_decFontButton + `</a>
						</div>
						
						<div class="btn-group">
							<a class="btn btn-default" data-` + o_settings.s_dataCommand + `="forecolor" title="` + o_settings.s_foreColorTitle + `">` + o_settings.s_foreColorButton + `<input type="color" name="` + o_settings.s_toolbarId + `fontColor" id="` + o_settings.s_toolbarId + `fontColor" style="display: none;"></a>
							<a class="btn btn-default" data-` + o_settings.s_dataCommand + `="backcolor" title="` + o_settings.s_backColorTitle + `">` + o_settings.s_backColorButton + `<input type="color" name="` + o_settings.s_toolbarId + `backgroundColor" id="` + o_settings.s_toolbarId + `backgroundColor" style="display: none;"></a>
						</div>
						
						<div class="btn-group">
							<a class="btn btn-default" data-` + o_settings.s_dataCommand + `="insertunorderedlist" title="` + o_settings.s_ulTitle + `">` + o_settings.s_ulButton + `</a>
							<a class="btn btn-default" data-` + o_settings.s_dataCommand + `="insertorderedlist" title="` + o_settings.s_olTitle + `">` + o_settings.s_olButton + `</a>
							<a class="btn btn-default" data-` + o_settings.s_dataCommand + `="outdent" title="` + o_settings.s_outTitle + `">` + o_settings.s_outButton + `</a>
							<a class="btn btn-default" data-` + o_settings.s_dataCommand + `="indent" title="` + o_settings.s_inTitle + `">` + o_settings.s_inButton + `</a>
						</div>
						
						<div class="btn-group">
							<a class="btn btn-default" data-` + o_settings.s_dataCommand + `="justifyleft" title="` + o_settings.s_leftTitle + `">` + o_settings.s_leftButton + `</a>
							<a class="btn btn-default" data-` + o_settings.s_dataCommand + `="justifycenter" title="` + o_settings.s_centerTitle + `">` + o_settings.s_centerButton + `</a>
							<a class="btn btn-default" data-` + o_settings.s_dataCommand + `="justifyright" title="` + o_settings.s_rightTitle + `">` + o_settings.s_rightButton + `</a>
							<a class="btn btn-default" data-` + o_settings.s_dataCommand + `="justifyfull" title="` + o_settings.s_fullTitle + `">` + o_settings.s_fullButton + `</a>
						</div>
						`;
						
						if (o_settings.b_createLink) {
							s_richtextHTML += `
							<div class="btn-group">
								<a class="btn btn-default" data-` + o_settings.s_dataCommand + `="createlink" title="` + o_settings.s_linkTitle + `">` + o_settings.s_linkButton + `</a>
								<a class="btn btn-default" data-` + o_settings.s_dataCommand + `="unlink" title="` + o_settings.s_unlinkTitle + `">` + o_settings.s_unlinkButton + `</a>
							</div>`;
						}
						
						if (o_settings.b_undoAndredo) {
							s_richtextHTML += `
							<div class="btn-group">
								<a class="btn btn-default" data-` + o_settings.s_dataCommand + `="undo" title="` + o_settings.s_undoTitle + `">` + o_settings.s_undoButton + `</a>
								<a class="btn btn-default" data-` + o_settings.s_dataCommand + `="redo" title="` + o_settings.s_redoTitle + `">` + o_settings.s_redoButton + `</a>
							</div>`;
						}
						
						s_richtextHTML += `
							<div class="btn-group">
							<a class="btn btn-default" data-` + o_settings.s_dataCommand + `="removeformat" title="` + o_settings.s_removeTitle + `">` + o_settings.s_removeButton + `</a>
						</div>
					</div>
					
					<input type="hidden" id="` + o_settings.s_hiddenId + `" name="` + o_settings.s_hiddenId + `" value="">`;
				}
				
				s_richtextHTML += `<div id="` + o_settings.s_id + `"></div>
			</div>
		`;
		
		$(this).replaceWith(s_richtextHTML);
		
		$('#' + o_settings.s_id).fphp_richtext(o_JSON_richtext[i_cnt]);
		
		i_cnt++;
	});
});