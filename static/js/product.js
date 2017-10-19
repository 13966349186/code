var Category = function(){
	
	var handleCategoriesTree = function (data) {
		var $category_id = $("[name='category_id']");
		var $tree = $('#tree_categoies');

		$tree.jstree({
			'plugins': ["wholerow", "types","sort"],
			'core': {
				check_callback : true,
				"themes" : {
					"responsive": false
				},
				'data': data.children
			},
			"types" : {
				"default" : {
					"icon" : "fa fa-folder icon-state-warning icon-lg"
				},
				"game" : {
					"icon" : "fa fa-gamepad icon-state-warning icon-lg"
				}
			}
		});

		$tree.on('select_node.jstree', function(e, data) {
			var selectd = $tree.jstree(true).get_selected ();
			$category_id.val(selectd[0]);
		});

		$tree.on('loaded.jstree', function() {
			$tree.jstree(true).select_node($category_id.val());
		});
	};
	
	return {
		init:function(ajax_url, game_id){
			$.ajax({
				url:  ajax_url + game_id,
				type: "POST",
				dataType: 'json',
				success: function(msg){
					handleCategoriesTree(msg);
				}
			});
		}
	}
}();



