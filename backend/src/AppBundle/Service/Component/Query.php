<?php

namespace AppBundle\Service\Component;

use Symfony\Component\HttpFoundation\Request;

class MultiQuery
{
    private $managers = [];

    function __construct()
    {

    }

    public function process($source)
    {

    }

    public function fromRequest(Request $request)
    {
        $families = ['inverter', 'module', 'string_box', 'structure', 'variety'];

        $family = $request->query->get('family');
        $like = $request->query->get('like');
        $perPage = 10;
        $page = $request->query->getInt('page', 1);

        if($family) $families = [$family];

        $data = [];
        foreach ($families as $family){

            //$data[] = ['code' => 'breakpoint', 'description' => $family];

            $manager = $this->manager($family);
            $qb = $manager->createQueryBuilder();
            $alias = $manager->alias();
            $field = in_array($family, ['inverter', 'module']) ? 'model' : 'description';

            if($like) {
                $qb->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->like(
                            sprintf('%s.%s', $alias, $field),
                            $qb->expr()->literal('%' . $like . '%')
                        ),
                        $qb->expr()->like(
                            sprintf('%s.code', $alias),
                            $qb->expr()->literal('%' . $like . '%')
                        )
                    )
                );
            }

            $result = $qb->getQuery()->getResult();

            $data = array_merge($data, $result);
        }

        $paginator = $this->getPaginator();

        $pagination = $paginator->paginate(
            $data,
            $page,
            $perPage
        );

        foreach ($pagination as $key => $item){
            dump($item->getDescription());
        }

        die;
    }

    private function family()
    {

    }

    /**
     * @param $family
     * @return mixed
     */
    private function manager($family)
    {
        if(!array_key_exists($family, $this->managers))
            $this->managers[$family] = $this->container->get(sprintf('%s_manager', $family));

        return $this->managers[$family];
    }
}
