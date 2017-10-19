var Category = function(){
	var $game_id;
	var $parent_id;
	var id;
	var $tree;

	var initTree = function(ajax_url){
		$.jstree.destroy ();
		if($game_id.val() == ''){
			return;
		}
		$.ajax({
			url:  ajax_url + $game_id.val(),
			type: "POST",
			dataType: 'json',
			success: function(msg){
				handleCategoriesTree(msg);
			}
		});
	}

	var handleCategoriesTree = function (data) {
		$tree.jstree({
			'plugins': ["wholerow", "types"],
			'core': {
				check_callback : true,
				"themes" : {
					"responsive": false
				},
				'data': data
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
			$parent_id.val(selectd[0]);
		});

		$tree.on('loaded.jstree', function() {
			$tree.jstree(true).select_node($parent_id.val());
			var self_node =  $tree.jstree(true).get_node (id);
			$tree.jstree(true).disable_node(self_node);
			$tree.jstree(true).delete_node(self_node.children);
		});
	};

	return {
		init:function(ajaxUrl){
			$game_id = $("[name='game_id']");
			$parent_id = $("[name='parent_id']");
			id = $("#form_edit").data('category-id');
			$tree = $('#tree_categoies');

			$game_id.on('change',function(){
				initTree(ajaxUrl);
			});
			initTree(ajaxUrl);
		}
	}
}();



