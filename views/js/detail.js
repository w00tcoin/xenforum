/**
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*/

$(document).ready(function(){
    $('.post_sharing .btn').on('click', function(){
        type = $(this).attr('data-type');
        if (type.length) {
            switch(type) {
                case 'twitter':
                    window.open('https://twitter.com/intent/tweet?text=' + sharing_name + ' ' + encodeURIComponent(sharing_url), 'sharertwt', 'toolbar=0,status=0,width=640,height=445');
                    break;
                case 'facebook':
                    window.open('http://www.facebook.com/sharer.php?u=' + sharing_url, 'sharer', 'toolbar=0,status=0,width=660,height=445');
                    break;
                case 'google-plus':
                    window.open('https://plus.google.com/share?url=' + sharing_url, 'sharer', 'toolbar=0,status=0,width=660,height=445');
                    break;
            }
        }
    });
});

$(function(){
    $(document).on('click', '.message .delete', function(e){
        a = $(this);
        e.preventDefault();
        _block = a.parents('.message');
        if (confirm(a.attr('data-confirm'))) {
            $.get(a.attr('rel'), {action: 'delete'}).done(function(data){
                _block.fadeOut(400, function(){
                    $(this).remove();
                });
            });
        }

        return false;
    });

    $('.report-topic').fancybox({
        hideOnContentClick: false,
        afterLoad: function() {
            var rel = $(this.element).attr('rel');
            $(this.content).find("input[name=url]").val(rel);
            $(this.content).find("[name=reason]").val('');
            $(this.content).find("#report_box_form_error").hide();
        }
    });

    $('#submitReport').click(function(e) {
        // Kill default behaviour
        e.preventDefault();
        var reason = $('#report_box_form').find("[name=reason]").val();
        var url = $('#report_box_form').find("input[name=url]").val();
        if (reason == '') {
            $('#report_box_form').find("#report_box_form_error").show();
            return false;
        }

        $.post(url + '?action=report',
               $('#report_box_form').serialize(),
               function(response){
            if (response.saved == true) {
                alert(alert_report_thankyou);
            }
            //console.log('data', data);
        },"json"
              );
        $.fancybox.close();

        return false;
    });

    $('.LikeLink').click(function(e){
        a = $(this);
        e.preventDefault();
        $.get(a.attr('href'), {action: 'like'}).done(function(c){
            var b = $(a.data("container"));
            if(c==="")
                b.html('');
            else
                b.html(c);

            if (a.hasClass('like')) {
                a.removeClass('like').addClass('unlike');
                a.find(".LikeLabel").html(str_unlike);
            }
            else {
                a.removeClass('unlike').addClass('like');
                a.find(".LikeLabel").html(str_like);		
            }
        });
        return false;
    });

    $('.ReplyQuote').click(function(e){
        var a = $(this);
        e.preventDefault();
        $.get(a.attr('href'), {action: 'quote'}).done(function(data){
            $(document).scrollTop($('#psform').offset().top - 150);
            if (tinyMCE.activeEditor) {
                tinyMCE.activeEditor.execCommand('mceInsertContent', false, data + '<br />  ');
            } else {
                typeInTextarea($('#comment'), data + '\n');
            }
        });
        return false;
    });

    mce_settings = {
        selector:'.rteXF',
        //		skin: "lightblue",
        theme: "modern",
        entity_encoding : "raw",
        language_url : tinymce_lang_link,
        menubar : false,
        plugins: [
            "advlist autolink lists link image charmap preview hr anchor pagebreak",
            "searchreplace visualblocks visualchars code",
            "insertdatetime media nonbreaking save table contextmenu directionality",
            "emoticons paste textcolor colorpicker textpattern"
        ],
        toolbar1: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code preview media | forecolor backcolor emoticons",
        image_advtab: true,
        force_br_newlines : true,
        forced_root_block: '',
        convert_urls: false,
        remove_script_host : false,
        statusbar: false
    };

    tinymce.init(mce_settings);

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

    $('ul.product_list, div.products.row').bxSlider({
		pager: slide_pager,
		infiniteLoop: slide_infiniteLoop,
		hideControlOnEnd: slide_hideControlOnEnd,
		autoHover: true,
		auto: slide_auto,
		minSlides: 2,
		maxSlides: 4,
		slideWidth: slide_slideWidth,
		slideMargin: slide_slideMargin
	});
    

    $(document).on('click', '.attribution', function(e){ console.log('clicked');
        e.preventDefault();
        var _this = $(this);
        _this.parent().find('.quote').toggleClass('fullview');
        return false;
    });

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
});
