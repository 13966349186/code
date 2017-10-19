var report = function(){
    var tableInit = function(table){
	    table.dataTable({
	        // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
	        // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js). 
	        // So when dropdowns used the scrollable div should be removed. 
	        //"dom": "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
			"dom": '<"top">rt<"bottom"flpi><"clear">',
	        "bFilter":false,
	        "bInfo": false,
	        //"ordering":false,
	        "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
	        /*
	        "columns": [{
	            "orderable": true
	        }, {
	            "orderable": true
	        }, {
	            "orderable": false
	        }, {
	            "orderable": true
	        }, {
	            "orderable": true
	        }],
	        */
	        "lengthMenu": [
	            [10, 20, 50, -1],
	            [10, 20, 50, "All"] // change per page values here
	        ],
	        // set the initial value
	        "pageLength": 10,            
	        "pagingType": "simple_numbers",
	        "language": {
	            "lengthMenu": "每页 _MENU_ 行",
	            "paginate": {
	                "previous":"Prev",
	                "next": "Next",
	                "last": "Last",
	                "first": "First"
	            }
	        },
	        /*
	        "columnDefs": [{  // set default column settings
	            'orderable': false,
	            'targets': [0]
	        }],
	        "order": [
	            [0, "asc"]
	        ] // set first column as a default sort by asc
	        */
	    });
    }

    return {
    	init:function(){
    		var table = $('#report_table');
    		tableInit(table);
    	}
    }
}();