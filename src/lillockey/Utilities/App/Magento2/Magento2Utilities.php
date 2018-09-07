<?php
/**
 * Created by PhpStorm.
 * User: cgoehrs
 * Date: 9/7/2018
 * Time: 1:35 PM
 */

namespace lillockey\Utilities\App\Magento2;


use lillockey\Utilities\App\Access\ArrayAccess\AccessibleArray;
use lillockey\Utilities\App\InstanceHolder;

class Magento2Utilities
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
}