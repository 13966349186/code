var Category = function(){
	var $site_id;
	var $parent_id;
	var id;
	var $tree;

	var initTree = function(ajax_url){
		$.jstree.destroy ();
		if($site_id.val() == ''){
			return;
		}
		$.ajax({
			url:  ajax_url + $site_id.val(),
			type: "POST",
			dataType: 'json',
			success: function(msg){
				handleCategoriesTree(msg);
			}
		});
	}

	var handleCategoriesTree = function (data) {
		$tree.jstree({
			'plugins': ["wholerow", "types","sort"],
			'core': {
				check_callback : true,
				"themes" : {
					"responsive": false
				},
				'data': data
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
			$site_id = $("[name='site_id']");
			$parent_id = $("[name='parent_id']");
			id = $("#form_edit").data('category-id');
			$tree = $('#tree_categoies');

			$site_id.on('change',function(){
				initTree(ajaxUrl);
			});
			initTree(ajaxUrl);
		}
	}
}();