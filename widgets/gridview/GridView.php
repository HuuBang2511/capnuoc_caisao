<?php

/**
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2020
 * @version   3.3.6
 */

namespace app\widgets\gridview;

use kartik\base\BootstrapInterface;
use kartik\base\BootstrapTrait;
use kartik\grid\GridViewInterface;
use Yii;
use yii\base\InvalidConfigException;
use yii\grid\Column;
use yii\grid\GridView as YiiGridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class GridView extends YiiGridView implements BootstrapInterface, GridViewInterface
{
    use GridViewTrait;
    use BootstrapTrait;

    /**
     * @var string the default data column class if the class name is not explicitly specified when configuring a data
     * column. Defaults to 'kartik\grid\DataColumn'.
     */
    public $dataColumnClass = 'kartik\grid\DataColumn';

    /**
     * @var array the HTML attributes for the grid caption
     */
    public $captionOptions = ['class' => 'kv-table-caption'];

    /**
     * @var array the HTML attributes for the grid element
     */
    public $tableOptions = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->initGridView();
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->prepareGridView();
        if ($this->pjax) {
            $this->beginPjax();
            parent::run();
            $this->endPjax();
        } else {
            parent::run();
        }
    }

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function renderTableBody()
    {
        $content = parent::renderTableBody();
        if ($this->showPageSummary) {
            $summary = $this->renderPageSummary();

            return $this->pageSummaryPosition === self::POS_TOP ? ($summary.$content) : ($content.$summary);
        }

        return $content;
    }

    /**
     * @inheritdoc
     */
    public function renderTableHeader()
    {
        return $this->renderTablePart('thead', parent::renderTableHeader());
    }

    /**
     * @inheritdoc
     */
    public function renderTableFooter()
    {
        return $this->renderTablePart('tfoot', parent::renderTableFooter());
    }

    /**
     * @inheritdoc
     */
    public function renderColumnGroup()
    {
        $requireColumnGroup = false;
        foreach ($this->columns as $column) {
            /* @var $column Column */
            if (!empty($column->options)) {
                $requireColumnGroup = true;
                break;
            }
        }
        if ($requireColumnGroup) {
            $cols = [];
            foreach ($this->columns as $column) {
                //Skip column with groupedRow
                if (property_exists($column, 'groupedRow') && $column->groupedRow) {
                    continue;
                }
                $cols[] = Html::tag('col', '', $column->options);
            }

            return Html::tag('colgroup', implode("\n", $cols));
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function renderSummary()
    {
        $count = $this->dataProvider->getCount();
        if ($count <= 0) {
            return '';
        }
        $summaryOptions = $this->summaryOptions;
        $tag = ArrayHelper::remove($summaryOptions, 'tag', 'div');
        $configItems = [
            'item' => $this->itemLabelSingle,
            'items' => $this->itemLabelPlural,
            'items-few' => $this->itemLabelFew,
            'items-many' => $this->itemLabelMany,
            'items-acc' => $this->itemLabelAccusative,
        ];
        $pagination = $this->dataProvider->getPagination();
        if ($pagination !== false) {
            $totalCount = $this->dataProvider->getTotalCount();
            $begin = $pagination->getPage() * $pagination->pageSize + 1;
            $end = $begin + $count - 1;
            if ($begin > $end) {
                $begin = $end;
            }
            $page = $pagination->getPage() + 1;
            $pageCount = $pagination->pageCount;
            $configSummary = [
                'begin' => $begin,
                'end' => $end,
                'count' => $count,
                'totalCount' => $totalCount,
                'page' => $page,
                'pageCount' => $pageCount,
            ];
            if (($summaryContent = $this->summary) === null) {
                return Html::tag($tag, Yii::t('kvgrid',
                    'Hiển thị <b>{begin, number}-{end, number}</b> của <b>{totalCount, number}</b> {totalCount, plural, one{đối tượng} other{đối tượng}}.',
                    $configSummary + $configItems
                ), $summaryOptions);
            }
        } else {
            $begin = $page = $pageCount = 1;
            $end = $totalCount = $count;
            $configSummary = [
                'begin' => $begin,
                'end' => $end,
                'count' => $count,
                'totalCount' => $totalCount,
                'page' => $page,
                'pageCount' => $pageCount,
            ];
            if (($summaryContent = $this->summary) === null) {
                return Html::tag($tag,
                    Yii::t('kvgrid', 'Tổng <b>{count, number}</b> {count, plural, one{{item}} other{{items}}}.',
                        $configSummary + $configItems
                    ), $summaryOptions);
            }
        }

        return Yii::$app->getI18n()->format($summaryContent, $configSummary, Yii::$app->language);
    }
}
