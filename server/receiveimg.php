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
					case IMAGETYPE_JPEG:
						$ext = "jpg";
						break;
					case IMAGETYPE_PNG:
						$ext = "png";
						break;
					case IMAGETYPE_GIF:
						$ext = "gif";
						break;
					case IMAGETYPE_BMP:
						$ext = "bmp";
						break;
					default:
						$ext = "other"
						break;
				}
				var_dump(gd_info());
				if($ext == "other")
				{
					echo "画像ファイルを選択してください。"
				}
				else
				{

				}
			}
			else {
				echo "ファイルが選択されていません。";
			}
		?>
	</body>

</html>
