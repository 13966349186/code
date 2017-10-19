<div class="modal-body">
	<div class="portlet">
		<div class="portlet-body">
			<div class="row">
				<div class="col-xs-12">
					<div class="table-scrollable table_color">
						<table class="table table-hover table-bordered table-striped about_table ">
							<thead>
								<tr>
									<th style="width:21%;">订单号</th>
									<th style="width:17%;">添加时间</th>
									<th style="width:15%;">操作人员</th>
									<th style="width:15%;">电话</th>
									<th>备注</th>
								</tr>
							</thead>
							<tbody>
							<?php foreach ($lst as $v){?>
								<tr>
									<td style="text-align:left;"><?=htmlspecialchars($v->no)?></td>
									<td style="text-align:left;"><?=date('Y-m-d H:i:s', $v->create_time)?></td>
									<td style="text-align:left;"><?=htmlspecialchars($v->admin)?></td>
									<td style="text-align:left;"><?=htmlspecialchars($v->verify_value)?></td>
									<td style="text-align:left;word-break:break-all;"><?=htmlspecialchars($v->note)?></td>
								</tr>
							<?php }?>
							</tbody>
						</table>
					</div>
					<div class="col-xs-12 text-right">
						<?=$pagination?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
