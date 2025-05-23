<?php

namespace App\Services;

use TADPHP\TADFactory;

class ZKTecoService
{
    public static function connectToDevice($ip, $port, $com_key)
    {
        $options = [
            'ip' => $ip,
            'com_key' => $com_key,
            'udp_port' => $port,
        ];

        $factory = new TADFactory($options);
        return $factory->get_instance();
    }
}
