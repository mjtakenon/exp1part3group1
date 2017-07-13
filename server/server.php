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
          echo "msg : $msg\n";
          var_dump($msg);
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
          $json = array('x' => 1, 'y' => 1 , 'url' => 'https://pbs.twimg.com/media/B0wx8kpCAAAfFjx.jpg');
          $from->send(json_encode($json));
      }
  }

  // Run the server application through the WebSocket protocol on port 8080
  $server = IoServer::factory(new HttpServer(new WsServer(new Chat())), 9000);
  $server->run();
