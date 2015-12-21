<?php
namespace Alin\Cadou\Api\Data;

interface CadouInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const CADOU_ID = 'cadou_id';
    const PRODUCT_ID = 'product_id';
    const QUOTE_ID = 'quote_id';
    const STORE_ID = 'store_id';
    const ORDER_ID = 'order_id';
    const FULLNAME = 'fullname';
    const BIRTHDATE = 'birthdate';
    const ADDRESS = 'address';
    const NOTIFIED = 'notified';
    const SORT_ORDER_DESC = 'desc';
    const SORT_ORDER_ASC = 'ASC';


    public function getId();

    public function getProductId();

    public function getQuoteId();

    public function getStoreId();

    public function getFullname();

    public function getBirthdate();

    public function getAddress();

    public function getNotified();

    public function setId($id);

    public function setProductId($productId);

    public function setQuoteId($quoteId);

    public function setStoreId($storeId);

    public function setFullname($fullname);

    public function setBirthdate($birthdate);

    public function setAddress($address);

    public function setNotified($notified);

}