<?php
/**
 * Created by PhpStorm.
 * User: cgoehrs
 * Date: 2/1/2016
 * Time: 1:53 PM
 */

namespace lillockey\Utilities\App\Containers;


use lillockey\Utilities\App\Access\ArrayAccess\AccessibleArray;

class FileStats
{
    public $dev;
    public $ino;
    public $mode;
    public $nlink;
    public $uid;
    public $gid;
    public $rdev;
    public $size;
    public $atime;
    public $mtime;
    public $ctime;
    public $blksize;
    public $blocks;

    public function __construct(AccessibleArray $ar)
    {
        $this->dev = $ar->int('dev');
        $this->ino = $ar->int('ino');
        $this->mode = $ar->int('mode');
        $this->nlink = $ar->int('nlink');
        $this->uid = $ar->int('uid');
        $this->gid = $ar->int('gid');
        $this->rdev = $ar->int('rdev');
        $this->size = $ar->int('size');
        $this->atime = $ar->int('atime');
        $this->mtime = $ar->int('mtime');
        $this->ctime = $ar->int('ctime');
        $this->blksize = $ar->int('blksize');
        $this->blocks = $ar->int('blocks');
    }
}