<?php

namespace AppBundle\Twig;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\User;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Twig\Extension\MediaExtension as SonataMediaExtension;

/**
 * Class MediaExtension
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class MediaExtension extends \Twig_Extension
{
    /**
     * @var SonataMediaExtension
     */
    private $mediaExtension;

    public function __construct(SonataMediaExtension $mediaExtension)
    {
        $this->mediaExtension = $mediaExtension;
    }

    /**
     * @inheritDoc
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('picture', [$this, 'getPicture'], ["is_safe" => ["html"]])
        ];
    }

    public function getPicture($owner, $format = 'medium', array $options = [])
    {
        if($owner instanceof User)
            $owner = $owner->getInfo();

        if($owner instanceof BusinessInterface){

            $media = $owner->getMedia();

            if($media instanceof MediaInterface) {

                if(!array_key_exists('title', $options))
                    $options['title'] = $owner->getName();
                
                return $this->mediaExtension->thumbnail($media, $format, $options);
            }

            $id = array_key_exists('id', $options) ? $options['id'] : 'picture-' . uniqid() ;
            $class = array_key_exists('class', $options) ? $options['class'] : '';
            $icon = 'businessman.png';

            if($owner->getContext()->getId() == BusinessInterface::CONTEXT_COMPANY
                || $owner->getContext()->getId() == BusinessInterface::CONTEXT_ACCOUNT){
                $icon = 'company.png';
            }

            // TODO: Temporary, see: AppMediaManager
            return sprintf('<img id="%s" class="%s" src="/assets/img/%s"/>', $id, $class, $icon);
        }

        return '...';
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'app_media_extension';
    }
}