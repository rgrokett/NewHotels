<?php

require_once 'renderer.php';

class ViewAllRenderer extends Renderer
{
    function RenderLookupColumn($LookupColumn)
    {
        $this->DisplayTemplate('list/lookup_column.tpl',
            array('Column' => $LookupColumn),
            array()
        );
    }

    function RenderCustomPageNavigator($pageNavigator)
    {
        if ($pageNavigator->GetNavigationStyle() == NS_LIST)
            $templateName = 'custom_page_navigator.tpl';
        elseif ($pageNavigator->GetNavigationStyle() == NS_COMBOBOX)
            $templateName = 'combo_box_custom_page_navigator.tpl';

        $this->DisplayTemplate('list/'.$templateName,
            array(
            'PageNavigator' => $pageNavigator,
            'PageNavigatorPages' => $pageNavigator->GetPages()),
            array()
        );
    }

    function RenderCompositePageNavigator($PageNavigator)
    {
        $this->DisplayTemplate('list/composite_page_navigator.tpl',
            array(
            'PageNavigator' => $PageNavigator),
            array()
        );
    }

    function RenderPageNavigator($PageNavigator)
    {
        $this->DisplayTemplate('list/page_navigator.tpl',
            array(
            'PageNavigator' => $PageNavigator,
            'PageNavigatorPages' => $PageNavigator->GetPages()),
            array()
        );
    }

    function RenderPage($Page)
    {
        $this->SetHTTPContentTypeByPage($Page);
        $Page->BeforePageRender->Fire(array(&$Page));

        $Grid = $this->Render($Page->GetGrid());
        $PageNavigator = $Page->GetPageNavigator();
        if (isset($PageNavigator))
            $PageNavigator = $this->Render($Page->GetPageNavigator());
        $PageList = $Page->GetPageList();
        if (isset($PageList))
            $PageList = $this->Render($PageList);
        else
            $PageList = '';

        $isAdvancedSearchActive = false;
        if (isset($Page->AdvancedSearchControl))
            $isAdvancedSearchActive = $Page->AdvancedSearchControl->IsActive();

        $this->DisplayTemplate('list/page.tpl',
            array('Page' => $Page),
            array(
            'Grid' => $Grid,
            'AdvancedSearch' => isset($Page->AdvancedSearchControl) ? $this->Render($Page->AdvancedSearchControl) : '',
            'PageNavigator' => $PageNavigator,
            'IsAdvancedSearchActive' => $isAdvancedSearchActive,
            'FriendlyAdvancedSearchCondition' => $Page->AdvancedSearchControl->GetUserFriendlySearchConditions(),
            'PageList' => $PageList
        ));
    }

    function RowOperationByLinkColumn($Column)
    {
        $this->result = "<a class=\"page_link\" href=\"" . $Column->GetLink() . "\">" . $Column->GetCaption() . "</a> ";
    }

    function RenderIntegerColumn($Column)
    {
        $this->RenderGridColumn($Column);
    }

    function RenderGridColumn($GridColumn)
    {
        $this->result = $GridColumn->GetDataset()->GetFieldValueByName($GridColumn->GetFieldName());
    }

    function RenderHyperLinkColumn($column)
    {
        $this->result =
            '<a class="page_link" href="' .
            $column->GetHref() . '">' .
            $column->GetLinkText() . '</a>';
    }

    function GetStylesForColumn($grid, $rowData)
    {
        $cellFontColor = array();
        $cellFontSize = array();
        $cellBgColor = array();
        $cellItalicAttr = array();
        $cellBoldAttr = array();

        $grid->OnCustomDrawCell_Simple->Fire(array($rowData, &$cellFontColor, &$cellFontSize, &$cellBgColor, &$cellItalicAttr, &$cellBoldAttr));

        $result = array();
        $fieldNames = array_unique(array_merge(
            array_keys($cellFontColor),
            array_keys($cellFontSize),
            array_keys($cellBgColor),
            array_keys($cellItalicAttr),
            array_keys($cellBoldAttr)));

        foreach ($fieldNames as $fieldName)
        {
            $currentFieldStyle = '';
            if (array_key_exists($fieldName, $cellFontColor))
                $currentFieldStyle .= 'color: '.$cellFontColor[$fieldName].';';
            if (array_key_exists($fieldName, $cellFontSize))
                $currentFieldStyle .= 'font-size: '.$cellFontSize[$fieldName].';';
            if (array_key_exists($fieldName, $cellBgColor))
                $currentFieldStyle .= 'background-color: '.$cellBgColor[$fieldName].';';
            if (array_key_exists($fieldName, $cellItalicAttr))
            {
                if ($cellItalicAttr[$fieldName])
                    $currentFieldStyle .= 'font-style: italic;';
                else
                    $currentFieldStyle .= 'font-style: normal;';
            }
            if (array_key_exists($fieldName, $cellBoldAttr))
            {
                if ($cellBoldAttr[$fieldName])
                    $currentFieldStyle .= 'font-weight: bold;';
                else
                    $currentFieldStyle .= 'font-weight: normal;';
            }
            $result[$fieldName] = $currentFieldStyle;
        }

        return $result;
    }

    function RenderGrid($Grid)
    {
        $Rows = array();
        $RowPrimaryKeys = array();
        $AfterRows = array();
        $rowCssStyles = array();
        $rowColumnsChars = array();
        $rowColumnsCssStyles = array();
        $headColumnsStyles = array();
        $columnsNames = array();

        foreach($Grid->GetViewColumns() as $Column)
        {
            $headColumnsStyle = '';
            if ($Grid->GetVerticalLineBeforeWidth($Column) != null)
                $headColumnsStyle .= 'border-right: ' . $Grid->GetVerticalLineBeforeWidth($Column) . ' #000000;';
            if ($Column->GetFixedWidth() != null)
                $headColumnsStyle .= sprintf('width: %dpx;', $Column->GetFixedWidth());
            $headColumnsStyles[] = $headColumnsStyle;
            $columnsNames[] = $Column->GetName();
        }
        $Grid->GetDataset()->Open();
        $recordCount = 0;
        while($Grid->GetDataset()->Next())
        {
            $show = true;
            $Grid->BeforeShowRecord->Fire(array(&$show));
            if (!$show)
                continue;

            $Row = array();
            $AfterRowControls = '';

            $rowValues = $Grid->GetDataset()->GetFieldValues();
            $rowCssStyle = '';
            $cellCssStyles = array();

            $Grid->OnCustomDrawCell->Fire(array($rowValues, &$cellCssStyles, &$rowCssStyle));
            $cellCssStyles_Simple = $this->GetStylesForColumn($Grid, $rowValues);
            $cellCssStyles = array_merge($cellCssStyles_Simple, $cellCssStyles);

            $currentRowColumnsCssStyles = array();

            $columnsChars = array();
            foreach($Grid->GetViewColumns() as $Column)
            {
                $columnName = $Grid->GetDataset()->IsLookupField($Column->GetName()) ?
                    $Grid->GetDataset()->IsLookupFieldNameByDisplayFieldName($Column->GetName()) :
                    $Column->GetName();

                if (array_key_exists($columnName, $cellCssStyles))
                    $currentRowColumnsCssStyles[] = $rowCssStyle . ';' .$cellCssStyles[$columnName];
                else
                    $currentRowColumnsCssStyles[] = $rowCssStyle;

                if ($Column->GetFixedWidth() != null)
                    $currentRowColumnsCssStyles[count($currentRowColumnsCssStyles) - 1] .=  ';' . sprintf('width: %dpx;', $Column->GetFixedWidth());

                if ($Grid->GetVerticalLineBeforeWidth($Column) != null)
                    $currentRowColumnsCssStyles[count($currentRowColumnsCssStyles) - 1] .= ';' . 'border-right: ' . $Grid->GetVerticalLineBeforeWidth($Column) . ' #000000;';

                $columnRenderResult = '';
                $customRenderColumnHandled = false;
                $Grid->OnCustomRenderColumn->Fire(array($columnName, $Column->GetData(), $rowValues, &$columnRenderResult, &$customRenderColumnHandled));
                $columnRenderResult = $customRenderColumnHandled ? $Grid->GetPage()->RenderText($columnRenderResult) : $this->Render($Column);
                $Row[] = $columnRenderResult;
                $columnsChars[] = ($Column->IsDataColumn() ? 'data' : 'misc');

                $afterRow = $Column->GetAfterRowControl();
                if (isset($afterRow))
                    $AfterRowControls .= $this->Render($afterRow);
            }
            $recordCount++;
            if ($Grid->GetAllowDeleteSelected())
                $RowPrimaryKeys[] = $Grid->GetDataset()->GetPrimaryKeyValues();
            $Rows[] = $Row;
            $AfterRows[] = $AfterRowControls;
            $rowCssStyles[] = $rowCssStyle;
            $rowColumnsCssStyles[] = $currentRowColumnsCssStyles;
            $rowColumnsChars[] = $columnsChars;
        }

        $this->DisplayTemplate('list/grid.tpl',
            array(
            'Grid' => $Grid
            ),
            array(
            'SearchControl' => isset($Grid->SearchControl) ? $this->Render($Grid->SearchControl) : '',
            'Columns' => $Grid->GetViewColumns(),
            'AfterRows' => $AfterRows,
            'ColumnCount' => count($Grid->GetViewColumns()) + ($Grid->GetAllowDeleteSelected() ? 1 : 0),
            'Rows' => $Rows,
            'UseFilter' => $Grid->GetPage()->GetSimpleSearchAvailable() && $Grid->UseFilter,
            'RowCssStyles' => $rowCssStyles,
            'RowColumnsCssStyles' => $rowColumnsCssStyles,
            'HeadColumnsStyles' => $headColumnsStyles,
            'RowPrimaryKeys' => $RowPrimaryKeys,
            'AllowDeleteSelected' => $Grid->GetAllowDeleteSelected(),
            'RecordCount' => $recordCount,
            'RowColumnsChars' => $rowColumnsChars,
            'ColumnsNames' => $columnsNames
        ));
    }

    // Column rendering
    function ShowHtmlNullValue()
    { return true; }
}

class ErrorStateRenderer extends ViewAllRenderer
{
    private $exception;

    public function  __construct($captions, $exception)
    {
        parent::__construct($captions);
        $this->exception = $exception;
    }

    function RenderPage($Page)
    {
        $this->SetHTTPContentTypeByPage($Page);

        $PageList = $Page->GetPageList();
        $PageList = isset($PageList) ? $this->Render($PageList) : '';

        $this->DisplayTemplate('list/error_page.tpl',
            array('Page' => $Page),
            array(
                'PageList' => $PageList,
                'ErrorMessage' => $this->exception->getMessage(),
                )
        );
    }
}


?>