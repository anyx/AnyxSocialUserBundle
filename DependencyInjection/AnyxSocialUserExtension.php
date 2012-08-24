<?php

namespace Anyx\SocialUserBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class AnyxSocialUserExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        if ('custom' !== $config['db_driver']) {
            $loader->load(sprintf('%s.xml', $config['db_driver']));
        }

        if ( array_key_exists( 'accounts', $config ) ) {
            $this->configureAccountFactory( $config, $container );
        }
    }

	/**
	 *
	 * @param array $config
	 * @param ContainerBuilder $container 
	 */
	private function configureAccountFactory( array $config, ContainerBuilder $container ) {

		$container->setParameter('anyx_social_user.acconts.map',  $config['accounts']['map']);
		$container->getDefinition('anyx_social_user.account.factory')
				->addArgument('%anyx_social_user.acconts.map%');
	}
}
