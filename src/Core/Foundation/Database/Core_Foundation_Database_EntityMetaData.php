<?php

/**
 * Class Core_Foundation_Database_EntityMetaData
 *
 * @since 1.9.1.0
 */
// @codingStandardsIgnoreStart
class Core_Foundation_Database_EntityMetaData {

    // @codingStandardsIgnoreEnd

    protected $tableName;
    protected $primaryKeyFieldnames;

    /**
     * @param string $name
     *
     * @return Core_Foundation_Database_EntityMetaData $this
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function setTableName($name) {

        $this->tableName = $name;

        return $this;
    }

    /**
     * @return string
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function getTableName() {

        return $this->tableName;
    }

    /**
     * @param array $primaryKeyFieldnames
     *
     * @return $this
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function setPrimaryKeyFieldNames(array $primaryKeyFieldnames) {

        $this->primaryKeyFieldnames = $primaryKeyFieldnames;

        return $this;
    }

    /**
     * @return mixed
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function getPrimaryKeyFieldnames() {

        return $this->primaryKeyFieldnames;
    }

    /**
     * @param string $entityClassName
     *
     * @return $this
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function setEntityClassName($entityClassName) {

        $this->entityClassName = $entityClassName;

        return $this;
    }

    /**
     * @return mixed
     *
     * @return string
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function getEntityClassName() {

        return $this->entityClassName;
    }
}
