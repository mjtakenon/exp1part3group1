<?php
//使い方　phpの最初のほうにrequire('ImageClass.php');とでも書いて置いてください
class BaseImage
{
    private $width = 0;
    private $height = 0;
    private $ext = '';//extension 拡張子

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

    public function setWidth($w){
        $this->width = $w;
    }
    public function setHeight($h){
        $this->height = $h;
    }
    public function setExt($e){
        $this->ext = $e;
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

    public function setRGB($rgb){
        $this->RGBColor = $rgb;
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
    private $pixColor = null;

    function __construct($w,$h,$e,$dimg){
        parent::__construct($w,$h,$e);
        //echo count($dimg[0]).','.count($dimg);
        $this->setDivision(count($dimg[0]),count($dimg));
        
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

class FlickerImage extends BaseImage
{
    private $url;
    
    function __construct($w,$h,$e,$u)
    {
        parent::__construct($w,$h,$e);
        $this->url = $u;
    }

    function getUrl() 
    {
        return $this->url;
    }

    function setUrl($u)
    {
        $this->url = $u;
    }
}

class ImageAnalizer
{
    private $m_ReceiveImage = null;
    private $save_path = '';
    private $page = 1;

    public function __construct($divwidth,$divheight)
    {
        if(!$this->initalize($divwidth,$divheight))
        {
            echo '画像の初期化に失敗しました。\n';
        }
        else
        {
            $num = 2;

            foreach($this->m_ReceiveImage->getPixcolor() as $row)
            {
                foreach($row as $image)
                {
                    $start_time = microtime(true);
                    //var_dump($width->getRGB());
                    //echo '\n';
                    $margin = 500;
                    var_dump($image->getRGB());
                    $flickrimage = $this->getSimilarImage($image,$margin);
                    echo '<img src="'.$flickrimage->getUrl().'" width="200" height="200"/>';
                    //echo $flickrimage->getUrl().'\n';
                    $end_time = microtime(true);
		
                    echo '画像取得時間:'.($end_time-$start_time).'秒';
                }
            }   
        }

    }

    //初期化 成功するとtrue,失敗するとfalseを返す
    private function initalize($divwidth,$divheight)
    {
        $start_time = microtime(true);

        //画像データの取得
        list($width,$height,$mime_type,$attr) = getimagesize($_FILES['upfile']['tmp_name']);

        //画像ファイル種別の取得
        $ext = $this->isImageFile($mime_type);

        if($ext === 'other')
        {
            echo "画像ファイルを選択してください。\n";
            return false;
        }

        //画像をimages/に保存
        if(!$this->saveImg($ext))
        {
            echo "画像の保存ができませんでした。path=".$this->save_path."\n";
            return false;
        }

        //クライアントから送られた情報の表示
        echo "path=".$this->save_path."\n";
        echo "width=".$width."\n";
        echo "height=".$height."\n";
        echo "ext=".$ext."\n";

        echo "divwidth=".$divwidth."\n";
        echo "divheight=".$divheight."\n";

        echo "divedwidth=".floor($width/$divwidth)."\n";
        echo "divedheight=".floor($height/$divheight)."\n";

        //保存したローカルデータから画像の作成
        $image = $this->createImageBySavepath($mime_type);

        if(!$image)
        {
            echo "保存した画像を開けませんでした。\n";
            return false;
        }
        
        //平均値を出す
        $averageRGB = $this->getAverageRGB($image,$width,$height,$divwidth,$divheight,1);

        //受信した画像のクラスを作成
        $this->m_ReceiveImage = new ReciveImage($width,$height,$ext,$averageRGB);
        
        //print_r($this->m_ReceiveImage);
        $end_time = microtime(true);
        
        header ('Content-Type: image/jpeg');
        imagejpeg($image);
        
        echo "初期化処理時間:".($end_time-$start_time)."秒 \n";

        return true;
    }

    //画像の保存 成功でtrue 失敗でfalse
    private function saveImg($ext)
    {
        $save_dir = '\\images\\';
        $save_filename = date('YmdHis');
        $save_basename = $save_filename. '.'. $ext;
        $this->save_path = $_SERVER['DOCUMENT_ROOT']. $save_dir. $save_basename;

        while (file_exists($this->save_path))
        {
            $save_filename .= mt_rand(0, 9);
            $save_basename = $save_filename. '.'. $ext;
            $this->save_path = $_SERVER['DOCUMENT_ROOT']. $save_dir. $save_basename;
        }
        
        if(!move_uploaded_file($_FILES['upfile']['tmp_name'],$this->save_path))
        {
            return false;
        }
        else
        {
            chmod($this->save_path,0644);
            return true;
        }
    }
    
    //ファイル種別の判別 画像でないとotherを返す
    private function isImageFile($mime_type)
    {
        switch($mime_type)
        {
            case IMAGETYPE_JPEG:
                return 'jpg';
            case IMAGETYPE_PNG:
                return 'png';
            case IMAGETYPE_GIF:
                return 'gif';
            case IMAGETYPE_BMP:
                return 'bmp';
            default:
                return 'other';
        }
    }

    //save_pathより画像データの作成
    private function createImageBySavepath($mime_type)
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

    //urlより画像データの作成
    private function createImageByUrl($url)
    {
        list($width,$height,$mime_type,$attr) = getimagesize($url);
        
        switch($mime_type)
        {
            case IMAGETYPE_JPEG:
                return imagecreatefromjpeg($url);
            case IMAGETYPE_PNG:
                return imagecreatefrompng($url);
            case IMAGETYPE_GIF:
                return imagecreatefromgif($url);
            case IMAGETYPE_BMP:
                return imagecreatefrombmp($url);
        }
    }
    //urlより画像データの作成(jpgのみ)
    
    //画像の合計画素値をRGBで返す $xpos,$yposを左上座標に$xsize,$ysizeの大きさで,$space飛ばしで
    private function getSumRGB($image,$xpos,$ypos,$xsize,$ysize,$space)
    {
        $sumrgb = new RGB();

        for($y = 0; $y < $ysize; $y+=$space)
        {
            for($x = 0; $x < $xsize; $x+=$space)
            {
                $rgb = imagecolorat($image,$xpos+$x,$ypos+$y);
                $colors = imagecolorsforindex($image,$rgb);
                $sumrgb->setR($sumrgb->getR()+$colors['red']);
                $sumrgb->setG($sumrgb->getG()+$colors['green']);
                $sumrgb->setB($sumrgb->getB()+$colors['blue']);
            }
        }
        return $sumrgb;
    }

    //画像の平均画素値をRGB[][]で返す $divwidth,$divheightに分割数、$spaceに間隔(間隔なしは0で)
    private function getAverageRGB($image,$width,$height,$divwidth,$divheight,$space)
    {
        $divedwidth = floor($width/$divwidth);
        $divedheight = floor($height/$divheight);

        $rgbarray = array();
        for($y = 0; $y < $divheight; ++$y)
        {
            $rgbarray[] = array();
            $row = $y/$space;
            for($x = 0; $x < $divwidth; ++$x)
            {
                $rgbarray[$row][] = new RGB();
            }
        }

        for($y = 0; $y < $divheight; ++$y)
        {
            for($x = 0; $x < $divwidth; ++$x)
            {
                $rgb = $this->getSumRGB($image,$x*$divedwidth,$y*$divedheight,$divedwidth,$divedheight,$space);
                $rgbarray[$y][$x]->setRGB($rgb->getRGB());
            }
        }

        $allpixels = ($divedheight*$divedwidth)/pow($space,2);

        for($y = 0; $y < $divheight; ++$y)
        {
            for($x = 0; $x < $divwidth; ++$x)
            {
                $rgbarray[$x][$y]->setR(floor($rgbarray[$x][$y]->getR()/$allpixels));
                $rgbarray[$x][$y]->setG(floor($rgbarray[$x][$y]->getG()/$allpixels));
                $rgbarray[$x][$y]->setB(floor($rgbarray[$x][$y]->getB()/$allpixels));
            }
        }
        return $rgbarray;
    }

    //flickrのAPIを叩いてFlickrImage[]を返す
    private function getFlickerImages($per_page,$page)
    {
        $image = array();
        $Flickr_apikey = '600dfca58e06413caa4125ce28da02b7';
        $Flickr_getRecent = 'https://api.flickr.com/services/rest/?method=flickr.photos.getRecent&api_key='.$Flickr_apikey.'&extras=url_s&per_page='.$per_page.'&page='.$page.'&format=php_serial';
        $result = unserialize(file_get_contents($Flickr_getRecent));
        
        $ext = 'image/jpeg';

        foreach($result['photos']['photo'] as $k => $photo){
            if(isset($photo['url_s'])){
                $url   = $photo['url_s'];
                $width = $photo['width_s']-1;
                $height= $photo['height_s']-1;

                $image[] = new FlickerImage($width,$height,$ext,$url);
            }
        }
        return $image;
    }
    
    //FlickrImage[]から似た画像を返す
    private function getSimilarImage($src,$margin)
    {
        $num = 500;
        for(;;)
        {
            $flickerimages = $this->getFlickerImages($num,$this->page);

            foreach($flickerimages as $flickerimage)
            {
                $image = $this->createImageByUrl($flickerimage->getUrl());
                $average = $this->getAverageRGB($image,$flickerimage->getWidth(),$flickerimage->getHeight(),1,1,4);
                
                if($this->compareImage($src,$average[0][0]) < $margin)
                {
                    echo "diff = " .$this->compareImage($src,$average[0][0])."\n";
                    var_dump($average[0][0]->getRGB());
                    ++$this->page;
                    return $flickerimage;
                }
            }
            ++$this->page;
        }
    }

    //画像の比較 |R^2|+|G^2|+|B^2|の値を返す
    private function compareImage($src1,$src2)
    {
        return abs(pow($src1->getR()-$src2->getR(),2))
              +abs(pow($src1->getG()-$src2->getG(),2))
              +abs(pow($src1->getB()-$src2->getB(),2));
    }
}
?>
