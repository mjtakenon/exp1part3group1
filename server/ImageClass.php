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
        return $this->width;
    }
    public function getHeight(){
        return $this->height;
    }
    public function getExt(){
        return $this->ext;
    }

}

/*RGBのためのクラス*/
class RGB
{
    private $RGBColor = array('Red' => 0,'Green' => 0,'Blue' => 0);

    public function getRGB(){
        return $this->RGBColor;
    }
    
    public function getR(){
        return $this->RGBColor['Red'];
    }
    public function getG(){
        return $this->RGBColor['Green'];
    }
    public function getB(){
        return $this->RGBColor['Blue'];
    }

    public function setRGB($R,$G,$B){
        $this->RGBColor['Red'] = $R;
        $this->RGBColor['Green'] = $G;
        $this->RGBColor['Blue'] = $B;
    }
    public function setR($R){
        $this->RGBColor['Red'] = $R;
    }
    public function setG($G){
        $this->RGBColor['Green'] = $G;
    }
    public function setB($B){
        $this->RGBColor['Blue'] = $B;
    }
    
}

//アップロード
class ReciveImage extends BaseImage
{
    private $division = array('X' => 0,'Y' => 0);
    private $size = array('width' => 0, 'height' => 0);
    private $ext = "";
    private $pixColor = null;

    
    function __construct($w,$h,$e,$dimg){
        parent::__construct($w,$h,$e);

        $this->setDivision($dimg[0],count($dimg));
        $this->setSize($w,$h);
        $this->setExt($e);

        for ($i=0; $i < $dy; $i++) {
            $this->pixColor = $dimg;
            //$this->pixColor[$i] = array_fill(0,$dx,new RGB());
        }
    }
    
    public function setDivision($x,$y)
    {
        $this->division['X'] = $x;
        $this->division['Y'] = $y;
    }
    public function setSize($w,$h)
    {
        $this->size['width'] = $w;
        $this->size['height'] = $h;
    }
    public function setExt($e)
    {
        $this->ext = $e;
    }
    public function setPixcolor($dimg)
    {
        $this->setDivision($dimg[0],count($dimg));
        $this->pixColor = $dimg;
    }


    public function getDivision()
    {
        return $this->division;
    }
    public function setSize()
    {
        return $this->size['width'];
    }
    public function setExt()
    {
        return $this->ext;
    }
    public function getPixcolor()
    {
        return $this->pixColor;
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
    private $m_ReceiveImage = null;
    private $save_path = "";

    public function __construct($divwidth,$divheight)
    {
        echo "construct<br>";
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
            $this->saveImg($ext);

            echo "path=".$this->save_path."<br>";
            echo "width=".$width."<br>";
            echo "height=".$height."<br>";
            echo "ext=".$ext."<br>";

            echo "divwidth=".$divwidth."<br>";
            echo "divheight=".$divheight."<br>";

            $divedwidth = floor($width/$divwidth);
            $divedheight = floor($height/$divheight);

            echo "divedwidth=".$divedwidth."<br>";
            echo "divedheight=".$divedheight."<br>";

            $image = imagecreatefromjpeg($this->save_path);
            
            switch($mime_type){
                case IMAGETYPE_JPEG:
                    $image = imagecreatefromjpeg($this->save_path);
                    break;
                case IMAGETYPE_PNG:
                    $image = imagecreatefrompng($this->save_path);
                    break;
                case IMAGETYPE_GIF:
                    $image = imagecreatefromgif($this->save_path);
                    break;
                case IMAGETYPE_BMP:
                    $image = imagecreatefrombmp($this->save_path);
                    break;
                default:
                    $ext = "other";
            }

            if(!$image)
            {
                echo "image open failed<br>";
            }
            else
            {
                echo "image created<br>";
            }
            
            $tmpRGB = array();

            for($x = 0; $x < $divheight; $x++)
            {
                $tmpRGB[$x] = array();

                for($y = 0; $y < $divwidth; $y++)
                {
                    $tmpRGB[$x][$y] = new RGB();
                }
            }
            echo "calc ok<br>";

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
                            $tmpRGB[$divy][$divx]->setR($tmpRGB[$divy][$divx]->getR()+$colors["red"]);
                            $tmpRGB[$divy][$divx]->setG($tmpRGB[$divy][$divx]->getG()+$colors["green"]);
                            $tmpRGB[$divy][$divx]->setB($tmpRGB[$divy][$divx]->getB()+$colors["blue"]);
                        }
                    }
                }
            }
            echo "calc ok<br>";
            for($x = 0; $x < $divheight; $x++)
            {
                for($y = 0; $y < $divwidth; $y++)
                {
                    $tmpRGB[$x][$y]->setR($tmpRGB[$x][$y]->getR()/($divedheight*$divedwidth));
                    $tmpRGB[$x][$y]->setG($tmpRGB[$x][$y]->getG()/($divedheight*$divedwidth));
                    $tmpRGB[$x][$y]->setB($tmpRGB[$x][$y]->getB()/($divedheight*$divedwidth));
                }
            }
            echo "calc ok<br>";

            $this->m_ReceiveImage = new ReceiveImage($width,$height,$ext,$tmpRGB);
        }
    }

    private function saveImg($ext)
    {
        $save_dir = '\\images\\';
        $save_filename = date('YmdHis');
        $save_basename = $save_filename. '.'. $ext;
        $this->save_path = $_SERVER["DOCUMENT_ROOT"]. $save_dir. $save_basename;

        while (file_exists($this->save_path))
        {
            $save_filename .= mt_rand(0, 9);
            $save_basename = $save_filename. '.'. $ext;
            $this->save_path = $_SERVER["DOCUMENT_ROOT"]. $save_dir. $save_basename;
        }
        
        if(!move_uploaded_file($_FILES["upfile"]["tmp_name"],$this->save_path))
        {
            echo "image save failed".$this->save_path."<br>\n";
        }
        else
        {
            echo "image saved:".$this->save_path."<br>\n";
        }
        chmod($this->save_path,0644);
    }
}

?>
