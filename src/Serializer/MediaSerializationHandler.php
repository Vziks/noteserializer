<?php


namespace App\Serializer;

use App\Application\Sonata\MediaBundle\Entity\Media;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;
use Sonata\MediaBundle\Provider\FileProvider;
use Sonata\MediaBundle\Provider\ImageProvider;
use JMS\Serializer\Context;


/**
 * Class MediaSerializationHandler
 * @package App\Serializer
 * @author  Anton Prokhorov <vziks@live.ru>
 */
class MediaSerializationHandler implements SubscribingHandlerInterface
{
    /**
     * @var ImageProvider
     */
    private $imageProvider;

    /**
     * @var FileProvider
     */
    private $fileProvider;

    public function __construct(ImageProvider $imageProvider, FileProvider $fileProvider)
    {
        $this->imageProvider = $imageProvider;
        $this->fileProvider = $fileProvider;
    }

    public static function getSubscribingMethods()
    {
        return array(
            array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => Media::class,
                'method' => 'serializeMedia',
            ),
        );
    }

    public function serializeMedia(JsonSerializationVisitor $visitor, Media $media)
    {

        switch ($media->getProviderName()) {
            case 'sonata.media.provider.file':
                $serialization = $this->serializeFile($media);
                break;

            case 'sonata.media.provider.image':
                $serialization = $this->serializeImage($media);
                break;

            default:
                throw new \RuntimeException("Serialization media provider not recognized");
        }

        if ($visitor->getRoot() === null) {
            $visitor->setRoot($serialization);
        }

        return $serialization;
    }

    private function serializeImage(Media $media)
    {
        return [
            "url" => [
                "reference" => $this->imageProvider->generatePublicUrl($media, "reference"),
            ]
        ];
    }

    private function serializeFile(Media $media)
    {
        return [
            "name" => $media->getName(),
            "size" => $media->getSize(),
            "url" => $this->fileProvider->generatePublicUrl($media, 'reference')
        ];
    }
}