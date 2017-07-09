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
    private $RGBColor = array('Red' => 0,'Green' => 0,'Blue' => 0);

    public function getRGB(){
        return $RGBColor;
    }
    public function setRGB($R,$G,$B){
        $this->RGBColor['Red'] = $R;
        $this->RGBColor['Green'] = $G;
        $this->RGBColor['Blue'] = $B;
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

$rim = new ReciveImage(100,200,"png",3,3);
echo 'new ReciveImage(100,200,"png",3,3);';
var_dump($rim);
?>
