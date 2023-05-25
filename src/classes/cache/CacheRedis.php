<?php

/**
 * This class require Redis server to be installed
 *
 * @since 1.0.0
 */
class CacheRedis extends Cache {

    /**
     * @var bool Connection status
     *
     * @since 1.0.0
     */
    public $is_connected = false;
    /**
     * @var RedisClient $redis
     *
     * @since 1.0.0
     */
    protected $redis;
    /**
     * @var array RedisParams
     *
     * @since 1.0.0
     */
    protected $_params = [];
    protected $_servers = [];

    /**
     * CacheRedisCore constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {

        $this->connect();

        if ($this->is_connected) {
            $this->keys = @$this->redis->get(_COOKIE_IV_);

            if (!is_array($this->keys)) {
                $this->keys = [];
            }

        }

    }

    /**
     * Connect to redis server
     *
     * @return void
     *
     * @throws PhenyxDatabaseExceptionException
     * @throws PhenyxException
     * @since 1.0.0
     */
    public function connect() {

        $this->is_connected = false;
        $this->_servers = static::getRedisServers();

        if (!$this->_servers) {
            return;
        } else {

            if (count($this->_servers) > 1) {
                // Multiple servers, set up redis array
                $hosts = [];

                foreach ($this->_servers as $server) {
                    $hosts[] = $server['ip'] . ':' . $server['port'];
                }

                $this->redis = new RedisArray($hosts, ['pconnect' => true]);

                foreach ($this->_servers as $server) {
                    $instance = $this->redis->_instance($server['ip'] . ':' . $server['port']);

                    if (!empty($server['auth'])) {

                        if (is_object($instance)) {

                            if ($instance->auth($server['auth'])) {
                                // We're connected as soon as authentication is successful
                                $this->is_connected = true;
                            }

                        }

                    } else {
                        $ping = $this->redis->ping();
                        // We're connected if a connection without +AUTH receives a +PONG

                        if ($ping === '+PONG') {
                            $this->is_connected = true;
                        } else if (is_array($ping)) {
                            $ping = array_values($ping);

                            if (!empty($ping) && $ping[0] === '+PONG') {
                                $this->is_connected = true;
                            }

                        }

                    }

                }

                if (!empty($this->_servers[0]['auth'])) {

                    if (!($this->redis->auth($this->_servers[0]['auth']))) {
                        return;
                    }

                }

            } else if (count($this->_servers) === 1) {
                $this->redis = new Redis();

                if ($this->redis->pconnect($this->_servers[0]['ip'], $this->_servers[0]['port'])) {
                    $this->redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);

                    if (!empty($this->_servers[0]['auth'])) {

                        if (!($this->redis->auth($this->_servers[0]['auth']))) {
                            return;
                        } else {
                            $this->is_connected = true;
                        }

                    } else {
                        try {
                            $this->redis->select($this->_servers[0]['db']);
                            $ping = $this->redis->ping();

                            if (is_array($ping)) {
                                $ping = array_values($ping);

                                if (!empty($ping) && $ping[0] === '+PONG') {
                                    // We're connected if a connection without +AUTH receives a +PONG
                                    $this->is_connected = true;
                                }

                            }

                        } catch (Exception $e) {
                            $this->is_connected = false;
                        }

                    }

                }

            }

        }

    }

    /**
     * Get list of redis server information
     *
     * @return array
     *
     * @throws PhenyxDatabaseExceptionException
     * @throws PhenyxException
     * @since 1.0.0
     */
    public static function getRedisServer() {

        $server = [];
        // bypass the memory fatal error caused functions nesting on PS 1.5
        $sql = new DbQuery();
        $sql->select('`name`, `value`');
        $sql->from('configuration');
        $sql->where('`name` = \'EPH_REDIS_SERVER\' OR `name` = \'EPH_REDIS_PORT\' OR name = \'EPH_REDIS_AUTH\' OR name = \'EPH_REDIS_DB\'');
        $params = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS($sql, true, false);

        foreach ($params as $key => $val) {
            $server[$val['name']] = $val['value'];
        }

        return $server;
    }
	
	public static function getLastRedisServer() {

        $server = [];
        // bypass the memory fatal error caused functions nesting on PS 1.5
        $sql = new DbQuery();
        $sql->select('`id_redis_serveur`');
        $sql->from('redis_servers');
		$sql->orderBy('`id_redis_serveur` DESC');
        return Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue($sql);

        
    }

    /**
     *Add a redis server
     *
     * @param string $ip   IP address or hostname
     * @param int    $port Port number
     * @param string $auth Authentication key
     * @param int    $db   Redis database ID
     *
     * @return bool Whether the server was successfully added
     * @throws PhenyxDatabaseExceptionException
     * @throws PhenyxException
     */
    public static function addServer($ip, $port, $auth, $db) {

        $sql = new DbQuery();
        $sql->select('count(*)');
        $sql->from('redis_servers');
        $sql->where('`ip` = \'' . pSQL($ip) . '\'');
        $sql->where('`port` = ' . (int) $port);
        $sql->where('`auth` = \'' . pSQL($auth) . '\'');
        $sql->where('`db` = ' . (int) $db);

        if (Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue($sql, false)) {
            $context = Context::getContext();
            $context->controller->errors[] =
            Tools::displayError('Redis server has already been added');

            return false;
        }

        return Db::getInstance()->insert(
            'redis_servers',
            [
                'ip'   => pSQL($ip),
                'port' => (int) $port,
                'auth' => pSQL($auth),
                'db'   => (int) $db,
            ],
            false,
            false
        );
    }

    /**
     * Get list of redis server information
     *
     * @return array
     * @throws PhenyxDatabaseExceptionException
     * @throws PhenyxException
     */
    public static function getRedisServers() {

        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('redis_servers');

        return Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS($sql, true, false);
    }

    /**
     * Delete a redis server
     *
     * @param int $idServer Server ID
     *
     * @return bool Whether the server was successfully deleted
     * @throws PhenyxDatabaseExceptionException
     */
    public static function deleteServer($idServer) {

        return Db::getInstance()->delete(
            'redis_servers',
            '`id_redis_server` = ' . (int) $idServer,
            0,
            false
        );
    }

    /**
     * @since 1.0.0
     */
    public function __destruct() {

        $this->close();
    }

    /**
     * Close connection to redis server
     *
     * @return bool
     *
     * @since 1.0.0
     */
    protected function close() {

        if (!$this->is_connected) {
            return false;
        }

        // Don't close the connection, needs to be persistent across PHP-sessions
        return true;
    }

    /**
     * @see   Cache::flush()
     *
     * @return bool
     *
     * @since 1.0.0
     */
    public function flush() {

        if (!$this->is_connected) {
            return false;
        }

        return (bool) $this->redis->flushDB();
    }

    /**
     * @see   Cache::_set()
     *
     * @return bool
     *
     * @since 1.0.0
     */
    protected function _set($key, $value, $ttl = 0) {

        if (!$this->is_connected) {
            return false;
        }

        return $this->redis->set($key, $value);
    }

    /**
     * @see   Cache::_exists()
     *
     * @return bool
     *
     * @since 1.0.0
     */
    protected function _exists($key) {

        if (!$this->is_connected) {
            return false;
        }

        return (bool) $this->_get($key);
    }

    /**
     * @see   Cache::_get()
     *
     * @return bool
     *
     * @since 1.0.0
     */
    protected function _get($key) {

        if (!$this->is_connected) {
            return false;
        }

        return $this->redis->get($key);
    }

    /**
     * @see   Cache::_delete()
     *
     * @return bool
     *
     * @since 1.0.0
     */
    protected function _delete($key) {

        if (!$this->is_connected) {
            return false;
        }

        return $this->redis->del($key);
    }

    /**
     * @see   Cache::_writeKeys()
     *
     * @return bool
     *
     * @since 1.0.0
     */
    protected function _writeKeys() {

        if (!$this->is_connected) {
            return false;
        }

        $this->redis->set(_COOKIE_IV_, $this->keys);

        return true;
    }

}
