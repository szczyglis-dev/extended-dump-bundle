<?php

/**
 * This file is part of szczyglis/extended-dump-bundle.
 *
 * (c) Marcin Szczyglinski <szczyglis@protonmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Szczyglis\ExtendedDumpBundle\Internals;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Szczyglis\ExtendedDumpBundle\Contracts\InternalDumperInterface;

/**
 * DoctrineDumper
 *
 * @package szczyglis/extended-dump-bundle
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/extended-dump-bundle
 */
class DoctrineDumper implements InternalDumperInterface
{
    const LABEL_ITEMS = 'entities';

    /**
     * @var RequestStack
     */
    private $em;

    /**
     * @var ResponseEvent
     */
    private $event;

    /**
     * @var array
     */
    private $config = [];

    /**
     * DoctrineDumper constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param array $config
     * @param ResponseEvent $event
     */
    public function init(array $config, ResponseEvent $event)
    {
        $this->config = $config;
        $this->event = $event;
    }

    /**
     * @param int $type
     * @return array
     * @throws \ReflectionException
     */
    public function dump(int $type = 0): array
    {
        return $this->prepareEntities();
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    public function prepareEntities()
    {
        $hiddenMethods = [
            '__call',
            '__construct',
            'clear',
            'count',
            'getClassMetadata',
            'getClassName',
            'getEntityManager',
            'getEntityName',
            'matching',
            'createNamedQuery',
            'createNativeNamedQuery',
            'createQueryBuilder',
            'createResultSetMappingBuilder',
        ];

        $entities = [];
        $c = 0;
        $meta = $this->em->getMetadataFactory()->getAllMetadata();
        foreach ($meta as $item) {
            $class = $item->getName();
            if (!class_exists($class)) {
                continue;
            }

            $reflector = new \ReflectionClass($class);
            $shortName = $reflector->getShortName();

            $properties = [];
            $list = $reflector->getProperties();
            foreach ($list as $entry) {
                $n = $entry->getName();
                $type = null;
                if (isset($item->fieldMappings[$n]['type'])) {
                    $type = $item->fieldMappings[$n]['type'];
                } else if (isset($item->associationMappings[$n]['targetEntity'])) {
                    $isOwningSide = $item->associationMappings[$n]['isOwningSide'];
                    if ($isOwningSide) {
                        $type = '['.(new \ReflectionClass($item->associationMappings[$n]['targetEntity']))->getShortName().']';
                        if (!empty($item->associationMappings[$n]['inversedBy'])) {
                            $type .= ' (' . $item->associationMappings[$n]['inversedBy'].')';
                        }
                    } else {
                        $type = '['.(new \ReflectionClass($item->associationMappings[$n]['targetEntity']))->getShortName().']';
                        $n = '['.$n.']';
                    }                    
                }
                $properties[$n] = $type;
            }
            ksort($properties);

            $methods = [];
            $list = $reflector->getMethods();
            foreach ($list as $entry) {
                $methods[] = $entry->getName();
            }
            sort($methods);

            $repositoryMethods = [];
            try {
                $repo = $this->em->getRepository($class);
                if (!empty($repo)) {
                    $reflector = new \ReflectionClass($repo);
                    $repositoryMethods = [];
                    $list = $reflector->getMethods();
                    foreach ($list as $entry) {
                        $m = $entry->getName();
                        if (!in_array($m, $hiddenMethods)) {
                            $repositoryMethods[] = $m;
                        }                        
                    }
                    sort($repositoryMethods);
                }
            } catch (\Exception) {
            }

            $f = strtoupper(substr($shortName, 0, 1));
            $entities[$f][$shortName] = [
                'properties' => $properties,
                'methods' => $methods,
            ];
            if (!empty($repositoryMethods)) {
                $entities[$f][$shortName]['repository'] = $repositoryMethods;
            }

            $c++;
        }

        ksort($entities);

        $k = $c . ' ' . self::LABEL_ITEMS;
        return [
            $k => $entities,
        ];
    }
}