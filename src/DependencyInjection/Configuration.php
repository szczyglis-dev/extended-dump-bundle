<?php

/**
 * This file is part of szczyglis/extended-dump-bundle.
 *
 * (c) Marcin Szczyglinski <szczyglis@protonmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Szczyglis\ExtendedDumpBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration
 *
 * @package szczyglis/extended-dump-bundle
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/extended-dump-bundle
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('extended_dump');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('env')
                    ->scalarPrototype()
                    ->info('Array with enabled environments, if empty then only DEV environment will be enabled')
                    ->end()
                ->end()
                ->arrayNode('display')
                    ->children()
                        ->booleanNode('enabled')
                            ->defaultTrue()
                            ->info('Enable/disable Xdump dockable window')
                            ->end()
                        ->arrayNode('dump')
                            ->children()
                                ->integerNode('max_depth')
                                    ->defaultValue(1)
                                    ->info('Var Dumper max depth config value')
                                    ->end()
                                ->integerNode('max_string_depth')
                                    ->defaultValue(160)
                                    ->info('Var Dumper max max string depth config value')
                                    ->end()
                                ->integerNode('max_items')
                                    ->defaultValue(-1)
                                    ->info('Var Cloner max items config value')
                                    ->end()
                                ->end()
                    ->end()
                    ->arrayNode('sections')
                        ->children()
                            ->arrayNode('app')
                                ->children()
                                    ->booleanNode('enabled')
                                        ->defaultTrue()
                                        ->info('Enable/disable App section')
                                        ->end()
                                    ->booleanNode('collapsed')
                                        ->defaultFalse()
                                        ->info('Collapse App section at start')
                                        ->end()
                                ->end()
                            ->end()
                            ->arrayNode('event')
                                ->children()
                                    ->booleanNode('enabled')
                                        ->defaultTrue()
                                        ->info('Enable/disable Event section')
                                        ->end()
                                    ->booleanNode('collapsed')
                                        ->defaultFalse()
                                        ->info('Collapse Event section at start')
                                        ->end()
                                ->end()
                            ->end()
                            ->arrayNode('system')
                                ->children()
                                    ->booleanNode('enabled')
                                        ->defaultTrue()
                                        ->info('Enable/disable System section')
                                        ->end()
                                    ->booleanNode('collapsed')
                                        ->defaultFalse()
                                        ->info('Collapse System section at start')
                                        ->end()
                                    ->arrayNode('items')
                                        ->children()
                                            ->booleanNode('request')
                                                ->defaultTrue()
                                                ->info('Enable/disable Request dump')
                                                ->end()
                                            ->booleanNode('response')
                                                ->defaultTrue()
                                                ->info('Enable/disable Response dump')
                                                ->end()
                                            ->booleanNode('session')
                                                ->defaultTrue()
                                                ->info('Enable/disable Session dump')
                                                ->end()
                                            ->booleanNode('get')
                                                ->defaultTrue()
                                                ->info('Enable/disable $_GET dump')
                                                ->end()
                                            ->booleanNode('post')
                                                ->defaultTrue()
                                                ->info('Enable/disable $_POST dump')
                                                ->end()
                                            ->booleanNode('cookies')
                                                ->defaultTrue()
                                                ->info('Enable/disable Cookies dump')
                                                ->end()
                                            ->booleanNode('user')
                                                ->defaultTrue()
                                                ->info('Enable/disable User dump')
                                                ->end()
                                            ->booleanNode('server')
                                                ->defaultTrue()
                                                ->info('Enable/disable Server dump')
                                                ->end()
                                            ->booleanNode('doctrine')
                                                ->defaultTrue()
                                                ->info('Enable/disable Doctrine dump')
                                                ->end()
                                            ->booleanNode('parameters')
                                                ->defaultTrue()
                                                ->info('Enable/disable Parameters dump')
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}