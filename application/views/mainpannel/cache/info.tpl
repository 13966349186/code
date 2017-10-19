<style type="text/css">

h1{
font-size:14pt;
font-weight:bold;
padding:0 0 10px 0;
line-height:1.2em;
margin:0;
color:#911;
_padding-left:0px;
}
.box{
border:1px solid #ccc;
padding:10px;
background:#ffffd6;
line-height:1.4em;
-moz-border-radius:4px;
-webkit-border-radius:4px;
border-radius:4px;
/**
width:500px;
*/
height:auto;
}
</style>

<h1>系统提示</h1>
<div class="box">
<div align="center"><?php echo $infoMsg;?><br /><br />
<a href="/<?=$thisModule.$thisController ?>"  target="_self">点击此处返回！</a>
</div>
</div>