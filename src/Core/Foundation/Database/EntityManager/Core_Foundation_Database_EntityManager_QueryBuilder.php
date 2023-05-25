<?php

/**
 * Class Core_Foundation_Database_EntityManager_QueryBuilder
 *
 * @since 1.9.1.0
 */
// @codingStandardsIgnoreStart
class Core_Foundation_Database_EntityManager_QueryBuilder {

    // @codingStandardsIgnoreEnd

    protected $db;

    /**
     * Core_Foundation_Database_EntityManager_QueryBuilder constructor.
     *
     * @param Core_Foundation_Database_DatabaseInterface $db
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function __construct(Core_Foundation_Database_DatabaseInterface $db) {

        $this->db = $db;
    }

    /**
     * @param string $value
     *
     * @return string
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function quote($value) {

        $escaped = $this->db->escape($value);

        if (is_string($value)) {
            return "'" . $escaped . "'";
        } else {
            return $escaped;
        }

    }

    /**
     * @param string $andOrOr
     * @param array  $conditions
     *
     * @return string
     * @throws Core_Foundation_Database_Exception
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function buildWhereConditions($andOrOr, array $conditions) {

        $operator = strtoupper($andOrOr);

        if ($operator !== 'AND' && $operator !== 'OR') {
            throw new Core_Foundation_Database_Exception(sprintf('Invalid operator %s - must be "and" or "or".', $andOrOr));
        }

        $parts = [];

        foreach ($conditions as $key => $value) {

            if (is_scalar($value)) {
                $parts[] = $key . ' = ' . $this->quote($value);
            } else {
                $list = [];

                foreach ($value as $item) {
                    $list[] = $this->quote($item);
                }

                $parts[] = $key . ' IN (' . implode(', ', $list) . ')';
            }

        }

        return implode(" $operator ", $parts);
    }

}
