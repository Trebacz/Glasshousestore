/**
 * file upload js
 * @since 8.4
 **/

var isCartBlock = false;
var upload_instance = Array();
var file_count = Array();
var $filelist_DIV = Array();

var featherEditor = '';

jQuery(function($){
	
	
    $.each(ppom_file_vars.file_inputs, function(index, file_input){
        
        var file_id = file_input.data_name;
        file_count[file_id] = 0;
    	// delete file
    	$("#nm-uploader-area-"+file_id).on('click','.u_i_c_tools_del', function(e){
    		e.preventDefault();
    
    		// console.log($(this));
    		var del_message = ppom_file_vars.delete_file_msg;
    		var a = confirm(del_message);
    		if(a){
    			// it is removing from uploader instance
    			var fileid = $(this).closest('.u_i_c_box').attr("data-fileid");
    			
    			upload_instance[file_id].removeFile(fileid);
    
    			var filename  = $('input:checkbox[name="thefile_'+file_id+'['+fileid+']"]').val();
    			
    			// it is removing physically if uploaded
    			$("#u_i_c_"+fileid).find('img').attr('src', nm_personalizedproduct_vars.plugin_url+'/images/loading.gif');
    			
    			// console.log('filename thefile_<?php echo $args['id']?>['+fileid+']');
    			var data = {action: 'nm_personalizedproduct_delete_file', file_name: filename};
    			
    			$.post(nm_personalizedproduct_vars.ajaxurl, data, function(resp){
    				alert(resp);
    				$("#u_i_c_"+fileid).hide(500).remove();
    
    				// it is removing for input Holder
    				$('input:checkbox[name="thefile_'+file_id+'['+fileid+']"]').remove();
    				file_count[file_id] -= 1;		
    			});
    		}
    	});
    	
    	$filelist_DIV[file_id] = $('#filelist-'+file_id);
    	
    	upload_instance[file_id] = new plupload.Uploader({
    		runtimes 			: ppom_file_vars.plupload_runtime,
    		browse_button 		: 'selectfiles-'+file_id, // you can pass in id...
    		container			: 'nm-uploader-area-'+file_id, // ... or DOM Element itself
    		drop_element		: 'nm-uploader-area-'+file_id,
    		url 				: nm_personalizedproduct_vars.ajaxurl,
    		multipart_params 	: {'action' : 'nm_personalizedproduct_upload_file', 'settings': file_input},
    		max_file_size 		: file_input.file_size,
    		max_file_count 		: parseInt(file_input.files_allowed),
    	    
    	    chunk_size: '1mb',
    		
    	    // Flash settings
    // 		flash_swf_url 		: nm_personalizedproduct_vars.plugin_url+'/js/plupload-2.1.2/js/Moxie.swf?nocache='+Math.random(),
    		// Silverlight settings
    // 		silverlight_xap_url : nm_personalizedproduct_vars.plugin_url+'/js/plupload-2.1.2/js/Moxie.xap',
    		
    		filters : {
    			mime_types: [
    				{title : "Filetypes", extensions : file_input.file_types}
    			]
    		},
    		
    		init: {
    			PostInit: function() {
    				$filelist_DIV[file_id].html('');
    
    				$('#uploadfiles-'+file_id).bind('click', function() {
    					upload_instance[file_id].start();
    					return false;
    				});
    			},
    
    			FilesAdded: function(up, files) {
    
    				var files_added = files.length;
    				var max_count_error = false;
    
    				if((file_count[file_id] + files_added) > upload_instance[file_id].settings.max_file_count){
    					alert(upload_instance[file_id].settings.max_file_count + nm_personalizedproduct_vars.mesage_max_files_limit);
    				}else{
    					
    					plupload.each(files, function (file) {
    						file_count[file_id]++;
    			    		// Code to add pending file details, if you want
    			            add_thumb_box(file, $filelist_DIV[file_id], up);
    			            setTimeout('upload_instance[\''+file_id+'\'].start()', 100);
    			        });
    				}
    			    
    				
    			},
    			
    			FileUploaded: function(up, file, info){
    				
    				//console.log(info);
    
    				var obj_resp = $.parseJSON(info.response);
    				
    				if(obj_resp.file_name === 'ThumbNotFound'){
    					
    					upload_instance[file_id].removeFile(file.id);
    					$("#u_i_c_"+file.id).hide(500).remove();
    					file_count[file_id]--;	
    					
    					alert('There is some error please try again');
    					return;
    					
    				}else if(obj_resp.status == 'error'){
    					
    					upload_instance[file_id].removeFile(file.id);
    					
    					$("#u_i_c_"+file.id).hide(500).remove();
    
    					file_count[file_id]--;	
    					alert(obj_resp.message);
    					return;
    				};
    				
    				var file_thumb 	= ''; 
    
                    if( file_input.file_cost != "" ) {
                        $('input[name="woo_file_cost"]').val( file_input.file_cost );
                    }
    
    				$filelist_DIV[file_id].find('#u_i_c_' + file.id).html(obj_resp.html);
    
    				
    				// checking if uploaded file is thumb
    				ext = obj_resp.file_name.substring(obj_resp.file_name.lastIndexOf('.') + 1);					
    				ext = ext.toLowerCase();
    				
    				if(ext == 'png' || ext == 'gif' || ext == 'jpg' || ext == 'jpeg'){
    
    					//$filelist_DIV[file_id].html(obj_resp.html);
    					
    					//file_thumb = nm_personalizedproduct_vars.file_upload_path_thumb + obj_resp.file_name + '?nocache='+obj_resp.nocache;
    					//$filelist_DIV[file_id].find('#u_i_c_' + file.id).find('.u_i_c_thumb').html('<img src="'+file_thumb+ '" id="thumb_'+file.id+'" />');
    					
    					var file_full 	= nm_personalizedproduct_vars.file_upload_path + obj_resp.file_name;
    					// thumb thickbox only shown if it is image
    					$filelist_DIV[file_id].find('#u_i_c_' + file.id).find('.u_i_c_thumb').append('<div style="display:none" id="u_i_c_big' + file.id + '"><img src="'+file_full+ '" /></div>');
    
    					// Aviary editing tools
    					if( file_input.photo_editing === 'on' && ppom_file_vars.aviary_api_key !== ''){
    						var editing_tools = file_input.editing_tools;
    						$filelist_DIV[file_id].find('#u_i_c_' + file.id).find('.u_i_c_tools_edit').append('<a onclick="return   (\'thumb_'+file.id+'\', \''+file_full+'\', \''+obj_resp.file_name+'\', \''+editing_tools+'\')" href="javascript:;" title="Edit"><img width="15" src="'+nm_personalizedproduct_vars.plugin_url+'/images/edit.png" /></a>');
    					}
    
    					is_image = true;
    				}else{
    					file_thumb = nm_personalizedproduct_vars.plugin_url+'/images/file.png';
    					$filelist_DIV[file_id].find('#u_i_c_' + file.id).find('.u_i_c_thumb').html('<img src="'+file_thumb+ '" id="thumb_'+file.id+'" />');
    					is_image = false;
    				}
    				
    				// adding checkbox input to Hold uploaded file name as array
    				$filelist_DIV[file_id].append('<input style="display:none" checked="checked" type="checkbox" value="'+obj_resp.file_name+'" name="thefile_'+file_id+'['+file.id+']" />');
    				$('form.cart').unblock();
    				isCartBlock = false;
    			},
    
    			UploadProgress: function(up, file) {
    				//document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
    				//console.log($filelist_DIV[file_id].find('#' + file.id).find('.progress_bar_runner'));
    				$filelist_DIV[file_id].find('#u_i_c_' + file.id).find('.progress_bar_number').html(file.percent + '%');
    				$filelist_DIV[file_id].find('#u_i_c_' + file.id).find('.progress_bar_runner').css({'display':'block', 'width':file.percent + '%'});
    				
    				//disabling add to cart button for a while
    				if( ! isCartBlock ){
    				$('form.cart').block({
    		                    message: null,
    		                    overlayCSS: {
    		                    background: "#fff",
    		                    opacity: .6,
    		                    onBlock: function() { 
    				                isCartBlock = true;
    				            } 
    					                    }
    			         });
    				}
    			},
    
    			Error: function(up, err) {
    				//document.getElementById('console').innerHTML += "\nError #" + err.code + ": " + err.message;
    				alert("\nError #" + err.code + ": " + err.message);
    			}
    		}
    		
    
    	});
    	
    	upload_instance[file_id].init();
    	uploaderInstances[file_id] = upload_instance[file_id];
    	
    	
    	// ==================== If Aviary Editor is Enabled =======================
    	if(ppom_file_vars.aviary_api_key !== '' && file_input.photo_editing == 'on') {
    	    
    	    
            featherEditor = new Aviary.Feather({
                apiKey: ppom_file_vars.aviary_api_key,
                apiVersion: 3,
                theme: 'dark',
                onSave: function(imageID, newURL) {
                    var img = document.getElementById(imageID);
                    img.src = newURL;
                    save_edited_photo(imageID, newURL);
                    featherEditor.close();
                },
                onError: function(errorObj) {
                    alert(errorObj.message);
                }
            });
    	} 
    	
    });         // $.each(ppom_file_vars

	
});	//	jQuery(function($){});

// generate thumbbox 
function add_thumb_box(file, $filelist_DIV){

	var inner_html	= '<div class="u_i_c_thumb"><div class="progress_bar"><span class="progress_bar_runner"></span><span class="progress_bar_number">(' + plupload.formatSize(file.size) + ')<span></div></div>';
	inner_html		+= '<div class="u_i_c_name"><strong>' + file.name + '</strong></div>';
	  
	jQuery( '<div />', {
		'id'	: 'u_i_c_'+file.id,
		'class'	: 'u_i_c_box',
		'data-fileid': file.id,
		'html'	: inner_html,
		
	}).appendTo($filelist_DIV);

	// clearfix
	// 1- removing last clearfix first
	$filelist_DIV.find('.u_i_c_box_clearfix').remove();
	
	jQuery( '<div />', {
		'class'	: 'u_i_c_box_clearfix',				
	}).appendTo($filelist_DIV);
	
}

// internal cropping editor
function launch_crop_editor( id, src, file_name, ratios ){
				
	var w = window.innerWidth * .85;
	var h = window.innerHeight * .85;
	
	
	var uri_string = encodeURI('action=nm_personalizedproduct_crop_image_editor&width='+w+'&height='+h+'&image_url='+src+'&image_name='+file_name+'&file_id='+id+'&ratios='+ratios);
	
	var url = nm_personalizedproduct_vars.ajaxurl + '?' + uri_string;
	tb_show('Crop image', url);
}

// save croped/edited photo
function save_edited_photo(img_id, photo_url){
			
	//console.log(img_id);
	
	//setting new image width to 75
	jQuery('#'+img_id).attr('width', 75);
	
	//disabling add to cart button for a while
	jQuery('form.cart').block({
                message: null,
                overlayCSS: {
                background: "#fff",
                opacity: .6
		                    }
         });
	var post_data = {action: 'nm_personalizedproduct_save_edited_photo', image_url: photo_url,
						filename: jQuery('#'+img_id).attr('data-filename')
	};
	
	jQuery.post(nm_personalizedproduct_vars.ajaxurl, post_data, function(resp) {
	    
	    //console.log( resp );
	    jQuery('form.cart').unblock();
	    
	});
}

function launch_aviary_editor(id, src, file_name, editing_tools, cropping_preset) {
    
    editing_tools = (editing_tools == "" && editing_tools == undefined) ? 'all' : editing_tools;
    featherEditor.launch({
        image: id,
        url: src,
        tools: editing_tools,
        cropPresets: eval(cropping_preset),
        postData: {
            filename: file_name
        },
    });
    return false;
}