$(document).ready(function() {
	$(".deletefile").click(function(e) {
		console.log($(this));
		$this = $(this);
		e.preventDefault();
		var thisFile = $(this).parents('li').find('a:first').attr('href').split('/').reverse();
		$.post('deletefile.php', { filename:thisFile[1]+'/'+thisFile[0] }, function(data) { alert(data); window.location.reload(true)});
		// alert(thisFile[1] + '/' + thisFile[0]);

	})
	$("input[type=file]").on('change',function() {
		//console.log($(this));
		var thisContent = $(this).val();
		if (thisContent != 'No file selected') {
			$(this).removeClass('inactive').addClass('active');
			$.post('retrieve_folder.php',{ currentfile:thisContent }, function(data) { 
				if (data != '') {
					data = $.parseJSON(data);
					for (var key in data) {
						var optionText = ucfirst(data[key]);
						$("#folderchoicecontainer").show();
						$('#folderchoice').append('<option value="'+data[key]+'">'+optionText+'</option>').removeAttr('disabled');
					}
				}
			});
			$('#createfolder').removeClass('hidden');
		};
	})
	$("#uploadreset").click(function() {
		$("#file").val('');
		$("#file").removeClass('active').addClass('inactive');
	})

	$(function() {
		equalHeight($('.video'));
		var elementHeight = $('.video').height();
		$('.video > a > img').css({'height':elementHeight});

	})
});
function ucfirst(text) {
	   return text.substr(0, 1).toUpperCase() + text.substr(1);    
}
function equalHeight(group) {
   	tallest = 0;
		group.each(function() {
   		thisHeight = $(this).height();
			if(thisHeight > tallest) {
			tallest = thisHeight;
        }
    })
    group.height(tallest);
}
