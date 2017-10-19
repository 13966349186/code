<div class="modal-body">
	<div class="portlet-body form">
		<?=form_open('', Array('class'=>'form-horizontal', 'role'=>'form'))?>
			<div class="form-body">
				<div class="form-group<?=form_error('verify_value')?' has-error':''?>">
					<label class="col-xs-3 control-label">附件</label>
					<div class="col-xs-7">
						<div id="uploader" class="my_web_uploader">
						    <div class="queueList">
						        <div id="dndArea" class="placeholder">
						            <div class="filePicker">选择图片</div>
						        </div>
						    </div>
						    <div class="statusBar" style="display:none;">
						        <div class="progress">
						            <span class="text">0%</span>
						            <span class="percentage"></span>
						        </div><div class="info"></div>
						        <div class="btns">
						            <div class="filePicker2"></div><div class="uploadBtn">开始上传</div>
						        </div>
						    </div>
						    <input type="hidden" name="verify_value" class="uploader_input" value="<?=set_value('verify_value', '')?>" />
						</div>
						<?=form_error('verify_value', '<p class="help-block">', '</p>')?>
					</div>
				</div>
				<?=edit_form_textarea('note', set_value('note'), '备注')?>
				<div class="form-group modal-footer-style pull-right">
					<div class="col-xs-12">
					   <button type="button" class="btn default" data-dismiss="modal">关闭</button>
			   		   <button type="submit" class="btn green">保存</button>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
		<?=form_close()?>
	</div>
</div>
<script>
function modal_init(){
	$('.modal-body').find("#uploader").bg_upload({
		upload_server: "<?=$this->config->item('upload_server')?>", //上传地址
		upload_show: "<?=$this->config->item('upload_show')?>", //显示的地址
		multiple: true, //是否多文件上传
		fileNumLimit: 5, //限制数量
		width: 305, //图片显示宽度
		height: 110, //图片显示高度
		auto_size: true //是否在指定大小范围内自动适应图片大小
	});
}
$('.modal-body').find('.col-xs-4').removeClass('col-xs-4').addClass('col-xs-7');
$('.modal-body').find('textarea').attr('rows', '4');
</script>
