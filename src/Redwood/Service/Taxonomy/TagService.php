<?php
namespace Redwood\Service\Taxonomy;

interface TagService
{
   
    public function createTag($tag);

    public function updateTag($id, array $fields);

    public function deleteTag($id);

    public function getTag($id);

    public function getTagByName($name);

    public function getTagsByNames(array $names);

    public function getTagByLikeName($name);
    
    public function getTagsByIds($ids);

    public function isTagNameAvalieable($name);

    public function findTagById($id);

    public function searchTagCount($conditions);
    
    public function searchTags(array $conditions, $oderBy, $start, $limit);




    
}
