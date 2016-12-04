$(document).ready(function() {

	// debug
	// $(document).click(function(e){
	// 	console.log(e.target);
	// }); 

	messageBoxHide();

	var masonryGrid = $('.grid').masonry({
  		itemSelector: '.grid-item',
  		columnWidth: 50,
  		gutter: 2
  		// percentPosition: true
	});
	masonryGrid.imagesLoaded().progress(function() {
  		masonryGrid.masonry();
  		$('#pictures_list').css({'max-width':'59em'});
	});

	if (GetURLParameter() == '/userprofile') {
		$('.deletefile').click(function(e) {
			$this = $(this);
			e.preventDefault();
			var userName = '';
			var thisListID = $(this).parents('ul').prop('id');
			var thisListFolder = $(this).closest('li').prevAll('li.heading:first').text().toLowerCase();
			var thisFile = $(this).parents('li').find('.filename').text();
			$.post('deletefile.php', { filename:thisListFolder+'/'+thisFile,username:userName }, function(data) { 
				data = $.parseJSON(data);
				showUpdateInfo(''+data.content+'',''+data.infotype+'');
				if ((($this.parents('li').prev('li').hasClass('heading')) === true) && ((($this.parents('li').next('li').hasClass('heading')) === true) || ($this.parents('li').next('li').length == 0))) {
					$this.parents('li').prev('li.heading:first').remove();
				}
				$this.parents('li').remove();
			})
		})
		$('.make_public').click(function(e) {
			$this = $(this);
			e.preventDefault();
			var thisListFolder = $(this).parents('li').prevAll('li.heading:first').text().toLowerCase();
			var thisFile = $(this).parents('li').find('.filename').text();
			if ($this.val() == 0) {
				$.post('create_public_link.php',{filename:thisListFolder+'/'+thisFile}, function(data) {
					data = $.parseJSON(data);
					showUpdateInfo(''+data.content+'',''+data.infotype+'');
					$this.prop('checked',true).val(1);
					$this.next('label').find('i').addClass('fa-check-square').removeClass('fa-square');
				})	
			} else {
				$.post('deletefile.php', { deletepublic:true,filename:thisListFolder+'/'+thisFile }, function(data) {
		            data = $.parseJSON(data);
		            showUpdateInfo(''+data.content+'',''+data.infotype+'');
		            $this.prop('checked',false).val(0);
		            $this.next('label').find('i').addClass('fa-square').removeClass('fa-check-square');
	        	})
			}
		})
	}
	if (GetURLParameter() == '/gallery' || GetURLParameter() == '/moderate') {
		$('.sharefile').click(function(e) {
			$this = $(this);
			e.preventDefault();
			var userName = $('#username_display').find('i').children('span').text();
			if (GetURLParameter('user')) {
				userName = GetURLParameter('user');
			}
			var modifiedLink = $(this).parents('li').find('a:first').attr('href').split('?')[1].split('=');
			if (modifiedLink[1].indexOf('__') >= 0) {
				var thisFile = document.location.origin+'/?'+modifiedLink[0]+'='+modifiedLink[1];
			} else {
				var thisFile = document.location.origin+'/?'+modifiedLink[0]+'='+userName+'__'+modifiedLink[1];	
			}
			alert(thisFile);
		})
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
			$.post('deletefile.php', { filename:thisListFolder+'/'+thisFile,username:userName }, function(data) { 
	            data = $.parseJSON(data);
	            showUpdateInfo(''+data.content+'',''+data.infotype+'');
	            $this.parents('li').remove();
	       //      $('.pictures > a > img').each(function() {
	    			// var getImgDimension = $(this).position();    
	    			// $(this).parent('a').next('span').css({'width':'4em','position':'absolute','left':getImgDimension.left});  
	       //  	}) 
			masonryGrid.imagesLoaded().progress(function() {
  				masonryGrid.masonry();
			});
			    if (($('#'+thisListID+' li').length) === 0) {
	                $('#'+thisListID).parent('.container').remove();       
	                if (($('.container').length) == 1 && $('.container').hasClass('hidden')) {
	                    $('.container').removeClass('hidden').addClass('visible');
	                }
	            }
	        })
		})
		$('.approvefile').click(function(e) {
			$this = $(this);
			e.preventDefault();
			var thisListID = $(this).parents('ul').prop('id');
			var thisListFolder = $(this).parents('ul').prop('id').split('_')[0];
			var thisFile = $(this).parents('li').find('a:first').attr('href').split('=')[1].split('&')[0];
			$.post('approvefile.php',{ filename:thisListFolder+'/'+thisFile }, function(data) {
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
			})
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
					$this.next('label').find('i').addClass('fa-check-square').removeClass('fa-square');
				})	
			} else {
				$.post('deletefile.php', { deletepublic:true,filename:thisListFolder+'/'+thisFile,username:'public' }, function(data) {
		            data = $.parseJSON(data);
		            showUpdateInfo(''+data.content+'',''+data.infotype+'');
		            $this.prop('checked',false).val(0);
		           	$this.next('label').find('i').addClass('fa-square').removeClass('fa-check-square');
	        	})
			}
		})
	}
	
	$('input[type=file]').on('change',function() {
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

	$('#use_db').click(function() {
		if ($(this).is(':checked')) {
			$('#configform p').each(function() {
				if ($(this).hasClass('hidden')) {
					$(this).removeClass('hidden').addClass('washidden');
				}
			})
		} else {
			$('#configform p').each(function() {
				if ($(this).hasClass('washidden')) {
					$(this).removeClass('washidden').addClass('hidden');
				}
			})
		}
	})
	
	$('#uploadreset').click(function() {
		$('#file').val('');
		$('#file').removeClass('active').addClass('inactive');
	})

	$('.sortlinks a').click(function(e) {
		e.preventDefault();
		var urlParam = $(this).attr('href').split('=')[1];
		$.post('update_cookie.php',{'setsort':urlParam}, function(data) {
			data = $.parseJSON(data);
			showUpdateInfo(''+data.content+'',''+data.infotype+'');
			window.location.reload(true);
		})
	})
	
	if (GetURLParameter() == '/gallery') {
		if ($('.container > #pictures_list').length > 0) {
			var endResult = ($('#pictures_list li').length * 10) + 10;
			$('#pictures_list').parents('.container').css(({'max-width':endResult+'em','min-width':'20em'}));
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
          if (!$('[id^=filelist_]').hasClass('hidden')) {
          	localStorage.setItem('showuserfilelist',1);
          } else {
          	localStorage.setItem('showuserfilelist',0);
          }
          return val === 'Show filelist' ? 'Hide filelist' : 'Show filelist';
      })
    })
    if (localStorage.getItem('showuserfilelist') == 1) {
    	$('#showhidefilelist').val('Hide filelist');
    	$('[id^=filelist_]').removeClass('hidden');
    } else if (localStorage.getItem('showuserfilelist') == 0) {
    	$('#showhidefilelist').val('Show filelist');
    	$('[id^=filelist_]').addClass('hidden');
    }

    $('select').selectmenu({
    	width: '10em'
    });
    
    $('[id^=filelist_] li').each(function() {
    	if ($(this).hasClass('heading')) {
    		if ($(this).next().hasClass('heading')) {
    			$(this).remove();
    		}
    	}
    })

	$(window).on('resize',function() { //this shows the new windowwidth (in conjunction with the above) on resize
		if (viewportSize.getWidth() <= 320 && $('#username_view').length > 0) {
			var userNameWidth = $('#username_display').width();
    		$('#username_display').css({'border-radius':'0'});
    		$('#username_view').css({'width':userNameWidth,'text-align':'center'});
    	}
    })
	if (viewportSize.getWidth() <= 320 && $('#username_view').length > 0) {
		var userNameWidth = $('#username_display').width();
    	$('#username_display').css({'border-radius':'0'});
    	$('#username_view').css({'width':userNameWidth,'text-align':'center'});
    }
    $(window).on('resize', function() {
    	if (viewportSize.getWidth() <= 480) {
    		$('#logoutform input').val('X');
    	} else {
    		$('#logoutform input').val('Log out');
    	}
    })
    if (viewportSize.getWidth() <= 480) {
    	$('#logoutform input').val('X');
    } 


	$('#searchusers').on('keyup', function() {
	    var g = $(this).val();
	    $("#userlist_body .content .alternate li:not(.heading)").each(function() {
	        var s = $(this).text();
	        if (s.indexOf(g)!=-1) {
	            $(this).show();
	        }
	        else {
	            $(this).hide();
	        }
	    })
	});

    $('#resetlocalstorage').click(function() {
    	localStorage.clear();
    	showUpdateInfo('Your removed items are back, and other site-preferences have been reset','success');
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
					$('body').append('<div id="overlay"></div>');
				}
				if ($('#lightbox_wrapper').hasClass('hidden')) {
					$('#lightbox_wrapper').removeClass('hidden').addClass('visible');
					$('header,#username_view,#username_display').addClass('hidden');
				}
				$('#lightbox_container img').remove();
				$('#lightbox_container').append(image);
				image.onload = function() { window.URL.revokeObjectURL(image.src); };
				$('#lightbox_container .nextbutton, #lightbox_container .prevbutton').remove();
				 $('#overlay,.closebutton').on('click',function(e) {
			    	$('#overlay').remove();
			    	$('#lightbox_wrapper').removeClass('visible').addClass('hidden');
			    	$('#lightbox_container img').remove();
			    	$('header,#username_display,#username_view').removeClass('hidden');
			    })
				$(document).keyup(function(e) {
					if (e.which == 27) {
				    	$('#overlay').remove();
				    	$('#lightbox_wrapper').removeClass('visible').addClass('hidden');
				    	$('#lightbox_container img').remove();
			    		$('header,#username_display,#username_view').removeClass('hidden');				    	
					}					
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
	};

    $('.lightbox,.prevbutton,.nextbutton').on('click', function(e) {
    	e.preventDefault();
    	var $this = $(this);
	    	if ($(e.target).hasClass('prevbutton') && $('a[href$="'+linkName[1]+'"]').parents('.pictures').prev('li.pictures').length == 0) {
	    		fetchFile = $('.pictures:last-of-type').find('a').attr('href').split('/').reverse()[0].split('?')[1];
	    		linkName = $('.pictures:last-of-type').find('a').attr('href').split('=');
	    	} else if ($(e.target).hasClass('nextbutton') && $('a[href$="'+linkName[1]+'"]').parents('.pictures').next('li.pictures').length == 0) {
	    		fetchFile = $('.pictures:first-of-type').find('a').attr('href').split('/').reverse()[0].split('?')[1];
	    		linkName = $('.pictures:first-of-type').find('a').attr('href').split('=');
	    	} else if ($(e.target).hasClass('prevbutton') && $('a[href$="'+linkName[1]+'"]').parents('.pictures').prev('li.pictures').length != 0)  {
	    		fetchFile = $('a[href$="'+linkName[1]+'"]').parents('.pictures').prev('li.pictures').find('a').attr('href').split('/').reverse()[0].split('?')[1];
	    		linkName = $('a[href$="'+linkName[1]+'"]').parents('.pictures').prev('li.pictures').find('a').attr('href').split('=');
	    	} else if ($(e.target).hasClass('nextbutton')) {
	    		fetchFile = $('a[href$="'+linkName[1]+'"]').parents('.pictures').next('li.pictures').find('a').attr('href').split('/').reverse()[0].split('?')[1];
	    		linkName = $('a[href$="'+linkName[1]+'"]').parents('.pictures').next('li.pictures').find('a').attr('href').split('=');
	    	} else {
	    		linkName = $(this).attr('href').split('=');
	    		fetchFile = $this[0]['href'].split('/').reverse()[0].split('?')[1];
	    	}
	    	var joinLink = linkName.join('='),
    			requestLink = (($this[0]['search'] != undefined) ? $this[0]['search'] : joinLink),
    			requestType = requestLink.split('?')[1].split('=')[0],
    			requestFile = requestLink.split('?')[1].split('=')[1],
    			requestFileExt = requestFile.split('.').reverse()[0];
    			console.log(requestFileExt);
	    if (requestType == 'imgfile') {    	
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
					$('body').append('<div id="overlay"></div>');
				}
				if ($('#lightbox_wrapper').hasClass('hidden')) {
					$('#lightbox_wrapper').removeClass('hidden').addClass('visible');
					$('header,#username_view,#username_display').addClass('hidden');
				}
				$('#lightbox_container img').remove();
				$('#lightbox_container').append(image);
		    	if (viewportSize.getWidth() <= 480) {
		    		image.onload = function() { $('#lightbox_wrapper').css({'left':0,'width':'100%','padding':'1em','box-sizing':'border-box'}).find('#lightbox_container img').css({'width':'100%','height':'initial'}); $('.closebutton,.nextbutton,.prevbutton').css({'top':'20em'}); $('.prevbutton').css({'left':'3em'}); $('.nextbutton').css({'right':'3em'}); $('.closebutton').css({'left':'50%','margin-left':'-1.25em'}); window.URL.revokeObjectURL(image.src);};
		    	} else {
					image.onload = function() { window.URL.revokeObjectURL(image.src);};
				}
				 $('#overlay,.closebutton').on('click',function(e) {
			    	$('#overlay').remove();
			    	$('#lightbox_wrapper').removeClass('visible').addClass('hidden');
			    	$('#lightbox_container img').remove();
			    	$('header,#username_display,#username_view').removeClass('hidden');
			    })
				$(document).keyup(function(e) {
					if (e.which == 27) {
				    	$('#overlay').remove();
				    	$('#lightbox_wrapper').removeClass('visible').addClass('hidden');
				    	$('#lightbox_container img').remove();
				    	$('header,#username_display,#username_view').removeClass('hidden');
					}					
				})
	          }
			}); 
		} else if (requestType == 'vidfile') {
			$('body').append('<a href="showfile.php'+requestLink+'" id="directlink" class="hidden">Direct Link</a>');
			$('#directlink').simulate('click');
			$('#directlink').remove();
		}
    })

    if ($('[id^=filelist_] li:last-of-type').hasClass('heading')) {
    	$('[id^=filelist_] li:last-of-type').remove();
    }
});

function showUpdateInfo(data,infotype) {
    $('#updateinfo').removeClass('error success info warning');
    $('#updateinfo').stop().css({'opacity':'1','display':'block'}).addClass(infotype).text(data).fadeOut(5000);
}

function messageBoxHide() {
	$('.messagebox').each(function() {
		var thisID = $(this).attr('id');
		if (localStorage.getItem(thisID) != 1 && !$(this).hasClass('visible') && thisID != 'updateinfo') {
			$('#'+thisID).show();
		}
		if (localStorage.getItem(thisID) == 1) {
			$('#'+thisID).addClass('hidden');
		}
		if ($('#'+thisID).hasClass('remove_box')) {
			var msgcontent = $('#'+thisID).append('<span class="remove" title="Close the information-container"><i class="fa fa-remove"></i></span>').html();
		}
		$('#'+thisID+' .remove').click(function() {
			$('#'+thisID).hide();
			localStorage.setItem(thisID,'1');
		})
	})
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
				this.on('error', function(file, response) {
					var data = $.parseJSON(response);
					showUpdateInfo(''+data.content+'',''+data.infotype+'');
					$('.dz-error-mark svg g g').attr('fill','#F00');
					_ref = file.previewElement.querySelectorAll("[data-dz-errormessage]");
        			_results = [];
        			for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            			node = _ref[_i];
            			_results.push(node.textContent = data.content);
        			}
        			return _results;
				});
			};
		});
	}
}

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