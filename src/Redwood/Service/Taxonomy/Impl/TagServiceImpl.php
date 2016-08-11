<?php
namespace Redwood\Service\Taxonomy\Impl;

use Redwood\Common\ArrayToolkit;
use Redwood\Service\Common\BaseService;
use Redwood\Service\Taxonomy\TagService;

class TagServiceImpl extends BaseService implements TagService
{   
    public function createTag($tag)
    {
        var_dump($tag); exit;
        
        $tag = ArrayToolkit::parts($tag, array('name'));

        $tag                = $this->filterTagFields($tag);
        $tag['createdTime'] = time();
        $tag                = $this->setTagOrg($tag);
        $tag                = $this->getTagDao()->addTag($tag);

        // @todo
        // $this->getLogService()->info('tag', 'create', "添加标签{$tag['name']}(#{$tag['id']})");

        return $tag;
    }

    public function updateTag($id, array $fields)
    {
       
       $tag = $this->getTag($id);

       if (empty($tag)) {
           throw $this->createServiceException("标签(#{$id})不存在，更新失败！");
       }

       $fields = ArrayToolkit::parts($fields, array('name'));

       
       $this->filterTagFields($fields, $tag);

       // @todo
       // $this->getLogService()->info('tag', 'update', "编辑标签{$fields['name']}(#{$id})");

        $fields['updatedTime'] = time();
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


    protected function filterTagFields(&$tag, $relatedTag = null)
    {
        if (empty($tag['name'])) {
            throw $this->createServiceException('标签名不能为空，添加失败！');
        }

        $tag['name'] = (string) $tag['name'];

        $exclude = $relatedTag ? $relatedTag['name'] : null;

        if (!$this->isTagNameAvalieable($tag['name'], $exclude)) {
            throw $this->createServiceException('该标签名已存在，添加失败！');
        }

        return $tag;
    }

    private function getTagDao()
    {
        return $this->createDao('Taxonomy.TagDao');
    }

}