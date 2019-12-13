<?php

include "lib.php";

//ini_set('error_reporting', E_ALL);
//error_reporting(E_ALL);

//Папка з хмл даними
$path_to_xml = "/home/tarac196/xml/";

//Розділ сайту по замовчуванню
$Menu_Section_Default = "services";

//Основний інформаційний ХМЛ файл
$Xml_Info = new DOMDocument;
$Xml_Info->load($path_to_xml . "info.xml");

//Основний шаблон для трансформації
$XslTemplate_Info = new DOMDocument;
$XslTemplate_Info->load($path_to_xml . "info.xsl");

$XslTemplate_Proc = new XSLTProcessor;
$XslTemplate_Proc->importStyleSheet($XslTemplate_Info);

//XPath навігація по інформаційному файлі
$Xpath_Info = new DOMXPath($Xml_Info);

//Відкритий розділ сайту
$Menu_Section = isset($_GET["section"]) ? $_GET["section"] : "";

//Перевірка розділу сайту
if ($Menu_Section == "") {
    $Menu_Section = $Menu_Section_Default;
}
else {
	$xpath_query = "count(/root/menu/item[@key = " . QuoteXPathAttributeString($Menu_Section) . "])";
	$xpath_count = $Xpath_Info->evaluate($xpath_query);

	//Якщо результат не 1, тоді встановлюємо розділ по замовчуванню
	if (!($xpath_count == 1)) {
	    $Menu_Section = $Menu_Section_Default;
	}
}

//Каталог розділів
$Catatalog = isset($_GET["catatalog"]) ? $_GET["catatalog"] : "";

//Перевірка розділу каталогу
if ($Catatalog == "") {
    $Catatalog = GetDefaultCatalog($Menu_Section, $Xpath_Info);
}
else {
    //Запит: пошук ключа
	$xpath_query = "count(/root/catalog/block[@section_key = " . QuoteXPathAttributeString($Menu_Section)
	             . "]/item[@key = " . QuoteXPathAttributeString($Catatalog) . "])";

	$xpath_count = $Xpath_Info->evaluate($xpath_query);

	//Якщо результат не 1, тоді встановлюємо розділ по замовчуванню
	if (!($xpath_count == 1)) {
	    $Catatalog = GetDefaultCatalog($Menu_Section, $Xpath_Info);
	}
}

?>

<!DOCTYPE html>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>ТОВ &quot;ТЕХНОЛОГІЯ БЕЗПЕКИ&quot;</title>
  
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-153819832-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-153819832-1');
</script>

</head>
<body>

<div class="jumbotron">
    <div class="container-fluid" style="padding-bottom:30px;">
	   <h1>ТОВ ТЕХНОЛОГІЯ БЕЗПЕКИ</h1>
	   <h4>Продаж та встановлення систем бизпеки</h4>
	</div>
	<div class="container-fluid">

<?php

$XslTemplate_Proc->setParameter("", array(
		"template" => "head", 
		"section" => $Menu_Section
	)
);

echo $XslTemplate_Proc->transformToXML($Xml_Info);

?>
    </div>
</div>

<div class="container-fluid">
  <div class="row">

    <div class="col">

<?php

$XslTemplate_Proc->setParameter("", array(
		"template" => "catalog",
		"section" => $Menu_Section,
		"catalog" => $Catatalog
	)
);

echo $XslTemplate_Proc->transformToXML($Xml_Info);

?>

    </div>

    <div class="col-7">

<?php

if ($Menu_Section != "" && $Catatalog != "") {
	
	//Сторінка
	$OpenPage = isset($_GET["page"]) ? $_GET["page"] : 0;

	if (!is_numeric($OpenPage))
		$OpenPage = 0;

	$filename_xml_block = "";

	if ($OpenPage > 0) {
	    //Перевірити наявність сторінки
		$filename_xml_block = GetFileNameXmlBlock($Menu_Section, $Catatalog, $OpenPage, $Xpath_Info);

		if ($filename_xml_block == "") {
			echo "<p>404. Сторінка не знайдена!</p>";
		}
	}
	else {
		//Отримати перший наявний блок
		$filename_xml_block = GetFileNameXmlBlock($Menu_Section, $Catatalog, $OpenPage, $Xpath_Info);

		if ($filename_xml_block == "") {
			echo "<p>Даних немає!</p>";
		}
	}

	//Вивід даних блоку
	if ($filename_xml_block != "") {

		$Xml_Block = new DOMDocument;
		$Xml_Block->load($path_to_xml . "block/" . $Menu_Section . "/" . $Catatalog . "/" . $filename_xml_block);

		$XslTemplate_Block = new DOMDocument;
		$XslTemplate_Block->load($path_to_xml . "block.xsl");

		$XslTemplate_BlockProc = new XSLTProcessor;
		$XslTemplate_BlockProc->importStyleSheet($XslTemplate_Block);

		echo $XslTemplate_BlockProc->transformToXML($Xml_Block);
	}
}

?>

	</div>

	<div class="col">
	    <p></p>
	</div>

  </div>
</div>

<div class="jumbotron" style="margin-bottom:0;margin-top:30px;">

<?php

//Блок повторення меню в низу сайту
$XslTemplate_Proc->setParameter("", array(
		"template" => "footer",
		"section" => $Menu_Section,
		"catalog" => $Catatalog
	)
);

echo $XslTemplate_Proc->transformToXML($Xml_Info);

?>

<p>Copyright © 2014–2019 ТОВ ТЕХНОЛОГІЯ БЕЗПЕКИ</p>

</div>

</body>
</html>