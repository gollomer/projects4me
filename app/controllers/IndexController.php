<?php

class IndexController extends \Phalcon\Mvc\Controller
{

    public function indexAction()
    {
        $ctime = explode(" ",microtime());
        $ctime = $ctime[1] + $ctime[0];
        global $stime;
        print $ctime - $stime;
    }

}
