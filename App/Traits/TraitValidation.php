<?php
/**
 * Created by PhpStorm.
 * User: Christopher Goehrs
 * Date: 1/8/2015
 * Time: 10:23 PM
 */

namespace Missilesilo\Utilities\App\Traits;

trait TraitValidation
{
    /* ==============================================================================================
	 * START: Validation helpers
	 * Added By: Christopher Goehrs
	 * Added On: 6/5/2014
	 * This validates various field types
	 * ==============================================================================================
	 */

    /**
     * Filters the column/table name to match MYSQL documentation
     *
     * Based on: http://stackoverflow.com/questions/4977898/check-for-valid-sql-column-name
     * @param string $field
     * @return mixed
     */
    public function field_name_is_valid($field)
    {
        return filter_var($field, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '^[a-zA-Z_][a-zA-Z0-9_]*$^']]);
    }

    /**
     * Checks to see if the provided email address is valid
     * @param string $email - the email address to be validated
     * @param boolean $check_mx - (optional) true will attempt to validate the DNS records for the given email address
     * @return boolean - true if valid/false if invalid
     */
    public function validate_email($email, $check_mx = true)
    {
        $sanitized_email = filter_var($email, FILTER_VALIDATE_EMAIL);
        $email_as_address_is_okay = $sanitized_email?true:false;
        if(!$check_mx || !$email_as_address_is_okay) return $email_as_address_is_okay;

        list($user, $domain) = explode('@', $sanitized_email);
        return checkdnsrr($domain, 'MX');
    }

    /**
     * Validates the credit card number using the Luhn algorithm and a null checker
     *
     * Luhn algorithm number checker - (c) 2005-2008 shaman - www.planzero.org *
     * This code has been released into the public domain, however please      *
     * give credit to the original author where possible.                      *
     *
     * @param string $number - The credit card number to be validated
     * @return boolean true if okay/false if not
     */
    public function validate_credit_card_number($number)
    {
        if(strlen($number) == 0) return false;

        // Strip any non-digits (useful for credit card numbers with spaces and hyphens)
        $number=preg_replace('/\D/', '', $number);

        // Set the string length and parity
        $number_length=strlen($number);
        $parity=$number_length % 2;

        // Loop through each digit and do the maths
        $total=0;
        for ($i=0; $i<$number_length; $i++) {
            $digit=$number[$i];
            // Multiply alternate digits by two
            if ($i % 2 == $parity) {
                $digit*=2;
                // If the sum is two digits, add them together (in effect)
                if ($digit > 9) {
                    $digit-=9;
                }
            }
            // Total up the digits
            $total+=$digit;
        }

        // If the total mod 10 equals 0, the number is valid
        $is_valid = ($total % 10 == 0) ? TRUE : FALSE;
        return $is_valid;
    }

    /**
     * Validates a credit card expiration month and year. <br/>
     * The expiration is considered valid when it is both in the future and within 10 years of the current date
     *
     * @param string $month - MM
     * @param string $year - YYYY
     * @return boolean true if valid/false if not
     */
    public function validate_credit_card_expiration($month, $year)
    {
        //Grab the expiration time
        $exp_ts = mktime(0, 0, 0, ($month + 1), 1, $year);

        //Grab the current time
        $cur_ts = time();

        // Don't validate for dates more than 10 years in future.
        $max_ts = $cur_ts + (10 * 365 * 24 * 60 * 60);

        //Validate the date
        return $exp_ts > $cur_ts && $exp_ts < $max_ts;
    }

    /**
     * Validates a phone number
     *
     * @param string $number
     * @return boolean true if valid/false if not
     */
    public function validate_phone_number($number)
    {
        $regex = "/^(\d[\s-]?)?[\(\[\s-]{0,2}?\d{3}[\)\]\s-]{0,2}?\d{3}[\s-]?\d{4}$/i";
        return (preg_match( $regex, $number )?true:false);
    }

    /**
     * Validates the given date and ensures that it is at least a certain number of years ago
     * @param int $mm - Birth month (1-12)
     * @param int $dd - Birth day (1-31)
     * @param int $yyyy - Birth year (1-32767)
     * @param int $at_least_x_years_ago [optional] - the number of years in the past this date should be<br/>
     *              In order to be processed, it must be an integer greater than 0
     * @return bool
     */
    public function validate_birth_date($mm, $dd, $yyyy, $at_least_x_years_ago = 0)
    {
        //Make sure it's a valid Gregorian date
        $its_a_date = checkdate($mm, $dd, $yyyy);

        //If it's not, let's forget the rest and move on
        if(!$its_a_date) return 'not a real date';

        //Should we check for the number of years in the past this day ought to be?
        if(is_int($at_least_x_years_ago) && $at_least_x_years_ago > 0){
            //We should?  Here we go ...
            $past_date = strtotime("-{$at_least_x_years_ago} year", time());
            $listed_date = strtotime("$yyyy-$mm-$dd");
            return $listed_date <= $past_date;
        }else{
            //No?  Well then I guess everything is fine
            return true;
        }
    }

    /* ===========================================================================
     * END: Validation helpers
     * ===========================================================================
     */
}