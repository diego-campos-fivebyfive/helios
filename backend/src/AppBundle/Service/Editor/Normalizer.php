<?php

namespace AppBundle\Service\Editor;

class Normalizer
{
    private static $startBlock = '<!--{cke_protected}{C}%3C!%2D%2D';
    private static $endBlock = '%2D%2D%3E-->';

    public static function prepare(&$template, array $data = [])
    {
        $blocks = self::createBlocks($data);

        $pattern = array_keys($blocks);
        $replacement = array_values($blocks);

        $template = preg_replace($pattern, $replacement, $template);
    }

    /**
     * @param array $data
     * @return array
     */
    private static function createBlocks(array $data = [])
    {
        $blocks = [];
        foreach ($data as $property => $tag){

            $block = sprintf('%s%s%s', self::$startBlock, $property, self::$endBlock);
            $pattern = sprintf('/%s(.*)%s/', $block, $block);

            $value = 'static' == $tag['handle'] ? $tag['value'] : '';
            $replacement = sprintf('%s%s%s', $block, $value, $block);

            $blocks[$pattern] = $replacement;
        }

        return $blocks;
    }
}