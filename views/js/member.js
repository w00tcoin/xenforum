/**
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*/

$(function() {
	if ($('.resubmit').length) {
		$('#psform').hide();
	}
	function toogleForm() {
		$('#psform').toggle();
	}

	// Activate / desactivate next button when licence checkbox is clicked
	$('#approved_license').click(function() {
		if ($(this).prop('checked')) {
			$('#btSubmit').removeClass('disabled').attr('disabled', false);
		} else {
			$('#btSubmit').addClass('disabled').attr('disabled', true);
		}
	});

	// Handle when click to delete a notification
	$('.deleteNotif').on('click', function(e){
		var a = $(this);
		e.preventDefault();
		_block = a.parents('.notification');
		if (confirm(a.attr('data-confirm')))
		{
			$.get(a.attr('href'), {action: 'deletenotif'}).done(function(data){
				_block.fadeOut(400, function(){
					$(this).remove();
				});
			});
		}
	});
});