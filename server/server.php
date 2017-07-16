<?php
  use Ratchet\MessageComponentInterface;
  use Ratchet\ConnectionInterface;
  use Ratchet\Server\IoServer;
  use Ratchet\WebSocket\WsServer;
  use Ratchet\Http\HttpServer;

  require __DIR__.'/vendor/autoload.php';

  /**
  * chat.php
  * Send any incoming messages to all connected clients (except sender)
  */
  class Chat implements MessageComponentInterface {

      protected $clients;

      public function __construct() {
          //とりあえずハッシュマップみたいなものと考えておけばよ
          //なぜかオブジェクトをキーにデータを格納できるみたいなので、それで実行。
          $this->clients = new \SplObjectStorage;
      }

      public function onOpen(ConnectionInterface $conn) {
          $this->clients->attach($conn);
      }

      public function onMessage(ConnectionInterface $from, $msg) {


          $base64 = base64_decode($msg);
	      $base64 = preg_replace("/data:[^,]+,/i","",$base64);
	      $base64 = base64_decode($base64);
	      file_put_contents("tmp.bin", $base64);
          echo "type : ".gettype($msg)."\nmsg : $msg \n";
          $resource = imagecreatefromstring($base64);
          var_dump($resource);

          $this->sendJson($from);
        //   foreach ($this->clients as $client) {
        //       if ($from != $client){
        //
        //           //$client->send($msg);
        //       }
        //   }
      }

      public function onClose(ConnectionInterface $conn) {
          $this->clients->detach($conn);
      }

      public function onError(ConnectionInterface $conn, \Exception $e) {
          $conn->close();
      }
      public function sendJson(ConnectionInterface $from){
          //チェックのため
          $json = array('x' => 1, 'y' => 1 , 'url' => 'https://c1.staticflickr.com/4/3667/13774932844_20d65fa27b_n.jpg');
          $from->send(json_encode($json));
      }
  }

  // Run the server application through the WebSocket protocol on port 8080
  $server = IoServer::factory(new HttpServer(new WsServer(new Chat())), 9000);
  $server->run();
