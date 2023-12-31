<?php

require_once 'renderer.php';

include('libs/pdf/fpdf.php');

class PDF extends FPDF
{
    var $col=0;
    var $y0;
    var $maxheight;
    private $columnWidth;
    private $leftmargin;
    private $columnNames;
    
    function PDF($columnWidth, $leftmargin, $columnNames, $orientation='P',$unit='mm',$format='A4')
    {
        parent::FPDF($orientation, $unit, $format);
        $this->columnWidth = $columnWidth;
        $this->leftmargin = $leftmargin;
        $this->columnNames = $columnNames;
    }
    
    function AcceptPageBreak()
    {
        if($this->y0 + $this->rowheight > $this->PageBreakTrigger)
            return true;
            
        $x = $this->leftmargin;
        if($this->maxheight < $this->PageBreakTrigger - $this->y0)
            $this->maxheight = $this->PageBreakTrigger - $this->y0;
        
        for($i = 0; $i < count($this->columnNames); $i++) 
        {
            $this->Rect($x, $this->y0, $this->columnWidth, $this->maxheight);
            $x += $this->columnWidth;
        }
                
        $this->maxheight = $this->rowheight;                
        return true;
    }

    function Header()
    {                
        $this->SetFillColor(192);
        $this->SetX($this->leftmargin);                
        foreach($this->columnNames as $name) 
            $this->Cell($this->columnWidth, $this->rowheight, $name, 1, 0, 'C', 1);
        $this->Ln($this->rowheight);
        $this->y0 = $this->GetY();
    }

}

class PdfRenderer extends Renderer
{
    function RenderPageNavigator($PageNavigator)
    { }

    function RenderPage($Page)
    {
        header("Content-type: application/pdf");
    	header("Content-Disposition: attachment;Filename=" .
            $this->PrepareStringForDownloadFileName($Page->GetCaption() . ".pdf"));
    	$this->Render($Page->GetGrid());
    }
    
    function RenderCustomViewColumn($column)
    {
        $this->result = $column->GetValue();
    }
        
    function RenderGrid($Grid)
    {        
        $leftmargin = 5;
        $pagewidth = 200;
        $pageheight = 290;
        $rowHeight = 5;
        if (count($Grid->GetExportColumns()) > 0)
            $columnWidth = $pagewidth / count($Grid->GetExportColumns());
        else
            $columnWidth = $pagewidth;
            
        $columnNames = array();        
        foreach($Grid->GetExportColumns() as $Column)
            $columnNames[] = $Column->GetCaption();

        $pdf = new PDF($columnWidth, $leftmargin, $columnNames);
        
        $pdf->AddFont('CourierNewPSMT', '', 'courcp1251.php');
        $pdf->rowheight = $rowHeight;
        
        $pdf->SetFont('CourierNewPSMT', '', 8);
        $pdf->AddPage();
        
        $Grid->GetDataset()->Open();
        while($Grid->GetDataset()->Next())
        {
            $pdf->maxheight = $rowHeight;
            $x = $leftmargin;
            foreach($Grid->GetExportColumns() as $Column)
            {
                $pdf->SetY($pdf->y0);
                $pdf->SetX($x);
                $pdf->MultiCell($columnWidth, $rowHeight, $this->Render($Column)); 
                $x += $columnWidth;
                if($pdf->GetY() - $pdf->y0 > $pdf->maxheight)
                    $pdf->maxheight = $pdf->GetY() - $pdf->y0;
            }
            
            $x = $leftmargin;
            foreach($Grid->GetExportColumns() as $Column)
            {
                $pdf->Rect($x, $pdf->y0, $columnWidth, $pdf->maxheight);
                $x += $columnWidth;
            }
            
            $pdf->y0+=$pdf->maxheight;
        }
        $pdf->Output();
    }
}
?>