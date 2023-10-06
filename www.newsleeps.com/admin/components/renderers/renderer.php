<?php

require_once 'components/grid/grid.php';

abstract class Renderer
{
    protected $result;
    private $captions;

    function RenderPageNavigator($Page) { }
    function RenderLookupColumn($LookupColumn) { }
    function RenderIntegerColumn($Column) { }
    abstract function RenderPage($Page);
	function RenderGridColumn($GridColumn) {}
	abstract function RenderGrid($Grid);


    protected function SetHTTPContentTypeByPage($page)
    {
        $headerString = 'Content-Type: text/html';
        if ($page->GetContentEncoding() != null)
            AddStr($headerString, 'charset=' . $page->GetContentEncoding(), ';');
        header($headerString);
    }

	protected  function Captions() { return $this->captions; }
	
	public function __construct($captions)
	{
	   $this->captions = $captions;
	}

    function CreateSmatryObject()
    {
        $result = new Smarty();
        $result->template_dir = '/components/templates';
        return $result;
    }

    function PrepareStringForDownloadFileName($string)
    {
        $illegal_charaters = array('\\', '/', ':', '*', '?', '<', '>', '|', '"', '#', ' ');
        $result = $string;
        foreach($illegal_charaters as $charater)
            $result = str_replace($charater, '_', $result);
        return $result;
    }
    
    function DisplayTemplate($TemplateName, $InputObjects, $InputValues)
    {
        $smarty = $this->CreateSmatryObject();
        foreach($InputObjects as $ObjectName => &$Object)
            $smarty->assign_by_ref($ObjectName, $Object);
        $smarty->assign_by_ref('Renderer', $this);
        $smarty->assign_by_ref('Captions', $this->captions);

        foreach($InputValues as $ValueName => $Value)
            $smarty->assign($ValueName, $Value);

	    $this->result = $smarty->fetch($TemplateName);
    }

    function Render($Object)
    {
        $Object->Accept($this);
        return $this->result;
    }

    function RenderCustomErrorPage($errorPage)
    {
        $this->DisplayTemplate('security_error_page.tpl',
            array(
                'Page' => $errorPage),
            array(
                'Message' => $errorPage->GetMessage(),
                'Description' => $errorPage->GetDescription()
                ));
    }

    function RenderTextBlobViewer($textBlobViewer)
    {
        $this->DisplayTemplate('text_blob_viewer.tpl',
            array(
                'Viewer' => $textBlobViewer,
                'Page' => $textBlobViewer->GetParentPage()),
            array());
    }

    function RenderCustomViewColumn($column)
    {
        $this->result = $column->GetValue();
    }
    
    function RenderComponent($Component)
    {
        $this->result = '';
    }

    function RenderCheckBox($checkBox)
    {
        $this->DisplayTemplate('check_box.tpl',
            array('CheckBox' => $checkBox),
            array());
    }

    function RenderSpinEdit($SpinEdit)
    {
        $this->DisplayTemplate('spin_edit.tpl',
            array('SpinEdit' => $SpinEdit),
            array());
    }

    function RenderComboBox($ComboBox)
    {
        $this->DisplayTemplate('combo_box.tpl',
            array('ComboBox' => $ComboBox),
            array());
    }

    function RenderTextAreaEdit($textArea)
    {
        $this->DisplayTemplate('textarea.tpl',
            array('TextArea' => &$textArea),
            array());
    }

    function RenderTextEdit($textEdit)
    {
        $this->DisplayTemplate('text_edit.tpl',
            array('TextEdit' => &$textEdit),
            array());
    }

    function RenderRadioEdit($radioEdit)
    {
        $this->DisplayTemplate('radio_edit.tpl',
            array('RadioEdit' => &$radioEdit),
            array());
    }

    function RenderCheckBoxGroup($checkBoxGroup)
    {
        $this->DisplayTemplate('check_box_group.tpl',
            array('CheckBoxGroup' => &$checkBoxGroup),
            array());
    }


    function RenderImage($Image)
    {
        $this->DisplayTemplate('image.tpl',
            array('Image' => $Image),
            array());
    }

    function RenderTextBox($textBox)
    {
        $this->result = $textBox->GetCaption();
    }
    
    function RenderCustomHtmlControl($control)
    {
        $this->result = $control->GetHtml();
    }

    function RenderDetailColumn($detailColumn)
    {
        $this->result =
            '<a class="page_link" onclick="expand(' . $detailColumn->GetDataset()->GetCurrentRowIndex() .
             ' , this);" href="' . $detailColumn->GetLink() . '">+</a>&nbsp;' .
            '<a class="page_link" href="' . $detailColumn->GetSeparateViewLink() . '">view</a>';
    }

    function RenderDetailPage($DetailPage)
    {
        $this->SetHTTPContentTypeByPage($DetailPage);
    
        $Grid = $this->Render($DetailPage->GetGrid());
        $this->DisplayTemplate('list/detail_page.tpl',
            array(
              'Page' => $DetailPage,
              'DetailPage' => $DetailPage),
            array(
                'Grid' => $Grid,
            ));
    }

    function RenderDetailPageEdit($DetailPage)
    {
        $this->SetHTTPContentTypeByPage($DetailPage);
    
        $Grid = $this->Render($DetailPage->GetGrid());
        $PageNavigator = $DetailPage->GetPageNavigator();
        if ($DetailPage->GetPageList() != null)
            $pageList = $this->Render($DetailPage->GetPageList());
        else
            $pageList = null;
        if (isset($PageNavigator))
            $PageNavigator = $this->Render($DetailPage->GetPageNavigator());
        else
            $PageNavigator = '';

        $isAdvancedSearchActive = false;
        if (isset($DetailPage->AdvancedSearchControl))
            $isAdvancedSearchActive = $DetailPage->AdvancedSearchControl->IsActive();
                    
        $this->DisplayTemplate('list/detail_page_edit.tpl',
            array(
                'Page' => $DetailPage,
                'DetailPage' => $DetailPage,
                'PageList' => $pageList),
            array(
                'Grid' => $Grid,
                'AdvancedSearch' => isset($DetailPage->AdvancedSearchControl) ? $this->Render($DetailPage->AdvancedSearchControl) : '',
                'IsAdvancedSearchActive' => $isAdvancedSearchActive,
                'FriendlyAdvancedSearchCondition' => $DetailPage->AdvancedSearchControl->GetUserFriendlySearchConditions(),
                'PageNavigator' => $PageNavigator,
                'MasterGrid' => $this->Render($DetailPage->GetMasterGrid())
            ));
    }

    function RenderDateTimeColumn($column)
    {
        $this->RenderGridColumn($column);
    }

    function RenderSimpleSearch($searchControl)
    {
        $this->DisplayTemplate('search_control.tpl',
            array('SearchControl' => $searchControl),
            array());
    }

    function RenderAdvancedSearchControl($advancedSearchControl)
    {
        
        $this->DisplayTemplate('advanced_search_control.tpl',
            array(
                'AdvancedSearchControl' => $advancedSearchControl
            ),
            array(
                'TextsForHighlight' => $advancedSearchControl->GetHighlightedFieldText(),
                'HighlightOptions' => $advancedSearchControl->GetHighlightedFieldOptions(),
                'EditorControl' => '' //$this->Render($Column->GetEditorControl())
            ));
    }

    function RenderPageList($pageList)
    {
        $this->DisplayTemplate('page_list.tpl',
            array('PageList' => $pageList),
            array());
    }

    function RenderDateTimeEdit($dateTimeEdit)
    {
        $this->DisplayTemplate('datetime_edit.tpl',
            array('DateTimeEdit' => $dateTimeEdit),
            array());
    }

    function RenderLoginControl($loginControl)
    {
        $this->DisplayTemplate('login_control.tpl',
            array('LoginControl' => $loginControl),
            array());
    }

    function RenderLoginPage($loginPage)
    {
        $this->SetHTTPContentTypeByPage($loginPage);
        
        $this->DisplayTemplate('login_page.tpl',
            array(
                'Page' => $loginPage,
                'LoginControl' => $loginPage->GetLoginControl()),
            array());
    }
    
    function RenderHyperLink($hyperLink)
    {
        $this->result = sprintf('<a href="%s">%s</a>%s', $hyperLink->GetLink(), $hyperLink->GetInnerText(), $hyperLink->GetAfterLinkText());    
    }
        
    // Column rendering
    function ShowHtmlNullValue() { return false; }
    function HttpHandlersAvailable() { return true; }
    function HtmlMarkupAvailable() { return true; }
    function ChildPagesAvailable() { return true; }
    
    
    function RenderCustomDatasetFieldViewColumn($column)
    {
        $value = $column->GetValue();
        if (!isset($value))
        {
            if ($this->ShowHtmlNullValue())
                $this->result = '<i><font color="#AAAAAA">NULL</font></i>';
            else
                $this->result = '';
        }
        else
        {
            $this->result = $value;
        }
    }
    
    function GetNullValuePresentation($column)
    {
        if ($this->ShowHtmlNullValue())
            return '<i><font color="#AAAAAA">NULL</font></i>';
        else
            return '';        
    }
    
    function RenderTextViewColumn($column)
    {
        $value = $column->GetValue(); 
        $dataset = $column->GetDataset();
        $column->BeforeColumnRender->Fire(array(&$value, &$dataset));
        
        if (!isset($value))
        {
            $this->result = $this->GetNullValuePresentation($column);
        }
        else
        {
            if ($column->GetEscapeHTMLSpecialChars())
            	$value = htmlspecialchars($value);
            	
            $columnMaxLength = $column->GetMaxLength();
            if ($this->HttpHandlersAvailable() && $this->ChildPagesAvailable() && isset($columnMaxLength) && isset($value) && strlen($value) > $columnMaxLength)
            {
                $originalValue = $value;
                if ($this->HtmlMarkupAvailable() && $column->GetReplaceLFByBR())
                    $originalValue = str_replace("\n", "<br/>", $originalValue);
                
                $value = substr($value, 0, $columnMaxLength) . '...';
                $value .= '<span class="more_hint"><a href="'.$column->GetMoreLink().'" '.
                    'onClick="javascript: pwin = window.open(\'\',null,\'height=300,width=400,status=yes,resizable=yes,toolbar=no,menubar=no,location=no,left=150,top=200,scrollbars=yes\'); pwin.location=\''.$column->GetMoreLink().'\'; return false;">'. $this->captions->GetMessageString('more') .'</a>';
                $value .= '<div class="box_hidden">'.$originalValue.'</div></span>';
            }
            if ($this->HtmlMarkupAvailable() && $column->GetReplaceLFByBR())
                $value = str_replace("\n", "<br/>", $value);
                
            $this->result = $value;    
        }   
    }
    
    function RenderDivTagViewColumnDecorator($column)
    {
        $styles = '';
        if (isset($column->Bold))
            AddStr($styles, 'font-weight: ' . ($column->Bold ? 'bold' : 'normal'), '; ');
        if (isset($column->Italic))
            AddStr($styles, 'font-style: ' . ($column->Italic ? 'italic' : 'normal'), '; ');

        $this->result = '<div '. ($styles != '' ? ('style="' . $styles. '"') : '') .
            (isset($column->Align) ? ' align="' . $column->Align . '" ' : '') .
            (isset($column->CustomAttributes) ? $column->CustomAttributes . ' ' : '') . '>'. $this->Render($column->GetInnerField()) . '</div>';
    }
    
    function RenderCheckBoxViewColumn($column)
    {
        $value = $column->GetInnerField()->GetData();
        if (!isset($value))
            $this->result = $this->Render($column->GetInnerField());
        else if (empty($value))
        {
            if ($this->HtmlMarkupAvailable())
                $this->result = $column->GetFalseValue();
            else 
                $this->result = 'false';
        }
        else
        {
            if ($this->HtmlMarkupAvailable())
                $this->result = $column->GetTrueValue();
            else 
                $this->result = 'true';
            
        }
    }
    
    function RenderImageViewColumn($column)
    {
        if ($column->GetData() == null)
        {
            $this->result = $this->GetNullValuePresentation($column);
        }
        else
        {
            if ($this->HtmlMarkupAvailable() && $this->HttpHandlersAvailable())
            {
                if($column->GetEnablePictureZoom())
                    $this->result = sprintf(
                        '<a class="image" href="%s" rel="zoom" title="%s"><img src="%s" alt="%s"></a>',
                            $column->GetFullImageLink(),
                            $column->GetImageHint(),
                            $column->GetImageLink(),
                            $column->GetImageHint());
                else
                    $this->result = sprintf(
                        '<img src="%s" alt="%s">',
                            $column->GetImageLink(),
                            $column->GetImageHint());
            }
            else 
            {
                $this->result = $this->Captions()->GetMessageString('BinaryDataCanNotBeExportedToXls');   
            }
        }        
    }
    
    function RenderDownloadDataColumn($column)
    {
        if ($column->GetData() == null)
        {
            $this->result = $this->GetNullValuePresentation($column);
        }
        else    
        {
            if ($this->HtmlMarkupAvailable() && $this->HttpHandlersAvailable())
                $this->result = '<a class="image" title="download" href="' . $column->GetDownloadLink() . '">' . $column->GetLinkInnerHtml() . '</a>';
            else
                $this->result = $this->Captions()->GetMessageString('BinaryDataCanNotBeExportedToXls');
        }
    }
}
?>