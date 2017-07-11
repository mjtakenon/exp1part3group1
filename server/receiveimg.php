<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>さーばーんなちほーへよーこそー</title>
	</head>
	<body>
		<h2>結果だっていってるだろぉぉぉぉぉん！？</h2>
		<?php
			$start_time = microtime(true);
			require('ImageClass.php');
			if(is_uploaded_file($_FILES["upfile"]["tmp_name"]))
			{
				$analizer = new ImageAnalizer(2,2);
			}
			else
			{
				echo "ファイルが選択されていません。<br>\n";
			}
			$end_time = microtime(true);
			echo "総処理時間:".($end_time-$start_time)."秒<br>\n";
		?>
	</body>

</html>
