<?php

//Функція для екранування параметрів XPath
function QuoteXPathAttributeString($haystack) {

	$index = strpos($haystack, "'", 0);

	if($index === false) {
		return "'" . $haystack . "'";
	}
	else {
		return "concat('" . str_replace("'", "',\"'\",'", $haystack) . "')";
	}
}

//Функція пошуку першого розділу
function GetDefaultCatalog($section, $xpath_info_obj) {

	$xpath_query = "/root/catalog/block[@section_key = " . QuoteXPathAttributeString($section) . "]/item[1]/@key";

    $xpath_result = $xpath_info_obj->evaluate($xpath_query);

	if ($xpath_result === false) {
		echo "error query get default catalog!";
		return "";
	}
	else {
		//var_dump($xpath_result);
		if ($xpath_result->length == 1) {
			return $xpath_result->item(0)->nodeValue;
		}
		else {
		    echo "error get default catalog! length: " . $xpath_result->length;
			return "";
		}
	}
}

//Функція пошуку файлу блоку даних
function GetFileNameXmlBlock($section, $catalog, $open_page, $xpath_info_obj) {

	//Пошук списку файлів
	$xpath_query = "/root/catalog/block[@section_key = " . QuoteXPathAttributeString($section) . "]" . 
		           "/item[@key = " . QuoteXPathAttributeString($catalog) . "]" . 
				   "/card/file";

	if ($open_page > 0) {
		//Перевірка конкретного файлу
		$xpath_query = $xpath_query . "[text() = '" . $open_page . ".xml']";
	}
	else {
		//Пошук першого файлу
		$xpath_query = $xpath_query . "[1]";
	}

	$xpath_result = $xpath_info_obj->evaluate($xpath_query);

	if ($xpath_result === false) {
		echo "error query!";
		return "";
	}
	else {
		//
		if ($xpath_result->length == 1) {
			return $xpath_result->item(0)->nodeValue;
		}
		else {
			return "";
		}
	}
}



?>