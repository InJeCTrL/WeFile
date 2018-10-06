<?php
	$link = mysqli_connect('localhost','Guest','aisdug9-=1-3434u');//连接数据库
	mysqli_set_charset($link, 'utf8');//设置字符集
	mysqli_select_db($link, 'wefile');//选定数据库
	$SQLQuery = "call GetInvalidList()";
	$res = mysqli_query($link, $SQLQuery);//返回结果集
	if ($res->num_rows > 0)//有无效存储
	{
		while ($ret = mysqli_fetch_assoc($res))//处理返回结果集(一次一行)
		{
			if (file_exists(__DIR__ . '/FILE/' . $ret['FilePath']))
			{
				unlink('FILE/' . $ret['FilePath']);//先删除文件
				rmdir('FILE/' . $ret['FolderPath']);//再删除文件夹
			}
		}
	}
	mysqli_close($link);//关闭数据库
	
	$link2 = mysqli_connect('localhost','Guest','aisdug9-=1-3434u');//重新连接数据库
	mysqli_set_charset($link2, 'utf8');//设置字符集
	mysqli_select_db($link2, 'wefile');//选定数据库
	$SQLQuery_CR = "call FileClear()";
	$res_CR = mysqli_query($link2, $SQLQuery_CR);//删除数据库中的无效文件记录项
	mysqli_close($link2);//关闭数据库
	
	$link3 = mysqli_connect('localhost','Guest','aisdug9-=1-3434u');//重新连接数据库
	mysqli_set_charset($link3, 'utf8');//设置字符集
	mysqli_select_db($link3, 'wefile');//选定数据库
	$SQLQuery_CRS = "call ResumeIndex()";
	$res_CRS = mysqli_query($link3, $SQLQuery_CRS);//重置ID起始位置
	mysqli_close($link3);//关闭数据库
?>