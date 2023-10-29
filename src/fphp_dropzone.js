/**
 * javascript library for fphp_dropzone module
 *
 * @category    forestPHP Framework
 * @author      Rene Arentz <rene.arentz@forestphp.de>
 * @copyright   (c) 2019 forestPHP Framework
 * @license     https://www.gnu.org/licenses/gpl-3.0.de.html GNU General Public License 3
 * @license     https://opensource.org/licenses/MIT MIT License
 * @version     1.0.0 stable
 * @link        http://www.forestphp.de/
 * @object-id   0x2 00003
 * @since       File available since Release 0.1.4 alpha
 * @deprecated  -
 *
 * @version log Version     Developer   Date        Comment
 * 		          0.1.4 alpha	renatus		  2019-09-28	added to framework
 */
$(function() {
	
	/* ++++++++++++++++++++++++++++++ */
	/* ++++++++++Constants+++++++++++ */
	/* ++++++++++++++++++++++++++++++ */
	
	const alert00 = 'Object[%s] is no valid file.';
	const alert01 = 'File[%s] has been already uploaded.';
	const alert02 = 'An error occured: %s - %s';
	const alert03 = 'Error: [%s] Could not move tmp file.';
	const alert04 = 'Error: [%s] Max file upload size is 2 MB.';
	const alert05 = 'Error: [ERR-3] File has not been transferred correctly.';
	const alert06 = 'Error: [ERR-4] Please note files larger than 8 MB cannot be uploaded.';
	const alert07 = 'Error: [%s] Could not delete tmp file.';
	const alert08 = 'Error: [%s] Filename missing.';
	const alert09 = 'Error: [ERR-3] Communication error.';
	const alert10 = 'Error: Exception found in request.';
	
	var a_fileList = [];
	
	/* +++++++++++++++++++++++++++++ */
	/* JSON-Settings and Replacement */
	/* +++++++++++++++++++++++++++++ */

	if (!$('div#fphp_dropzone').length) {
		return;
	}
	
	var o_JSON_upload = null;

	try {
		o_JSON_upload = JSON.parse($('div#fphp_dropzone').text());
	} catch (error) {
		if (error instanceof SyntaxError) {
			alert('There was a syntax error. Please correct it and try again: ' + error.message);
			return;
		} else {
			alert(error.message);
			return;
		}
	}
	
	const s_dropzoneHTML = `
		<div id="` + o_JSON_upload['dropzoneContainerId'] + `">
			<input type="hidden" id="` + o_JSON_upload['dropzonePostDataId'] + `" name="` + o_JSON_upload['dropzonePostDataName'] + `" value="" />
			<input type="file" id="` + o_JSON_upload['dropzoneInputFileId'] + `" name="` + o_JSON_upload['dropzoneInputFileName'] + `" style="display:none" multiple />
			<a href="#" id="` + o_JSON_upload['dropzoneClickId'] + `">
				<div id="` + o_JSON_upload['dropzoneId'] + `">
						<span class="` + o_JSON_upload['dropzoneIconClass'] + `"></span><br />
						` + o_JSON_upload['dropzoneText'] + `
				</div>
			</a>
			
			<div id="` + o_JSON_upload['dropzoneListContainerId'] + `">
				<ul id="` + o_JSON_upload['dropzoneListId'] + `"></ul>
			</div>
		</div>	
	`;
	
	$('div#fphp_dropzone').replaceWith(s_dropzoneHTML);
	
	/* ++++++++++++++++++++++++++++++ */
	/* +++++++++++Drop-Event+++++++++ */
	/* ++++++++++++++++++++++++++++++ */
	
	$(o_JSON_upload['s_dropzoneId']).on('dragover dragenter', function(p_e_event) {
		p_e_event.preventDefault();
		p_e_event.stopPropagation();
	});
	
	$(o_JSON_upload['s_dropzoneId']).on('dragleave', function(p_e_event) {
		p_e_event.preventDefault();  
		p_e_event.stopPropagation();
	});
	
	$(o_JSON_upload['s_dropzoneId']).on('drop', function(p_e_event) {
		p_e_event.preventDefault();
		p_e_event.stopPropagation();
		
		const a_fileArray = p_e_event.originalEvent.dataTransfer.files;
		
		for (let i = 0; i < a_fileArray.length; i++) {
			if (a_fileArray[i].name.lastIndexOf('.') > 3) {
				var s_randomId = fphp_MakeRandomId(o_JSON_upload['i_randomIdLength']);
				
				fphp_AddFileToList(
					s_randomId,
					a_fileArray[i].name,
					a_fileArray[i].size
				);
				
				fphp_AjaxFileUpload(
					a_fileArray[i],
					a_fileArray[i].type,
					a_fileArray[i].name,
					a_fileArray[i].size,
					s_randomId
				);
			} else {
				alert(sprintf(alert00, a_fileArray[i].name));
			}
		}	
	});
	
	/* ++++++++++++++++++++++++++++++ */
	/* ++++++++++Click-Event+++++++++ */
	/* ++++++++++++++++++++++++++++++ */

	
	$(o_JSON_upload['s_dropzoneAId']).on('click', function(p_e_event) {
		if ($(o_JSON_upload['s_dropzoneInputFileId']).length) {
			$(o_JSON_upload['s_dropzoneInputFileId']).click();
		}
		
		p_e_event.preventDefault();
	});
	
	$(o_JSON_upload['s_dropzoneInputFileId']).on('change', function() {
		if ($(o_JSON_upload['s_dropzoneInputFileId']).prop('files').length > 0) {
			for (let i = 0; i < $(o_JSON_upload['s_dropzoneInputFileId']).prop('files').length; i++) {
				var s_randomId = fphp_MakeRandomId(o_JSON_upload['i_randomIdLength']);
				
				fphp_AddFileToList(
					s_randomId,
					$(o_JSON_upload['s_dropzoneInputFileId']).prop('files')[i].name,
					$(o_JSON_upload['s_dropzoneInputFileId']).prop('files')[i].size
				);
				
				fphp_AjaxFileUpload(
					$(o_JSON_upload['s_dropzoneInputFileId']).prop('files')[i],
					$(o_JSON_upload['s_dropzoneInputFileId']).prop('files')[i].type,
					$(o_JSON_upload['s_dropzoneInputFileId']).prop('files')[i].name,
					$(o_JSON_upload['s_dropzoneInputFileId']).prop('files')[i].size,
					s_randomId
				);
			}
		}
	});

	/* ++++++++++++++++++++++++++++++ */
	/* ++++++++++Paste-Event+++++++++ */
	/* ++++++++++++++++++++++++++++++ */

	$('html').on('paste', function(p_e_pasteEvent) {
		fphp_RetrieveFileFromClipboard(p_e_pasteEvent.originalEvent, function(o_file, s_fileType, s_fileName, i_fileSize){
			if (o_file) {
				if (s_fileName.lastIndexOf('.') > 3) {
					var s_randomId = fphp_MakeRandomId(o_JSON_upload['i_randomIdLength']);
					var s_newFileName = s_fileName;
					
					if (s_fileType.indexOf('image') >= 0) {
						s_newFileName = prompt(o_JSON_upload['s_promptTitle'], o_JSON_upload['s_promptFileName'] + (a_fileList.length + 1) + '.' + fphp_GetFileExtension(s_fileName));
					}
					
					if (s_newFileName.lastIndexOf('.') < 0) {
						s_newFileName += '.' + fphp_GetFileExtension(s_fileName);
					}
					
					if (s_newFileName) {
						fphp_AddFileToList(
							s_randomId,
							s_newFileName,
							i_fileSize
						);
						
						fphp_AjaxFileUpload(
							o_file,
							s_fileType,
							s_newFileName,
							i_fileSize,
							s_randomId
						);
					}
				} else {
					alert(sprintf(alert00, s_fileName));
				}
			}
		});
		
		/*fphp_RetrieveImageFromClipboardAsBase64(p_e_event.originalEvent, function(s_fileBase64, s_fileType, s_fileName, i_fileSize){
			if (s_fileBase64) {
				
			}
		});*/
	});
	
	function fphp_RetrieveFileFromClipboard(p_e_pasteEvent, p_o_callback){
		if (p_e_pasteEvent.clipboardData == false) {
			if (typeof(p_o_callback) == 'function') {
				p_o_callback(undefined);
			}
		};

		var a_items = p_e_pasteEvent.clipboardData.items;

		if (a_items == undefined) {
			if (typeof(p_o_callback) == 'function') {
				p_o_callback(undefined);
			}
		};
		
		for (let i = 0; i < a_items.length; i++) {
			if (a_items[i].kind == 'file') {
				var o_blob = a_items[i].getAsFile();
				
				if (typeof(p_o_callback) == 'function') {
					p_o_callback(o_blob, o_blob.type, o_blob.name, o_blob.size);
				}
			} else if (a_items[i].kind == 'string') {
				// nothing to do, we want files only
			} else {
				// nothing to do, we want files only
			}
		}
	}
	
	function fphp_RetrieveImageFromClipboardAsBase64(p_e_pasteEvent, p_o_callback){
		if (p_e_pasteEvent.clipboardData == false) {
			if (typeof(p_o_callback) == 'function') {
				p_o_callback(undefined);
			}
		};
		
		var a_items = p_e_pasteEvent.clipboardData.items;
		
		if (a_items == undefined) {
			if (typeof(p_o_callback) == 'function') {
				p_o_callback(undefined);
			}
		};

		for (let i = 0; i < a_items.length; i++) {
			if (a_items[i].kind == 'file') {
				var o_blob = a_items[i].getAsFile();
				
				if (o_blob.type.indexOf('image') >= 0) {
					if (typeof(p_o_callback) == 'function') {
						// Retrieve image on clipboard as base64 string
						p_o_callback(fphp_ImageBlobToBase64String(o_blob, o_blob.type), o_blob.type, o_blob.name, o_blob.size);
					}
				}
			} else if (a_items[i].kind == 'string') {
				// nothing to do, we want files only
			} else {
				// nothing to do, we want files only
			}
		}
	}

	function fphp_ImageBlobToBase64String(p_o_blob, p_s_imageFormat) {
		// Create an abstract canvas and get context
		var o_fphpCanvas = document.createElement('canvas');
		var o_fphpCanvasCtx = o_fphpCanvas.getContext('2d');
		
		// Create an image
		var o_fphpImage = new Image();

		// Once the image loads, render the img on the canvas
		o_fphpImage.onload = function(){
			// Update dimensions of the canvas with the dimensions of the image
			o_fphpCanvas.width = this.width;
			o_fphpCanvas.height = this.height;

			// Draw the image
			o_fphpCanvasCtx.drawImage(o_fphpImage, 0, 0);
		};

		// Crossbrowser support for URL
		var s_urlObj = window.URL || window.webkitURL;

		// Creates a DOMString containing a URL representing the object given in the parameter
		// namely the original Blob
		o_fphpImage.src = s_urlObj.createObjectURL(p_o_blob);
		
		// Execute callback with the base64 URI of the image
		return o_fphpCanvas.toDataURL(p_s_imageFormat || 'image/png');
	}
	
	/* ++++++++++++++++++++++++++++++ */
	/* +++++File-Ajax-Functions++++++ */
	/* ++++++++++++++++++++++++++++++ */
	
	function fphp_AjaxFileUpload(p_o_file, p_s_fileType, p_s_fileName, p_i_fileSize, p_s_randomId) {
		let j = 0;
		
		for (let i = 0; i < a_fileList.length; i++) { 
			if (a_fileList[i].fileName == p_s_fileName) {
				j++;
			}
		}
		
		if (j > 1) {
			fphp_RemoveFileFromList(p_s_fileName, p_s_randomId);
			alert(sprintf(alert01, p_s_fileName));
			return;
		}
		
		var o_formData = new FormData();    
		o_formData.append(o_JSON_upload['s_uploadPostFieldNameFile'], p_o_file);
		o_formData.append(o_JSON_upload['s_uploadPostFieldNameFileName'], p_s_randomId + '_' + p_s_fileName);
		
		$.ajax({
			xhr: function() {
				var xhr = new window.XMLHttpRequest();

				xhr.upload.addEventListener('progress', function(p_e_event) {
					if (p_e_event.lengthComputable) {
						var i_percentComplete = p_e_event.loaded / p_e_event.total;
						i_percentComplete = parseInt(i_percentComplete * 100);
						
						$(o_JSON_upload['s_uploadStatusId'] + p_s_randomId).text(i_percentComplete + '%');
						
						if (i_percentComplete === 100) {
							$(o_JSON_upload['s_uploadStatusId'] + p_s_randomId).text('100%');
							$(o_JSON_upload['s_uploadStatusId'] + p_s_randomId).removeClass('badge-secondary').addClass('badge-success');
						}
					}
				}, false);
				
				return xhr;
			},
			url: o_JSON_upload['s_uriFileUploader'],
			method: 'POST',
			data: o_formData,
			contentType: false,
			processData: false, 
			dataType: 'text',
			beforeSend: function(){
				$(o_JSON_upload['s_uploadStatusId'] + p_s_randomId).text('0%');
			},
			error: function(xhr){
				alert(sprintf(alert02, xhr.status, xhr.statusText));
				fphp_RemoveFileFromList(p_s_fileName, p_s_randomId);
			},
			success: function(result) {
				if (result.indexOf('ERR-1') !== -1) {
					alert(sprintf(alert03, result));
					fphp_RemoveFileFromList(p_s_fileName, p_s_randomId);
				} else if (result.indexOf('ERR-2') !== -1) {
					alert(sprintf(alert04, result));
					fphp_RemoveFileFromList(p_s_fileName, p_s_randomId);
				} else if (result.indexOf('ERR-3') !== -1) {
					alert(sprintf(alert05));
					fphp_RemoveFileFromList(p_s_fileName, p_s_randomId);
				} else if (result.indexOf('ERR-4') !== -1) {
					alert(sprintf(alert06));
					fphp_RemoveFileFromList(p_s_fileName, p_s_randomId);
				} else if (result.indexOf('INF-1') !== -1) {
					$(o_JSON_upload['s_uploadStatusId'] + p_s_randomId).text('100%');
					$(o_JSON_upload['s_uploadStatusId'] + p_s_randomId).removeClass('badge-secondary').addClass('badge-success');
				} else {
					console.log(result);
					alert(sprintf(alert10));
					fphp_RemoveFileFromList(p_s_fileName, p_s_randomId);
				}
			}
		});
	}
	
	function fphp_AjaxFileDelete(p_s_fileName, p_s_randomId) {
		$.ajax({
			url: o_JSON_upload['s_uriFileDeleter'],
			method: 'POST',
			data: {
			  [o_JSON_upload['s_deletePostFieldName']]: p_s_randomId + '_' + p_s_fileName
			},
			dataType: 'text',
			error: function(xhr){
				alert(sprintf(alert02, xhr.status, xhr.statusText));
			},
			success: function(result) {
				if (result.indexOf('ERR-1') !== -1) {
					alert(sprintf(alert07, result));
				} else if (result.indexOf('ERR-2') !== -1) {
					alert(sprintf(alert08, result));
				} else if (result.indexOf('ERR-3') !== -1) {
					alert(sprintf(alert09));
				} else if (result.indexOf('INF-1') !== -1) {
					fphp_RemoveFileFromList(p_s_fileName, p_s_randomId);
				}
			}
		});
	}
	
	/* ++++++++++++++++++++++++++++++ */
	/* +++++++++Help-Funtions++++++++ */
	/* ++++++++++++++++++++++++++++++ */
	
	function fphp_MakeRandomId(p_i_length) {
		var s_text = '';
		var s_possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

		for (var i = 0; i < p_i_length; i++)
		s_text += s_possible.charAt(Math.floor(Math.random() * s_possible.length));

		return s_text;
	}
	
	function fphp_GetFileExtension(p_s_fileName) {
		return p_s_fileName.slice((p_s_fileName.lastIndexOf('.') - 1 >>> 0) + 2);
	}
	
	function sprintf(p_s_format) {
		for(let i= 1; i < arguments.length; i++) {
			p_s_format = p_s_format.replace(/%s/, arguments[i]);
		}
		
		return p_s_format;
	}
	
	function fphp_AddFileToList(p_s_randomId, p_s_fileName, p_s_fileSize) {
		a_fileList.push({
			'randomId' : p_s_randomId, 
			'fileName' : p_s_fileName 
		});
		
		var s_upload_status = '<span id="' + o_JSON_upload['s_uploadStatusIdValue'] + p_s_randomId + '" class="badge badge-secondary">0%</span>';
		var s_upload_delete = '<span id="' + o_JSON_upload['s_uploadDeleteIdValue'] + p_s_randomId + '" class="fas fa-times text-danger" style="cursor: pointer;"></span>';
		
		$(o_JSON_upload['s_dropzoneListId']).append('<li>' + s_upload_status + ' ' + p_s_fileName + ' - (' + (p_s_fileSize / 1024 / 1024).toFixed(2) + ' MB) ' + s_upload_delete + '</li>');
		
		$(o_JSON_upload['s_uploadDeleteId'] + p_s_randomId).on('click', function () {
			fphp_AjaxFileDelete(p_s_fileName, p_s_randomId);
		});
	}
	
	function fphp_RemoveFileFromList(p_s_fileName, p_s_randomId) {
		for (let i = 0; i < a_fileList.length; i++) { 
			if (a_fileList[i].randomId == p_s_randomId) {
				a_fileList.splice(i, 1);
				break;
			}
		}
		
		$(o_JSON_upload['s_uploadDeleteId'] + p_s_randomId).parent().remove();
	}
	
	$(o_JSON_upload['s_dropzoneFormId']).submit(function(p_e_event) {
		var b_valid = true;
		
		if ($(o_JSON_upload['s_dropzoneFormId'])[0].noValidate) {
			$(o_JSON_upload['s_dropzoneFormId']).validate();
			b_valid = $(o_JSON_upload['s_dropzoneFormId']).valid();
		}
		
		p_e_event.preventDefault();

		if (b_valid) {
			for (let i = 0; i < a_fileList.length; i++) {
				if (i > 0) {
					$(o_JSON_upload['s_dropzonePostDataId']).val($(o_JSON_upload['s_dropzonePostDataId']).val() + '/' + a_fileList[i].randomId + '_' + a_fileList[i].fileName)
				} else {
					$(o_JSON_upload['s_dropzonePostDataId']).val(a_fileList[i].randomId + '_' + a_fileList[i].fileName);
				}
			}
			
			$(this).unbind('submit').submit();
		}
	})
});