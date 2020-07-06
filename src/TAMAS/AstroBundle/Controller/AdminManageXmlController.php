<?php

namespace TAMAS\AstroBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SimpleXMLElement;

class AdminManageXmlController extends Controller {

    public function transformXMLAction() {

        /**
         * getText()
         * 
         * This function helps to get the full text of a node (including child node text content)
         * 
         * @param SimpleXMLElement object (one node) or null
         * @return string (the textual value of the node and its subnode)
         */
        function getText(SimpleXMLElement $node = null) {
            if ($node) {
                return dom_import_simplexml($node)->nodeValue;
            }
            return "";
        }

        /**
         * getAttrValue
         * 
         * This funtion helps to get the value of an xml attribute
         * 
         * @param SimpleXMLElement object (attribute node) or null
         * @return string (the textual value of the attribute)
         */
        function getAttrValue(SimpleXMLElement $attr = null) {
            if ($attr) {
                return $attr->__toString();
            }
            return "";
        }

        function getWork($item, $withMs = false) {
            $locus = $item->locus['from'] . '-' . $item->locus['to'];
            $lang = ['key' => $item->textLang['mainLang']->__toString(), 'name' => getText($item->textLang)];
            $title = getText($item->title);
            $incipit = '';
            $incipitLocus = '';
            if ($item->incipit && $item->incipit->locus['from'] && $item->incipit->locus['to']) {
                $incipitLocus = $item->incipit->locus['from']->__toString() . '-' . $item->incipit->locus['to']->__toString();
            }
            $incipit = ['title' => getText($item->incipit), 'locus' => $incipitLocus];
            $work = ['id' => getAttrValue($item['n']), 'locus' => $locus, 'lang' => $lang, 'title' => $title, 'incipit' => $incipit];
            if (!$withMs) {
                return $work;
            }
            $ms = getManuscript($item->xpath('./ancestor-or-self::msDesc')[0]);
            $work['ms'] = $ms;

            return $work;
        }

        $xml = simplexml_load_string(file_get_contents('./xml/manuscript_list.xml'));
        $msItem = $xml->xpath('//msItem');
        $itemsList = [];

        function getManuscript($ms, $withWork = false) {
            $country = getText($ms->msIdentifier->country);
            $settlement = getText($ms->msIdentifier->settlement);
            $institution = ['key' => getAttrValue($ms->msIdentifier->institution['key']), 'name' => getText($ms->msIdentifier->institution)];
            $shelfmark = getText($ms->msIdentifier->repository) . " " . getText($ms->msIdentifier->idno);
            $date = getAttrValue($ms->history->origin->date['notBefore']) . ' &ndash; ' . getAttrValue($ms->history->origin->date['notAfter']);
            $place = getText($ms->history->origin->placeName);

            $manuscript = ['country' => $country, 'settlement' => $settlement, 'institution' => $institution, 'shelfmark' => $shelfmark, 'date' => $date, 'place' => $place];
            if (!$withWork) {
                return $manuscript;
            }
            $works = [];
            foreach ($ms->xpath('.//msItem') as $item) {
                $works[] = getWork($item);
            }
            $manuscript['works'] = $works;
            return $manuscript;
        }

        function getAllWorks($msItem) {
            foreach ($msItem as $item) {
                $itemsList[] = getWork($item, true);
            }
            return $itemsList;
        }

        function getUniqueWorks(Array $works) {
            $idList = [];
            foreach ($works as $work) {

                $idList[$work['id']] = [];
            }
            // $idList = array_unique($idList);
            foreach ($works as $work) {
                $id = $work['id'];
                $idList[$id]['msContext'][] = ['incipit' => $work['incipit'], 'ms' => $work['ms']];
                $idList[$id]['title'] = $work['title'];
                $idList[$id]['id'] = $work['id'];
            }
            return $idList;
        }

        $msDesc = $xml->xpath('//msDesc');
        foreach ($msDesc as $ms) {
            $msList[] = getManuscript($ms, true);
        }

        var_dump(getUniqueWorks(getAllWorks($xml->xpath('//msItem'))));
        die;
    }

}
