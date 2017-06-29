<?php

namespace AppBundle\Util\KitGenerator\StructureCalculator;

/**
 * Interface ComponentInterface
 * @author Daniel Martins <daniel@kolinalabs.com>
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
interface ItemInterface
{
    //-- Types
    const TYPE_PROFILE  = 'profile';
    const TYPE_JUNCTION = 'junction';
    const TYPE_TERMINAL = 'terminal';
    const TYPE_FIXER    = 'fixer';
    const TYPE_BASE     = 'base';
    const TYPE_MODULE   = 'module';

    // --- Subtypes
    # BASE
    const BASE_TAPE                 = 'base_tape';                  // Fita
    const BASE_HOOK                 = 'base_hook';                  // Gancho
    const BASE_SCREW_DRILLING       = 'base_screw_drilling';        // Parafuso autoperfurante
    const BASE_SCREW_STRUCTURAL     = 'base_screw_structural';      // Parafuso estrutural
    const BASE_SPEED_CLIP           = 'base_speed_clip';            // Speed clip
    const BASE_TRIANGLE_HORIZONTAL  = 'base_triangle_horizontal';   // Triângulo Horizontal
    const BASE_TRIANGLE_VERTICAL    = 'base_triangle_vertical';     // Triângulo Vertical
    # FIXER
    const FIXER_SCREW        = 'fixer_screw';   // Parafuso
    const FIXER_NUT          = 'fixer_nut';     // Porca
    # PROFILES
    const PROFILE_ROMAN      = 'profile_roman';         // Perfil Roman
    const PROFILE_INDUSTRIAL = 'profile_industrial';    // Perfil Industrial
    const PROFILE_MIDDLE     = 'profile_middle';        // Perfil Meio Metro
    # TERMINAL
    const TERMINAL_FINAL     = 'terminal_final';        // Terminal Final
    const TERMINAL_MIDDLE    = 'terminal_middle';       // Terminal Intermediário

    # JUNCTION and MODULE has no subtype

    /**
     * @param $id
     * @return ItemInterface
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getId();

    /**
     * @param $size
     * @return ItemInterface
     */
    public function setSize($size);

    /**
     * @return float
     */
    public function getSize();

    /**
     * @param $width
     * @return ItemInterface
     */
    public function setWidth($width);

    /**
     * @return float
     */
    public function getWidth();

    /**
     * @param $length
     * @return ItemInterface
     */
    public function setLength($length);

    /**
     * @return float
     */
    public function getLength();

    /**
     * @param $cellNumber
     * @return ItemInterface
     */
    public function setCellNumber($cellNumber);

    /**
     * @return int
     */
    public function getCellNumber();

    /**
     * @param $description
     * @return ItemInterface
     */
    public function setDescription($description);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param $quantity
     * @return ItemInterface
     */
    public function setQuantity($quantity);

    /**
     * @param $quantity
     * @return ItemInterface
     */
    public function increase($quantity);

    /**
     * @return int
     */
    public function getQuantity();

    /**
     * @param $type
     * @return mixed
     */
    public function setType($type);

    /**
     * @return mixed
     */
    public function getType();

    /**
     * @param $subtype
     * @return ItemInterface
     */
    public function setSubtype($subtype);

    /**
     * @return string
     */
    public function getSubtype();

    /**
     * @param $assert
     * @return bool
     */
    public function is($assert);

    /**
     * @return array
     */
    public function getErrors();

    /**
     * @return bool
     */
    public function isValid();

    /**
     * @return array
     */
    public static function getAcceptedTypes();

    /**
     * @param null $type
     * @return array
     */
    public static function getAcceptedSubtypes($type = null);
}

