<?php
	require('ClearInvalid.php');
	if ($_FILES)
	{
		if ($_FILES['UpFile']['error'] == 2 || $_FILES['UpFile']['error'] == 1)//判断大小超限
		{
			echo "文件大小超过限制！(不超过 1 GB)";
			exit;
		}
		$filename = $_FILES['UpFile']['name'];//存储文件名
		if ($filename == '')
		{
			echo "没有上传任何文件！";
			exit;
		}
		$filesize_Byte = $_FILES['UpFile']['size'];//存储文件字节数
		$fileunit = 'B';//文件大小单位
		$filesize = 0;//计算后的文件大小
		$fileadd = $_REQUEST['Addition'];//存储文件备注
		$filetype = $_REQUEST['SelectType'];//存储文件公开私密类型
		$filekey = '';//存储文件密钥
		if ($filetype == 0)//私密文件类型，随机生成六位文件密钥
			$filekey = CreateRandomKey();
		if ($filesize_Byte >= 1024 * 1024)//大等于1MB
		{
			$fileunit = 'MB';
			$filesize = number_format($filesize_Byte / (1024 * 1024),2);
		}
		else if ($filesize_Byte >= 1024)//大等于1KB
		{
			$fileunit = 'KB';
			$filesize = number_format($filesize_Byte / 1024,2);
		}
		else//不足1KB
		{
			$fileunit = 'B';
			$filesize = $filesize_Byte;
		}
		
		$link = mysqli_connect('localhost','Guest','aisdug9-=1-3434u');//连接数据库
		mysqli_set_charset($link, 'utf8');//设置字符集
		mysqli_select_db($link, 'wefile');//选定数据库
		$SQLQuery_Ins = "call NewFile('" . $filename . "'," . $filesize . ",'" . $fileunit . "','" . $fileadd . "'," . $filetype . ",'" . $filekey . "')";//插入一条存储记录
		mysqli_query($link, $SQLQuery_Ins);//插入新文件记录
		$SQLQuery = "call GetThisID()";//获取本次连接创建的自增ID
		$res = mysqli_query($link, $SQLQuery);//执行查询
		$ret = mysqli_fetch_assoc($res);
		$fileID = $ret['ID'];//存储文件ID
		mysqli_close($link);//关闭数据库

		//创建文件夹并移动文件
		$path = __DIR__ . '/FILE/' . $fileID;
		mkdir($path);//创建文件夹
		move_uploaded_file($_FILES['UpFile']['tmp_name'],$path . '/' . $filename);//移动文件
		
		//修改文件为Ready
		$link2 = mysqli_connect('localhost','Guest','aisdug9-=1-3434u');//连接数据库
		mysqli_set_charset($link2, 'utf8');//设置字符集
		mysqli_select_db($link2, 'wefile');//选定数据库
		$SQLQuery_Set = "call SetReady(" . $fileID . ")";//文件置Ready
		mysqli_query($link2, $SQLQuery_Set);//执行修改
		mysqli_close($link2);//关闭数据库
		
		echo '文件上传完成';
		if ($filetype == 0)
			echo '文件密钥：' . $filekey ;
	}
	function CreateRandomKey()
	{//大写字母中随机选取，组成六位密码，并且保证唯一性
		$ok = 0;//表示成功生成
		$_filekey = '';
		do
		{
			$_filekey = '';
			for ($i=0;$i<6;$i++)
				$_filekey .= chr(mt_rand(65, 90));
			$link3 = mysqli_connect('localhost','Guest','aisdug9-=1-3434u');//连接数据库
			mysqli_set_charset($link3, 'utf8');//设置字符集
			mysqli_select_db($link3, 'wefile');//选定数据库
			$SQLQuery = "call IsUniqueKey('" . $_filekey . "')";
			$res = mysqli_query($link3, $SQLQuery);//返回结果集
			$ret = mysqli_fetch_assoc($res);
			$ok = $ret['Result'];
			mysqli_close($link3);//关闭数据库
		}while($ok == 0);
		return $_filekey;
	}
?>