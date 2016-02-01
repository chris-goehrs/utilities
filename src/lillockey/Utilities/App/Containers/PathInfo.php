<?php
/**
 * Created by PhpStorm.
 * User: cgoehrs
 * Date: 2/1/2016
 * Time: 1:16 PM
 */

namespace lillockey\Utilities\App\Containers;


use lillockey\Utilities\App\Access\ArrayAccess\AccessibleArray;

class PathInfo
{
    public $dirname = null;
    public $basename = null;
    public $extension = null;
    public $filename = null;

    public function __construct(AccessibleArray $ar = null)
    {
        if($ar == null) return;
        $this->dirname = $ar->string('dirname');
        $this->basename = $ar->string('basename');
        $this->extension = $ar->string('extension');
        $this->filename = $ar->string('filename');
    }
}