<?php

/**
 * Interface Core_Foundation_Database_EntityInterface
 *
 * @since 1.9.1.0
 */
// @codingStandardsIgnoreStart
interface Core_Foundation_Database_EntityInterface {
    // @codingStandardsIgnoreEnd

    /**
     * Returns the name of the repository class for this entity.
     * If unspecified, a generic repository will be used for the entity.
     *
     * @return string or falsey value
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public static function getRepositoryClassName();

    /**
     * @return mixed
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function save();

    /**
     * @return mixed
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function delete();

    /**
     * @param array $keyValueData
     *
     * @return mixed
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function hydrate(array $keyValueData);
}
