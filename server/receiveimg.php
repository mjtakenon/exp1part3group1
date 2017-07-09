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
					
					$divwidth = 2;
					$divheight = 2;

					echo "divwidth=".$divwidth."<br>";
					echo "divheight=".$divheight."<br>";

					$divedwidth = floor($width/$divwidth);
					$divedheight = floor($height/$divheight);
					
					echo "divedwidth=".$divedwidth."<br>";
					echo "divedheight=".$divedheight."<br>";

					//ファイル保存
					$save_dir = '.\\images\\';
					$save_filename = date('YmdHis');
					$save_basename = $save_filename. '.'. $ext;
					$save_path = $_SERVER["DOCUMENT_ROOT"]. $save_dir. $save_basename;
					while (file_exists($save_path)) { // 同名ファイルがあればファイル名を変更する
						$save_filename .= mt_rand(0, 9);
						$save_basename = $save_filename. '.'. $ext;
						$save_path = $_SERVER["DOCUMENT_ROOT"]. $save_dir. $save_basename;
					}

					echo "path=".$save_path."<br>";
					
					//$files = $_FILES["upfile"]["tmp_name"];
					/*
					for($divy = 0; $divy < $divheight; $divy++)
					{
						for($divx = 0; $divx < $divwidth; $divx++)
						{
							for($y = 0; $y < $divedheight; $divy++)
							{
								for($x = 0; $x < $divedwidth; $divx++)
								{
									//$rgb = imagecolorat($_FILES["upfile"]["tmp_name"],$x+$divx*$divedwidth,$y+$divy*$divedheight);
									//$colors = imagecolorsforindex($_FILES["upfile"]["tmp_name"],$rgb);
									//var_dump($colors);
									//var_dump($rgb);
								}
							}
						}
					}*/
				}
			}
			else 
			{
				echo "ファイルが選択されていません。";
			}
			
		?>
	</body>

</html>
