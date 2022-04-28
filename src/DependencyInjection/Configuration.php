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
                    ->scalarPrototype()->end()
                ->end()
                ->arrayNode('display')
                    ->children()
                        ->booleanNode('enabled')
                            ->defaultTrue()
                        ->end()
                        ->arrayNode('sections')
                            ->children()
                                ->arrayNode('app')
                                    ->children()
                                        ->booleanNode('enabled')
                                            ->defaultTrue()
                                        ->end()
                                        ->booleanNode('collapsed')
                                            ->defaultFalse()
                                        ->end()
                                    ->end()
                                ->end() 
                                ->arrayNode('event')
                                    ->children()
                                        ->booleanNode('enabled')
                                            ->defaultTrue()
                                        ->end()
                                        ->booleanNode('collapsed')
                                            ->defaultFalse()
                                        ->end()
                                    ->end()
                                ->end() 
                                ->arrayNode('system')
                                    ->children()
                                        ->booleanNode('enabled')
                                            ->defaultTrue()
                                        ->end()
                                        ->booleanNode('collapsed')
                                            ->defaultFalse()
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