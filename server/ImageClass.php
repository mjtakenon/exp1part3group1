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
    /*public function setRGB($R,$G,$B){
        $this->RGBColor['Red'] = $R;
        $this->RGBColor['Green'] = $G;
        $this->RGBColor['Blue'] = $B;
    }*/

    public function setRGB($rgb){
        $this->RGBColor['Red'] = $rgb->getR();
        $this->RGBColor['Green'] = $rgb->getG();
        $this->RGBColor['Blue'] = $rgb->getB();
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
    //private $size = array('width' => 0, 'height' => 0);
    //private $ext = "";
    private $pixColor = null;
    function __construct($w,$h,$e,$dimg){
        parent::__construct($w,$h,$e);
        echo count($dimg[0]).",".count($dimg);
        $this->setDivision(count($dimg[0]),count($dimg));
        //$this->setSize($w,$h);
        //$this->setExt($e);
        //for ($i=0; $i < $dy; $i++) {

            $this->pixColor = $dimg;
            //$this->pixColor[$i] = array_fill(0,$dx,new RGB());
        //}
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
    public function getSize()
    {
        return $this->size['width'];
    }
    public function getExt()
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
        if(!$this->initalize($divwidth,$divheight))
        {
            //画像処理
            //flickerから取ってきたりする
        }
        else
        {
            //エラー処理
        }

    }
    private function initalize($divwidth,$divheight)
    {
        list($width,$height,$mime_type,$attr) = getimagesize($_FILES["upfile"]["tmp_name"]);
        $ext = $this->isImageFile($mime_type);
        if($ext === "other")
        {
            echo "画像ファイルを選択してください。<br>";
            return false;
        }
        if(!$this->saveImg($ext))
        {
            echo "画像の保存ができませんでした。<br>path=".$this->save_path."<br>\n";
            return false;
        }
        echo "path=".$this->save_path."<br>";
        echo "width=".$width."<br>";
        echo "height=".$height."<br>";
        echo "ext=".$ext."<br>";
        echo "divwidth=".$divwidth."<br>";
        echo "divheight=".$divheight."<br>";
        echo "divedwidth=".floor($width/$divwidth)."<br>";
        echo "divedheight=".floor($height/$divheight)."<br>";
        $image = $this->createImage($mime_type);
        if(!$image)
        {
            echo "保存した画像を開けませんでした。<br>";
            return false;
        }

        $averageRGB = $this->getAverageRGB($image,$width,$height,$divwidth,$divheight);
        $this->m_ReceiveImage = new ReciveImage($width,$height,$ext,$averageRGB);
        print_r($this->m_ReceiveImage);
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
            return false;
        }
        else
        {
            chmod($this->save_path,0644);
            return true;
        }
    }

    private function isImageFile($mime_type)
    {
        switch($mime_type)
        {
            case IMAGETYPE_JPEG:
                return "jpg";
            case IMAGETYPE_PNG:
                return "png";
            case IMAGETYPE_GIF:
                return "gif";
            case IMAGETYPE_BMP:
                return "bmp";
            default:
                return"other";
        }
    }
    private function createImage($mime_type)
    {
        switch($mime_type)
        {
            case IMAGETYPE_JPEG:
                return imagecreatefromjpeg($this->save_path);
            case IMAGETYPE_PNG:
                return imagecreatefrompng($this->save_path);
            case IMAGETYPE_GIF:
                return imagecreatefromgif($this->save_path);
            case IMAGETYPE_BMP:
                return imagecreatefrombmp($this->save_path);
        }
    }
    private function getSumRGB($image,$xpos,$ypos,$xsize,$ysize)
    {
        $sumrgb = new RGB();
        for($y = 0; $y < $ysize; $y++)
        {
            for($x = 0; $x < $xsize; $x++)
            {
                $rgb = imagecolorat($image,$xpos+$x,$ypos+$y);
                $colors = imagecolorsforindex($image,$rgb);
                $sumrgb->setR($sumrgb->getR()+$colors["red"]);
                $sumrgb->setG($sumrgb->getG()+$colors["green"]);
                $sumrgb->setB($sumrgb->getB()+$colors["blue"]);
            }
        }
        return $sumrgb;
    }
    private function getAverageRGB($image,$width,$height,$divwidth,$divheight)
    {
        $divedwidth = floor($width/$divwidth);
        $divedheight = floor($height/$divheight);
        $rgbarray = array();
        for($y = 0; $y < $divheight; $y++)
        {
            $rgbarray[$y] = array();
            for($x = 0; $x < $divwidth; $x++)
            {
                $rgbarray[$y][$x] = new RGB();
            }
        }
        for($y = 0; $y < $divheight; $y++)
        {
            for($x = 0; $x < $divwidth; $x++)
            {
                $rgb = $this->getSumRGB($image,$x*$divedwidth,$y*$divedheight,$divedwidth,$divedheight);
                $rgbarray[$y][$x]->setR($rgb->getR());
                $rgbarray[$y][$x]->setG($rgb->getG());
                $rgbarray[$y][$x]->setB($rgb->getB());
            }
        }
        for($y = 0; $y < $divheight; $y++)
        {
            for($x = 0; $x < $divwidth; $x++)
            {
                $rgbarray[$x][$y]->setR(floor($rgbarray[$x][$y]->getR()/($divedheight*$divedwidth)));
                $rgbarray[$x][$y]->setG(floor($rgbarray[$x][$y]->getG()/($divedheight*$divedwidth)));
                $rgbarray[$x][$y]->setB(floor($rgbarray[$x][$y]->getB()/($divedheight*$divedwidth)));
            }
        }
        return $rgbarray;
    }
    private function getFlickerImages($per_page,$page)
    {
        $image = array();
        $Flickr_apikey = "600dfca58e06413caa4125ce28da02b7";
        $Flickr_getRecent = "https://api.flickr.com/services/rest/?method=flickr.photos.getRecent&api_key=".$Flickr_apikey."&extras=url_s&per_page=".$per_page."&page=".$page."&format=php_serial";
        $result = unserialize(file_get_contents($Flickr_getRecent));

        foreach($result["photos"]["photo"] as $k => $photo){
            if(isset($photo["url_s"])){
                $url   = $photo["url_s"];
                $width = $photo["width_s"]-1;
                $height= $photo["height_s"]-1;

                $ext = "image/jpeg";
                $image[] = new FlickerImage($width,$height,$ext,$url);
            }
        }
        return $image;
    }

    private function getSimilarImage($src,$margin)
    {
        $num = 500;
        for(;;)
        {
            $flickerimages = $this->getFlickerImages($num,$this->page);

            foreach($flickerimages as $flickerimage)
            {
                $image = $this->createImageByUrl($flickerimage->getUrl());
                $average = $this->getAverageRGB($image,$flickerimage->getWidth(),$flickerimage->getHeight(),1,1);

                if($this->compareImage($src,$average[0][0]) < $margin)
                {
                    echo "diff = " .$this->compareImage($src,$average[0][0])."<br>\n";
                    var_dump($average[0][0]->getRGB());
                    $this->page++;
                    return $flickerimage;
                }
            }
            $this->page++;
        }
    }

    private function compareImage($src1,$src2)
    {
        return abs(pow($src1->getR()-$src2->getR(),2))
              +abs(pow($src1->getG()-$src2->getG(),2))
              +abs(pow($src1->getB()-$src2->getB(),2));
    }
}
?>
