<?php

namespace Sprint\Migration;


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

        $this->parse($csvFile, $IBLOCK_ID);

        $this->out("Парсинг выпонен");
    }

    public function down() {
        $helper = new HelperManager();

        $IBLOCK_ID = $helper->Iblock()->getIblockIdIfExists('VACANCIES', 'CONTENT_RU');
        
        if ($IBLOCK_ID) {
            $rsElements = \CIBlockElement::GetList([], ['IBLOCK_ID' => $IBLOCK_ID], false, false, ['ID']);
            while ($element = $rsElements->GetNext()) {
                \CIBlockElement::Delete($element['ID']);
            }
            $this->out("Откат выполнен");
        }
    }

    private function parse($csvFile, $IBLOCK_ID) {
        $row = 1;
        $el = new \CIBlockElement;
        $arProps = [];

        $rsProp = \CIBlockPropertyEnum::GetList(
            ["SORT" => "ASC", "VALUE" => "ASC"],
            ['IBLOCK_ID' => $IBLOCK_ID]
        );
        while ($arProp = $rsProp->Fetch()) {
            $key = trim($arProp['VALUE']);
            $arProps[$arProp['PROPERTY_CODE']][$key] = $arProp['ID'];
        }
        if (($handle = fopen($csvFile, "r")) !== false) {
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                if ($row == 1) {
                    $row++;
                    continue;
                }
                $row++;

                $PROP['ACTIVITY'] = $data[9];
                $PROP['FIELD'] = $data[11];
                $PROP['OFFICE'] = $data[1];
                $PROP['LOCATION'] = $data[2];
                $PROP['REQUIRE'] = $data[4];
                $PROP['DUTY'] = $data[5];
                $PROP['CONDITIONS'] = $data[6];
                $PROP['EMAIL'] = $data[12];
                $PROP['DATE'] = date('d.m.Y');
                $PROP['TYPE'] = $data[8];
                $PROP['SALARY_TYPE'] = '';
                $PROP['SALARY_VALUE'] = $data[7];
                $PROP['SCHEDULE'] = $data[10];

                foreach ($PROP as $key => &$value) {
                    $value = trim($value);
                    $value = str_replace('\n', '', $value);
                    if (stripos($value, '•') !== false) {
                        $value = explode('•', $value);
                        array_splice($value, 0, 1);
                        foreach ($value as &$str) {
                            $str = trim($str);
                        }
                    } elseif ($arProps[$key]) {
                        $arSimilar = [];
                        foreach ($arProps[$key] as $propKey => $propVal) {
                            if ($key == 'OFFICE') {
                                $value = strtolower($value);
                                if ($value == 'центральный офис') {
                                    $value .= 'свеза ' . $data[2];
                                } elseif ($value == 'лесозаготовка') {
                                    $value = 'свеза ресурс ' . $value;
                                } elseif ($value == 'свеза тюмень') {
                                    $value = 'свеза тюмени';
                                }
                                $arSimilar[similar_text($value, $propKey)] = $propVal;
                            }
                            if (stripos($propKey, $value) !== false) {
                                $value = $propVal;
                                break;
                            }

                            if (similar_text($propKey, $value) > 50) {
                                $value = $propVal;
                            }
                        }
                        if ($key == 'OFFICE' && !is_numeric($value)) {
                            ksort($arSimilar);
                            $value = array_pop($arSimilar);
                        }
                    }
                }
                if ($PROP['SALARY_VALUE'] == '-') {
                    $PROP['SALARY_VALUE'] = '';
                } elseif ($PROP['SALARY_VALUE'] == 'по договоренности') {
                    $PROP['SALARY_VALUE'] = '';
                    $PROP['SALARY_TYPE'] = $arProps['SALARY_TYPE']['Договорная'];
                } else {
                    $arSalary = explode(' ', $PROP['SALARY_VALUE']);
                    if ($arSalary[0] == 'от' || $arSalary[0] == 'до') {
                        $PROP['SALARY_TYPE'] = $arProps['SALARY_TYPE'][mb_strtoupper($arSalary[0], 'UTF-8')];
                        array_splice($arSalary, 0, 1);
                        $PROP['SALARY_VALUE'] = implode(' ', $arSalary);
                    } else {
                        $PROP['SALARY_TYPE'] = $arProps['SALARY_TYPE']['='];
                    }
                }
                $arLoadProductArray = [
                    "MODIFIED_BY" => $GLOBALS['USER']->GetID(),
                    "IBLOCK_SECTION_ID" => false,
                    "IBLOCK_ID" => $IBLOCK_ID,
                    "PROPERTY_VALUES" => $PROP,
                    "NAME" => $data[3],
                    "ACTIVE" => end($data) ? 'Y' : 'N',
                ];

                if ($PRODUCT_ID = $el->Add($arLoadProductArray)) {
                    $this->out("Добавлен элемент с ID: " . $PRODUCT_ID . " - " . $data[3]);
                } else {
                    $this->outError("Error: " . $el->LAST_ERROR);
                }
            }
            fclose($handle);
        }
    }
}