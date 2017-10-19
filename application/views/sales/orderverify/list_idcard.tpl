<div class="modal-body" id="list_idcard_id">
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
									<th style="width:15%;">证件附件</th>
									<th>备注</th>
								</tr>
							</thead>
							<tbody>
							<?php foreach ($lst as $v){
								$arr = explode('|', $v->verify_value);
								$idx = 1;
								$pre_url = $this->config->item('upload_show');
							?>
								<tr>
									<td style="text-align:left;"><?=htmlspecialchars($v->no)?></td>
									<td style="text-align:left;"><?=date('Y-m-d H:i:s', $v->create_time)?></td>
									<td style="text-align:left;"><?=htmlspecialchars($v->admin)?></td>
									<td style="text-align:left;">
									<?php foreach ($arr as $url){?>
										<?php if(strlen(trim($url)) < 1){continue;}?>
										<a href="<?=$pre_url.$url?>" data-url="<?=$pre_url.$url?>" target="_blank">附件<?=$idx++?></a>
									<?php }?>
									</td>
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
<script>
//$('#list_idcard_id').find('a').imgBox();
</script>