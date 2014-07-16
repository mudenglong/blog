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
        );
    }

    public function getFunctions()
    {
        return array(
            'dict' => new \Twig_Function_Method($this, 'getDict') ,
            'dict_text' => new \Twig_Function_Method($this, 'getDictText', array('is_safe' => array('html'))) ,
        );
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