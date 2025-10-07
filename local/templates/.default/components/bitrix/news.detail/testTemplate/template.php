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
$themeClass = isset($arParams['TEMPLATE_THEME']) ? ' bx-'.$arParams['TEMPLATE_THEME'] : '';
CUtil::InitJSCore(['fx', 'ui.fonts.opensans']);
?>
<div class="article-card">
	<?if($arParams["DISPLAY_NAME"]!="N" && $arResult["NAME"]):?>
    	<div class="article-card__title"><?=$arResult["NAME"]?></div>
	<?endif;?>
	<?if($arParams["DISPLAY_DATE"]!="N" && $arResult["DISPLAY_ACTIVE_FROM"]):?>
		<div class="article-card__date"><?echo $arResult["DISPLAY_ACTIVE_FROM"]?></div>
	<?endif?>



    <div class="article-card__content">
        <?if($arParams["DISPLAY_PICTURE"]!="N"):?>
			<? if (is_array($arResult["DETAIL_PICTURE"]))
			{
				?>
				<div class="article-card__image sticky">
					<img
						class="card-img-top"
						src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>"
						alt="<?=$arResult["DETAIL_PICTURE"]["ALT"]?>"
						title="<?=$arResult["DETAIL_PICTURE"]["TITLE"]?>"
						data-object-fit="cover"
						/>
				</div>
				<?
			}
			?>
		<?endif?>
        <div class="article-card__text">
            <div class="block-content" data-anim="anim-3"><?if($arResult["NAV_RESULT"]):?>
					<?if($arParams["DISPLAY_TOP_PAGER"]):?><?=$arResult["NAV_STRING"]?><br /><?endif;?>
					<?echo $arResult["NAV_TEXT"];?>
					<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?><br /><?=$arResult["NAV_STRING"]?><?endif;?>
				<?elseif($arResult["DETAIL_TEXT"] <> ''):?>
					<?echo $arResult["DETAIL_TEXT"];?>
				<?else:?>
					<?echo $arResult["PREVIEW_TEXT"];?>
				<?endif?></div>
            <a class="article-card__button" href="<?=$arParams["SEARCH_PAGE"]?>"><?=GetMessage("T_NEWS_DETAIL_BACK")?></a>
            </div>

    </div>
</div>
<script>
	BX.ready(function() {
		var slider = new JCNewsSlider('<?=CUtil::JSEscape($this->GetEditAreaId($arResult['ID']));?>', {
			imagesContainerClassName: 'news-detail-slider-container',
			leftArrowClassName: 'news-detail-slider-arrow-container-left',
			rightArrowClassName: 'news-detail-slider-arrow-container-right',
			controlContainerClassName: 'news-detail-slider-control'
		});
	});
</script>
