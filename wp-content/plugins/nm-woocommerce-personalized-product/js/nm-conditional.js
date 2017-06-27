var boxes		= new Array();	//checking bound connection


jQuery(function($){
   
   //conditional elements handling
	$(".nm-productmeta-box").on('change', 'select, input[type="radio"]', function(e){
		
		var element_name 	= $(this).attr("name");
		var element_value	= '';
		var element_type	= $(this).attr("data-type");
		var this_selector	= $(this);
		
		
		if($(this).attr('data-type') === 'radio'){
			element_value	= $(this).filter(':checked').val();
		}else if($(this).attr('data-type') === 'image'){
			element_value	= $.parseJSON($(this).val());
			element_value 	= element_value.title;
		}else{
			element_value	= $(this).val();
		}
		
		// console.log(element_name);
		// console.log( 'changed_element_val '+element_value );
		
		$(".nm-productmeta-box div, .nm-productmeta-box div.fileupload-box").each(function(i, p_box){

			var parsed_conditions 	= $.parseJSON ($(p_box).attr('data-rules'));
			var box_id				= $(p_box).attr('id');
			var element_box = new Array();
			// console.log( parsed_conditions );
			
			if(parsed_conditions !== null){
			
				
				var _visiblity		= parsed_conditions.visibility;
				var _bound			= parsed_conditions.bound;
				var _total_rules 	= Object.keys(parsed_conditions.rules).length;
				
				 var matched_rules = {};
				 var last_meched_element = '';
				$.each(parsed_conditions.rules, function(i, rule){
					
					var _element 		= rule.elements;
					var _elementvalues	= rule.element_values;
					var _operator 		= rule.operators;
					
					//console.log('_element ='+_element+' element_name ='+element_name);
					var matched_rules = {};	
					
					if(_element === element_name && last_meched_element !== _element){
						
						
						var temp_matched_rules = {};
						
						switch(_operator){
						
							case 'is':
								
								if(_elementvalues === element_value){
									
									last_meched_element = element_name;
									
									if(boxes[box_id]){
					                    jQuery.each(boxes[box_id], function(j, matched){
					                        if(matched !== undefined){
					                            jQuery.each(matched, function(k,v){
					                            	if(k !== _element){
					                            		temp_matched_rules[k]=v;
						                                element_box.push(temp_matched_rules);
					                            	}
					                            });
					                        }
					                    });
					                }
									
									matched_rules[_element]=element_value;
					                element_box.push(matched_rules);
					                boxes[box_id] = element_box;
					       
								}else{
									
									remove_existing_rules(boxes[box_id], _element);
									//reset value if set before
									jQuery('#'+box_id).find(':input').not(':checkbox, :radio').val('');
									jQuery('#'+box_id).find(':input','select').removeAttr('checked').removeAttr('selected');
									jQuery('#'+box_id).find('select input[type="radio"]').change();
									
									
								}		
								break;
								
								
							case 'not':
								
								if(_elementvalues !== element_value){
									
									if(boxes[box_id]){
					                    jQuery.each(boxes[box_id], function(j, matched){
					                        if(matched !== undefined){
					                            jQuery.each(matched, function(k,v){
					                            	if(k !== _element){
					                            		temp_matched_rules[k]=v;
						                                element_box.push(temp_matched_rules);
					                            	}
					                            });
					                        }
					                    });
					                }
									
									matched_rules[_element]=element_value;
					                element_box.push(matched_rules);
					                boxes[box_id] = element_box;
								}else{
									
									remove_existing_rules(boxes[box_id], _element);
									jQuery('#'+box_id).find(':input').not(':checkbox, :radio').val('');
									 jQuery('#'+box_id).find(':input','select').removeAttr('checked').removeAttr('selected');
									 jQuery('#'+box_id).find('select, input[type="radio"]').change();
									
								}		
								break;
								
								
								case 'greater than':
									
									if(parseFloat(_elementvalues) < parseFloat(element_value) ){
										
										if(boxes[box_id]){
						                    jQuery.each(boxes[box_id], function(j, matched){
						                        if(matched !== undefined){
						                            jQuery.each(matched, function(k,v){
						                            	if(k !== _element){
						                            		temp_matched_rules[k]=v;
							                                element_box.push(temp_matched_rules);
						                            	}
						                            });
						                        }
						                    });
						                }
										
										matched_rules[_element]=element_value;
						                element_box.push(matched_rules);
						                boxes[box_id] = element_box;
									}else{
										
										remove_existing_rules(boxes[box_id], _element);
										jQuery('#'+box_id).find(':input').not(':checkbox, :radio').val('');
									 	jQuery('#'+box_id).find(':input','select').removeAttr('checked').removeAttr('selected');
									 	jQuery('#'+box_id).find('select, input[type="radio"]').change();
										
									}		
									break;
									
								
								case 'less than':
									
									if(parseFloat(_elementvalues) > parseFloat(element_value) ){
										
										if(boxes[box_id]){
						                    jQuery.each(boxes[box_id], function(j, matched){
						                        if(matched !== undefined){
						                            jQuery.each(matched, function(k,v){
						                            	if(k !== _element){
						                            		temp_matched_rules[k]=v;
							                                element_box.push(temp_matched_rules);
						                            	}
						                            });
						                        }
						                    });
						                }
										
										matched_rules[_element]=element_value;
						                element_box.push(matched_rules);
						                boxes[box_id] = element_box;
									}else{
										
										remove_existing_rules(boxes[box_id], _element);
										jQuery('#'+box_id).find(':input').not(':checkbox, :radio').val('');
										jQuery('#'+box_id).find(':input','select').removeAttr('checked').removeAttr('selected');
									 	jQuery('#'+box_id).find('select, input[type="radio"]').change();
										
									}		
									break;
									}
						
						set_visibility(p_box, _bound, _total_rules, _visiblity);
					}
					
				});
				
			}
			
			
		});
		
	});
	
	// for checkbox
	$(".nm-productmeta-box").on('change', 'input[type="checkbox"]', function(e){
		
		var element_name 	= '';
		var element_value	= '';
		var element_type	= $(this).attr("data-type");
		var this_selector	= $(this);
		
		console.log('======== START =========');
		console.log(this_selector.closest('div').find('input:checkbox:checked').length);
		
		// var element_name 	= $(this).attr("data-dataname");
		// element_value	= $(this).filter(':checked').val();
		
		// console.log(element_name);
		// console.log( 'changed_element_val '+element_value );
		
		$(".nm-productmeta-box div, .nm-productmeta-box div.fileupload-box").each(function(i, p_box){

			var parsed_conditions 	= $.parseJSON ($(p_box).attr('data-rules'));
			var box_id				= $(p_box).attr('id');
			var element_box = new Array();
			// console.log( parsed_conditions );
			
			if(parsed_conditions !== null){
			
				
				var _visiblity		= parsed_conditions.visibility;
				var _bound			= parsed_conditions.bound;
				var _total_rules 	= Object.keys(parsed_conditions.rules).length;
				
				 var matched_rules = {};
				 var last_meched_element = '';
				 
				$.each(this_selector.closest('div').find('input:checkbox'), function(i, cb){
					
					element_name 	= $(this).attr("data-dataname");
					element_value		= $(this).val();
					var element_id		= $(this).attr("id");
					var $CB				= $(this);
					
				$.each(parsed_conditions.rules, function(i, rule){
					
					var _element 		= rule.elements;
					var _elementvalues	= rule.element_values;
					var _operator 		= rule.operators;
					
					console.log('_element ='+_element+' element_name ='+element_name);
					var matched_rules = {};	
					
					if(_element === element_name && last_meched_element !== _element){
						
						if(_elementvalues !== element_value
						&& this_selector.closest('div').find('input:checkbox:checked').length > 0) {
							return;
						}
						
						var temp_matched_rules = {};
						
						switch(_operator){
						
							case 'is':
								
								if(_elementvalues === element_value && $CB.is(':checked')){
									
									last_meched_element = element_name;
									
									if(boxes[box_id]){
					                    jQuery.each(boxes[box_id], function(j, matched){
					                    	console.log(matched);
					                        if(matched !== undefined){
					                            jQuery.each(matched, function(k,v){
					                            	if(k !== element_id){
					                            		temp_matched_rules[k]=v;
						                                element_box.push(temp_matched_rules);
					                            	}
					                            });
					                        }
					                    });
					                }
									
									matched_rules[element_id]=element_value;
					                element_box.push(matched_rules);
					                boxes[box_id] = element_box;
					                // console.log(element_box);
								}else{
									// console.log('removing '+element_id);
									remove_existing_rules(boxes[box_id], element_id);
									//reset value if set before
									jQuery('#'+box_id).find(':input').not(':checkbox, :radio').val('');
									jQuery('#'+box_id).find(':input','select').removeAttr('checked').removeAttr('selected');
								 	jQuery('#'+box_id).find('select, input[type="checkbox"], input[type="radio"]').change();
								
									
								}		
								break;
								
								
							case 'not':
								
								if(_elementvalues !== element_value && $CB.is(':checked')){
									
									if(boxes[box_id]){
					                    jQuery.each(boxes[box_id], function(j, matched){
					                        if(matched !== undefined){
					                            jQuery.each(matched, function(k,v){
					                            	if(k !== element_id){
					                            		temp_matched_rules[k]=v;
						                                element_box.push(temp_matched_rules);
					                            	}
					                            });
					                        }
					                    });
					                }
									
									matched_rules[element_id]=element_value;
					                element_box.push(matched_rules);
					                boxes[box_id] = element_box;
								}else{
									
									remove_existing_rules(boxes[box_id], element_id);
									jQuery('#'+box_id).find(':input').not(':checkbox, :radio').val('');
									 jQuery('#'+box_id).find(':input','select').removeAttr('checked').removeAttr('selected');
									 jQuery('#'+box_id).find('select, input[type="checkbox"], input[type="radio"]').change();
									
								}		
								break;
								
								
								case 'greater than':
									
									if(parseFloat(_elementvalues) < parseFloat(element_value) && $CB.is(':checked')){
										
										if(boxes[box_id]){
						                    jQuery.each(boxes[box_id], function(j, matched){
						                        if(matched !== undefined){
						                            jQuery.each(matched, function(k,v){
						                            	if(k !== element_id){
						                            		temp_matched_rules[k]=v;
							                                element_box.push(temp_matched_rules);
						                            	}
						                            });
						                        }
						                    });
						                }
										
										matched_rules[element_id]=element_value;
						                element_box.push(matched_rules);
						                boxes[box_id] = element_box;
									}else{
										
										remove_existing_rules(boxes[box_id], element_id);
										jQuery('#'+box_id).find(':input').not(':checkbox, :radio').val('');
									 	jQuery('#'+box_id).find(':input','select').removeAttr('checked').removeAttr('selected');
									 	jQuery('#'+box_id).find('select, input[type="checkbox"], input[type="radio"]').change();
										
									}		
									break;
									
								
								case 'less than':
									
									if(parseFloat(_elementvalues) > parseFloat(element_value) && $CB.is(':checked')){
										
										if(boxes[box_id]){
						                    jQuery.each(boxes[box_id], function(j, matched){
						                        if(matched !== undefined){
						                            jQuery.each(matched, function(k,v){
						                            	if(k !== element_id){
						                            		temp_matched_rules[k]=v;
							                                element_box.push(temp_matched_rules);
						                            	}
						                            });
						                        }
						                    });
						                }
										
										matched_rules[element_id]=element_value;
						                element_box.push(matched_rules);
						                boxes[box_id] = element_box;
									}else{
										
										remove_existing_rules(boxes[box_id], element_id);
										jQuery('#'+box_id).find(':input').not(':checkbox, :radio').val('');
										jQuery('#'+box_id).find(':input','select').removeAttr('checked').removeAttr('selected');
									 	jQuery('#'+box_id).find('select, input[type="checkbox"], input[type="radio"]').change();
										
									}		
									break;
									}
						
						set_visibility(p_box, _bound, _total_rules, _visiblity);
					}
					
				});
				
			});
				
			}
			
			
		});
		
	});
	
	
	function set_visibility(p_box, _bound, _total_rules, _visiblity){
	
		var box_id				= jQuery(p_box).attr('id');
		if(boxes[box_id] !== undefined){
			
			console.log(box_id+': total rules = '+_total_rules+' rules matched = '+Object.keys(boxes[box_id]).length);
			switch(_visiblity){
			
			case 'Show':
				if((_bound === 'Any' &&  (Object.keys(boxes[box_id]).length > 0)) || _total_rules === Object.keys(boxes[box_id]).length){
					jQuery(p_box).show(200, function(){
						var inner_input = jQuery(p_box).find('input');
						var hidden_name = '_'+inner_input.attr('id')+'_';
						jQuery('input:hidden[name="'+hidden_name+'"]').remove();
						inner_input.after('<input type="hidden" name="'+hidden_name+'" value="showing" />');
						
						// ios fix for fileuploader instace, Najeeb did on May 9, 2016
						if( jQuery(this).attr('class') === 'fileupload-box' ){
							
							//uploader instance
							if ( uploaderInstances[jQuery(p_box).attr('data-dataname')] !== undefined ){
								uploaderInstances[jQuery(p_box).attr('data-dataname')].refresh();
							}
							
						}
					});
					
				}else{
					jQuery(p_box).hide(200, function(){
						var inner_input = jQuery(p_box).find('input');
						var hidden_name = '_'+inner_input.attr('id')+'_';
						jQuery('input:hidden[name="'+hidden_name+'"]').remove();
						inner_input.after('<input type="hidden" name="'+hidden_name+'" value="hidden" />');
					});
				}
				break;					
			
			case 'Hide':
				if((_bound === 'Any' &&  (Object.keys(boxes[box_id]).length > 0)) || _total_rules === Object.keys(boxes[box_id]).length){
					jQuery(p_box).hide(200, function(){
						jQuery(p_box).find('select, input:radio, input:text, textarea').val('');
						var inner_input = jQuery(p_box).find('input');
						var hidden_name = '_'+inner_input.attr('id')+'_';
						jQuery('input:hidden[name="'+hidden_name+'"]').remove();
						inner_input.after('<input type="hidden" name="'+hidden_name+'" value="hidden" />');
					});
					// console.log('hiddedn rule '+box_id);
					
				}else{
					jQuery(p_box).show(200, function(){
						var inner_input = jQuery(p_box).find('input');
						var hidden_name = '_'+inner_input.attr('id')+'_';
						jQuery('input:hidden[name="'+hidden_name+'"]').remove();
						inner_input.after('<input type="hidden" name="'+hidden_name+'" value="showing" />');
						
						// ios fix for fileuploader instace, Najeeb did on May 9, 2016
						if( jQuery(this).attr('class') === 'fileupload-box' ){
							
							//uploader instance
							if ( uploaderInstances[jQuery(p_box).attr('data-dataname')] !== undefined ){
								uploaderInstances[jQuery(p_box).attr('data-dataname')].refresh();
							}
							
						}
					});
					
				}
				break;
		}
		}
	}
});