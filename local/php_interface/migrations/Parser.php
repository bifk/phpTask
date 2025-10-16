<?

namespace Sprint\Migration;

require_once __DIR__ . '/ParseMigrationInterface.php';

class Parser implements ParseMigrationInterface {

	private $PROP = [];

	private $IBLOCK_ID;

    private $outCall;
    private $outErrorCall;

	function __construct($IBLOCK_ID) {
        $this->IBLOCK_ID = $IBLOCK_ID;
    }

 	public function setOutCalls($outCall, $outErrorCall) {
        $this->outCall = $outCall;
        $this->outErrorCall = $outErrorCall;
    }

    private function out($message) {
		call_user_func($this->outCall, $message);
    }

    private function outError($message) {
     	call_user_func($this->outErrorCall, $message);
    }


	 public function parse($csvFile) {
        $row = 1;

        $arProps = [];

        $rsProp = \CIBlockPropertyEnum::GetList(
            ["SORT" => "ASC", "VALUE" => "ASC"],
            ['IBLOCK_ID' => $this->IBLOCK_ID]
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

				$this->initProp($data);

                foreach ($this->PROP as $key => &$value) {
                    $value = $this->cleanStr($value);

                    if (stripos($value, '•') !== false) {
                        $value = explode('•', $value);
                        array_splice($value, 0, 1);

                        foreach ($value as &$str) {
							$str = $this->cleanStr($str);
                        }
                    } elseif ($arProps[$key]) {
						$value = $this->propsСomparison($arProps, $key, $value);
                    }
                }

                $this->salaryFormat($arProps);
                $this->addElement($data);
			}
            fclose($handle);
        }
    }


	private function cleanStr($str) {
		$str = trim($str);
    	return str_replace('\n', '', $str);
	}

	private function initProp($data) {
		$this->PROP['ACTIVITY'] = $data[9];
		$this->PROP['FIELD'] = $data[11];
		$this->PROP['OFFICE'] = $data[1];
		$this->PROP['LOCATION'] = $data[2];
		$this->PROP['REQUIRE'] = $data[4];
		$this->PROP['DUTY'] = $data[5];
		$this->PROP['CONDITIONS'] = $data[6];
		$this->PROP['EMAIL'] = $data[12];
		$this->PROP['DATE'] = date('d.m.Y');
		$this->PROP['TYPE'] = $data[8];
		$this->PROP['SALARY_TYPE'] = '';
		$this->PROP['SALARY_VALUE'] = $data[7];
		$this->PROP['SCHEDULE'] = $data[10];
		$this->PROP['NAME'] = $data[3];
	}

	private function salaryFormat($arProps) {
		if ($this->PROP['SALARY_VALUE'] == '-' || $this->PROP['SALARY_VALUE'] == '')   {
			$this->PROP['SALARY_VALUE'] = '';
		} elseif ($this->PROP['SALARY_VALUE'] == 'по договоренности') {
			$this->PROP['SALARY_VALUE'] = '';
			$this->PROP['SALARY_TYPE'] = $arProps['SALARY_TYPE']['Договорная'];
		} else {
			$arSalary = explode(' ', $this->PROP['SALARY_VALUE']);
			if ($arSalary[0] == 'от' || $arSalary[0] == 'до') {
				$this->PROP['SALARY_TYPE'] = $arProps['SALARY_TYPE'][mb_strtoupper($arSalary[0], 'UTF-8')];
				array_splice($arSalary, 0, 1);
				$this->PROP['SALARY_VALUE'] = implode(' ', $arSalary);
			} else {
				$this->PROP['SALARY_TYPE'] = $arProps['SALARY_TYPE']['='];
			}
		}
	}


	private function propsСomparison($arProps, $key, $value) {
		$arSimilar = [];
	 	foreach ($arProps[$key] as $propKey => $propVal) {
			if ($key == 'OFFICE') {
				$value = strtolower($value);
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

		if ($key == 'OFFICE') {
			ksort($arSimilar);
			$value = array_pop($arSimilar);
		}

		return $value;
	}


	private function addElement($data) {
		$el = new \CIBlockElement;

		$arLoadProductArray = [
			"MODIFIED_BY" => $GLOBALS['USER']->GetID(),
			"IBLOCK_SECTION_ID" => false,
			"IBLOCK_ID" => $this->IBLOCK_ID,
			"PROPERTY_VALUES" => $this->PROP,
			"NAME" => $this->PROP['NAME'],
			"ACTIVE" => end($data) ? 'Y' : 'N',
		];

		if ($PRODUCT_ID = $el->Add($arLoadProductArray)) {
			$this->out("Добавлен элемент с ID: " . $PRODUCT_ID . " - " . $this->PROP['NAME']);
		} else {
			$this->outError("Error: " . $el->LAST_ERROR);
		}
	}

}

?>