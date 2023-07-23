/**
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*/

$(function() {
	var attachment_templ = $("#attachment_templ").clone().removeAttr("id").show();
    $("#attachment_templ").remove();
    var store_attachment = $('#store_attachment');

	$(document).on('click', '#add_attachment', function(e){
		$(this).parent().find('input[type=file]').trigger('click');
		return false;
	});

	$(document).on('change', '[name=add_attachment]', function(e){
		var fd = new FormData();
		var maxSize = $(this).attr('data-max-size');
		if (($(this)[0].files[0].size / 1000) > maxSize) {
			alert('Max file size is ' + maxSize + ' KB');
			$(this).closest('form').get(0).reset();
			return false;
		}
		fd.append('images', $(this)[0].files[0]);
		fd.append('action', 'upload');
		fd.append('id_post', id_xenforum_post);
		var _url = $(this).attr('rel');
		$.ajax({
			url: _url,
			type: 'POST',
			data: fd,
			processData: false,
			contentType: false,
			dataType: 'json',
			success: function(response) {
				// .. do something
				if (response && typeof response.image !== 'undefined') {
					var _template = attachment_templ.clone();
					_template.find("[name='attachments[]']").val(response.image.id);
					_template.find(".attachment-img img").attr('src', response.image.abs_path);
					_template.find(".attachment-name a").attr('href', response.image.abs_path).text(response.image.name);
                    $("#attachment_wrap").append(_template);
                    checkHasFiles();
				} else {
					alert(response.join());
				}
			}/**,
					error: function(jqXHR, textStatus, errorMessage) {
						console.log(errorMessage); // Optional
					}*/
		});
		$(this).wrap('<form>').closest('form').get(0).reset();
		$(this).unwrap();
		return false;
	});

	$(document).find('#attachment_wrap').on('click', '.insert-attachment', function(e){
		var a = $(this);
		e.preventDefault();
		$(document).scrollTop($('#comment').offset().top);
		var image_id = a.parents('.attachment').find('input[name="attachments[]"]').val();
		var image_url = a.parents('.attachment').find('.attachment-img img').attr('src');
		if (tinyMCE.activeEditor) {
			tinyMCE.activeEditor.execCommand('mceInsertContent', false, renderImage(image_id, image_url) + '<br />  ');
		} else {
			typeInTextarea($('#comment'), image_url + '\n');
		}
		return false;
	});

	function renderImage(id, link) {
		return '<img data-id="' + id + '" src="' + link + '">';
	}

	function typeInTextarea(el, newText) {
		var start = el.prop("selectionStart");
		var end = el.prop("selectionEnd");
		var text = el.val();
		var before = text.substring(0, start);
		var after  = text.substring(end, text.length);
		el.val(before + newText + after);
		el[0].selectionStart = el[0].selectionEnd = start + newText.length;
		el.focus();
		return false;
	}

    $(document).find('#attachment_wrap').on('click', '.delete', function(e){
		var _this = $(this);
        e.preventDefault();
		if (confirm(_confirm_delete_)) {
			var _url = _this.attr('rel');
			var _id = _this.closest('.attachment').find('input[name="attachments[]"]').val();
			var _path = _this.closest('.attachment').find(".attachment-img img").attr('src');
            _this.closest('.attachment').fadeOut('slow', function(e) {
				var _fade = $(this);
				$.post(_url, {action: 'deleteAttachment', id: _id}, function(response) { // result
					if (typeof response.result !== 'undefined' && response.result == 'OK') {
						_fade.remove();
						if (tinyMCE.activeEditor) {
							tinyMCE.activeEditor.execCommand('mceRemoveNode', false, tinyMCE.activeEditor.dom.select('img[src="' + _path + '"]'));
                        }
                        checkHasFiles();
					} else {
						_fade.fadeIn();
						alert(response.join());
					}
				}, 'json');
			});
		}
		return false;
	});

    function checkHasFiles() {
        if (store_attachment.find('.attachment').length) {
            store_attachment.show();
        } else {
            store_attachment.hide();
        }
    }

	$(document).ready(function() {
		tinySetupXF({
			language_url: tinymce_lang_link
		});
	});


	$(document).on('keyup change', '#meta_title', function(e){
		$('#createTopic').html($('#meta_title').val());
	});
});
