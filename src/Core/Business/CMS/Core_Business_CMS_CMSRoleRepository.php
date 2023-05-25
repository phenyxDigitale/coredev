<?php

/**
 * Class Core_Business_CMS_CMSRoleRepository
 *
 * @since 1.9.1.0
 */
// @codingStandardsIgnoreStart
class Core_Business_CMS_CMSRoleRepository extends Core_Foundation_Database_EntityRepository {

    // @codingStandardsIgnoreEnd

    /**
     * Return all CMSRoles which are already associated
     *
     * @return array|null
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function getCMSRolesAssociated() {

        $sql = '
            SELECT *
            FROM `' . $this->getTableNameWithPrefix() . '`
            WHERE `id_cms` != 0';

        return $this->hydrateMany($this->db->select($sql));
    }
}
