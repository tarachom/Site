<?php

ini_set("error_reporting", E_ALL);
error_reporting(E_ALL);

/**
 * Вернёт массив, содержащий имена файлов из указанной директории
 * (содержащиеся директории будут проигнорированы)
 * 
 * @param string $dirpath - путь к диретории 
 * @return array  - массив имён файлов
 */
function getSimpleFilesList($dirpath) {
    $result = array();
     
    $cdir = scandir($dirpath); 
    foreach ($cdir as $value) {
        // если это "не точки" и не директория
        if (!in_array($value,array(".", "..")) && !is_dir($dirpath . DIRECTORY_SEPARATOR . $value)) {
            $result[] = $value;
         }
    } 
     
    return $result;
}

/**
* Пошук розділів каталогу та очистка
*
* @param $xpath_info - навігація по інформаційний хмл файл
*/
function clearNodes($xpath_info) {

	$xpath_query = "/root/catalog/block[@section_key='shop']/item";

	$entries_items = $xpath_info->query($xpath_query);
	$entries_item_array = array(); 

	foreach ($entries_items as $entries_item)
		$entries_item_array[] = $entries_item;

	//Видалення
	foreach($entries_item_array as $entries_item_element)
		$entries_item_element->parentNode->removeChild($entries_item_element);
}

/**
* Обробка файлу імпорту
*
* @param $importxmlfile - шлях до файлу імпорту
* @param $export_dir
* @param $block_dir - шлях до каталогу блоку даних
* @param $images_dir
* @param $xml_info - основний інформаційний хмл файл
* @param $xpath_info - навігація по інформаційний хмл файл
*/
function workImportXmlFile(
	$xml_info, $xpath_info, 
	$export_dir, $block_dir, $images_dir, 
	$importxmlfile) {
	
	echo $importxmlfile . "\n";

	$Xml_Import = new DOMDocument;
	$Xml_Import->load($export_dir . $importxmlfile);

	$Xpath_Import = new DOMXPath($Xml_Import);
	$Xpath_Import->registerNamespace("m", "urn:1C.ru:commerceml_2");

	//Розділ каталогу
	$catalog_id_nodes = $Xpath_Import->query("/m:КоммерческаяИнформация/m:Каталог/m:Ид");
	$catalog_id = $catalog_id_nodes->item(0)->nodeValue;

	//Наименование
	$catalog_name_nodes = $Xpath_Import->query("/m:КоммерческаяИнформация/m:Каталог/m:Наименование");
	$catalog_name = $catalog_name_nodes->item(0)->nodeValue;

	//Список товарів
	$goods_nodes = $Xpath_Import->query("/m:КоммерческаяИнформация/m:Каталог/m:Товары/m:Товар");

	/*
	 * block/shop/  $catalog_id  /1.xml
	 */

	//Каталог блоку даних
	if (!file_exists($block_dir . $catalog_id)) 
		mkdir($block_dir . $catalog_id);

	//Файл блоку даних
	$block_xmlfile = new DOMDocument( "1.0", "utf-8" );

	//<root>
	$block_root_item = $block_xmlfile->createElement("root");
	$block_xmlfile->appendChild($block_root_item);
	
	foreach ($goods_nodes as $goods_node) {

		//Наименование товару
		$goods_node_name = $Xpath_Import->query("m:Наименование", $goods_node);
		$goods_name = $goods_node_name->item(0)->nodeValue;

		//Картинка
		$goods_node_image = $Xpath_Import->query("m:Картинка", $goods_node);
		$goods_image = $goods_node_image->item(0)->nodeValue;

		//Описание
		$goods_node_desc = $Xpath_Import->query("m:Описание", $goods_node);
		$goods_desc = $goods_node_desc->item(0)->nodeValue;

		//Копіювання картинки в публічний каталог
		$goods_image_basename = pathinfo($export_dir . $goods_image, PATHINFO_BASENAME);
	    copy($export_dir . $goods_image, $images_dir . $goods_image_basename);

		//<card>
		$block_item_card = $block_xmlfile->createElement("card");
		$block_root_item->appendChild($block_item_card);

		//<title>
		$block_item_title = $block_xmlfile->createElement("title", $goods_name);
		$block_item_card->appendChild($block_item_title);

		$block_item_paragraf = $block_xmlfile->createElement("p");
		$block_item_card->appendChild($block_item_paragraf);

		$block_item_paragraf_img = $block_xmlfile->createElement("img");
		$block_item_paragraf->appendChild($block_item_paragraf_img);

		$block_item_paragraf_src = $block_xmlfile->createElement("src", $goods_image_basename);
		$block_item_paragraf_img->appendChild($block_item_paragraf_src);

		$block_item_paragraf_description = $block_xmlfile->createElement("description");
		$block_item_paragraf->appendChild($block_item_paragraf_description);

		$block_item_paragraf_description_cdata = $block_xmlfile->createCDATASection($goods_desc);
		$block_item_paragraf_description->appendChild($block_item_paragraf_description_cdata);
		
	}

	$block_xmlfile->save($block_dir . $catalog_id . "/1.xml");

	/*
	 * info.xml
	 */

	//Вітка block
	$block_node = $xpath_info->query("/root/catalog/block[@section_key='shop']");

	//<item>
	$element_item = $xml_info->createElement("item");
	$block_node->item(0)->appendChild($element_item);

	//@key
	$element_item_key_attr = $xml_info->createAttribute("key");
	$element_item_key_attr->value = $catalog_id;
	$element_item->appendChild($element_item_key_attr);

	//<title>
	$element_item_title = $xml_info->createElement("title", $catalog_name);
	$element_item->appendChild($element_item_title);

	//<card>
	$element_item_card = $xml_info->createElement("card");
	$element_item->appendChild($element_item_card);

	//<file>
	$element_item_file = $xml_info->createElement("file", "1.xml");
	$element_item_card->appendChild($element_item_file);
}


//Папка з хмл даними
$path_to_xml = "/home/tarac196/xml/";

//Папка блоку даних
$path_to_block_dir = $path_to_xml . "block/shop/";

//Папка для картинок
$path_to_public_images = "/home/tarac196/public_html/images/";

//Шлях до хмл файлів 1С
$path_to_1C_xml = $path_to_xml . "webdata/";

//Основний інформаційний ХМЛ файл
$Xml_Info = new DOMDocument;
$Xml_Info->load($path_to_xml . "info.xml");

//XPath навігація по інформаційному файлі
$Xpath_Info = new DOMXPath($Xml_Info);

//Очистка
clearNodes($Xpath_Info);

//Файли імпорту
$file_item_array = getSimpleFilesList($path_to_1C_xml);

foreach($file_item_array as $file_item) {
    $path_parts = pathinfo($path_to_1C_xml . $file_item);

	var_dump($path_parts);

	if ($path_parts["extension"] == "xml") {

		workImportXmlFile(
			$Xml_Info, $Xpath_Info,
			$path_to_1C_xml, $path_to_block_dir, $path_to_public_images,
			$file_item
		);
	}
}

$Xml_Info->save($path_to_xml . "info.xml");

?>