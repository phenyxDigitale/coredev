<?php

/**
 * Class Adapter_AddressFactory
 *
 * @since 1.9.1.0
 */
// @codingStandardsIgnoreStart
class Adapter_AddressFactory {

    // @codingStandardsIgnoreEnd

    /**
     * Initialize an address corresponding to the specified id address or if empty to the
     * default shop configuration
     *
     * @param null $idAddress       Address ID
     * @param bool $withGeoLocation Indicates whether Geo location is used
     *
     * @return Address
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function findOrCreate($idAddress = null, $withGeoLocation = false) {

        $funcArgs = func_get_args();

        return call_user_func_array(['Address', 'initialize'], $funcArgs);
    }

    /**
     * Check if an address exists depending on given $idAddress
     *
     * @param int $idAddress Address ID
     *
     * @return bool Indicates whether the address exists
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function addressExists($idAddress) {

        return Address::addressExists($idAddress);
    }
}
