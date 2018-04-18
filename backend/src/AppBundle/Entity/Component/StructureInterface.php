<?php

namespace AppBundle\Entity\Component;

interface StructureInterface
{
    /* Types */
    const TYPE_PROFILE = 'perfil';
    const TYPE_JUNCTION = 'juncao';
    const TYPE_TERMINAL = 'terminal';
    const TYPE_FIXER = 'fixador';
    const TYPE_BASE = 'base';
    const TYPE_GROUND_PORTICO = 'ground_portico';
    const TYPE_GROUND_SCREW = 'ground_screw';
    const TYPE_GROUND_CLAMPS = 'ground_clamps';
    const TYPE_GROUND_DIAGONAL_UNION = 'ground_diagonal_union';
    const TYPE_GROUND_CROSS = 'ground_cross';
    const TYPE_GROUND_DIAGONAl = 'ground_diagonal';

    /* Subtypes */
    const ST_ROMAN = 'roman';
    const ST_INDUSTRIAL = 'industrial';
    const ST_FINAL = 'final';
    const ST_MIDDLE = 'intermediario';
    const ST_SCREW = 'parafuso';
    const ST_NUT = 'porca';
    const ST_HOOK = 'gancho';
    const ST_SCREW_STR = 'parafuso_estrutural';
    const ST_TRIANGLE_V = 'triangulo_vertical';
    const ST_TRIANGLE_H = 'triangulo_horizontal';
    const ST_SCREW_AUTO = 'parafuso_autoperfurante';
    const ST_TAPE = 'fita';
    const ST_HALF_METER = 'meio_metro';
    const ST_SPEEDCLIP = 'speedclip';

    /**
     * @return int
     */
    public function getId();

    /**
     * @param $code
     * @return mixed
     */
    public function setCode($code);

    /**
     * @return mixed
     */
    public function getCode();

    /**
    * @param $type
    * @return mixed
     */
    public function setType($type);

    /**
     *  @return mixed
    */
    public function getType();

    /**
     *  @param $subtype
     *  @return mixed
     */
    public function setSubType($subtype);

    /**
     * @return mixed
     */
    public function getSubType();

    /**
     * @param $description
     * @return mixed
     */
    public function setDescription($description);

    /**
     * @return mixed
     */
    public function getDescription();

    /**
     * @param $size
     * @return mixed
     */
    public function setSize($size);

    /**
      * @return mixed
      */
    public function getSize();

    /**
     * @param $datasheet
     * @return StructureInterface
     */
    public function setDatasheet($datasheet);

    /**
     * @return string
     */
    public function getDatasheet();

    /**
     * @param $image
     * @return StructureInterface
     */
    public function setImage($image);

    /**
     * @return string
     */
    public function getImage();

    /**
     * @return mixed
     */
    public function getCreatedAt();

    /**
     * @return mixed
     */
    public function getUpdatedAt();

    /**
     * @param MakerInterface $maker
     * @return StructureInterface
     */
    public function setMaker(MakerInterface $maker);

    /**
     * @return MakerInterface
     */
    public function getMaker();

    /**
     * @return array
     */
    public static function getTypes();

    /**
     * @return array
     */
    public static function getSubtypes();
}
