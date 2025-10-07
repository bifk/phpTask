<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

$this->addExternalCss($templateFolder . '/css/common.css');
?>

<div id="barba-wrapper">
    <div class="article-list">
        <?if(isset($arResult["SECTION"]) && $arResult["SECTION"]):?>
            <div>
                <h1><?=$arResult["SECTION"]["PATH"][0]["NAME"]?></h1>
            </div>
        <?endif;?>

        <?if($arParams["DISPLAY_TOP_PAGER"]):?>
            <?=$arResult["NAV_STRING"]?><br />
        <?endif;?>

        <?foreach($arResult["ITEMS"] as $arItem):?>
            <?
            $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
            ?>

            <a class="article-item article-list__item" href="<?=$arItem["DETAIL_PAGE_URL"]?>" data-anim="anim-3" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
                <div class="article-item__background">
                    <img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"];?>" alt="<?=$arItem["NAME"]?>"/>
                </div>

                <div class="article-item__wrapper">
                    <?if($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"]):?>
                        <div class="article-item__title"><?=$arItem["NAME"]?></div>
                    <?endif;?>

                    <?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arItem["PREVIEW_TEXT"]):?>
                        <div class="article-item__content">
                            <?=$arItem["PREVIEW_TEXT"];?>
                        </div>
                    <?endif;?>
                </div>
            </a>
        <?endforeach;?>

        <?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
            <br /><?=$arResult["NAV_STRING"]?>
        <?endif;?>
    </div>
</div>