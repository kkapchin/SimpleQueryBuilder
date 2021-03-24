<?php
class Connection {
    private static $config = [];

    private function __construct() {
        if (count(self::$config) == 5) {
            self::make();
            //var_dump(self::$config);
        }
    }

    public static function setConfig() {
        return new self;
    }

    public function driver ($driver) {
        self::$config += array('driver' => $driver);
        return new self;
    }

    public function host ($host) {
        self::$config += array('host' => $host);
        return new self;
    }

    public function dbname ($dbname) {
        self::$config += array('dbname' => $dbname);
        return new self;
    }

    public function root ($root) {
        self::$config += array('root' => $root);
        return new self;
    }

    public function password($password) {
        self::$config += array('password' => $password);
        return new self;
    }

    public static function make() {
        return new PDO
        (
            self::$config['driver'] . ":host=" .
            self::$config['host'] . "; dbname=" .
            self::$config['dbname'],
            self::$config['root'],
            self::$config['password']
        );
    }
}