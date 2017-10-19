(function($){
	if(typeof($('').bg_upload) == 'function'){return;}
	var swf_url = '/webuploader-0.1.5/js/Uploader.swf';
	$.fn.extend({
		bg_upload :(function(cfg){
			var img_width = 210;
			var img_height = 110;
			var mult = false;
			var auto_size = 'max-';
			var file_limit = 10;
			if('width' in cfg){img_width = cfg.width;}
			if('height' in cfg){img_height = cfg.height;}
			if(('auto_size' in cfg) && !cfg.auto_size){auto_size = '';}
			if(('multiple' in cfg) && cfg.multiple){mult = true;}
			if(('fileNumLimit') in cfg){file_limit = cfg.fileNumLimit;}
			var top_div = $(this);
			if(top_div.length != 1){return false;}
			//alert(top_div.html());
			// 图片容器
			var queue = $('<ul class="filelist"></ul>');
			queue.insertBefore( top_div.find('.queueList') );
			// 状态栏，包括进度和控制按钮
			var statusBar = top_div.find('.statusBar');
			// 文件总体选择信息。
			var info = statusBar.find('.info');
			// 上传按钮
			var upload_btn = top_div.find('.uploadBtn');
			// 没选择文件之前的内容。
			var placeHolder = top_div.find('.placeholder');
			// 总体进度条
			var progress = statusBar.find('.progress').hide();
			// 添加的文件数量
			var fileCount = 0;
			// 添加的文件总大小
			var fileSize = 0;
			// 优化retina, 在retina下这个值是2
			var ratio = window.devicePixelRatio || 1;
			// 缩略图大小
			var thumbnailWidth = 110 * ratio;
			var thumbnailHeight = 110 * ratio;
			// 可能有pedding, ready, uploading, confirm, done.
			var state = 'pedding';
			// 所有文件的进度信息，key为file id
			percentages = {};
			var supportTransition = (function(){
				var s = document.createElement('p').style;
				var r = 'transition' in s || 'WebkitTransition' in s || 'MozTransition' in s || 'msTransition' in s || 'OTransition' in s;
				s = null;
				return r;
			})();
			if ( !WebUploader.Uploader.support() ) {
				alert( 'Web Uploader 不支持您的浏览器！如果你使用的是IE浏览器，请尝试升级 flash 播放器');
				throw new Error( 'WebUploader does not support the browser you are using.' );
			}
			// 实例化
			var uploader = WebUploader.create({
				pick: {
					id: top_div.find('.filePicker'),
					multiple: mult,
					innerHTML: top_div.find('.filePicker').html()
				},
				dnd: top_div.find('.queueList'),//'#uploader .queueList',
				paste: document.body,
				accept: {
					title: 'Images',
					extensions: 'gif,jpg,jpeg,bmp,png',
					mimeTypes: 'image/*'
				},
				thumb: {
					width: 1,
					height: 1,
					quality: 100,
					allowMagnify: false,
					type: ''
				},
				swf: swf_url,
				disableGlobalDnd: true,
				chunked: true,
				server: cfg.upload_server,
				fileNumLimit: (mult?file_limit:1),
				//fileSizeLimit: 2 * 1024 * 1024,	// 2 M
				fileSingleSizeLimit: 2 * 1024 * 1024	// 2 M
			});
			//添加“添加文件”的按钮，
			if(mult){
				uploader.addButton({
					id: top_div.find('.filePicker2'),
					label: '继续添加'
				});
			}
		
			// 当有文件添加进来时执行，负责view的创建
			function addFile( file ) {
				var pic_li = $( '<li id="' + file.id + '" style="min-width:30px;min-height:30px;'+auto_size+'width:'+img_width+'px;'+auto_size+'height:'+img_height+'px;">' +
						//'<p class="title">' + file.name + '</p>' +
						'<p class="imgWrap" style="min-width:30px;min-height:30px;'+auto_size+'width:'+img_width+'px;'+auto_size+'height:'+img_height+'px;"></p>'+
						'<p class="progress"><span></span></p>' +
						'</li>' );
		
				var btns = $('<div class="file-panel">' +
					'<span class="cancel">删除</span>' +
					//'<span class="rotateRight">向右旋转</span>' +
					//'<span class="rotateLeft">向左旋转</span>' +
					'</div>').appendTo( pic_li );
				var prgress = pic_li.find('p.progress span');
				var img_div = pic_li.find( 'p.imgWrap' ),
				info = $('<p class="error"></p>'),
	
				showError = function( code ) {
					switch( code ) {
						case 'exceed_size':
							text = '文件大小超出';
							break;
	
						case 'interrupt':
							text = '上传暂停';
							break;
	
						default:
							text = '上传失败，请重试';
							break;
					}
	
					info.text( text ).appendTo( pic_li );
				};
		
				if ( file.getStatus() === 'invalid' ) {
					showError( file.statusText );
				} else {
					// @todo lazyload
					img_div.text( '预览中' );
					uploader.makeThumb( file, function( error, src ) {
						if ( error ) {
							img_div.text( '不能预览' );
							return;
						}
		
						var img = $('<img src="'+src+'">');
						img_div.empty().append( img );
					});//, thumbnailWidth, thumbnailHeight );
		
					percentages[ file.id ] = [ file.size, 0 ];
					file.rotation = 0;
				}
		
				file.on('statuschange', function( cur, prev ) {
					if ( prev === 'progress' ) {
						prgress.hide().width(0);
					} else if ( prev === 'queued' ) {
		//				pic_li.off( 'mouseenter mouseleave' );
		//				btns.remove();
					}
		
					// 成功
					if ( cur === 'error' || cur === 'invalid' ) {
						console.log( file.statusText );
						showError( file.statusText );
						percentages[ file.id ][ 1 ] = 1;
					} else if ( cur === 'interrupt' ) {
						showError( 'interrupt' );
					} else if ( cur === 'queued' ) {
						percentages[ file.id ][ 1 ] = 0;
					} else if ( cur === 'progress' ) {
						info.remove();
						prgress.css('display', 'block');
					} else if ( cur === 'complete' ) {
						pic_li.append( '<span class="success"></span>' );
					}
		
					pic_li.removeClass( 'state-' + prev ).addClass( 'state-' + cur );
				});
		
				pic_li.on( 'mouseenter', function() {
					btns.stop().animate({height: 30});
				});
		
				pic_li.on( 'mouseleave', function() {
					btns.stop().animate({height: 0});
				});
		
				btns.on( 'click', 'span', function() {
					var index = $(this).index(),
						deg;
		
					switch ( index ) {
						case 0:
							uploader.removeFile( file );
							return;
		
						case 1:
							file.rotation += 90;
							break;
		
						case 2:
							file.rotation -= 90;
							break;
					}
		
					if ( supportTransition ) {
						deg = 'rotate(' + file.rotation + 'deg)';
						img_div.css({
							'-webkit-transform': deg,
							'-mos-transform': deg,
							'-o-transform': deg,
							'transform': deg
						});
					} else {
						img_div.css( 'filter', 'progid:DXImageTransform.Microsoft.BasicImage(rotation='+ (~~((file.rotation/90)%4 + 4)%4) +')');
						// use jquery animate to rotation
						// $({
						//	 rotation: rotation
						// }).animate({
						//	 rotation: file.rotation
						// }, {
						//	 easing: 'linear',
						//	 step: function( now ) {
						//		 now = now * Math.PI / 180;
		
						//		 var cos = Math.cos( now ),
						//			 sin = Math.sin( now );
		
						//		 img_div.css( 'filter', "progid:DXImageTransform.Microsoft.Matrix(M11=" + cos + ",M12=" + (-sin) + ",M21=" + sin + ",M22=" + cos + ",SizingMethod='auto expand')");
						//	 }
						// });
					}
		
		
				});
		
				pic_li.appendTo( queue );
			}
			// 负责view的销毁
			function removeFile( file ) {
				var pic_li = top_div.find('#'+file.id);
				removeFromInput(pic_li.attr("data-src"));
				delete percentages[ file.id ];
				updateTotalProgress();
				pic_li.off().find('.file-panel').off().end().remove();
			}
		
			function updateTotalProgress() {
				var loaded = 0,
					total = 0,
					spans = progress.children(),
					percent;
		
				$.each( percentages, function( k, v ) {
					total += v[ 0 ];
					loaded += v[ 0 ] * v[ 1 ];
				} );
		
				percent = total ? loaded / total : 0;
		
				spans.eq( 0 ).text( Math.round( percent * 100 ) + '%' );
				spans.eq( 1 ).css( 'width', Math.round( percent * 100 ) + '%' );
				updateStatus();
			}
		
			function updateStatus() {
				var text = '', stats;
		
				if ( state === 'ready' ) {
					text = '选中' + fileCount + '张图片，共' +
							WebUploader.formatSize( fileSize ) + '。';
				} else if ( state === 'confirm' ) {
					stats = uploader.getStats();
					if ( stats.uploadFailNum ) {
						text = '已成功上传' + stats.successNum+ '张照片至XX相册，'+
							stats.uploadFailNum + '张照片上传失败，<a class="retry" href="#">重新上传</a>失败图片或<a class="ignore" href="#">忽略</a>'
					}
		
				} else {
					stats = uploader.getStats();
					text = '共' + fileCount + '张（' +
							WebUploader.formatSize( fileSize )  +
							'），已上传' + stats.successNum + '张';
		
					if ( stats.uploadFailNum ) {
						text += '，失败' + stats.uploadFailNum + '张';
					}
				}
		
				info.html( text );
			}
		
			function setState( val ) {
				var file, stats;
		
				if ( val === state ) {
					return;
				}
		
				upload_btn.removeClass( 'state-' + state );
				upload_btn.addClass( 'state-' + val );
				state = val;
		
				switch ( state ) {
					case 'pedding':
						placeHolder.removeClass( 'element-invisible' );
						queue.parent().removeClass('filled');
						queue.hide();
						statusBar.addClass( 'element-invisible' );
						uploader.refresh();
						break;
		
					case 'ready':
						placeHolder.addClass( 'element-invisible' );
						top_div.find('.filePicker2').removeClass( 'element-invisible');
						queue.parent().addClass('filled');
						queue.show();
						statusBar.removeClass('element-invisible');
						upload_btn.removeClass( 'disabled' );
						uploader.refresh();
						break;
		
					case 'uploading':
						top_div.find('.filePicker2').addClass( 'element-invisible' );
						progress.show();
						upload_btn.text( '暂停上传' );
						break;
		
					case 'paused':
						progress.show();
						upload_btn.text( '继续上传' );
						break;
		
					case 'confirm':
						progress.hide();
						upload_btn.text( '开始上传' ).addClass( 'disabled' );
		
						stats = uploader.getStats();
						if ( stats.successNum && !stats.uploadFailNum ) {
							setState( 'finish' );
							return;
						}
						break;
					case 'finish':
						stats = uploader.getStats();
						if ( stats.successNum ) {
							//上传成功
							statusBar.addClass( 'element-invisible' );
							if(mult){
								placeHolder.removeClass( 'element-invisible' );
							}
						} else {
							// 没有成功的图片，重设
							state = 'done';
							location.reload();
						}
						break;
				}
		
				updateStatus();
			}
		
			uploader.onUploadProgress = function( file, percentage ) {
				var pic_li = top_div.find('#'+file.id);
				var percent = pic_li.find('.progress span');
				percent.css( 'width', percentage * 100 + '%' );
				percentages[ file.id ][ 1 ] = percentage;
				updateTotalProgress();
			};
		
			uploader.onFileQueued = function( file ) {
				fileCount++;
				fileSize += file.size;
		
				if ( fileCount === 1 ) {
					if(!mult){
						placeHolder.addClass( 'element-invisible' );
					}
					statusBar.show();
				}
		
				addFile( file );
				setState( 'ready' );
				updateTotalProgress();
			};
		
			uploader.onFileDequeued = function( file ) {
				fileCount--;
				fileSize -= file.size;
		
				if ( !fileCount ) {
					setState( 'pedding' );
				}
		
				removeFile( file );
				updateTotalProgress();
		
			};
			uploader.on( 'all', function( type ) {
				var stats;
				switch( type ) {
					case 'uploadFinished':
						setState( 'confirm' );
						break;
					case 'startUpload':
						setState( 'uploading' );
						break;
					case 'stopUpload':
						setState( 'paused' );
						break;
				}
			});
			uploader.on( 'uploadSuccess', function( obj, res ) {
				top_div.find('#'+obj.id).attr('data-src', res.id);
				if(top_div.find('.uploader_input').length > 0){
					if($.trim(top_div.find('.uploader_input').val()).length > 0){
						top_div.find('.uploader_input').val($.trim(top_div.find('.uploader_input').val()) + "|" + res.id);
					}else{
						top_div.find('.uploader_input').val(res.id);
					}
				}
			});
			uploader.onError = function( code ) {
				if(code == 'F_DUPLICATE'){
					alert('不可以重复添加！');
					return;
				}
				alert( 'Eroor: ' + code );
			};
		
			upload_btn.on('click', function() {
				if ( $(this).hasClass( 'disabled' ) ) {
					return false;
				}
				if ( state === 'ready' ) {
					uploader.upload();
				} else if ( state === 'paused' ) {
					uploader.upload();
				} else if ( state === 'uploading' ) {
					uploader.stop();
				}
			});
		
			info.on( 'click', '.retry', function() {
				uploader.retry();
			} );
		
			info.on( 'click', '.ignore', function() {
				alert( 'todo' );
			} );
		
			upload_btn.addClass( 'state-' + state );
			updateTotalProgress();

			function removeFromInput(img_src){
				if(top_div.find('.uploader_input').length != 1){return false;}
				var data_arr = top_div.find('.uploader_input').val().split('|');
				var new_data = '';
				for(var i=0;i<data_arr.length;i++){
					if($.trim(data_arr[i]) == img_src){
						continue;
					}
					if(new_data.length > 0){new_data += '|';}
					new_data += $.trim(data_arr[i]);
				}
				top_div.find('.uploader_input').val(new_data);
			}
			function add_init_img(img_src){
				var pic_li = $( '<li style="min-width:30px;min-height:30px;'+auto_size+'width:'+img_width+'px;'+auto_size+'height:'+img_height+'px;" data-src="'+img_src+'">' +
						'<p class="title"></p>' +
						'<p class="imgWrap" style="min-width:30px;min-height:30px;'+auto_size+'width:'+img_width+'px;'+auto_size+'height:'+img_height+'px;"><img src="'+cfg.upload_show+img_src+'"></p>'+
						'<p class="progress"><span></span></p>' +
						'</li>' );
				var btns = $('<div class="file-panel"><span class="cancel">删除</span></div>').appendTo( pic_li );
				var prgress = pic_li.find('p.progress span');
				pic_li.on( 'mouseenter', function() {
					btns.stop().animate({height: 30});
				});
				pic_li.on( 'mouseleave', function() {
					btns.stop().animate({height: 0});
				});
				btns.on( 'click', 'span', function() {
					pic_li.remove();
					if(!mult){
						placeHolder.removeClass( 'element-invisible' );
					}
					removeFromInput(pic_li.attr("data-src"));
				});
				pic_li.appendTo( queue );
			}
			if((ipt_obj = top_div.find('.uploader_input')).length > 0 && ipt_obj.val().length > 0){
				var arr = ipt_obj.val().split('|');
				var imgArr = [];
				for(var i=0;i<arr.length;i++){
					var img_src = $.trim(arr[i]);
					if(img_src.length < 1){
						continue;
					}
					imgArr[imgArr.length] = img_src;
				}
				if(imgArr.length > 0){
					if(!mult){
						placeHolder.addClass( 'element-invisible' );
					}
					for(var i=0;i<imgArr.length;i++){
						add_init_img(imgArr[i]);
					}
				}
			}
		})
	});
})(jQuery);
