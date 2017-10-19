<?php
$config['order_states_css'] = Array(
		'0'=>'order_unpaid',
		'1'=>'order_open',
		'2'=>'order_holding',
		'3'=>'order_close',
		'4'=>'order_cancel'
);
$config['order_states_label_css'] = Array(
		'0'=>'badge badge-default',
		'1'=>'badge badge-success',
		'2'=>'badge badge-warning',
		'3'=>'badge badge-default',
		'4'=>'order_cancel'
);
$config['order_log_type_css'] = Array(
		'0'=>'write',
		'1'=>'payment',
		'2'=>'verification',
		'3'=>'delivery',
		'4'=>'order_edit'
);
$config['pay_states_css'] = Array(
		'0'=>'badge grey-cascade badge-roundless no_pay',
		'1'=>'badge grey-cascade badge-roundless h_paying',
		'2'=>'badge grey-cascade badge-roundless part_pay',
		'3'=>'badge grey-cascade badge-roundless h_paid',
		'4'=>'badge grey-cascade badge-roundless refund',
		'5'=>'badge grey-cascade badge-roundless frozen'
);
$config['delivery_states_css'] = Array(
		'0'=>'badge grey-cascade badge-roundless no_delivery',
		'1'=>'badge grey-cascade badge-roundless part_delivery',
		'2'=>'badge grey-cascade badge-roundless to_delivery'
);
$config['risk_states_css'] = Array(
		'0'=>'fa fa-check-circle level_safe',
		'1'=>'fa fa-question-circle level_middle',
		'2'=>'fa fa-exclamation-circle danger_level',
		'3'=>'fa fa-times-circle danger_level'
);
$config['payment_state_css'] = Array(
		'0'=>'text-info',
		'1'=>'text-success',
		'-1'=>'text-default',
);
$config['payment_type_css'] = Array(
		'1'=>' text-success',
		'2'=>' text-danger',
		'3'=>' text-warning'
);
$config['product_state_css'] = Array(
		'0'=>'label label-danger status_icons label_status_icons',
		'1'=>'label label-success status_icons label_status_icons',
		'2'=>'label label-info status_icons label_status_icons',
);