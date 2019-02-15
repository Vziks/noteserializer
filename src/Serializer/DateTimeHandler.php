<?php


namespace App\Serializer;

use DateTime;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\Context;
use Symfony\Component\HttpFoundation\RequestStack;


/**
 * Class DateTimeHandler
 * @package App\Serializer
 * @author  Anton Prokhorov <vziks@live.ru>
 */
class DateTimeHandler implements SubscribingHandlerInterface
{
    public static function getSubscribingMethods()
    {
        return array(
            array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format'    => 'json',
                'type'      => 'DateTime',
                'method'    => 'serializeDateTimeToJson',
            ),
        );
    }

    public function serializeDateTimeToJson(JsonSerializationVisitor $visitor, \DateTime $date, array $type, Context $context)
    {
        return $this->getFormatDate($date);
    }


    public function getFormatDate(\Datetime $datetime, $lang = "ru_RU", $pattern = 'd MMMM Y')
    {
        $formatter = new \IntlDateFormatter($lang, \IntlDateFormatter::LONG, \IntlDateFormatter::LONG);
        $formatter->setPattern($pattern);
        return $formatter->format($datetime);
    }
}