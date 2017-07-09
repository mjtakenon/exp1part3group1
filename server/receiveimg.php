<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>さーばーんなちほーへよーこそー</title>
	</head>
	<body>
		<h2>結果だっていってるだろぉぉぉぉぉん！？</h2>
		<?php
			if(is_uploaded_file($_FILES["upfile"]["tmp_name"])){
				echo "filetype = ".$_FILES["upfile"]["type"];
				if(move_uploaded_file($_FILES["upfile"]["tmp_name"],"files/".$_FILES["upfile"]["name"])){
					chmod("files/".$_FILES["upfile"]["name"],0777);
					echo $_FILES["upfile"]["name"]."をアップロードしました。";
				}
				else{
					echo "ファイルをアップロードできませんでしたあああああああ！ざんねんでしたあああ！！";
				}
			}
			else {
				echo "ファイルが選択されていません。";
			}
		?>
	</body>

</html>
