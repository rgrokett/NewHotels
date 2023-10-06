<?php

require_once 'renderer.php';

class WordRenderer extends Renderer
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
            header('Content-type: application/vnd.ms-word; charset=' . $Page->GetContentEncoding());
        else
            header("Content-type: application/vnd.ms-word");
       
    	header("Content-Disposition: attachment;Filename=" . $this->PrepareStringForDownloadFileName($Page->GetCaption() . ".doc"));
    	
        $Grid = $this->Render($Page->GetGrid());
        $this->DisplayTemplate('export/word_page.tpl',
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
                $Row[] = $this->Render($Column);
            $Rows[] = $Row;
        }
            	
        $this->DisplayTemplate('export/word_grid.tpl',
            array(
                'Grid' => $Grid
                ),
            array(
                'HeaderCaptions' => $HeaderCaptions,
                'Rows' => $Rows
            ));
    }
    
    function HttpHandlersAvailable() { return false; }
    function HtmlMarkupAvailable() { return false; }        
}
?>