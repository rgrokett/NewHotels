<?php
class ViewRenderer extends Renderer
{
    function RenderDetailPageEdit($page)
    {
        $this->RenderPage($page);
    }

    function RenderPage($Page)
    {
        $this->SetHTTPContentTypeByPage($Page);
        $Page->BeforePageRender->Fire(array(&$Page));

        $this->DisplayTemplate('view/page.tpl',
            array('Page' => $Page),
            array(
            'Grid' => $this->Render($Page->GetGrid())
        ));
    }

    function RenderGrid($Grid)
    {
        $primaryKeyMap = array();
        $Grid->GetDataset()->Open();

        $Row = array();
        if($Grid->GetDataset()->Next())
        {
            $primaryKeyMap = $Grid->GetDataset()->GetPrimaryKeyValuesMap();
            foreach($Grid->GetSingleRecordViewColumns() as $Column)
                $Row[] = $this->Render($Column);
        }

        $this->DisplayTemplate('view/grid.tpl',
            array(
            'Grid' => $Grid,
            'Columns' => $Grid->GetSingleRecordViewColumns()),
            array(
            'Title' => $Grid->GetPage()->GetShortCaption(),
            'PrimaryKeyMap' => $primaryKeyMap,
            'ColumnCount' => count($Grid->GetSingleRecordViewColumns()),
            'Row' => $Row,
        ));
    }
}

class DeleteRenderer extends Renderer
{
    function RenderDetailPageEdit($page)
    {
        $this->RenderPage($page);
    }

    function RenderPage($Page)
    {
        $this->DisplayTemplate('delete/page.tpl',
            array('Page' => $Page),
            array(
            'Grid' => $this->Render($Page->GetGrid())
        ));
    }

    function RenderGrid($Grid)
    {
        $primaryKeyMap = array();
        $Grid->GetDataset()->Open();

        $Row = array();
        if($Grid->GetDataset()->Next())
        {
            foreach($Grid->GetSingleRecordViewColumns() as $column)
                $Row[] = $this->Render($column);

            $hiddenValues = array(OPERATION_PARAMNAME => OPERATION_COMMIT_DELETE);
            AddPrimaryKeyParametersToArray($hiddenValues, $Grid->GetDataset()->GetPrimaryKeyValues());

            $primaryKeyMap = $Grid->GetDataset()->GetPrimaryKeyValuesMap();
        }
        
        $this->DisplayTemplate('delete/grid.tpl',
            array(
            'Grid' => $Grid,
            'Columns' => $Grid->GetSingleRecordViewColumns()),
            array(
            'Title' => $Grid->GetPage()->GetShortCaption(),
            'PrimaryKeyMap' => $primaryKeyMap,
            'ColumnCount' => count($Grid->GetSingleRecordViewColumns()),
            'Row' => $Row,
            'HiddenValues' => $hiddenValues
        ));
    }
    
    function ShowHtmlNullValue()
    { 
        return true;
    }
}
?>