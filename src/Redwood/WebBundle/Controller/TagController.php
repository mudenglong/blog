<?php 

namespace Redwood\WebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Redwood\Common\Paginator;
use Redwood\Common\ArrayToolkit;

class TagController extends BaseController
{

    /**
     * 取所有标签
     * @return [type] [description]
     */
    public function indexAction()
    {     
        $conditions = array(
            'name' => ''
        );
        $filters['sort'] = 'latest';
        $orderBy = $this->convertFiltersToOrderBy($filters);
        $tags = $this->getTagService()->searchTags(
            $conditions,
            $orderBy,
            0,
            $this->getTagService()->searchTagCount($conditions)
        );
        var_dump($tags);

        return $this->render('RedwoodWebBundle:Tag:index.html.twig',array(
            'tags'=>$tags
        ));
    }

    
    protected function convertFiltersToOrderBy($filters)
    {
        switch ($filters['sort']) {
            case 'latest':
                $orderBy = array('createdTime', 'DESC');
                break;
            case 'jswidgetNum':
                $orderBy = array('jswidgetNum', 'DESC');
                break;
            default:
                $orderBy = array('createdTime', 'DESC');
                break;
        }
        return $orderBy;
    }


   

}