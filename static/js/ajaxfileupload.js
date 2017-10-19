
jQuery.extend({
	

    createUploadIframe: function(id, uri)
	{
			//create frame
            var frameId = 'jUploadFrame' + id;
            
            if(window.ActiveXObject) {
                var io = document.createElement('<iframe id="' + frameId + '" name="' + frameId + '" />');
                if(typeof uri== 'boolean'){
                    io.src = 'javascript:false';
                }
                else if(typeof uri== 'string'){
                    io.src = uri;
                }
            }
            else {
                var io = document.createElement('iframe');
                io.id = frameId;
                io.name = frameId;
            }
            io.style.position = 'absolute';
            io.style.top = '-1000px';
            io.style.left = '-1000px';

            document.body.appendChild(io);

            return io			
    },
    createUploadForm: function(id, fileElementId, pars)
	{
		//create form	
		var formId = 'jUploadForm' + id;
		var fileId = 'jUploadFile' + id;
		var form = $('<form  action="" method="POST" name="' + formId + '" id="' + formId + '" enctype="multipart/form-data"></form>');
		for(prop in pars){
			var tmpIpt = $('<input type="hidden" name="'+prop+'" value="" />');
			tmpIpt.val(pars[prop]);
			form.append(tmpIpt);
		}
		var oldElement = $('#' + fileElementId);
		var newElement = $(oldElement).clone();
		$(oldElement).attr('id', fileId);
		$(oldElement).before(newElement);
		$(oldElement).appendTo(form);
		//set attributes
		$(form).css('position', 'absolute');
		$(form).css('top', '-1200px');
		$(form).css('left', '-1200px');
		$(form).appendTo('body');		
		return form;
    },

    ajaxFileUpload: function(s) {
        // TODO introduce global settings, allowing the client to modify them for all requests, not only timeout		
        s = jQuery.extend({}, jQuery.ajaxSettings, s);
        var pars = (('params' in s)?s.params:{});
        var id = new Date().getTime()        
		var form = jQuery.createUploadForm(id, s.fileElementId, pars);
		var io = jQuery.createUploadIframe(id, s.secureuri);
		var frameId = 'jUploadFrame' + id;
		var formId = 'jUploadForm' + id;		
		// Watch for a new set of requests
        if ( s.global && ! jQuery.active++ )
		{
			jQuery.event.trigger( "ajaxStart" );
		}            
        var requestDone = false;
        // Create the request object
        var xml = {}   
        if ( s.global )
            jQuery.event.trigger("ajaxSend", [xml, s]);
        // Wait for a response to come back
        var uploadCallback = function(isTimeout)
		{			
			var io = document.getElementById(frameId);
            try 
			{				
				if(io.contentWindow)
				{
					 xml.responseText = io.contentWindow.document.body?io.contentWindow.document.body.innerHTML:null;
                	 xml.responseXML = io.contentWindow.document.XMLDocument?io.contentWindow.document.XMLDocument:io.contentWindow.document;
					 
				}else if(io.contentDocument)
				{
					 xml.responseText = io.contentDocument.document.body?io.contentDocument.document.body.innerHTML:null;
                	xml.responseXML = io.contentDocument.document.XMLDocument?io.contentDocument.document.XMLDocument:io.contentDocument.document;
				}						
            }catch(e)
			{
				jQuery.handleError(s, xml, null, e);
			}
            if ( xml || isTimeout == "timeout") 
			{				
                requestDone = true;
                var status;
                try {
                    status = isTimeout != "timeout" ? "success" : "error";
                    // Make sure that the request was successful or notmodified
                    if ( status != "error" )
					{
                        // process the data (runs the xml through httpData regardless of callback)
                        var data = jQuery.uploadHttpData( xml, s.dataType );    
                        // If a local callback was specified, fire it and pass it the data
                        if ( s.success )
                            s.success( data, status );
    
                        // Fire the global callback
                        if( s.global )
                            jQuery.event.trigger( "ajaxSuccess", [xml, s] );
                    } else
                        jQuery.handleError(s, xml, status);
                } catch(e) 
				{
                    status = "error";
                    jQuery.handleError(s, xml, status, e);
                }

                // The request was completed
                if( s.global )
                    jQuery.event.trigger( "ajaxComplete", [xml, s] );

                // Handle the global AJAX counter
                if ( s.global && ! --jQuery.active )
                    jQuery.event.trigger( "ajaxStop" );

                // Process result
                if ( s.complete )
                    s.complete(xml, status);

                jQuery(io).unbind()

                setTimeout(function()
									{	try 
										{
											$(io).remove();
											$(form).remove();	
											
										} catch(e) 
										{
											jQuery.handleError(s, xml, null, e);
										}									

									}, 100)

                xml = null

            }
        }
        // Timeout checker
        if ( s.timeout > 0 ) 
		{
            setTimeout(function(){
                // Check to see if the request is still happening
                if( !requestDone ) uploadCallback( "timeout" );
            }, s.timeout);
        }
        try 
		{
           // var io = $('#' + frameId);
			var form = $('#' + formId);
			$(form).attr('action', s.url);
			$(form).attr('method', 'POST');
			$(form).attr('target', frameId);
            if(form.encoding)
			{
                form.encoding = 'multipart/form-data';				
            }
            else
			{				
                form.enctype = 'multipart/form-data';
            }			
            $(form).submit();

        } catch(e) 
		{			
            jQuery.handleError(s, xml, null, e);
        }
        if(window.attachEvent){
            document.getElementById(frameId).attachEvent('onload', uploadCallback);
        }
        else{
            document.getElementById(frameId).addEventListener('load', uploadCallback, false);
        } 		
        return {abort: function () {}};	

    },

    uploadHttpData: function( r, type ) {
        var data = !type;
        data = type == "xml" || data ? r.responseXML : r.responseText;
        // If the type is "script", eval it in global context
        if ( type == "script" )
            jQuery.globalEval( data );
        // Get the JavaScript object, if JSON is used.
        if ( type == "json" )
            eval( "data = " + data );
        // evaluate scripts within html
        if ( type == "html" )
            jQuery("<div>").html(data).evalScripts();
			//alert($('param', data).each(function(){alert($(this).attr('value'));}));
        return data;
    }
})



var AjaxUploader = function(){
	return {
		uploadTempUrl:'',

		init:function(){
			var tempUrl = this.uploadTempUrl;

			$('div.ajax-uploader').each(function(){
				var $uploader = $(this);
				if($uploader.hasClass('__inited')){
					return;  //防止重复初始化
				}else{
					$uploader.addClass('__inited');
				}
				var $preview = $('<div></div>').appendTo($uploader);
				var $fileWrap = $('<div></div>').appendTo($uploader);
				var isMult = $uploader.hasClass('mult');
				var name = $uploader.attr('id');
				
				//初始化图片预览区域
				$uploader.find('input[type=hidden]').each(function(){
					if($(this).val() != ''){
						addFile($(this).val());
					}
				});
				setFileWarp();
				$preview.on('changed', function(){
					setInput();
					setFileWarp();
				});
				
				//设置hidden域
				function setInput(){
					//console.log(' setInput');
					if(isMult){
						$uploader.find('input[name=' + name + ']').remove();
						$preview.children('div').each(function(){
							$('<input type="hidden" name="' + name + '[]" value="' + $(this).data('path') + '">').appendTo($uploader);
						});
					}else{
						$uploader.find('input[name='+ name + ']').val($preview.children('div').data('path'));
					}
				}
				
				//设置文件上传组件
				function setFileWarp(){
					//console.log(' setFileWarp');
					$fileWrap.empty();
					if(!isMult && $uploader.find('input[type=hidden]').val() != ''){
						return;
					}
					var fileID = name + "-file-id"; 
					var $file = $('<input type="file"  name="'+ fileID +'" id="'+ fileID + '"  value=""  style="opacity: 0; position: absolute; width:120px">').on('change', function(){
						if( $(this).val() != ''){
							uploadToTemp(fileID);
						}
					});
					$('<span class="btn blue"><i class="fa fa-plus"></i><span > 选择文件 ... </span></span>').prepend($file).appendTo($fileWrap);
				}
				
				//在图片预览区域增加图片
				function addFile(path){
					//console.log("addFile:" + path);
					var $span = $('<div class="ajax-uploader-image" data-path="' + path + '"><img src="/' + path + '" style="max-width:120px;max-height:90px; margin:5px 15px 5px 0; "></div>');
					$('<button type="button" class="btn delete"><i class="fa fa-trash"></i><span> 删除 </span></button>').on('click',function(){
						$span.remove();
						$preview.trigger('changed');
					}).appendTo($span);
					$span.appendTo($preview);
					$preview.trigger('changed');
				}
				
				//上传文件
				function uploadToTemp(fileID){
					//console.log(fileID);
					$.ajaxFileUpload(
						{
							url: tempUrl + fileID,
							secureuri:false,
							fileElementId:fileID,
							dataType: 'json',
							success: function (data, status)
							{
								if(typeof(data.error) != 'undefined' && data.error != ''){
									alert(data.error);
								}else{
									addFile(data.path);
								}
							},error: function (data, status, e)	{alert(e);}
						}
					)
					return false;
				}
			});
		}
	}
}();
