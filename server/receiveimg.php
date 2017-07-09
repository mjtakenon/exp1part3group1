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
					default:
						$ext = "other";
				}

				if($ext === "other")
				{
					echo "画像ファイルを選択してください。";
				}
				else
				{
					echo "width=".$width."<br>";
					echo "height=".$height."<br>";
					echo "ext=".$ext."<br>";
				}
				
			}
			else 
			{
				echo "ファイルが選択されていません。";
			}
			
		?>
	</body>

</html>
