<?php
namespace Redwood\Service\Taxonomy\Impl;

use Redwood\Common\ArrayToolkit;
use Redwood\Service\Common\BaseService;
use Redwood\Service\Taxonomy\TagService;

class TagServiceImpl extends BaseService implements TagService
{   
    public function createTag($tag)
    {   
        if (empty($tag['name'])) {
            throw $this->createServiceException("标签名不能为空，添加失败！");
        }

        $tag['name'] = trim($tag['name'], " ");
        $origin = $tag['origin']?:'widget';

        if(!$this->isTagNameAvalieable($tag['name'])){
            throw $this->createServiceException("标签名#{$tag['name']}已存在，添加失败！");
        }

        $tag = ArrayToolkit::parts($tag, array('name'));
        $tag['createdTime'] = time();
        $tag['origin']      = $origin;
        $tag                = $this->getTagDao()->addTag($tag);

        // @todo
        // $this->getLogService()->info('tag', 'create', "添加标签{$tag['name']}(#{$tag['id']})");

        return $tag;
    }

    public function updateTag($id, array $fields)
    {
        $tag = $this->getTag($id);

        $origin = $tag['origin']?:'widget';

        if(!$this->isTagNameAvalieable($fields['name'])){
            throw $this->createServiceException("标签名#{$tag['name']}已存在，更新失败！");
        }

        $fields = ArrayToolkit::parts($fields, array('name'));
        $fields['createdTime'] = time();
        $fields['origin']      = $origin;

        // @todo
        // $this->getLogService()->info('tag', 'update', "编辑标签{$fields['name']}(#{$id})");
        // 
        return $this->getTagDao()->updateTag($id, $fields);
 
    }

    public function getTag($id)
    {
        $tag = $this->getTagDao()->getTag($id);
        if(!$tag){
            return null;
        } else {
            return $tag;
        }
    }

    public function getTagByName($name)
    {
        return $this->getTagDao()->getTagByName($name);
    }

    public function deleteTag($id){
        $tag = $this->getTag($id);
        if (empty($tag)) {
            $this->createNotFoundException("标签#{$id}不存在, 删除失败");
        }

        $this->getTagDao()->deleteTag($id);
        return true;
    }

    public function findTagById($id)
    {
        # code...
    }

    public function searchTagCount($conditions)
    {
        return $this->getTagDao()->searchTagCount($conditions);
    }

    public function searchTags(array $conditions, array $orderBy, $start, $limit)
    {
        $tag = $this->getTagDao()->searchTags($conditions, $orderBy, $start, $limit);
        return $tag;
    }

    public function isTagNameAvalieable($name)
    {
        $tag = $this->getTagByName($name);
        return empty($tag) ? true : false;
    }

    private function getTagDao()
    {
        return $this->createDao('Taxonomy.TagDao');
    }

}