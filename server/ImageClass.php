<?php
//使い方　phpの最初のほうにrequire('ImageClass.php');とでも書いて置いてください
class BaseImage
{
    private $width = 0;
    private $height = 0;
    private $ext = "";//extension 拡張子

    function __construct($w,$h,$e)
    {
        $this->width = $w;
        $this->height = $h;
        $this->ext = $e;
    }
    public function getWidth(){
        return $width;
    }
    public function getHeight(){
        return $height;
    }
    public function getExt(){
        return $ext;
    }

}

/*RGBのためのクラス*/
class RGB
{
    private $RGBColor = array('Red' => 0,'Green' => 0,'Blue' => 0,'Alpha' => 0);

    public function getRGB(){
        return $RGBColor;
    }
    public function setRGB($R,$G,$B,$Alpha){
        $this->RGBColor['Red'] = $R;
        $this->RGBColor['Green'] = $G;
        $this->RGBColor['Blue'] = $B;
        $this->RGBColor['Alpha'] = $Alpha;
    }
}

//アップロード
class ReciveImage extends BaseImage
{
    private $division = array('X' => 0,'Y' => 0);
    private $pixColor = array();

    public function setDivision($x,$y)
    {
        $this->division['X'] = $x;
        $this->division['Y'] = $y;
    }
    function __construct($w,$h,$e,$dx,$dy){
        parent::__construct($w,$h,$e);
        $this->setDivision($dx,$dy);
        for ($i=0; $i < $dy; $i++) {
            $this->pixColor[$i] = array_fill(0,$dx,new RGB());
        }
    }
}

class SendImage extends BaseImage
{
    private $RGB;
    private $put = array('X' => 0,'Y' => 0);

    function __construct($w,$h,$e)
    {
        parent::__construct($w,$h,$e);
        $this->RGB = new RGB();
    }
    public function setDivision($x,$y)
    {
        $this->division['X'] = $x;
        $this->division['Y'] = $y;
    }
}

class ImageAnalizer
{
    private $m_ReceiveImage;
    private $save_path;

    public function __construct($divwidth,$divheight)
    {
        list($width,$height,$mime_type,$attr) = getimagesize($_FILES["upfile"]["tmp_name"]);
        switch($mime_type)
        {
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
            saveImg();
            echo "path=".$save_path."<br>";
            echo "width=".$width."<br>";
            echo "height=".$height."<br>";
            echo "ext=".$ext."<br>";

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
            
            $tmpRGB = array();

            for($ii = 0; $ii < $divheight; $ii++)
            {
                $tmpRGB[$ii] = array();

                for($jj = 0; $jj < $divwidth; $jj++)
                {
                    $tmpRGB[$ii][$jj] = new RGB();
                }
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
                            $tmpRGB[$divy][$divx]['Red'] += $colors['red'];
                            $tmpRGB[$divy][$divx]['Green'] += $colors['green'];
                            $tmpRGB[$divy][$divx]['Blue'] += $colors['blue'];
                            $tmpRGB[$divy][$divx]['Alpha'] += $colors['alpha'];
                        }
                    }
                }
            }

            for($ii = 0; $ii < $divheight; $ii++)
            {
                for($jj = 0; $jj < $divwidth; $jj++)
                {
                    $tmpRGB[$ii][$jj]['Red'] /= $divedheight*$divedwidth;
                    $tmpRGB[$ii][$jj]['Green'] /= $divedheight*$divedwidth;
                    $tmpRGB[$ii][$jj]['Blue'] /= $divedheight*$divedwidth;
                    $tmpRGB[$ii][$jj]['Alpha'] /= $divedheight*$divedwidth;
                }
            }

            echo $tmpRGB[0][0]."<br>";


            $m_ReceiveImage = new ReceiveImage($width,$height,$ext,$divwidth,$divheight);
        }
    }

    private function saveImg()
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
        else
        {
            echo "image saved<br>";
        }
        chmod($save_path,0644);
    }
}

?>
