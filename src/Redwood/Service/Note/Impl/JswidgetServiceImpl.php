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




    public function deleteJswidget($id){
        $jswidget = $this->getJswidget($id);
        if (empty($jswidget)) {
            $this->createNotFoundException("组件#{$id}不存在, 删除失败");
        }

        $this->getJswidgetDao()->deleteJswidget($id);
        
        // @todo
        // $content = strip_tags($jswidget['content']);
        // $this->getLogService()->info('jswidget', 'delete', "删除{$jswidget['targetType']}(#{$jswidget['targetId']})的公告《{$content}》(#{$jswidget['id']})");

        // $this->dispatchEvent('jswidget.delete', $jswidget);

        return true;
    }

    public function findJswidgetById($id)
    {
        # code...
    }

    public function searchJswidgetCount($conditions)
    {
        
        return $this->getJswidgetDao()->searchJswidgetCount($conditions);
    }

    public function searchJswidget(array $conditions, $orderBy, $start, $limit)
    {
        $filters = array();
        if ($orderBy) {
            $filters = $this->convertFiltersToOrderBy($orderBy);
        }

        $jswidget = $this->getJswidgetDao()->searchJswidget($conditions, $filters, $start, $limit);

        return $jswidget;
    }

    protected function convertFiltersToOrderBy($orderBy)
    {
        switch ($orderBy) {
            case 'latest':
                $filters = array('createTime', 'DESC');
                break;
            default:
                $filters = array('createTime', 'DESC');
                break;
        }
        return $filters;
    }



    private function getJswidgetDao()
    {
        return $this->createDao('Note.JswidgetDao');
    }

}