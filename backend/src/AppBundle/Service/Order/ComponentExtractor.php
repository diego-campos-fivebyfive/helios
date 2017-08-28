<?php

namespace AppBundle\Service\Order;

use AppBundle\Entity\Component\InverterInterface;
use AppBundle\Entity\Component\ModuleInterface;
use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Entity\Component\ProjectModuleInterface;
use AppBundle\Entity\Component\ProjectStringBoxInterface;
use AppBundle\Entity\Component\ProjectStructureInterface;
use AppBundle\Entity\Component\ProjectVarietyInterface;
use AppBundle\Entity\Component\StringBoxInterface;
use AppBundle\Entity\Component\StructureInterface;
use AppBundle\Entity\Component\VarietyInterface;
use AppBundle\Entity\Order\ElementInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ComponentExtractor
{
    /**
     * @var Serializer
     */
    private static $serializer;

    /**
     * Init serializer
     */
    private static function initialize()
    {
        if (!self::$serializer) {

            $encoder = new JsonEncoder();
            $normalizer = new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter());

            $normalizer->setIgnoredAttributes([
                'maker',
                'project',
                'createdAt',
                'updatedAt',
                'published',
                'viewMode',
                'datasheet',
                'image',
                'disable',
                'active',
                'available',
                'status',
                'currentPrice',
                'height',
                'projectArea',
                'projectModule',
                '__initializer__',
                '__cloner__',
                '__is_initialized__'
            ]);

            self::$serializer = new Serializer([$normalizer], [$encoder]);
        }
    }

    /**
     * @param $source
     * @return array
     */
    public static function extract($source)
    {
        if ($source instanceof ProjectInterface) {
            return self::fromProject($source);
        }

        if($source instanceof ProjectModuleInterface){
            return self::fromProjectModule($source);
        }

        if($source instanceof ModuleInterface){
            return self::fromModule($source);
        }

        if($source instanceof InverterInterface){
            return self::fromInverter($source);
        }

        if($source instanceof ProjectStringBoxInterface){
            return self::fromProjectStringBox($source);
        }

        if($source instanceof StringBoxInterface){
            return self::fromStringBox($source);
        }

        if($source instanceof ProjectStructureInterface){
            return self::fromProjectStructure($source);
        }

        if($source instanceof StructureInterface){
            return self::fromStructure($source);
        }

        if($source instanceof ProjectVarietyInterface){
            return self::fromProjectVariety($source);
        }

        if($source instanceof VarietyInterface){
            return self::fromVariety($source);
        }

        throw new \InvalidArgumentException('Unsupported direct extraction');
    }

    /**
     * @param $projectModules
     * @return array
     */
    public static function fromProjectModules($projectModules)
    {
        $data = [];
        foreach ($projectModules as $projectModule) {
            $data[] = self::fromProjectModule($projectModule);
        }

        return $data;
    }

    /**
     * @param ProjectModuleInterface $projectModule
     * @return array
     */
    public static function fromProjectModule(ProjectModuleInterface $projectModule)
    {
        $module = $projectModule->getModule();

        return array_merge([
            'quantity' => $projectModule->getQuantity(),
            'unitPrice' => $projectModule->getUnitCostPrice(),
        ], self::fromModule($module));
    }

    /**
     * @param ModuleInterface $module
     * @return array
     */
    public static function fromModule(ModuleInterface $module)
    {
        return [
            'code' => $module->getCode(),
            'description' => $module->getDescription(),
            'tag' => ElementInterface::TAG_MODULE,
            'metadata' => self::extractMetadata($module)
        ];
    }

    /**
     * @param array $groupInverters
     * @return array
     */
    public static function fromGroupInverters(array $groupInverters)
    {
        $data = [];
        foreach ($groupInverters as $groupInverter) {
            $data[] = self::fromGroupInverter($groupInverter);
        }

        return $data;
    }

    /**
     * @param array $groupInverter
     * @return array
     */
    public static function fromGroupInverter(array $groupInverter)
    {
        $inverter = $groupInverter['inverter'];
        $quantity = $groupInverter['quantity'];
        $price = $groupInverter['unitCostPrice'];

        return array_merge([
            'quantity' => $quantity,
            'unitPrice' => $price,
        ], self::fromInverter($inverter));
    }

    /**
     * @param InverterInterface $inverter
     * @return array
     */
    public static function fromInverter(InverterInterface $inverter)
    {
        return [
            'code' => $inverter->getCode(),
            'description' => $inverter->getDescription(),
            'tag' => ElementInterface::TAG_INVERTER,
            'metadata' => self::extractMetadata($inverter)
        ];
    }

    /**
     * @param $projectStringBoxes
     * @return array
     */
    public static function fromProjectStringBoxes($projectStringBoxes)
    {
        $data = [];
        foreach ($projectStringBoxes as $projectStringBox) {
            $data[] = self::fromProjectStringBox($projectStringBox);
        }

        return $data;
    }

    /**
     * @param ProjectStringBoxInterface $projectStringBox
     * @return array
     */
    public static function fromProjectStringBox(ProjectStringBoxInterface $projectStringBox)
    {
        $stringBox = $projectStringBox->getStringBox();

        return array_merge([
            'quantity' => $projectStringBox->getQuantity(),
            'unitPrice' => $projectStringBox->getUnitCostPrice(),
        ], self::fromStringBox($stringBox));
    }

    /**
     * @param StringBoxInterface $stringBox
     * @return array
     */
    public static function fromStringBox(StringBoxInterface $stringBox)
    {
        return [
            'code' => $stringBox->getCode(),
            'description' => $stringBox->getDescription(),
            'tag' => ElementInterface::TAG_STRING_BOX,
            'metadata' => self::extractMetadata($stringBox)
        ];
    }

    /**
     * @param $projectStructures
     * @return array
     */
    public static function fromProjectStructures($projectStructures)
    {
        $data = [];
        foreach ($projectStructures as $projectStructure) {
            $data[] = self::fromProjectStructure($projectStructure);
        }

        return $data;
    }

    /**
     * @param ProjectStructureInterface $projectStructure
     * @return array
     */
    public static function fromProjectStructure(ProjectStructureInterface $projectStructure)
    {
        $structure = $projectStructure->getStructure();

        return array_merge([
            'quantity' => $projectStructure->getQuantity(),
            'unitPrice' => $projectStructure->getUnitCostPrice(),
        ], self::fromStructure($structure));
    }

    /**
     * @param StructureInterface $structure
     * @return array
     */
    public static function fromStructure(StructureInterface $structure)
    {
        return [
            'code' => $structure->getCode(),
            'description' => $structure->getDescription(),
            'tag' => ElementInterface::TAG_STRUCTURE,
            'metadata' => self::extractMetadata($structure)
        ];
    }

    /**
     * @param $projectVarieties
     * @return array
     */
    public static function fromProjectVarieties($projectVarieties)
    {
        $data = [];
        foreach($projectVarieties as $projectVariety){
            $data[] = self::fromProjectVariety($projectVariety);
        }

        return $data;
    }

    /**
     * @param ProjectVarietyInterface $projectVariety
     * @return array
     */
    private static function fromProjectVariety(ProjectVarietyInterface $projectVariety)
    {
        $variety = $projectVariety->getVariety();

        return array_merge([
            'unitPrice' => $projectVariety->getUnitCostPrice(),
            'quantity' => $projectVariety->getQuantity()
        ], self::fromVariety($variety));
    }

    /**
     * @param VarietyInterface $variety
     * @return array
     */
    public static function fromVariety(VarietyInterface $variety)
    {
        return [
            'code' => $variety->getCode(),
            'description' => $variety->getDescription(),
            'tag' => ElementInterface::TAG_VARIETY,
            'metadata' => self::extractMetadata($variety)
        ];
    }

    /**
     * @param ProjectInterface $project
     * @return array
     */
    public static function fromProject(ProjectInterface $project)
    {
        $fromProjectModules = self::fromProjectModules($project->getProjectModules());

        $fromGroupInverters = self::fromGroupInverters($project->groupInverters());

        $fromProjectStringBoxes = self::fromProjectStringBoxes($project->getProjectStringBoxes());

        $fromProjectStructures = self::fromProjectStructures($project->getProjectStructures());

        $fromProjectVarieties = self::fromProjectVarieties($project->getProjectVarieties());

        return array_merge(
            $fromProjectModules,
            $fromGroupInverters,
            $fromProjectStringBoxes,
            $fromProjectStructures,
            $fromProjectVarieties
        );
    }

    /**
     * @param $source
     * @return array
     */
    private static function extractMetadata($source)
    {
        self::initialize();

        $metadata = json_decode(self::$serializer->serialize($source, 'json'), true);

        unset($metadata['__is_initialized__']);

        return $metadata;
    }
}