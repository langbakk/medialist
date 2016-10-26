$(document).ready(function() {

	// debug
	// $(document).click(function(e){
	// 	console.log(e.target);
	// }); 


	$('.deletefile').click(function(e) {
		$this = $(this);
		e.preventDefault();
		var userName = '';
		if (GetURLParameter('user')) {
			userName = GetURLParameter('user');
		}
        var thisListID = $(this).parents('ul').prop('id');
        var thisListFolder = $(this).parents('ul').prop('id').split('_')[0];
		var thisFile = $(this).parents('li').find('a:first').attr('href').split('=')[1].split('&')[0];
		console.log(thisFile);
		$.post('deletefile.php', { filename:thisListFolder+'/'+thisFile,username:userName }, function(data) { 
            data = $.parseJSON(data);
            showUpdateInfo(''+data.content+'',''+data.infotype+'');
            $this.parents('li').remove();
            $('.pictures > a > img').each(function() {
    			var getImgDimension = $(this).position();    
    			$(this).parent('a').next('span').css({'width':'4em','position':'absolute','left':getImgDimension.left});  
        	}) 
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
		var thisListFolder = $(this).parents('ul').prop('id').split('_')[0];
		var thisFile = $(this).parents('li').find('a:first').attr('href').split('=')[1];
		if ($this.val() == 0) {
			$.post('create_public_link.php',{filename:thisListFolder+'/'+thisFile}, function(data) {
				data = $.parseJSON(data);
				showUpdateInfo(''+data.content+'',''+data.infotype+'');
				$this.prop('checked',true).val(1);
			})	
		} else {
			$.post('deletefile.php', { deletepublic:true,filename:thisListFolder+'/'+thisFile,username:'public' }, function(data) {
	            data = $.parseJSON(data);
	            showUpdateInfo(''+data.content+'',''+data.infotype+'');
	            $this.prop('checked',false).val(0);
        	})
		}
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
	
	if (GetURLParameter() == '/gallery') {
		if ($('.container > #pictures_list').length > 0) {
			var totalby2 = (Math.floor($('#pictures_list li').length / 2));
			var rem = (Math.floor($('#pictures_list li').length % 2));
			var endResult = ((totalby2 + rem) * 10) + 10;
			var endResult = (endResult < 70) ? endResult : 70;
			$('#pictures_list').parents('.container').css(({'max-width':endResult+'em'}));
		}
	}

	$('#upload > input[type=file],#upload > input[type=submit]').hide();
    
    $('.pictures > a > img').load(function() {
        $(this).each(function() {
        var getImgDimension = $(this).position();    
        $(this).parent('a').next('span').not('.public_sharename').css({'width':'4em','position':'absolute','left':getImgDimension.left});  
	        if ($(this).attr('src').split('/')[1] == 'public') {
		        var getUploaderName = $(this).attr('src').split('/').reverse()[0].split('_')[0];
				$(this).parent('a').append('<span class="hovername">Uploaded by: '+getUploaderName+'</span>');
			}
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

    if (GetURLParameter('imgfile') || GetURLParameter('docfile') || GetURLParameter('vidfile')) {
    	var getFile = ((GetURLParameter('imgfile')) ? 'imgfile='+GetURLParameter('imgfile') : ((GetURLParameter('docfile')) ? 'docfile='+GetURLParameter('docfile') : (GetURLParameter('vidfile')) ? 'vidfile='+GetURLParameter('vidfile') : ''));

    	var fileName = '',
    		fetchName = '';
    	if (GetURLParameter('imgfile')) {
	    	$.ajax({
	          url: 'showfile.php',
	          type: 'GET',
	          dataType: 'binary',
	          data: getFile,
	          responseType: 'blob',
	          processData: false,
	          success: function(result) {
	          	var image = new Image();
				image.src = URL.createObjectURL(result);
				if ($('#overlay').length <= 0) {
					$('#top').append('<div id="overlay"></div>');
				}
				if ($('#lightbox_container').hasClass('hidden')) {
					$('#lightbox_container').removeClass('hidden').addClass('visible');
				}
				$('#lightbox_container img').remove();
				$('#lightbox_container').append(image);
				image.onload = function() { var imageWidth = image.width/2; $('#lightbox_container').css({'margin-left':'-'+imageWidth+'px'}); window.URL.revokeObjectURL(image.src);};
				$('#lightbox_container .nextbutton, #lightbox_container .prevbutton').remove();
				 $('#overlay,.closebutton').on('click',function(e) {
			    	$('#overlay').remove();
			    	$('#lightbox_container img').remove();
			    })
	          }
			}); 
		} else if (GetURLParameter('vidfile')) {
			fileName = GetURLParameter('vidfile');
			fetchName = 'vidfile';
		} else if (GetURLParameter('docfile')) {
			fileName = GetURLParameter('docfile');
			fetchName = 'docfile';
		}
		if (fileName.length > 0) {
			$('#main').append('<div class="container"><h2>File download</h2><div class="content"><p>You will soon see a download-dialog asking you were you want to save your file</p></div></div>');
			$('body').append('<a href="showfile.php?'+fetchName+'='+fileName+'" id="directlink" class="hidden">Direct Link</a>');
			$('#directlink').simulate('click');
		}
	}

    $('.lightbox,.prevbutton,.nextbutton').click(function(e) {
    	e.preventDefault();
    	var $this = $(this);
    	if ($(e.target).hasClass('prevbutton') && $('a[href$="'+linkName+'"]').parent('.pictures').prev('li.pictures').length == 0) {
    		fetchFile = $('.pictures:last-of-type').find('a').attr('href').split('/').reverse()[0].split('?')[1];
    		linkName = $('.pictures:last-of-type').find('a').attr('href').split('=')[1];
    	} else if ($(e.target).hasClass('nextbutton') && $('a[href$="'+linkName+'"]').parent('.pictures').next('li.pictures').length == 0) {
    		fetchFile = $('.pictures:first-of-type').find('a').attr('href').split('/').reverse()[0].split('?')[1];
    		linkName = $('.pictures:first-of-type').find('a').attr('href').split('=')[1];
    	} else if ($(e.target).hasClass('prevbutton') && $('a[href$="'+linkName+'"]').parent('.pictures').prev('li.pictures').length != 0)  {
    		fetchFile = $('a[href$="'+linkName+'"]').parent('.pictures').prev('li.pictures').find('a').attr('href').split('/').reverse()[0].split('?')[1];
    		linkName = $('a[href$="'+linkName+'"]').parent('.pictures').prev('li.pictures').find('a').attr('href').split('=')[1];
    	} else if ($(e.target).hasClass('nextbutton')) {
    		fetchFile = $('a[href$="'+linkName+'"]').parent('.pictures').next('li.pictures').find('a').attr('href').split('/').reverse()[0].split('?')[1];
    		linkName = $('a[href$="'+linkName+'"]').parent('.pictures').next('li.pictures').find('a').attr('href').split('=')[1];
    	} else {
    		linkName = $(this).attr('href').split('=')[1];
    		fetchFile = $this[0]['href'].split('/').reverse()[0].split('?')[1];
    	}
    	// console.log($('a[href$="'+linkName+'"]').parent('.pictures').next('li.pictures').length == 0);
    	
   		$.ajax({
          url: 'showfile.php',
          type: 'GET',
          dataType: 'binary',
          data: fetchFile,
          responseType: 'blob',
          processData: false,
          success: function(result) {
          	var image = new Image();
			image.src = URL.createObjectURL(result);
			if ($('#overlay').length <= 0) {
				$('#top').append('<div id="overlay"></div>');
			}
			if ($('#lightbox_container').hasClass('hidden')) {
				$('#lightbox_container').removeClass('hidden').addClass('visible');
			}
			$('#lightbox_container img').remove();
			$('#lightbox_container').append(image);
			image.onload = function() { var imageWidth = image.width/2; $('#lightbox_container').css({'margin-left':'-'+imageWidth+'px'}); window.URL.revokeObjectURL(image.src);};
			 $('#overlay,.closebutton').on('click',function(e) {
		    	$('#overlay').remove();
		    	$('#lightbox_container img').remove();
		    })
          }
		}); 
    })

    if ($('[id^=filelist_] li:last-of-type').hasClass('heading')) {
    	$('[id^=filelist_] li:last-of-type').remove();
    }
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

 
// use this transport for "binary" data type
$.ajaxTransport("+binary", function(options, originalOptions, jqXHR){
    // check for conditions and support for blob / arraybuffer response type
    if (window.FormData && ((options.dataType && (options.dataType == 'binary')) || (options.data && ((window.ArrayBuffer && options.data instanceof ArrayBuffer) || (window.Blob && options.data instanceof Blob))))) {
        return {
	        // create new XMLHttpRequest
	        send: function(headers, callback){
				// setup all variables
		        var xhr = new XMLHttpRequest(),
				url = options.url,
				type = options.type,
				async = options.async || true,
				// blob or arraybuffer. Default is blob
				dataType = options.responseType || 'blob',
				data = options.data || null,
				username = options.username || null,
				password = options.password || null;
							
		        xhr.addEventListener('load', function(){
					var data = {};
					data[options.dataType] = xhr.response;
					// make callback and send data
					callback(xhr.status, xhr.statusText, data, xhr.getAllResponseHeaders());
		        });
		        xhr.open(type, url, async, username, password);
						
				// setup custom headers
				for (var i in headers ) {
					xhr.setRequestHeader(i, headers[i] );
				}
						
		        xhr.responseType = dataType;
		        xhr.send(data);
		    },
	        abort: function(){
	            jqXHR.abort();
	        }
        }
    }
});