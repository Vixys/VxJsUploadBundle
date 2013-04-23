<?php

namespace Vx\JsUploadBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Templating\Helper\AssetsHelper;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class VxJsUploadExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $profiles = array();
        $profiles['profile'] = $configs[0]['profile'];
        if (isset($configs[1]))
            $profiles['profile'] = $configs[1]['profile'];

        foreach ($profiles['profile'] as $profile => $value) {
            if ('default' == strtolower($profile))
                throw new InvalidArgumentException('The "default" profile is a reserved profile.');
                
            if ($profile != strtolower($profile))
            {
                $profiles['profile'][strtolower($profile)] = $profiles['profile'][$profile];
                unset($profiles['profile'][$profile]);
            }
        }

        unset($configs[0]['profile']);
        if (isset($configs[1]))
            unset($configs[1]['profile']);

        foreach ($profiles['profile'] as $profile => $value) {
            if ($profile != strtolower($profile))
            {
                $profiles['profile'][strtolower($profile)] = $profiles['profile'][$profile];
                unset($profiles['profile'][$profile]);
            }
        }

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $upload_url = dirname($_SERVER['SCRIPT_FILENAME']);
        foreach ($profiles['profile'] as $profile => $value) {
            if (isset($profiles['profile'][$profile]['upload_dir']))
            {
                if (substr($profiles['profile'][$profile]['upload_dir'], -1) != '/')
                    $profiles['profile'][$profile]['upload_dir'] .= '/';
                $profiles['profile'][$profile]['upload_url'] = $this->getFullUrl().'/'.$profiles['profile'][$profile]['upload_dir'];
                $profiles['profile'][$profile]['upload_dir'] = $upload_url.'/'.$profiles['profile'][$profile]['upload_dir'];
            }
        }

        foreach ($profiles['profile'] as $profile => $options) {
            $container->setParameter('vx_js_upload.profile.'.$profile, $options);
        }
        
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    protected function getFullUrl() {
        $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        return
            ($https ? 'https://' : 'http://').
            (!empty($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'].'@' : '').
            (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'].
            ($https && $_SERVER['SERVER_PORT'] === 443 ||
            $_SERVER['SERVER_PORT'] === 80 ? '' : ':'.$_SERVER['SERVER_PORT']))).
            substr($_SERVER['SCRIPT_NAME'],0, strrpos($_SERVER['SCRIPT_NAME'], '/'));
    }
}
