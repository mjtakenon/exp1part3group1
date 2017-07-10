<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>さーばーんなちほーへよーこそー</title>
	</head>
	<body>
		<h2>結果だっていってるだろぉぉぉぉぉん！？</h2>
		<h4>何で読み込まれないのこいつ</h4>
		<?php
			require('ImageClass.php');
			if(is_uploaded_file($_FILES["upfile"]["tmp_name"]))
			{
				echo "とりあえずなんか書いとけ";
				$analizer = new ImageAnalizer(3,3);
			}
			else
			{
				echo "ファイルが選択されていません。";
			}
		?>
	</body>

</html>
