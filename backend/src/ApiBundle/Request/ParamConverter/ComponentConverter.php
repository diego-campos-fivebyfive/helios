<?php

namespace ApiBundle\Request\ParamConverter;

use AppBundle\Entity\Component;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ComponentConverter implements ParamConverterInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var \AppBundle\Manager\AbstractManager
     */
    private $manager;

    /**
     * @var array
     */
    private $types = [
        'module' => Component\Module::class,
        'inverter' => Component\Inverter::class,
        'stringBox' => Component\StringBox::class,
        'structure' => Component\Structure::class,
        'variety' => Component\Variety::class
    ];

    /**
     * ComponentConverter constructor.
     * @param ContainerInterface $container
     */
    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $options = $configuration->getOptions();

        $type = $options['type'];

        $idOrCode = $request->attributes->get($type);

        if(null == $component = $this->manager->find($idOrCode)){
            $component = $this->manager->findOneBy(['code' => $idOrCode]);
        }

        if(null == $component){
            throw new NotFoundHttpException(sprintf('%s object not found.', ucfirst($type)));
        }

        $request->attributes->set($type, $component);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function supports(ParamConverter $configuration)
    {
        $options = $configuration->getOptions();

        if(array_key_exists('type', $options)){

            $type = $options['type'];
            if(in_array($type, $this->types) || array_key_exists($type, $this->types)){

                $this->manager = $this->manager($type);

                return true;
            }
        }

        return false;
    }

    /**
     * @param $type
     * @return object|\AppBundle\Manager\AbstractManager
     */
    private function manager($type)
    {
        return $this->container->get(sprintf('%s_manager', $type));
    }
}
