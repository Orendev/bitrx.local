<?
/**
 * Created by PhpStorm.
 * User: Abai Adelshin
 * www.orendev.ru
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>

<section class="news">
    <div class="container">
        <div class="section-title">
            <h2>Новости и события</h2>
        </div>
        <div id="carousel-news-generic" data-interval="6000" data-pause="hover" data-ride="carousel" class="carousel slide">

            <div class="carousel-inner">
                <?foreach ($arResult['elements']['items'] as $key => $arItems):?>
                    <div class="item <?if ($key == 0):?>active<?endif?>">
                        <ul class="list-unstyled row">
                            <?foreach ($arItems as $arItem):?>
                                <li class="col-md-3">
                                    <div class="thumbnail"><a href="<?= $arItem['DETAIL_PAGE_URL']?>"><img src="<?= $arItem['SMALL_PICTURE']['SRC']?>" alt="<?= $arItem['SMALL_PICTURE']['TITLE']?>"></a></div>
                                    <div class="caption">
                                        <p class="date text-danger"><?= $arItem['ACTIVE_FROM']?></p>
                                        <h5><a href="<?= $arItem['DETAIL_PAGE_URL']?>"><?echo $arItem['NAME']?></a></h5>
                                    </div>
                                </li>
                            <?endforeach;?>
                        </ul>
                    </div>
                <? endforeach;?>
            </div>

            <div class="control-box">
                <div class="btn-group"><a href="#carousel-news-generic" role="button" data-slide="prev" class="left carousel-control btn btn-default"><span aria-hidden="true" class="fa fa-chevron-left"></span><span class="sr-only">Previous</span></a><a href="#carousel-news-generic" role="button" data-slide="next" class="right carousel-control btn btn-default"><span aria-hidden="true" class="fa fa-chevron-right"></span><span class="sr-only">Next</span></a></div><a href="#" class="btn btn-default pull-right">Все события</a>
            </div>
        </div>
    </div>
</section>