<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>さーばーんなちほーへよーこそー</title>
	</head>
	<body>
		<h1>結果だっていってるだろぉぉぉぉぉん！？</h1>
		<?php
			if(is_uploaded_file($_FILES["upfile"]["tmp_name"])){
				list($width,$height,$mime_type,$attr) = getimagesize($_FILES["upfile"]["tmp_name"]);

				switch($mime_type){
					//jpegの場合
					case IMAGETYPE_JPEG:
						//拡張子の設定
						$ext = "jpg";
						break;
					//pngの場合
					case IMAGETYPE_PNG:
					//拡張子の設定
						$ext = "png";
						break;
					//gifの場合
					case IMAGETYPE_GIF:
						//拡張子の設定
						$ext = "gif";
						break;
				}
				//拡張子の出力

				echo "x=".$width."y=".$height."attr=".$attr."<br>";
				echo "ext=".$ext."<br>";

			}
			else {
				echo "ファイルが選択されていません。";
			}
		?>
	</body>

</html>
