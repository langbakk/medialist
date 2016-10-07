$(document).ready(function() {
	$('.deletefile').click(function(e) {
		$this = $(this);
		e.preventDefault();
		var userName = '';
		if (GetURLParameter('user')) {
			userName = GetURLParameter('user');
		}
        var thisListID = $(this).parents('ul').prop('id');
		var thisFile = $(this).parents('li').find('a:first').attr('href').split('/').reverse();
		$.post('deletefile.php', { filename:thisFile[1]+'/'+thisFile[0],username:userName }, function(data) { 
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
	$('.make_public').click(function(e) {
		$this = $(this);
		e.preventDefault();
		var userName = '';
		if (GetURLParameter('user')) {
			userName = GetURLParameter('user');
		}
		var thisListID = $(this).parents('ul').prop('id');
		var thisFile = $(this).parents('li').find('a:first').attr('href').split('/').reverse();
		$.post('create_public_link.php',{filename:thisFile[1]+'/'+thisFile[0],username:userName}, function(data) {
			data = $.parseJSON(data);
			showUpdateInfo(''+data.content+'',''+data.infotype+'');
		})
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
	$('#upload > input[type=file],#upload > input[type=submit]').hide();
    $('.pictures > a > img').load(function() {
        $(this).each(function() {
        var getImgDimension = $(this).position();    
        $(this).parent('a').next('span').css({'width':'4em','position':'absolute','left':getImgDimension.left});  
        })
        
    })
    $('#showhidefilelist').click(function() {
    	$(this).val(function(i, val){
          $('[id^=filelist_]').toggleClass('hidden');
          return val === "Show filelist" ? "Hide filelist" : "Show filelist";
      })
    })
    $('[id^=filelist_] li').each(function() {
    	if ($(this).hasClass('heading')) {
    		if ($(this).next().hasClass('heading')) {
    			$(this).remove();
    		}
    	}
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
function GetURLParameter(sParam) {
	if (sParam != undefined && sParam.length > 0) {
		var sPageHref = window.location.href;
    	var sPageURL = window.location.search.substring(1);
    	var sURLVariables = (sPageURL.length != '') ? sPageURL.split('&') : sPageHref.split('&');
    	for (var i = 0; i < sURLVariables.length; i++) {
      	  var sParameterName = sURLVariables[i].split('=');
        	if (sParameterName[0] == sParam) {
            return sParameterName[1];
        	}
    	}
  } else {
  	var sPageURL = document.location.pathname.split('&');
  	var sParameterName = sPageURL[0];
  	return sParameterName;
  }
}


Dropzone.options.upload = {
	paramName: 'file', // The name that will be used to transfer the file
	maxFilesize: 500, // MB
	dictDefaultMessage: 'Drop files here, or click, to upload',
	init: function () {
		this.on('uploadprogress', function(file, progress, response) {
			if (progress == 100) {
				this.on('success', function(file, response) {
					var data = $.parseJSON(response);
					showUpdateInfo(''+data.content+'',''+data.infotype+'');
				});
			};
		});
	}
// success: function(file, response) {
// var data = $.parseJSON(response);
// showUpdateInfo(''+data.content+'',''+data.infotype+'');
// }
  // init: function() {
  //   this.on("addedfile", function(file) {
  //   	if (this.files.length) {
  //  			var _i, _len;
  //  			for (_i = 0, _len = this.files.length; _i < _len; _i++) {
  //     			if(this.files[_i].name === file.name && this.files[_i].size === file.size) {
  //       			return false;
  //     			}
  //   		}
  // 		}
  // 	});
  // },
  // success: function(file, response) {
  	// var data = $.parseJSON(response);
  	// if (file.name+' already exist' == data.content) {
  	// 	if ($('.dz-filename').text() == file.name) {
  	// 		$('.dz-filename').parents('.dz-preview').remove();
  	// 	}
   // 	}
  	// showUpdateInfo(''+data.content+'',''+data.infotype+'');
  // }
};