<?php
/**
 * Created by PhpStorm.
 * User: chris
 * Date: 6/12/2017
 * Time: 9:10 PM
 */

namespace lillockey\Utilities\App\DBT;


use lillockey\Utilities\App\Access\ObjectAccessible;

class DBTQuery
{
    private $_query;
    private $_arguments;

    /**
     * DBTQuery constructor.
     * @param string $q
     * @param array|ObjectAccessible|null $args
     */
    public function __construct($q, $args = null)
    {
        $this->_query = $q;
        if($args != null){
            if(is_array($args)){
                $this->_arguments = $args;
            }elseif($args instanceof ObjectAccessible){
                $this->_arguments = $args->toArray();
            }else{
                $this->_arguments = null;
            }
        }
    }

    /**
     * The PDO query string
     * @return string
     */
    public function getQuery()
    {
        return $this->_query;
    }

    /**
     * The arguments for the query
     * @return array|null
     */
    public function getArguments()
    {
        return $this->_arguments;
    }


}