<?php
	if (!$_REQUEST)
		echo "非法请求:参数为空";
	else
	{
		$ID = $_REQUEST['id'];//保存文件id
		$KEY = $_REQUEST['key'];//保存文件密钥
		$link = mysqli_connect('localhost','Guest','aisdug9-=1-3434u');//连接数据库
		mysqli_set_charset($link, 'utf8');//设置字符集
		mysqli_select_db($link, 'wefile');//选定数据库
		$res = mysqli_query($link, "call GetFilePath(" . $ID . ",'" . $KEY . "')");//返回结果集
		if (!$res)//无法获取结果集
			echo "非法请求:查询失败";
		else
		{
			$ret = mysqli_fetch_assoc($res);//处理返回结果(文件地址)
			mysqli_close($link);
			if (!$ret)//查询条件不正确
				echo "非法请求:文件已经失效";
			if(file_exists(__DIR__. "/FILE/" . $ret['Path']))//所有文件都在FILE下
			{
				header("Content-Type: application/force-download; charset=utf-8");
				header("Content-Disposition: attachment; filename=" . basename($ret['Path']));
				ob_clean();
				flush();
				readfile(__DIR__. "/FILE/" . $ret['Path']);
				exit;
			}
			else
			{
				echo "非法请求:文件已经失效";
			}
		}
	}
	echo '</br>';
	echo "<a href='#' onClick='javascript:history.back(-1);'>点此返回列表</a>";
?>
