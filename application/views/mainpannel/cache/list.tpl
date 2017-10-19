<table class="table">
<tr align="center">
	<th align="left" class="td2" style="padding-left:15px;"><span ><?=$thisControllerName?></span></th>
</tr>
</table>
<table class="table">
	<tr>
		<td align="right" width="120">memcache 状态:</td>
		<td> <?php if($onlineStatus){ ?><span style="color:#00FF00">在线</span> ver:<?php echo $cacheVersion;?> <?php }else{?><span style="color:#FF0000">离线</span><?php }?></td>
	</tr>
	<?php if($cacheStates){ ?>
	<tr>
		<td align="right" width="120">memcache 用量:</td>
		<td><?php echo sprintf("%5.1f MBytes / %5.1f MBytes  (%.1f%%)",$cacheStates['bytes']/1048576 , $cacheStates['limit_maxbytes']/1048576, $cacheStates['bytes']*100/$cacheStates['limit_maxbytes']);?> </td>
	</tr>
	<?php } ?>
	<tr>
		<td align="right" width="120">memcache 清理:</td>
		<td>
		<?php if($onlineStatus){ ?><input type="button" name="clearok" id="clearok" value="清除缓存" class="button" onclick="javascript:if(confirm('确认 清理?')==false){return false;}else{ window.location.href='<?php echo site_url($thisModule.$thisController."/clearCache/"); ?>';}"  /><?php } ?>		
		 </td>
	</tr>
	
</table>