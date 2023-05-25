<?php

/**
 * Class Adapter_EntityMapper
 *
 * @since 1.9.1.0
 */

// @codingStandardsIgnoreStart
class Adapter_EntityMapper {

    // @codingStandardsIgnoreEnd

    /**
     * Load ObjectModel
     *
     * @param int    $id
     * @param int    $idLang
     * @param object $entity
     * @param mixed  $entityDefs
     * @param int    $idCompany
     * @param bool   $shouldCacheObjects
     *
     * @throws PhenyxShopDatabaseException
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function load($id, $idLang, $entity, $entityDefs, $shouldCacheObjects) {

        // Load object from database if object id is present
        $cacheId = 'objectmodel_' . $entityDefs['classname'] . '_' . (int) $id . '_' . (int) $idLang;

        if (!$shouldCacheObjects || !Cache::isStored($cacheId)) {
            $sql = new DbQuery();
            $sql->from($entityDefs['table'], 'a');
            $sql->where('a.`' . bqSQL($entityDefs['primary']) . '` = ' . (int) $id);

            // Get lang informations

            if ($idLang && isset($entityDefs['multilang']) && $entityDefs['multilang']) {
                $sql->leftJoin($entityDefs['table'] . '_lang', 'b', 'a.`' . bqSQL($entityDefs['primary']) . '` = b.`' . bqSQL($entityDefs['primary']) . '` AND b.`id_lang` = ' . (int) $idLang);

            }

            if ($objectData = Db::getInstance()->getRow($sql)) {

                if (!$idLang && isset($entityDefs['multilang']) && $entityDefs['multilang']) {
                    $sql = (new DbQuery())
                        ->select('*')
                        ->from($entityDefs['table'] . '_lang')
                        ->where('`' . $entityDefs['primary'] . '` = ' . (int) $id);

                    if ($objectDatasLang = Db::getInstance()->executeS($sql)) {

                        foreach ($objectDatasLang as $row) {

                            foreach ($row as $key => $value) {

                                if ($key !== $entityDefs['primary']
                                    && $key !== 'id_lang'
                                    && property_exists($entity, $key)) {

                                    if (!isset($objectData[$key]) || !is_array($objectData[$key])) {
                                        $objectData[$key] = [];
                                    }

                                    $objectData[$key][$row['id_lang']] = $value;
                                }

                            }

                        }

                    }

                }

                $entity->id = (int) $id;

                foreach ($objectData as $key => $value) {

                    if (property_exists($entity, $key)) {
                        $entity->{$key}
                        = $value;
                    } else {
                        unset($objectData[$key]);
                    }

                }

                if ($shouldCacheObjects) {
                    Cache::store($cacheId, $objectData);
                }

            }

        } else {
            $objectData = Cache::retrieve($cacheId);

            if ($objectData) {
                $entity->id = (int) $id;

                foreach ($objectData as $key => $value) {
                    $entity->{$key}
                    = $value;
                }

            }

        }

    }

}
