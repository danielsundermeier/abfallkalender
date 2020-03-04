<?php

namespace Abfallkalender;

class Ical {

    private $calendar = '';
    private $kalendername = 'Abfall';
    private $keys = [
        'biotonne'              => 'Biotonne',
        'gelbe_saecke'          => 'Gelbe Säcke',
        'papiertonne'           => 'Papiertonne',
        'restmuell'             => 'Restmüll',
        'schadstoffanlieferung' => 'Schadstoffanlieferung',
    ];

    private $dateFormat = 'Ymd';

    private function footer() {
        $this->calendar .= "END:VCALENDAR";
    }



    private function head() {

        $this->calendar = "BEGIN:VCALENDAR\r\n"
                . "VERSION:2.0\r\n"
                . "X-WR-CALNAME:".$this->kalendername."\r\n"
                . "X-WR-TIMEZONE:Europe/Berlin\r\n"
                . "BEGIN:VTIMEZONE\r\n"
                . "TZID:Europe/Berlin\r\n"
                . "X-LIC-LOCATION:Europe/Berlin\r\n"
                . "BEGIN:DAYLIGHT\r\n"
                . "DTSTART:19700329T020000\r\n"
                . "RRULE:BYMONTH=3;FREQ=YEARLY;BYDAY=-1SU\r\n"
                . "TZNAME:CEST\r\n"
                . "TZOFFSETFROM:+0100\r\n"
                . "TZOFFSETTO:+0200\r\n"
                . "END:DAYLIGHT\r\n"
                . "BEGIN:STANDARD\r\n"
                . "DTSTART:19701025T030000\r\n"
                . "RRULE:BYMONTH=10;FREQ=YEARLY;BYDAY=-1SU\r\n"
                . "TZNAME:CET\r\n"
                . "TZOFFSETFROM:+0200\r\n"
                . "TZOFFSETTO:+0100\r\n"
                . "END:STANDARD\r\n"
                . "END:VTIMEZONE\r\n"
                . "PRODID:-//" . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "//NONSGML//DE\r\n"
                . "CALSCALE:GREGORIAN\r\n";

    }

    private function body(array $data)
    {
        foreach ($data as $key => $dates) {

            foreach ($dates as $date) {

                $kw         = $date->format('W');
                $id         = $key . $date->format('o-W');
                $title      = $this->keys[$key] . ' KW ' . $kw;

                $startDate  = clone $date;
                $startDate->setTime(0, 0, 0, 0);

                $this->calendar    .= "BEGIN:VEVENT\r\n"
                                   . "UID:" . $id . "\r\n"
                                   . "DTSTART;VALUE=DATE:" . $startDate->format($this->dateFormat) . "\r\n"
                                   . "DTSTAMP:" . $startDate->format($this->dateFormat) . "\r\n"
                                   . "SUMMARY:" . addcslashes($title, ",\\;") . "\r\n"
                                   . "SEQUENCE:0\r\n"
                                   . "STATUS:CONFIRMED\r\n"
                                   . "TRANSP:OPAQUE\r\n"
                                   . "END:VEVENT\r\n";

            }

        }
    }

    public function handle(array $dates): string
    {
        if (count($dates) == 0) {
            return '';
        }

        $this->head();
        $this->body($dates);
        $this->footer();

        return $this->calendar;
    }

    public function getKeys(): array
    {
        return array_keys($this->keys);
    }

}