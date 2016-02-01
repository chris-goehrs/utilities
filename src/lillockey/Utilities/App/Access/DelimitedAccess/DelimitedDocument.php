<?php
/**
 * Created by PhpStorm.
 * User: cgoehrs
 * Date: 1/27/2016
 * Time: 2:55 PM
 */

namespace lillockey\Utilities\App\Access\DelimitedAccess;


class DelimitedDocument
{
    const DELIMITED_FILE__NEW_LINE__WINDOWS = "\r\n";
    const DELIMITED_FILE__NEW_LINE__NIX = "\n";


    private $header_keys = array();

    /** @var DelimitedRow[] $rows */
    private $rows = array();

    protected function __construct(array $data_rows, array $header_keys = array())
    {
        if(is_array($header_keys))
            $this->header_keys = $header_keys;

        foreach($data_rows as $raw){
            $this->rows[] = new DelimitedRow($raw, $this->get_header_row());
        }
    }

    public function get_full_array()
    {
        $ar = array();
        $header = $this->get_header_row();

        if(sizeof($header))
            $ar[] = $header;

        foreach($this->rows as $row) {
            $ar[] = $row->toArray();
        }

        return $ar;
    }

    public function &get_data_rows()
    {
        return $this->rows;
    }

    public function get_header_row()
    {
        return $this->header_keys;
    }

    public function append(DelimitedRow $row)
    {
        $this->rows[] = $row;
    }

    public function insert($before_row_index, DelimitedRow $row)
    {
        if($before_row_index < 0) return false;
        if($before_row_index >= sizeof($this->rows)) return false;
        if($row == null) return false;

        if($before_row_index == 0){
            $this->rows = array_merge(array($row), $this->rows);
        }else{
            $b = array_slice($this->rows, 0, $before_row_index, false);
            $a = array_slice($this->rows, $before_row_index, sizeof($this->rows) - $before_row_index, false);
            $this->rows = array_merge($b, array($row), $a);
        }

        return true;
    }


}