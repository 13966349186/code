<div class="portlet-body form" id="order_hold_frm">
	<?=form_open('', Array('class'=>'form-horizontal', 'role'=>'form'))?>
		<div class="modal-body" style="border-bottom:1px solid #EFEFEF;">
			<div class="form-body">
				<div class="row">
					<div class="col-xs-12">订单号：<?=htmlspecialchars($order->no)?>
					</div>
				</div>
				<div class="form-group<?=form_error('hold_reason')?' has-error':''?>" style="margin:10px 0px;">
				<?php $reasons = $this->config->item('hold_reason'); ?>
				<?php if($order->state == MOrder::STATE_HOLDING || $state == MOrder::STATE_HOLDING){?>
					<div class="radio-list">
					<?php foreach ($reasons as $k=>$v){?>
						<label class="radio-inline"><input type="radio" name="hold_reason" value="<?=$k?>" <?=set_radio('hold_reason', $k, $k.''==$order->hold_reason.'')?> <?=($state == MOrder::STATE_HOLDING)?'':'disabled="disabled"' ?>> <?=$v?></label>
					<?php }?>
					</div>
					<?=form_error('hold_reason', '<p class="help-block">', '</p>')?>
				<?php }?>
				</div>
				<div class="form-group<?=form_error('note')?' has-error':''?>">
					<div class="col-xs-12">
						<textarea class="form-control" name="note" rows="3"><?=set_value('note')?></textarea>
						<?=form_error('note', '<p class="help-block">', '</p>')?>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="col-xs-12">
				<div class="form-group modal-footer-style pull-right">
					<button type="button" class="btn default" data-dismiss="modal">取消</button>
					<button type="submit" class="btn green"><?=$thisControllerName?></button>
				</div>
			</div>
		</div>
	<?=form_close()?>
</div>