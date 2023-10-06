<?php

require_once 'renderer.php';

class CsvRenderer extends Renderer
{
    function RenderPageNavigator($PageNavigator)
    { }

    function RenderDetailPageEdit($DetailPage)
    {
        $this->RenderPage($DetailPage);
    }
        
    function RenderPage($Page)
    {
        if ($Page->GetContentEncoding() != null)
            header('Content-type: application/csv; charset=' . $Page->GetContentEncoding());
        else
        header("Content-type: application/csv");
    	
    	header("Content-Disposition: attachment;Filename=" .
            $this->PrepareStringForDownloadFileName($Page->GetCaption() . ".csv"));
                             
        $Grid = $this->Render($Page->GetGrid());
        $this->DisplayTemplate('export/csv_page.tpl',
            array('Page' => $Page),
            array('Grid' => $Grid));
    }
    
    function RenderCustomViewColumn($column)
    {
        $this->result = $column->GetValue();
    }
        
    function RenderGrid($Grid)
    {
        $Rows = array();
        $HeaderCaptions = array();
        $Grid->GetDataset()->Open();
        foreach($Grid->GetExportColumns() as $Column)
            $HeaderCaptions[] = $Column->GetCaption();
        while($Grid->GetDataset()->Next())
        {
            $Row = array();
            foreach($Grid->GetExportColumns() as $Column)
                $Row[] = htmlspecialchars($this->Render($Column));
            $Rows[] = $Row;
        }
            	
        $this->DisplayTemplate('export/csv_grid.tpl',
            array(
                'Grid' => $Grid
                ),
            array(
                'HeaderCaptions' => $HeaderCaptions,
                'Rows' => $Rows
            ));
    }
}
?>