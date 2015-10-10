<?php
/**
 * Created by PhpStorm.
 * User: Christopher Goehrs
 * Date: 10/9/2015
 * Time: 9:43 PM
 */

namespace lillockey\Utilities\App\XML;


class XmlElement
{
	public $name;
	public $attributes;
	public $content;
	public $children;

	/**
	 * @param $xml - The xml markup to be parsed
	 * @return null|XmlElement
	 */
	public static function parse($xml)
	{
		$parser = xml_parser_create();
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		$success = xml_parse_into_struct($parser, $xml, $tags);
		if(!$success) return null;
		xml_parser_free($parser);

		$elements = array();  // the currently filling [child] XmlElement array
		$stack = array();
		foreach ($tags as $tag) {
			$index = count($elements);
			if ($tag['type'] == "complete" || $tag['type'] == "open") {
				$elements[$index] = new XmlElement();
				$elements[$index]->name = $tag['tag'];
				$elements[$index]->attributes = $tag['attributes'];
				$elements[$index]->content = $tag['value'];
				if ($tag['type'] == "open") {  // push
					$elements[$index]->children = array();
					$stack[count($stack)] = &$elements;
					$elements = &$elements[$index]->children;
				}
			}
			if ($tag['type'] == "close") {  // pop
				$elements = &$stack[count($stack) - 1];
				unset($stack[count($stack) - 1]);
			}
		}
		return $elements[0];  // the single top-level element
	}
}