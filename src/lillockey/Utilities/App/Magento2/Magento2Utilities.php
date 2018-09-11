<?php
/**
 * Created by PhpStorm.
 * User: cgoehrs
 * Date: 9/7/2018
 * Time: 1:35 PM
 */

namespace lillockey\Utilities\App\Magento2;


use lillockey\Utilities\App\AbstractUtility;
use lillockey\Utilities\App\Access\ArrayAccess\AccessibleArray;
use lillockey\Utilities\App\InstanceHolder;

class Magento2Utilities extends AbstractUtility
{
    /**
     * Is the customer logged in?
     * @return bool
     */
    public function isCustomerLoggedIn()
    {
        return $this->loggedInCustomerId() != null;
    }

    /**
     * Retrieves the currently logged in customer (if any)
     * @return int|null
     */
    public function loggedInCustomerId()
    {
        $session = InstanceHolder::session();
        $customer_base = $session->v_array('customer_base');
        if(!is_array($customer_base)) return null;
        if(sizeof($customer_base) == 0) return null;
        $cb = new AccessibleArray($customer_base);
        $user = $cb->int('customer_id');
        if($user === null) return null;
        if($user <= 0) return null;
        return $user;
    }

    /**
     * @return QuoteModel The cart details
     * @throws \Exception
     */
    public function getCartQuote()
    {
        $session = InstanceHolder::session();
        $checkout_ar = $session->v_array("checkout");
        if($checkout_ar == null) return new QuoteModel();
        if(is_array($checkout_ar) && sizeof($checkout_ar) == 0) return new QuoteModel();
        $checkout = new AccessibleArray($checkout_ar);

        //Grab the first quote key
        $util = InstanceHolder::util();
        $keys = array_keys($checkout_ar);
        $quote_key = null;
        foreach($keys as $key){
            if($util->str_left_is($key, "quote_id_", true)){
                $quote_key = $key;
                break;
            }
        }

        //Grab the quote id
        $quote_id = $checkout->int($quote_key);
        $db = InstanceHolder::db($this->name);
        $quote = $db->select_one_by('quote','entity_id', $quote_id, null, 'ASC', \PDO::FETCH_CLASS, QuoteModel::class);
        if($quote instanceof QuoteModel){
            return $quote;
        }else{
            return new QuoteModel();
        }
    }
}