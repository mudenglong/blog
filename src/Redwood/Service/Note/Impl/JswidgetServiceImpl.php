<?php
namespace Redwood\Service\Note\Impl;

use Redwood\Service\Common\BaseService;
use Redwood\Service\Note\JswidgetService;

use Redwood\Common\ArrayToolkit;

class JswidgetServiceImpl extends BaseService implements JswidgetService
{
    public function createJswidget($jswidget)
    {
        $jswidget['userId'] = $this->getCurrentUser()->id;
        $jswidget['createTime'] = time();
        $this->filterJswidgetFields($jswidget);
        $newJswidget = $this->getJswidgetDao()->addJswidget(JswidgetSerialize::serialize($jswidget));

        $newJswidget = $this->getJswidget($newJswidget['id']);
        return $newJswidget;
    }

    public function updateJswidget($id, $fields)
    {   
        $jswidget = $this->getJswidgetDao()->getJswidget($id);
        if (empty($jswidget)) {
            throw $this->createServiceException('组件不存在，更新失败！');
        }

        $this->filterJswidgetFields($fields);

        $fields['updateTime'] = time();
        // 存储前过滤一下 tags
        $fields = JswidgetSerialize::serialize($fields);

        return JswidgetSerialize::unserialize(
            $this->getJswidgetDao()->updateJswidget($id, $fields)
        );
    }

    private function filterJswidgetFields(array &$fields)
    {
        // @todo 
        // if (!empty($fields['content'])) {
        //     $fields['about'] = $this->purifyHtml($fields['about']);
        // }

        if (!empty($fields['tags'])) {
            $fields['tags'] = explode(',', $fields['tags']);
            $fields['tags'] = $this->getTagService()->getTagsByNames($fields['tags']);

            array_walk($fields['tags'], function(&$item, $key) {
                $item = (int) $item['id'];
            });
        }
        return $fields;
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

    private function filterJswidgetTags(&$fields)
    {
       
        if (!empty($fields['tags'])) {
            $tempTags = explode('|', $fields['tags']);
            $fields['tags'] = implode(",", $tempTags);
        }

        return $fields;
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

    public function waveJswidget($id, $field, $diff)
    {
        return $this->getJswidgetDao()->waveJswidget($id, $field, $diff);
    }

    protected function convertFiltersToOrderBy($orderBy)
    {
        switch ($orderBy) {
            case 'latest':
                $filters = array('createTime', 'DESC');
                break;
            case 'viewest':
                $filters = array('views', 'DESC');
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

    protected function getTagService()
    {
        return $this->createService('Taxonomy.TagService');        
    }

}




class JswidgetSerialize
{   
    // 数组 -> 字符串
    public static function serialize(array &$jswidget)
    {
        if (isset($jswidget['tags'])) {
            if (is_array($jswidget['tags']) and !empty($jswidget['tags'])) {
                $jswidget['tags'] = '|' . implode('|', $jswidget['tags']) . '|';
            } else {
                $jswidget['tags'] = '';
            }
        }

        return $jswidget;
    }

    public static function unserialize(array $jswidget = null)
    {
        if (empty($jswidget)) {
            return $jswidget;
        }

        if(empty($jswidget['tags'] )) {
            $jswidget['tags'] = array();
        } else {
            $jswidget['tags'] = explode('|', trim($jswidget['tags'], '|'));
        }

        return $jswidget;
    }

    public static function unserializes(array $jswidgets)
    {
        return array_map(function($jswidget) {
            return JswidgetSerialize::unserialize($jswidget);
        }, $jswidgets);
    }
}
