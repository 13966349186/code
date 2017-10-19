var Page = function () {

	var $synflag = $("#synflag");
	var $inputs = $(".price-input");

	var initSynCalculate = function(){
	
		if($synflag.is(":checked")){
			$inputs.keyup(synCalculate);
		}
		$synflag.on('change',function(){
			if($synflag.is(":checked")){
				$inputs.keyup(synCalculate);
			}else{
				$inputs.unbind('keyup',synCalculate);
			}
		})
	};

	var synCalculate = function(){
		var price = $(this).val();
		var discount = $(this).data('discount');
		var gold_num = $(this).data('gold-num');
		//勾选了同步修改，则要修改所有记录的价格
		if(gold_num != 0 && discount != 0){
			//计算单个金币的价格
			var price_one = price/gold_num*100/discount;
			$inputs.not(this).val(function(){
				var _price = price_one * $(this).data('gold-num') * $(this).data('discount')/100;
				return _price.toFixed(2);
			});
		}
	}
	
	var initFormValidate = function () {
		var r = {};
		var msg = {};
		$inputs.each(function(){
			var name = $(this).attr('name');
			r[name] = {
			    required: true,
			    number:true,
			    min:0.01,
			    max:99999999
			};
			msg[name] = {
	        	required:"价格不能为空",
	        	number:"价格必须是数字",
	        	min:"价格必须大于 {0}",
	        	max:"价格必须小于 {0}"
	        };
		});

		var form = $('#batchprice')
		form.validate({
			onfocusout:false,
			onkeyup:false,
			doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
	   		errorElement: 'span', //default input error message container
	    	errorClass: 'help-block help-block-error', // default input error message class
	        focusInvalid: false, // do not focus the last invalid input
			rules:r,
			highlight: function (element) { // hightlight error inputs
	            $(element).closest('td').removeClass('has-success').addClass('has-error'); // set error class to the control group
	        },
	        unhighlight: function (element) { // revert the change done by hightlight
	            $(element).closest('td').removeClass('has-error'); // set error class to the control group
	        },
	        messages:msg
		});
    }

    return {
        //main function to initiate the module
        init: function () {
        	initFormValidate();
        	initSynCalculate();
        }
    };
}();