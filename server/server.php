<?php
  use Ratchet\MessageComponentInterface;
  use Ratchet\ConnectionInterface;
  use Ratchet\Server\IoServer;
  use Ratchet\WebSocket\WsServer;
  use Ratchet\Http\HttpServer;
  //ループ制御
  use React\EventLoop\LoopInterface;
  use React\EventLoop\Factory;
  use React\Socket\Server;

  require __DIR__.'/vendor/autoload.php';
  require('ImageClass.php');

  /**
  * chat.php
  * Send any incoming messages to all connected clients (except sender)
  */
  class Chat implements MessageComponentInterface {

      protected $clients;
      private $roop;

      public function __construct() {
          //とりあえずハッシュマップみたいなものと考えておけばよ
          //なぜかオブジェクトをキーにデータを格納できるみたいなので、それで実行。
          $this->clients = new \SplObjectStorage;
      }

      public function onOpen(ConnectionInterface $conn) {
          $this->clients->attach($conn);
      }

      public function onMessage(ConnectionInterface $from, $msg) {

          var_dump($msg);
          var_dump($this->clients[$from]);
          if (is_null($this->clients[$from])) {
              $this->setDivision($from,$msg);
          }
          else {
              $this->imageanalize($from,$msg);
          }
      }

      public function onClose(ConnectionInterface $conn) {
          $this->clients->detach($conn);
      }

      public function onError(ConnectionInterface $conn, \Exception $e) {
          $conn->close();
      }
      public function setRoop($loop)
      {
          $this->roop = $loop;
          echo var_dump($this->roop);
      }
      public function setDivision($from,$msg)
      {
          $this->clients[$from] = json_decode($msg,true);
          $from->send("ACK");
      }
      public function imageanalize($from,$msg){
          $base64 = base64_decode($msg);
	      $base64 = preg_replace("/data:[^,]+,/i","",$base64);
	      $base64 = base64_decode($base64);
	      file_put_contents("tmp.bin", $base64);
          $resource = imagecreatefromstring($base64);
          $div = $this->clients[$from];
          $analizer = new ImageAnalizer($div['width'],$div['height'],$from,$resource,$this);
          $this->clients[$from] = null;
      }
      public function sendJson(ConnectionInterface $from,$x,$y,$url){
          //チェックのため
          $json = array('x' => $x, 'y' => $y , 'url' => $url);
          print_r($json);
          $from->send(json_encode($json));
          $this->roop->tick();
      }
  }
  //$aa = "";
  // $loop = Factory::create();
  // $socket = new Server($loop);
  // $socket->listen(9000,'10.70.241.107');
  // Run the server application through the WebSocket protocol on port 8080
  // $server = IoServer::factory(new HttpServer(new WsServer(new Chat($loop))),$socket,$loop);
  // $server->run();
  $chat = new Chat();
  $server = IoServer::factory(new HttpServer(new WsServer($chat)), 9000);
  $chat->setRoop($server->loop);
  echo "server run\n";
  $server->run();
?>
