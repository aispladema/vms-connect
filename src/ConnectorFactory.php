<?php

declare(strict_types=1);

namespace VMSConnect;

use Exception;
use VMSConnect\Integrations\Digifort;

class ConnectorFactory
{

    /**
     * Return a connector instance of the specified type
     * @param VMSConnector $connector
     * @return Digifort
     * @throws Exception
     */
    public static function getConnector(VMSConnector $connector) {

        switch($connector->type) {
            case Digifort::fqcn():
                        $connObj = new Digifort($connector->host, [
                            'auth' => [
                                'user' => $connector->user,
                                'password' => $connector->password
                            ]
                        ]);
                        break;
                default:
                        throw new Exception('Not implemented');
        }

        return $connObj;
    }
}
