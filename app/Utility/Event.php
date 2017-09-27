<?php
/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2017 Klaus Fiedler <klaus@tollwerk.de>, tollwerk® GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

namespace App\Utility;


use Sabre\VObject\Component\VEvent;

class Event
{
    const FORMAT_SABRE = 'sabre';
    const FORMAT_JS = 'js';

    /**
     *  Serialize one ore multiple VEvent object(s) to array with given $format
     * @param \Sabre\VObject\Component\VEvent|\Sabre\VObject\Component\VEvent[] $event
     * @param string $format Something like \App\Utility\Event::FORMAT_JS or Event::FORMAT_SABRE
     * @return array
     */
    public static function serialize($event, $format = self::FORMAT_JS){

        // Define serializations for all given formats as anonymous functions
        $serializationFunctions = [
            self::FORMAT_JS => function($event){

                $mdParser = new \cebe\markdown\GithubMarkdown();

                return [
                    'id' => (string) $event->UID,
                    'title' => (string) $event->SUMMARY,
                    'description' => $mdParser->parse((string) $event->DESCRIPTION),
                    'allDay' => (strpos($event->DTSTART, 'T') && strpos($event->DTEND, 'T')) ? false : true,
                    'start' => (string) $event->DTSTART->getDateTime()->format(\DateTime::W3C),
                    'end' => $event->DTEND->getDateTime()->format(\DateTime::W3C),
                    'url' => false, // TODO: js calendar object: url
                    'className' => false, // TODO: js calendar object: className
                    'color' => false, // TODO: js calendar object: color
                    'backgroundColor' => false, // TODO: js calendar object: backgroundColor
                    'borderColor' => false, // TODO: js calendar object: borderColor
                    'textColor' => false, // TODO: js calendar object: textColor
                ];
            },
            self::FORMAT_SABRE => function($event){
                return [
                    'uid' => (string) $event->UID,
                    'status' => (string) $event->STATUS,
                    'start' => (string) $event->DTSTART->getDateTime()->format(\DateTime::W3C),
                    'end' => $event->DTEND->getDateTime()->format(\DateTime::W3C),
                    'rrule' => (string) $event->RRULE,
                    'summary' => (string) $event->SUMMARY,
                    'description' => (string) $event->DESCRIPTION
                ];
            }
        ];

        // If $event is single event object, return it serialized
        if(!is_array($event)){
            if(!($event instanceof VEvent)){
                return null;
            }
            return $serializationFunctions[$format]($event);
        }

        // If $event is an array of event objects, return it as array of serialized events
        $events = [];
        foreach($event as $e){
            if(!($e instanceof VEvent)){
                continue;
            }
            $events[] = $serializationFunctions[$format]($e);
        }
        return $events;
    }
}