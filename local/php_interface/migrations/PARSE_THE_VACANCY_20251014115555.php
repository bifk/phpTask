<?php

namespace Sprint\Migration;

require_once __DIR__ . '/Parser.php';

class PARSE_THE_VACANCY_20251014115555 extends Version 
{

    protected $description = "Парсер вакансий";

    public function up() {

		if (!$GLOBALS['USER']->IsAdmin()) {
			$this->outError("Недостаточно прав");
			return false;
		}
        $helper = new HelperManager();

        $IBLOCK_ID = $helper->Iblock()->getIblockIdIfExists('VACANCIES', 'CONTENT_RU');
        
        if (!$IBLOCK_ID) {
            $this->outError("Инфоблок не найден");
            return false;
        }

        $csvFile = __DIR__ . '/vacancy.csv';
        
        if (!file_exists($csvFile)) {
            $this->outError("CSV файл не найден");
            return false;
        }
        $parser = new Parser($IBLOCK_ID);
		$parser->setOutCalls(
            function($message) {
                $this->out($message);
            },
            function($message) {
                $this->outError($message);
            }
        );
		$parser->parse($csvFile);

        $this->out("Парсинг выпонен");
    }

    public function down() {
		if (!$GLOBALS['USER']->IsAdmin()) {
			$this->outError("Недостаточно прав");
			return false;
		}

        $helper = new HelperManager();

        $IBLOCK_ID = $helper->Iblock()->getIblockIdIfExists('VACANCIES', 'CONTENT_RU');

        if ($IBLOCK_ID) {
            $rsElements = \CIBlockElement::GetList([], ['IBLOCK_ID' => $IBLOCK_ID], false, false, ['ID']);
            while ($element = $rsElements->GetNext()) {
                \CIBlockElement::Delete($element['ID']);
            }
            $this->out("Откат выполнен");
		} else {
			$this->outError("Инфоблок не найден");
            return false;
		}
    }
}