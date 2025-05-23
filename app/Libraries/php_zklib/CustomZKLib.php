<?php
namespace App\Libraries\php_zklib;

use App\Libraries\php_zklib\zklib\ZKLib; // Adjust based on zklib.php's namespace

class CustomZKLib extends ZKLib
{
    protected $comm_key;

    public function __construct($ip, $port, $comm_key = 0)
    {
        parent::__construct($ip, $port);
        $this->comm_key = $comm_key;
    }

    public function connect()
    {
        $this->socket = @fsockopen("udp://" . $this->ip, $this->port, $errno, $errstr, 1);
        if ($this->socket) {
            \Log::info("Socket opened", ['ip' => $this->ip, 'port' => $this->port, 'errno' => $errno, 'errstr' => $errstr]);
            $packet = $this->createHeaderWithCommKey();
            \Log::info("Packet sent", ['packet' => bin2hex($packet)]);
            fwrite($this->socket, $packet);
            $response = fread($this->socket, 1024);
            \Log::info("Response received", ['response' => bin2hex($response)]);
            return $this->checkValid($response);
        }
        \Log::error("Socket failed", ['errno' => $errno, 'errstr' => $errstr]);
        return false;
    }

    protected function createHeaderWithCommKey()
    {
        $command = 1000; // CMD_CONNECT
        $chksum = 0; // Placeholder, refine with pyzk's checksum if needed
        $session_id = 0;
        $reply_id = -1; // Initial reply ID
        $comm_key_bin = pack('L', $this->comm_key); // 4-byte comm_key
        $header = pack('vvvva4', $command, $chksum, $session_id, $reply_id, $comm_key_bin);
        return $header;
    }
}