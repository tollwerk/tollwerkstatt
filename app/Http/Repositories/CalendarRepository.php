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

namespace App\Http\Repositories;

use Sabre\DAV\Client as CalDavClient;
use Sabre\VObject\Reader as VObjectReader;


class CalendarRepository
{
    const DATETIME_FORMAT_CALDAV = 'Ymd\THis\Z';

    /**
     * @param string $filter
     * @return null
     */
    public function findByCaldavFilter($filter = '<c:filter><c:comp-filter name="VCALENDAR"/></c:filter>')
    {
        // Basic authenticated connection
        $calDavClient = new CalDavClient([
            'baseUri' => env('CAL_BASE_URI'),
            'userName' => env('CAL_USERNAME'),
            'password' => env('CAL_PASSWORD')
        ]);

        // Get calendar events from caldav
        $response = $calDavClient->request(
            'REPORT',
            env('CAL_URI'),
            '<c:calendar-query xmlns:d="DAV:" xmlns:c="urn:ietf:params:xml:ns:caldav">
                    <d:prop>
                        <d:getetag />
                        <c:calendar-data />
                    </d:prop>
                    ' . $filter . '
                </c:calendar-query>',
            [
                'Depth' => 1,
                'Prefer' => 'return-minimal',
                'Content-Type' => 'application/xml; charset=utf-8'
            ]
        );


        // Parse caldav response (xml)
        $responseDom = new \DOMDocument();
        $responseDom->loadXML($response['body']);
        $responseXpath = new \DOMXPath($responseDom);
        $responseXpath->registerNamespace('cal', 'urn:ietf:params:xml:ns:caldav');

        // Create VCalendar objects and return them
        $events = [];
        foreach ($responseXpath->query('//cal:calendar-data') as $event) {
            /**
             * @var \Sabre\VObject\Component\VCalendar
             */
            $vCalendar = VObjectReader::read($event->textContent);
            $events[] = $vCalendar->VEVENT;
        }

        return count($events) ? $events : null;
    }

    /** @return  \Sabre\VObject\Component\VEvent[]|null */
    public function findAll()
    {
        return $this->findByCaldavFilter();
    }

    /**
     * @param \DateTime $start
     * @param \DateTime $end
     */
    public function findByDate(\DateTime $start = null, \DateTime $end = null)
    {
        $start = $start ? 'start="' . $start->format(self::DATETIME_FORMAT_CALDAV) . '"' : null;
        $end = $end ? 'end="' . $end->format(self::DATETIME_FORMAT_CALDAV) . '"' : null;

        $filter = '<c:filter>
                        <c:comp-filter name="VCALENDAR">
                            <c:comp-filter name="VEVENT">
                                <c:time-range ' . $start . ' ' . $end . '/>
                            </c:comp-filter>
                        </c:comp-filter>
                    </c:filter>';

        return $this->findByCaldavFilter($filter);
    }
}