var Order = function(){
	var form = $('#order_frm');
	var nav_define;
	var state_holding;
	var nav_filters_all;

	//问题种类联动
	var stateSelectChanged = function(){
		var state = $("SELECT[name='state']").val();
		if(state == state_holding){
			$("#hold_reason_div").show();
		}else{
			$("#hold_reason_div").hide();
			$("SELECT[name='hold_reason']")[0].selectedIndex = 0;
		}
	}
	/** 文本检索动作 */
	var orderSearch = function(){
		form.find('input[name="stxt"]').val($('#stxt').val());
		form.submit();
	}

	var initProductType = function(){
		$("SELECT[name='game_id']").RelativeDropdown({
			childid: "SELECT[name='product_type']",
			url: "/common/Ajax/GetTypes/{0}",
			empty_option: '',
			init_child_val: $("SELECT[name='product_type']").data('default')
		});
	}

	/** 标签显示 **/
	var initTags = function(){
		var $tags = $("#tags");
		form.find('[name]').each(function(){
			input = $(this);
			if(input.attr('name') == 'create_begin' && input.val() != ''){
				$tags.prepend($('<span class="tag"></span>').text( input.val() + ' 之后' )).append(' ');
			}else if(input.attr('name') == 'create_end' && input.val() != ''){
				$tags.prepend($('<span class="tag"></span>').text( input.val() + ' 之前' )).append(' ');
			}else if(input.attr('name') == 'product_type' && input.data('default') != ''){
				$tags.prepend($('<span class="tag"></span>').text(input.closest('.form-group').find('label').html() + ' = ' + input.data('default-text'))).append(' ');
			}else if(input[0].tagName == 'SELECT' && input.val() != ''){
				$tags.prepend($('<span class="tag"></span>').text( input.closest('.form-group').find('label').html() + ' = ' + input.find("option:selected").text() )).append(' ');
			}else if(input[0].tagName == 'INPUT' && input.attr('type') == 'text' && input.val() != ''){
				$tags.prepend($('<span class="tag"></span>').text( input.closest('.form-group').find('label').html()  + '=' + input.val() )).append(' ');
			}else if(input[0].tagName == 'INPUT' && input.attr('type') == 'checkbox' && input.is(':checked')){
				$tags.prepend($('<span class="tag"></span>').text(input.closest('label').text())).append(' ');
			}
		});
		if( $tags.find('span').size() >1){
			$tags.show();
		}
	}

	$("SELECT[name='state']").on('change',function(){
		stateSelectChanged();
	});

	$('#reset').on('click',function(){
		form.find('[name]').val('');
	});

	$('#search').on('click',function(){
		form.submit();
	})

	$('.search-type').on('click',function(){
		orderSearch();
	})

	$('#stxt').keydown(function(e){
		if(e.keyCode==13){
			orderSearch();
		}
	});

	//navbar点击事件
	$("#tab_head").find("a").click(function(){
		$.each(nav_filters_all, function(key,value){
			form.find("[name='"+value+"']").val('');
		});
  		$.each(nav_define[$(this).text()],function(key,value) {
        	form.find("[name='"+key+"']").val(value);
      	});
      	form.submit();
	});
	
	$("#remove_filter").click(function(){
		form.find('[name]').val('');
		form.submit();
	});



	return {
		_nav_define:'',
		_nav_filters_all:'',
		_state_holding:'',

		init:function(){
			nav_define = this._nav_define;
			nav_filters_all = this._nav_filters_all;
			state_holding = this._state_holding;
			initProductType();
			initTags();
			stateSelectChanged();
		}
	}
}();