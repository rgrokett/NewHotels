<?php

require_once 'renderer.php';

class XmlRenderer extends Renderer
{
    function RenderPageNavigator($PageNavigator)
    { }

    function RenderDetailPageEdit($DetailPage)
    {
        $this->RenderPage($DetailPage);
    }
        
    function RenderPage($Page)
    {
        header("Content-type: text/xml");
    	header("Content-Disposition: attachment;Filename=" .
            $this->PrepareStringForDownloadFileName($Page->GetCaption() . ".xml"));
                             
        $Grid = $this->Render($Page->GetGrid());
        $this->DisplayTemplate('export/xml_page.tpl',
            array('Page' => $Page),
            array('Grid' => $Grid));
    }
    
    function RenderCustomViewColumn($column)
    {
        $this->result = $column->GetValue();
    }
     
    private function PrepareColumnCaptionForXml($caption)
    {
        return htmlspecialchars(str_replace(' ', '', $caption));
    }
        
    function RenderGrid($Grid)
    {
        $Rows = array();
        $Grid->GetDataset()->Open();
        while($Grid->GetDataset()->Next())
        {
            $Row = array();
            foreach($Grid->GetExportColumns() as $Column)
                $Row[$this->PrepareColumnCaptionForXml($Column->GetCaption())] = $this->Render($Column);
            $Rows[] = $Row;
        }
            	
        $this->DisplayTemplate('export/xml_grid.tpl',
            array(
                'Grid' => $Grid
                ),
            array(
                'Rows' => $Rows,
                'TableName' => $Grid->GetPage()->GetCaption()
            ));
    }
    
    function HttpHandlersAvailable() { return false; }
    function HtmlMarkupAvailable() { return false; }
}
?>