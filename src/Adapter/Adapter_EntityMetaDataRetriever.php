<?php

/**
 * Class Adapter_EntityMetaDataRetriever
 *
 * @since 1.9.1.0
 */
// @codingStandardsIgnoreStart
class Adapter_EntityMetaDataRetriever {

    // @codingStandardsIgnoreEnd

    /**
     * @param string $className
     *
     * @return Core_Foundation_Database_EntityMetaData
     * @throws Adapter_Exception
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function getEntityMetaData($className) {

        $metaData = new Core_Foundation_Database_EntityMetaData();

        $metaData->setEntityClassName($className);

        if (property_exists($className, 'definition')) {
            // Legacy entity
            $classVars = get_class_vars($className);
            $metaData->setTableName($classVars['definition']['table']);
            $metaData->setPrimaryKeyFieldNames([$classVars['definition']['primary']]);
        } else {
            throw new Adapter_Exception(
                sprintf(
                    'Cannot get metadata for entity `%s`.',
                    $className
                )
            );
        }

        return $metaData;
    }

}
