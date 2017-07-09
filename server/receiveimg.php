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
				echo "filetype = ".$_FILES["upfile"]["type"];
				echo $_FILES["upfile"]["type"];


				list($img_width,$img_height,$mime_type,$attr) = getimagesize($_FILES["upfile"]["tmp_name"]);

				switch($mime_type){
					//jpegの場合
					case IMAGETYPE_JPEG:
						//拡張子の設定
						$img_extension = "jpg";
						break;
					//pngの場合
					case IMAGETYPE_PNG:
					//拡張子の設定
						$img_extension = "png";
						break;
					//gifの場合
					case IMAGETYPE_GIF:
						//拡張子の設定
						$img_extension = "gif";
						break;
				}
				//拡張子の出力
				echo $img_extension;

				//if($image["type"])
				/*if(move_uploaded_file($_FILES["upfile"]["tmp_name"],"files/".$_FILES["upfile"]["name"])){
					chmod("files/".$_FILES["upfile"]["name"],0777);
					echo $_FILES["upfile"]["name"]."をアップロードしました。";
				}
				else{
					echo "ファイルをアップロードできません";
				}*/
			}
			else {
				echo "ファイルが選択されていません。";
			}
		?>
	</body>

</html>
