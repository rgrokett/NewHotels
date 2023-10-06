<?php

require_once 'renderer.php';

class ExcelRenderer extends Renderer
{
    function RenderPageNavigator($PageNavigator)
    { }

    function RenderDetailPageEdit($DetailPage)
    {
        $this->RenderPage($DetailPage);
    }
        
    function RenderPage($page)
    {   
        if ($page->GetContentEncoding() != null)
            header('Content-type: application/vnd.ms-excel; charset=' . $page->GetContentEncoding());
        else
            header('Content-type: application/vnd.ms-excel');
    	header("Content-Disposition: attachment;Filename=" .
            $this->PrepareStringForDownloadFileName($page->GetCaption() . ".xls"));
                             
        $grid = $this->Render($page->GetGrid());
        $this->DisplayTemplate('export/excel_page.tpl',
            array('Page' => $page),
            array('Grid' => $grid));
    }
    
    function RenderCustomViewColumn($column)
    {
        $this->result = $column->GetValue();
    }
    
    function PrepareForExcel($str)
    {
    	$ret = htmlspecialchars($str);
    	if (substr($ret,0,1)== "=") 
    		$ret = "&#61;".substr($ret,1);
    	return $ret;    
    }    
    
    function RenderGrid($Grid)
    {
        $Rows = array();
        $HeaderCaptions = array();
        $Grid->GetDataset()->Open();
        foreach($Grid->GetExportColumns() as $Column)
            $HeaderCaptions[] = $this->PrepareForExcel($Column->GetCaption());
        while($Grid->GetDataset()->Next())
        {
            $Row = array();
            foreach($Grid->GetExportColumns() as $Column)
                $Row[] = $this->PrepareForExcel($this->Render($Column));
            $Rows[] = $Row;
        }
            	
        $this->DisplayTemplate('export/excel_grid.tpl',
            array(
                'Grid' => $Grid
                ),
            array(
                'HeaderCaptions' => $HeaderCaptions,
                'Rows' => $Rows
            ));
    }
    
    // Column rendering
    function HttpHandlersAvailable() { return false; }
    function HtmlMarkupAvailable() { return false; }    
}
?>