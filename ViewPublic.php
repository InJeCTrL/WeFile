<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title></title>
	</head>
	<body>
		<form action="ViewPublic.php" method="post">
			搜索文件名：<input type="text" name="FName" value=<?php echo "'" . $_REQUEST['FName'] . "'"; ?> />
			<input type="submit" value="查询"/>
		</form>
			<table width="100%">
				<tr>
					<th width="50%">文件名</th>
					<th width="10%">文件大小</th>
					<th width="10%">上传时间</th>
					<th width="10%">剩余时间</th>
					<th width="20%">备注</th>
				</tr>
				<?php
					if (count($_REQUEST) <= 1 || $_REQUEST['page'] < 1)
						$page = 1;
					else
						$page = $_REQUEST['page'];//标记页码
					$offset = ($page - 1) * 5;
					$PartName = $_REQUEST['FName'];
					$link = mysqli_connect('localhost','Guest','aisdug9-=1-3434u');//连接数据库
					mysqli_set_charset($link, 'utf8');//设置字符集
					mysqli_select_db($link, 'wefile');//选定数据库
					$SQLQuery = "call GetPublicList(" . $offset . ",'" . $PartName . "')";
					$res = mysqli_query($link, $SQLQuery);//返回结果集
					while ($ret = mysqli_fetch_assoc($res))//处理返回结果集(一次一行)
					{
						echo '<tr align="center">';
						echo "<td><a href='Download.php?id={$ret['ID']}&key={$ret['PrivateKey']}'>".$ret['Name']."</a></td>".'<td>'.$ret['Size'].$ret['Unit'].'</td>'.'<td>'.$ret['UpTime'].'</td>'.'<td>'.$ret['RestTime'].'min</td>'.'<td>'.$ret['Addition'].'</td>';
						echo '</tr>';
					}
					mysqli_close($link);//关闭数据库
				?>
			</table>
		<table align="right">
			<tr>
				<td>
					<form action="ViewPublic.php" method="post">
						<input type="hidden" value=<?php echo $page-1; ?> name="page" />
						<input type="submit" value="上一页" <?php 
															if ($page == 1)
																echo("disabled='disabled'");
															?> />
					</form>
				</td>
				<td>
					<form action="ViewPublic.php" method="post">
						<input type="hidden" value=<?php echo $page+1; ?> name="page" />
						<input type="submit" value="下一页" <?php
															$link = mysqli_connect('localhost','Guest','aisdug9-=1-3434u');//连接数据库
															mysqli_set_charset($link, 'utf8');//设置字符集
															mysqli_select_db($link, 'wefile');//选定数据库
															$SQLQuery = "call GetPageNum_PUB('" . $PartName ."')";
															$res = mysqli_query($link, $SQLQuery);//返回结果集
															$ret = mysqli_fetch_assoc($res);
															if ($ret['NUM'] == $page)
																echo("disabled='disabled'");
															mysqli_close($link);//关闭数据库
															?> />
					</form>
				</td>
			</tr>
		</table>
	</body>
</html>
