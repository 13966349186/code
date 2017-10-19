<div class="modal-body">
	<div class="portlet">
		<div class="portlet-title"><div class="caption"><i class="fa fa-shopping-cart"></i>发货信息</div></div>
		<!-- 发货信息开始 -->
		<div class="portlet-body">
			<div class="row">
				<div class="col-xs-8">
					<div class="well">
					<?php foreach ($attrs as $v){?>
						<div class="row static-info align-reverse">
							<div class="col-xs-4 name">
								<?=htmlspecialchars($v->name)?>:
							</div>
							<div class="col-xs-8" style="word-break:break-all;">
								<?=htmlspecialchars($v->value)?>
							</div>
						</div>
					<?php }?>
					</div>
				</div>
			</div>
		</div>
		<!--发货信息结束 -->
		<div class="portlet-title">
			<div class="caption">
				<i class="fa fa-shopping-cart"></i>商品信息
			</div>
		</div>
		<!-- 商品信息开始 -->
		<div class="portlet-body">
			<div class="row">
				<div class="col-xs-12">
					<div class="table-scrollable table_color">
						<table class="table table-hover table-bordered table-striped about_table ">
							<thead>
								<tr>
									<th>游戏</th>
									<th>商品类型</th>
									<th>目录</th>
									<th>商品</th>
									<th>价格</th>
									<th>数量</th>
									<th>总价</th>
									<th width="85px;">状态</th>
								</tr>
							</thead>
							<tbody>
							<?php
							$deliveryStatesCls = $this->config->item('delivery_states_css');
							$deliveryStates = $this->MOrder->getDeliveryState();
							?>
								<tr>
									<td><?=htmlspecialchars($obj->game_name)?></td>
									<td><?=htmlspecialchars($obj->type_name)?></td>
									<td><?=htmlspecialchars($obj->category_name)?></td>
									<td><?=htmlspecialchars($obj->name)?></td>
									<td><?=htmlspecialchars($obj->price)?></td>
									<td><?=htmlspecialchars($obj->num)?></td>
									<td><?=htmlspecialchars($obj->price*$obj->num)?></td>
									<td><span class="<?=element($obj->delivery_state, $deliveryStatesCls, '')?>"><?=element($obj->delivery_state, $deliveryStates, '--')?></span></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<!-- 商品信息结束 -->
	</div>
</div>
<!-- 模态框footer部分 -->
<div class="modal-footer">
	<?=form_open(site_url($thisModule . $thisController . '/edit/' . $obj->id), Array('class'=>'form-horizontal', 'role'=>'form'))?> 
		<input type="hidden" id="update_time" name="update_time" value="<?=set_value('update_time', $obj->update_time)?>" />
		<div class="form-inline">
			<select class="table-group-action-input form-control input-medium" name="delivery_state"  <?=$read_only?'disabled="disabled"':'' ?>>
			<?php foreach ($deliveryStates as $k=>$v){?>
				<option value="<?=$k?>" <?=set_select('delivery_state', $k, $k==$obj->delivery_state)?>><?=htmlspecialchars($v)?></option>
			<?php }?>
			</select>
			<?php
			$confirmTip = '';
			if($order->payment_state == MOrder::PAY_STATE_REVERSED){
				$confirmTip = '该订单处于付款冻结状态，确定要更改发货状态吗？';
			}else if($order->payment_state == MOrder::PAY_STATE_REFUNDED){
				$confirmTip = '该订单已经退款，确定要更改发货状态吗？';
			}else if($order->payment_state == MOrder::PAY_STATE_UNPAIED){
				$confirmTip = '该订单尚未付款，确定要更改发货状态吗？';
			}
			?>
			<button type="submit" <?php if($confirmTip){?>onclick="return confirm('<?=$confirmTip?>');"<?php }?> class="btn green <?=$read_only?'disabled':'' ?>">提交</button>
		</div>
	<?=form_close()?> 
</div>

