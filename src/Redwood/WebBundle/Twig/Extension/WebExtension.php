<?php
namespace Redwood\WebBundle\Twig\Extension;

class WebExtension extends \Twig_Extension
{
    protected $container;

    public function __construct ($container)
    {
        $this->container = $container;
    }

    public function getFilters ()
    {
        return array(
            'smart_time' => new \Twig_Filter_Method($this, 'smarttimeFilter') ,
            'tags_join' => new \Twig_SimpleFilter($this, 'tagsJoinFilter')
        );
    }

    public function getFunctions()
    {
        return array(
            'dict' => new \Twig_Function_Method($this, 'getDict') ,
            'dict_text' => new \Twig_Function_Method($this, 'getDictText', array('is_safe' => array('html'))) ,
        );
    }

    /**
     * support sqlUri convert webUri
     * For example: public//user/2014/09-13/test.jpg convert www.XXX.com/image/4564fsdf2164.jpg
     * @param  string $sqlUri 
     * @return boolen
     */
    public function sqlUriConvertWebUri($sqlUri)
    {
        $assets = $this->container->get('templating.helper.assets');
        $parts = explode('://', $sqlUri);
        // if (empty($parts) or count($parts)!=2) {
        //     throw $this->createServiceException('解析文件URI({$uri})失败！');
        // }
        $uri['access'] = $parts[0];
        $uri['path'] = $parts[1];
        if ($uri['access'] == 'public') {
            $url = rtrim($this->container->getParameter('redwood.upload.public_url_path'), ' /') . '/' . $uri['path'];
            $url = ltrim($url, ' /');
            $url = $assets->getUrl($url);

            return $url;
        }
    }

    public function tagsJoinFilter($tagIds)
    {
        if (empty($tagIds) || !is_array($tagIds)) {
            return '';
        }

        $tags  = ServiceKernel::instance()->createService('Taxonomy.TagService')->findTagsByIds($tagIds);
        $names = ArrayToolkit::column($tags, 'name');

        return join($names, ',');
    }

    public function smarttimeFilter ($time) {
        $diff = time() - $time;
        if ($diff < 0) {
            return '未来';
        }

        if ($diff == 0) {
            return '刚刚';
        }

        if ($diff < 60) {
            return $diff . '秒前';
        }

        if ($diff < 3600) {
            return round($diff / 60) . '分钟前';
        }

        if ($diff < 86400) {
            return round($diff / 3600) . '小时前';
        }

        if ($diff < 2592000) {
            return round($diff / 86400) . '天前';
        }

        if ($diff < 31536000) {
            return date('m-d', $time);
        }

        return date('Y-m-d', $time);
    }

    public function getDictText($type, $key)
    {
        return DataDict::text($type, $key);
    }

    public function getDict($type)
    {
        return DataDict::dict($type);
    }
    
    public function getName ()
    {
        return 'redwood_web_twig';
    }
}