<?php

/**
 * Class Core_Business_CMS_CMSRepository
 *
 * @since 1.9.1.0
 */
// @codingStandardsIgnoreStart
class Core_Business_CMS_CMSRepository extends Core_Foundation_Database_EntityRepository {

    // @codingStandardsIgnoreEnd

    /**
     * Return all CMSRepositories depending on $id_lang/$id_company tuple
     *
     * @param int $idLang
     * @param int $idCompany
     *
     * @return array|null
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function i10nFindAll($idLang, $idCompany) {

        $sql = '
            SELECT *
            FROM `' . $this->getTableNameWithPrefix() . '` c
            JOIN `' . $this->getPrefix() . 'cms_lang` cl ON c.`id_cms`= cl.`id_cms`
            WHERE cl.`id_lang` = ' . (int) $idLang . '
            AND cl.`id_shop` = ' . (int) $idCompany . '

        ';

        return $this->hydrateMany($this->db->select($sql));
    }

    /**
     * Return all CMSRepositories depending on $id_lang/$id_company tuple
     *
     * @param int $idCms
     * @param int $idLang
     * @param int $idCompany
     *
     * @return CMS|null
     * @throws Core_Foundation_Database_Exception
     *
     * @since 1.9.1.0
     * @version 1.0.0
     */
    public function i10nFindOneById($idCms, $idLang, $idCompany) {

        $sql = '
            SELECT *
            FROM `' . $this->getTableNameWithPrefix() . '` c
            JOIN `' . $this->getPrefix() . 'cms_lang` cl ON c.`id_cms`= cl.`id_cms`
            WHERE c.`id_cms` = ' . (int) $idCms . '
            AND cl.`id_lang` = ' . (int) $idLang . '
            AND cl.`id_shop` = ' . (int) $idCompany . '
            LIMIT 0 , 1
        ';

        return $this->hydrateOne($this->db->select($sql));
    }

    /**
     * Return CMSRepository lang associative table name
     *
     * @return string
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    protected function getLanguageTableNameWithPrefix() {

        return $this->getTableNameWithPrefix() . '_lang';
    }
}
