<?php

/**
 * Class Adapter_Database
 *
 * @since 1.9.1.0
 */
// @codingStandardsIgnoreStart
class Adapter_Database implements Core_Foundation_Database_DatabaseInterface {

    // @codingStandardsIgnoreEnd

    /**
     * Perform a SELECT sql statement
     *
     * @param string $sql
     *
     * @return array|false
     *
     * @throws PhenyxShopDatabaseException
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function select($sql) {

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Escape $unsafe to be used into a SQL statement
     *
     * @param mixed $unsafeData
     *
     * @return string
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function escape($unsafeData) {

        // Prepare required params
        $htmlOk = true;
        $bqSql = true;

        return Db::getInstance()->escape($unsafeData, $htmlOk, $bqSql);
    }
}
