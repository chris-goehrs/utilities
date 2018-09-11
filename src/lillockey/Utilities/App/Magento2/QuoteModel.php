<?php
/**
 * Created by PhpStorm.
 * User: cgoehrs
 * Date: 9/11/2018
 * Time: 11:18 AM
 */

namespace lillockey\Utilities\App\Magento2;


class QuoteModel
{
    public $entity_id;
    public $store_id;
    public $created_at;
    public $updated_at;
    public $converted_at;
    public $is_active;
    public $is_virtual;
    public $is_multi_shipping;
    public $items_count;
    public $items_qty;
    public $orig_order_id;
    public $store_to_base_rate;
    public $store_to_quote_rate;
    public $base_currency_code;
    public $store_currency_code;
    public $quote_currency_code;
    public $grand_total;
    public $base_grand_total;
    public $checkout_method;
    public $customer_id;
    public $customer_tax_class_id;
    public $customer_group_id;
    public $customer_email;
    public $customer_prefix;
    public $customer_firstname;
    public $customer_middlename;
    public $customer_lastname;
    public $customer_suffix;
    public $customer_dob;
    public $customer_note;
    public $customer_note_notify;
    public $customer_is_guest;
    public $remote_ip;
    public $applied_rule_ids;
    public $reserved_order_id;
    public $password_hash;
    public $coupon_code;
    public $global_currency_code;
    public $base_to_global_rate;
    public $base_to_quote_rate;
    public $customer_taxvat;
    public $customer_gender;
    public $subtotal;
    public $base_subtotal;
    public $subtotal_with_discount;
    public $base_subtotal_with_discount;
    public $is_changed;
    public $trigger_recollect;
    public $ext_shipping_info;
    public $is_persistent;
    public $gift_message_id;

    /**
     * Total item count
     * @return int
     */
    public function getItemsCount()
    {
        if($this->items_count == null) return 0;
        return intval($this->items_count);
    }

    /**
     * Total item quantity
     * @param bool $as_int
     * @return int|float
     */
    public function getItemsQty($as_int = true)
    {
        if($this->items_qty == null) return 0;
        return $as_int ? intval($this->items_qty) : $this->items_qty;
    }

    /**
     * Grand total
     * @return mixed
     */
    public function getGrandTotal($as_int = true)
    {
        if($this->grand_total == null) return 0;
        return $as_int ? intval($this->grand_total) : $this->grand_total;
    }


}