<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>さーばーんなちほーへよーこそー</title>
	</head>
	<body>
		<h2>結果だっていってるだろぉぉぉぉぉん！？</h2>
		<?php
			require('ImageClass.php');
			if(is_uploaded_file($_FILES["upfile"]["tmp_name"]))
			{
				$analizer = new ImageAnalizer(4,4);
			}
			else
			{
				echo "ファイルが選択されていません。";
			}
		?>
	</body>

</html>
