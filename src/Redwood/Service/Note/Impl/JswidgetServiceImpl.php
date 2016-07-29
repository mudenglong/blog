<?php
namespace Redwood\Service\Note\Impl;

use Redwood\Service\Common\BaseService;
use Redwood\Service\Note\JswidgetService;

class JswidgetServiceImpl extends BaseService implements JswidgetService
{
    public function createJswidget($jswidget)
    {
        $jswidget['userId'] = $this->getCurrentUser()->id;
        $jswidget['createTime'] = time();
        return $this->getJswidgetDao()->addJswidget($jswidget);
    }

    public function updateJswidget($jswidget)
    {
        $jswidget['updateTime'] = time();
        return $this->getJswidgetDao()->updateJswidget($jswidget['id'], $jswidget);
    }

    public function getJswidget($id)
    {
        $jswidget = $this->getJswidgetDao()->getJswidget($id);
        if(!$jswidget){
            return null;
        } else {
            return $jswidget;
        }
    }

    public function findJswidgetById($id)
    {
        # code...
    }

    private function getJswidgetDao()
    {
        return $this->createDao('Note.JswidgetDao');
    }

}