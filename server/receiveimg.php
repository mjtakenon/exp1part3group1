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
						$ext = "other";
				}

				if($ext === "other")
				{
					echo "画像ファイルを選択してください。";
				}
				else
				{
					$save_dir = '\\images\\';
					$save_filename = date('YmdHis');
					$save_basename = $save_filename. '.'. $ext;
					$save_path = $_SERVER["DOCUMENT_ROOT"]. $save_dir. $save_basename;
					while (file_exists($save_path)) 
					{
						$save_filename .= mt_rand(0, 9);
						$save_basename = $save_filename. '.'. $ext;
						$save_path = $_SERVER["DOCUMENT_ROOT"]. $save_dir. $save_basename;
					}
					//if(!saveImage($_FILES["upfile"]["tmp_name"],$save_path,$ext))
					if(!move_uploaded_file($_FILES["upfile"]["tmp_name"],$save_path))
					{
						echo "image save failed<br>";
					}
					chmod($save_path,0644);

					echo "path=".$save_path."<br>";
					
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
					
					$image = imagecreatefromjpeg($save_path);
					
					switch($mime_type){
						case IMAGETYPE_JPEG:
							$image = imagecreatefromjpeg($save_path);
							break;
						case IMAGETYPE_PNG:
							$image = imagecreatefrompng($save_path);
							break;
						case IMAGETYPE_GIF:
							$image = imagecreatefromgif($save_path);
							break;
						case IMAGETYPE_BMP:
							$image = imagecreatefrombmp($save_path);
							break;
						default:
							$ext = "other";
					}

					if(!$image)
					{
						echo "image open failed";
					}

					for($divy = 0; $divy < $divheight; $divy++)
					{
						for($divx = 0; $divx < $divwidth; $divx++)
						{
							for($y = 0; $y < $divedheight; $y++)
							{
								for($x = 0; $x < $divedwidth; $x++)
								{
									$rgb = imagecolorat($image,$x+$divx*$divedwidth,$y+$divy*$divedheight);
									$colors = imagecolorsforindex($image,$rgb);
									var_dump($colors);
									echo "<br>";
								}
							}
						}
					}
				}
			}
			else 
			{
				echo "ファイルが選択されていません。";
			}

		?>
	</body>

</html>
