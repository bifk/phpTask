
<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->addExternalCss($templateFolder . '/css/common.css');

$messageQuestion = null;
?>


<?=$arResult["FORM_NOTE"] ?? ''?>

<?if ($arResult["isFormNote"] != "Y")
{
?>
    
    <?=$arResult["FORM_HEADER"]?>
    
    <div class="contact-form">
        <div class="contact-form__head">
            <?if ($arResult["isFormTitle"] == "Y"):?>
                <div class="contact-form__head-title"><?=$arResult["FORM_TITLE"]?></div>
            <?endif;?>
            <?if ($arResult["isFormDescription"] == "Y"):?>
                <div class="contact-form__head-text"><?=$arResult["FORM_DESCRIPTION"]?></div>
            <?endif;?>
        </div>
        <div class="contact-form__form-inputs">
            <?
            foreach ($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion) {

				if ($arQuestion["CAPTION"] === GetMessage("FORM_MESSAGE_QUESTION")) {
                    // Переменная для настройки шаблона сообщения
					$messageQuestion = $arQuestion;
					continue;
				}

                if ($arQuestion['STRUCTURE'][0]['FIELD_TYPE'] == 'hidden') {
                    echo $arQuestion["HTML_CODE"];
                } else {
                    $isRequired = ($arQuestion["REQUIRED"] == "Y");
                    $hasError = isset($arResult["FORM_ERRORS"][$FIELD_SID]);
					if ($hasError) {
						$arQuestion["HTML_CODE"] = str_replace(
							'class="', 
							'class="invalid ', 
							$arQuestion["HTML_CODE"]
						);

			        }
            ?>
                <div class="input contact-form__input <?=$hasError ? 'input--error' : ''?>">
                    <label class="input__label" for="<?="medicine_" . $FIELD_SID?>">
                        <div class="input__label-text">
                            <?=$arQuestion["CAPTION"]?><?if ($isRequired):?>*<?endif;?>
                        </div>
                        <? // HTML-параметры указаны в полях для ответа в графе "Параметры" ?>
                        <?=$arQuestion["HTML_CODE"]?>
                        <?if ($hasError):?>
                            <div class="input__notification"><?=$arResult["FORM_ERRORS"][$FIELD_SID]?></div>
                        <?endif;?>
                    </label>
                </div>
            <?
                }
            }
            ?>

        </div>

		<div class="contact-form__form-message">
            <div class="input"><label class="input__label" for="medicine_message">
                <div class="input__label-text"><?=$messageQuestion["CAPTION"]?></div>
                 <?=$messageQuestion["HTML_CODE"]?>
                <div class="input__notification"></div>
            </label></div>
        </div>

        <div class="contact-form__bottom">
            <div class="contact-form__bottom-policy">
                <?=GetMessage("FORM_AGREEMENT"); ?>
            </div>

            <button name="web_form_submit" value="Y" class="form-button contact-form__bottom-button" data-success="<?=GetMessage("FORM_SENDED") ?>"
                    data-error="<?=GetMessage("FORM_SEND_ERROR") ?>">
    			<div class="form-button__title"><?=GetMessage("FORM_SEND"); ?></div>
			</button>
        </div>
    </div>
    
    <?=$arResult["FORM_FOOTER"]?>
    
<?
}