<?php

    class MasterDetailHTTPHandler extends HTTPHandler
    {
        private $page; 
				 
		public function __construct($page, $name)
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