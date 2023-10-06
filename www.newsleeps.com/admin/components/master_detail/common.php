<?php

class DetailRelation
{
    public $MasterKeyFields;
    public $DetailPage;
}

function AddDetailRelation($page, $grid, $detailRelation, $separatePageHandlerName)
{ 
	$result = $grid->AddViewColumn(new DetailColumn($detailRelation->MasterKeyFields, $separatePageHandlerName));
				
	GetApplication()->RegisterHTTPHandler(new 
	    MasterDetailHTTPHandler(
            $detailRelation->DetailPage,  
	        'detail_handler' 
	        ));

    return $result;
} 
 
class PageHttpHandler extends HTTPHandler
{
    private $page;
    
    public function __construct($name, $page)
    {
        parent::__construct($name);
        $this->page = $page;
    }
        
    public function Render($renderer)
    {
        $this->page->BeginRender();
        $this->page->EndRender();
    }
    
}
 
?>