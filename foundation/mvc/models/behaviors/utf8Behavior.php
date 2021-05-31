<?php

/**
 * Projects4Me Copyright (c) 2017. Licensing : http://legal.projects4.me/LICENSE.txt. Do not remove this line
 */

namespace Gaia\MVC\Models\Behaviors;

use Phalcon\Mvc\ModelInterface;
use Phalcon\Mvc\Model\BehaviorInterface;
use Phalcon\Mvc\Model\Behavior;
use Phalcon\Mvc\Model\Message;


class utf8Behavior extends Behavior implements BehaviorInterface
{
    public function notify($eventType,ModelInterface $model)
    {
        if (method_exists($this, $eventType)) {
            $this->$eventType($model);
        }
        return $model;
    }
    
    protected function beforeCreate(&$model)
    {
        $model->description = utf8_encode($model->description);
    }

    protected function afterFetch(&$model)
    {
        if($model->data){
            foreach ($model->data as $issue) {
                if($issue->description) {
                    // $test = &$model->data->current();
                    // $test->description = utf8_decode($issue->description);
                    ($model->data->current())->writeAttribute("description",123);
                    $model->data->current()->save();
                    $test = $model->data->current();
                    $testing = $model->data->toArray();
                } else break;
            }
            $testing = $model->data->toArray();
        }
    }
}