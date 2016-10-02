$(document).ready(function() {
	$(".deletefile").click(function(e) {
		$this = $(this);
		e.preventDefault();
        var thisListID = $(this).parents('ul').prop('id');
		var thisFile = $(this).parents('li').find('a:first').attr('href').split('/').reverse();
		$.post('deletefile.php', { filename:thisFile[1]+'/'+thisFile[0] }, function(data) { 
            data = $.parseJSON(data);
            showUpdateInfo(''+data.content+'',''+data.infotype+'');
            $this.parents('li').remove();
            if (($('#'+thisListID+' li').length) === 0) {
                $('#'+thisListID).parent('.container').remove();
                if (($('.container').length) == 1 && $('.container').hasClass('hidden')) {
                    $('.container').removeClass('hidden').addClass('visible');
                }
            }
        });
	})
	$("input[type=file]").on('change',function() {
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

    $('.pictures > a > img').load(function() {
        $(this).each(function() {
        var getImgDimension = $(this).position();    
        $(this).parent('a').next('span').css({'width':'4em','position':'absolute','left':getImgDimension.left});  
        })
        
    })
});
function showUpdateInfo(data,infotype) {
    $('#updateinfo').removeClass('error success info warning');
    $('#updateinfo').stop().css({'opacity':'1','display':'block'}).addClass(infotype).text(data).fadeOut(5000);
}

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

Dropzone.options.upload = {
  paramName: "file", // The name that will be used to transfer the file
  maxFilesize: 500, // MB
  dictDefaultMessage: 'Drop files here, or click, to upload',
  accept: function(file, done) {
    if (file.name == "justinbieber.jpg") {
      done("Naha, you don't.");
    }
    else { done(); }
  }
};