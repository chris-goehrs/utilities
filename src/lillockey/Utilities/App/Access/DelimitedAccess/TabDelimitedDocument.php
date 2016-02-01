<?php
/**
 * Created by PhpStorm.
 * User: cgoehrs
 * Date: 2/1/2016
 * Time: 10:43 AM
 */

namespace lillockey\Utilities\App\Access\DelimitedAccess;


class TabDelimitedDocument extends DelimitedDocument
{
    public function __construct($tab_delimited_text, $first_row_headers = true)
    {
        //Sort out which new line character to use
        $nl = self::DELIMITED_FILE__NEW_LINE__NIX;
        if(strpos($tab_delimited_text, self::DELIMITED_FILE__NEW_LINE__WINDOWS) !== false)
            $nl = self::DELIMITED_FILE__NEW_LINE__WINDOWS;

        //Grab the initial rows
        $rows = explode($nl, $tab_delimited_text);

        //Initialize the data
        $data_rows = array();
        $header_keys = array();

        //Form the document data and headers
        foreach($rows as $row){
            $columns = explode("\t", $row);

            if($first_row_headers){
                $first_row_headers = false;
                $header_keys = $columns;
            }else{
                $data_rows[] = $columns;
            }
        }

        parent::__construct($data_rows, $header_keys);
    }
}