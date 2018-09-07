<?php
/**
 * Created by PhpStorm.
 * User: cgoehrs
 * Date: 7/31/2017
 * Time: 1:36 PM
 */

namespace lillockey\Utilities\App\Wordpress;


use lillockey\Utilities\App\Access\ArrayAccess\AccessibleArray;

class WP_Utilities
{
    //////////////////////////////////////////////////
    // Post meta stuff
    //////////////////////////////////////////////////

    private static $metas = null;

    /**
     * Get all of the post meta as an array
     * @param $post_id
     * @return AccessibleArray
     */
    public static function meta_as_an_array($post_id)
    {
        if(self::$metas == null) self::$metas = new AccessibleArray();
        if(self::$metas->raw($post_id) instanceof AccessibleArray) return self::$metas->raw($post_id);

        $ar = new AccessibleArray();
        $metar = get_post_meta($post_id);
        if(!is_array($metar)) return $ar;

        foreach($metar as $key => $valar){
            if(is_array($valar)){
                $ar->set($key, array_pop($valar));
            }else{
                $ar->set($key, $valar);
            }
        }

        self::$metas->set($post_id, $ar);
        return $ar;
    }
}