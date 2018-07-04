<?php

/**
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecommerce\Kit\Service\Sices;

use AppBundle\Manager\AbstractManager;
use Doctrine\ORM\QueryBuilder;
use Ecommerce\Cart\Entity\CartHasKit;
use Ecommerce\Kit\Entity\Kit;
use Ecommerce\Kit\Manager\KitManager;
use Ecommerce\Cart\Manager\CartHasKitManager;
use Knp\Component\Pager\PaginatorInterface;
use AppBundle\Entity\Component\ComponentInterface;
use AppBundle\Service\Component\FileHandler;

class KitService
{
    /**
     * @var KitManager
     */
    private $manager;

    /**
     * @var PaginatorInterface
     */
    protected $paginator;

    /**
     * @var FileHandler
     */
    protected $fileHandler;

    /**
     * @var CartHasKitManager
     */
    private $cartHasKitManager;

    /**
     * @inheritDoc
     */
    public function __construct(
        KitManager $manager,
        PaginatorInterface $paginator,
        FileHandler $fileHandler,
        CartHasKitManager $cartHasKitManager
    ) {
        $this->manager = $manager;
        $this->paginator = $paginator;
        $this->fileHandler = $fileHandler;
        $this->cartHasKitManager = $cartHasKitManager;
    }

    /**
     * @param $actives
     * @param int $page
     * @param int $perPage
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function findAll($actives, $page = 1, $perPage = 8)
    {
        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $this->manager->createQueryBuilder();

        $qb->orderBy('k.position', 'asc');

        if ($actives) {
            $available = (int) $actives == 1 ? 1 : 0;

            $expression =
                $qb->expr()->eq(
                    'k.available',
                    $qb->expr()->literal($available)
                );

            $qb->andWhere($expression);
        }

        return $this->paginator->paginate(
            $qb->getQuery(),
            $page,
            $perPage
        );
    }

    /**
     * @param Kit $kit
     * @param $components
     * @param $files
     * @throws \Throwable
     */
    public function save(Kit $kit, $components, $files)
    {
        $this->formatValues($kit, true);

        $this->insertKitComponents($kit, $components);

        if (! $kit->getId()) {
            $this->manager->save($kit);
        }

        $this->fileHandler->upload($kit, $files);

        $this->manager->save($kit);
    }

    /**
     * @param Kit $kit
     */
    public function delete(Kit $kit)
    {
        $cartKits = $this->cartHasKitManager->findBy([
            'kit' => $kit
        ]);

        /** @var CartHasKit $cart */
        foreach ($cartKits as $cartKit) {
            $this->cartHasKitManager->delete($cartKit, false);
        }

        $this->cartHasKitManager->flush();

        $this->manager->delete($kit);
    }

    /**
     * @param string $family
     * @param AbstractManager $manager
     * @return array
     */
    public function getComponentsByFamily($family, AbstractManager $manager)
    {
        $field = in_array($family, ['module',  'inverter']) ? 'model' : 'description';

        /** @var QueryBuilder $qb */
        $qb = $manager->createQueryBuilder();
        $alias = $qb->getRootAlias();

        $qb->select("{$alias}.id, {$alias}.code, {$alias}.{$field} as description");

        return $qb->getQuery()->getResult();
    }

    public function getModel(): Kit
    {
        return $this->manager->create();
    }

    /**
     * @return array
     */
    public function getComponentFamilies()
    {
        return [
            ComponentInterface::FAMILY_MODULE => ComponentInterface::FAMILY_MODULE,
            ComponentInterface::FAMILY_INVERTER => ComponentInterface::FAMILY_INVERTER,
            ComponentInterface::FAMILY_STRING_BOX => ComponentInterface::FAMILY_STRING_BOX,
            ComponentInterface::FAMILY_STRUCTURE => ComponentInterface::FAMILY_STRUCTURE,
            ComponentInterface::FAMILY_VARIETY => ComponentInterface::FAMILY_VARIETY
        ];
    }

    /**
     * @param Kit $kit
     * @param bool $toDb
     */
    private function formatValues(Kit $kit, $toDb = false)
    {
        $properties = [
            'Price',
            'Power'
        ];

        foreach ($properties as $property){
            $getValue = 'get'.$property;
            $setValue = 'set'.$property;

            if ($kit->$getValue()){
                if ($toDb) {
                    $kit->$setValue(str_replace(',', '.', $kit->$getValue()));
                }
                else {
                    $kit->$setValue(str_replace('.', ',', $kit->$getValue()));
                }
            }
        }
    }

    /**
     * @param Kit $kit
     * @param $components
     */
    private function insertKitComponents(Kit $kit, $components)
    {
        $componentsDecoded = json_decode($components, true);
        $oldComponents = $kit->getComponents();

        $kit->setComponents([]);

        foreach ($componentsDecoded as $component) {
            $tag = $component['family'] . '_' . $component['componentId'];
            $quantity = $component['quantity'];
            $position = $component['position'];

            if (!is_numeric($quantity)) {
                $quantity = $oldComponents[$tag]['quantity'];
            }
            if (!is_numeric($quantity)) {
                $position = $oldComponents[$tag]['position'];
            }

            $component['quantity'] = intval($quantity);
            $component['position'] = intval($position);

            if (is_numeric($quantity) && is_numeric($position)) {
                $kit->addComponent($tag, $component);
            }
        }
    }
}
