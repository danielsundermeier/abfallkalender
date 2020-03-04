<?php

namespace Abfallkalender\Dom;

class Document {

    private $dom;

    public function __construct(string $html)
    {
        $this->dom = new \DOMDocument();
        $this->dom->loadHTML($html);

        $detail = $this->dom->getElementsByTagName('td');

        $i = 0;
        $j = 0;
        foreach($detail as $sNodeDetail)
        {
            if ($i > 2) {
                continue;
            }
            $tds[$j][] = trim($sNodeDetail->textContent);
            $i = $i++;
            $j = $i % 3 == 0 ? $j + 1 : $j;
        }

        echo '<pre>';
        var_dump($tds);
        echo '</pre>';
    }

}