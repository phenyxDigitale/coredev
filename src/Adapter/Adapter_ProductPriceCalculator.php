<?php

/**
 * Class Adapter_ProductPriceCalculator
 */
// @codingStandardIgnoreStart
class Adapter_ProductPriceCalculator {

    // @codingStandardIgnoreEnd

    /**
     * @param int          $idProduct
     * @param bool         $usetax
     * @param null         $idProductAttribute
     * @param int          $decimals
     * @param null         $divisor
     * @param bool         $onlyReduc
     * @param bool         $usereduc
     * @param int          $quantity
     * @param bool         $forceAssociatedTax
     * @param null         $idCustomer
     * @param null         $idCart
     * @param null         $idAddress
     * @param null         $specificPriceOutput
     * @param bool         $withEcotax
     * @param bool         $useGroupReduction
     * @param Context|null $context
     * @param bool         $useCustomerPrice
     *
     * @return float
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function getProductPrice(
        $idProduct,
        $usetax = true,
        $idProductAttribute = null,
        $decimals = 6,
        $divisor = null,
        $onlyReduc = false,
        $usereduc = true,
        $quantity = 1,
        $forceAssociatedTax = false,
        $idCustomer = null,
        $idCart = null,
        $idAddress = null,
        &$specificPriceOutput = null,
        $withEcotax = true,
        $useGroupReduction = true,
        Context $context = null,
        $useCustomerPrice = true
    ) {

        return Product::getPriceStatic(
            $idProduct,
            $usetax,
            $idProductAttribute,
            $decimals,
            $divisor,
            $onlyReduc,
            $usereduc,
            $quantity,
            $forceAssociatedTax,
            $idCustomer,
            $idCart,
            $idAddress,
            $specificPriceOutput,
            $withEcotax,
            $useGroupReduction,
            $context,
            $useCustomerPrice
        );
    }
}
