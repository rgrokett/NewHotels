<?php

function FormatDatasetFieldsTemplate($dataset, $template)
{
    $result = $template;
    foreach($dataset->GetFields() as $field)
    {
        $result = str_ireplace(
            '%' . $field->GetName() . '%',
            $dataset->GetFieldValueByName($field->GetNameInDataset()),
            $result);
    }
    return $result;
}

function GetOrderTypeCaption($orderType)
{
    global $orderTypeCaptions;
    return $orderTypeCaptions[$orderType];
}

abstract class CustomViewColumn
{
    private $caption;
    protected $grid;
    private $fixedWidth;
    public $headerControl;

    public function __construct($caption)
    {
        $this->caption = $caption;
        $this->fixedWidth = null;
        $this->verticalLine = null;
    }

    private $verticalLine;
    public function GetVerticalLine()
    {
        return $this->verticalLine;
    }
    public function SetVerticalLine($value)
    {
        $this->verticalLine = $value;
    }
    
    protected function CreateHeaderControl()
    {
        return new TextBox('HeaderControl', $this->caption);     
    }

    public function GetName() { }

    public function GetCaption() { return $this->caption; }

    public function SetGrid($value) 
    { 
        $this->grid = $value; 
        $this->caption = $this->grid->GetPage()->RenderText($this->caption);
    }
    public function GetGrid() { return $this->grid; }

    abstract public function GetValue();

    public function Accept($renderer)
    {
        $renderer->RenderCustomViewColumn($this);
    }
    
    public function ProcessMessages()
    { }

    public function GetHeaderControl() 
    { 
        if (!isset($this->headerControl))
            $this->headerControl = $this->CreateHeaderControl();
        return $this->headerControl; 
    }

    public function GetAfterRowControl()
    {
        return new NullComponent('');
    }

    public function GetData() { return null; }
    
    public function SetFixedWidth($value) { $this->fixedWidth = $value; }
    public function GetFixedWidth() { return $this->fixedWidth; }
    
    public function IsDataColumn() { return false; }
}

abstract class CustomDatasetFieldViewColumn extends CustomViewColumn
{
    private $fieldName;
    private $dataset;
    private $orderable;    
    //
    public $BeforeColumnRender;
    
    public function __construct($fieldName, $caption, $dataset, $orderable = true)
    {
        parent::__construct($caption);
        $this->fieldName = $fieldName;
        $this->dataset = $dataset;
        $this->orderable = $orderable;        
        $this->BeforeColumnRender = new Event();
    }
 
    public function SetOrderable($value) { $this->orderable = $value; }
    public function GetOrderable() { return $this->orderable; }
 
    protected function CreateHeaderControl()
    {
        if ($this->orderable)
            return new HyperLink('HeaderControl', $this->GetCaption());
        else
            return parent::CreateHeaderControl();
    }

    public function GetName() { return $this->fieldName; }
    public function GetDataset() { return $this->dataset; }
    public function GetData() { return $this->GetDataset()->GetFieldValueByName($this->GetName()); }

    private function GetOrderByLink($currentOrderType = null)
    {
        $linkBuilder = $this->GetGrid()->CreateLinkBuilder();
        
        switch($currentOrderType)
        {
            case otAscending:
                $linkBuilder->AddParameter('order', GetOrderTypeCaption(otDescending) . $this->fieldName);
                break;
            case otDescending:
                $linkBuilder->AddParameter(OPERATION_PARAMNAME, 'resetorder');
                break;
            case null:
                $linkBuilder->AddParameter('order', GetOrderTypeCaption(otAscending) . $this->fieldName);
                break;                                
        }
        
        return $linkBuilder->GetLink();        
    }
    
    private function GetSortCaption($currentOrderType = null)
    {
        switch($currentOrderType)
        {
            case otAscending:
                return ' <img style="border: 0" src="images/sortasc.gif">';
                break;
            case otDescending:
                return ' <img style="border: 0" src="images/sortdesc.gif">';
                break;
            case null:
                return '';
                break;
        }
    }
        
    protected abstract function DoGetValue();
    
    public function GetValue()
    {
        $result = $this->GetData();
        return isset($result) ? $this->DoGetValue() :  null;
    }
    
    public function Accept($renderer)
    {
        $renderer->RenderCustomDatasetFieldViewColumn($this);
    }

    public function ProcessMessages()
    {
        if ($this->orderable)
        {
            $orderColumn = $this->GetGrid()->GetOrderColumnFieldName();
            if ($orderColumn == $this->fieldName)
            {
                $this->GetHeaderControl()->SetAfterLinkText($this->GetSortCaption($this->GetGrid()->GetOrderType()));
                $this->GetHeaderControl()->SetLink($this->GetOrderByLink($this->GetGrid()->GetOrderType()));
            }
            else
                $this->GetHeaderControl()->SetLink($this->GetOrderByLink());
        }
    }
    
    public function IsDataColumn() { return true; }
}

class DateTimeViewColumn extends CustomDatasetFieldViewColumn
{
    private $dateTimeFormat;
 
    public function __construct($fieldName, $caption, $dataset, $orderable = true)
    {
        parent::__construct($fieldName, $caption, $dataset, $orderable);
        $this->dateTimeFormat = 'Y-m-d';
    } 
    
    public function SetDateTimeFormat($value) { $this->dateTimeFormat = $value; }
    public function GetDateTimeFormat() { return $this->dateTimeFormat; }
    
    protected function DoGetValue()
    {
        $value = $this->GetDataset()->GetFieldValueByNameAsDateTime($this->GetName());
        
        $stringValue = isset($value) ? $value->ToString($this->dateTimeFormat) : null;
        $dataset = $this->GetDataset();
        $this->BeforeColumnRender->Fire(array(&$stringValue, &$dataset));
                
        return isset($stringValue) ? $stringValue : 'NULL';
    }
}

class TextViewColumn extends CustomViewColumn
{
    private $fieldName;
    private $dataset;
    private $orderable;    
    private $maxLength;
    private $replaceLFByBR;
    private $escapeHTMLSpecialChars;
    //
    public $BeforeColumnRender;
    
    public function __construct($fieldName, $caption, $dataset, $orderable = true)
    {
        parent::__construct($caption);
        $this->fieldName = $fieldName;
        $this->dataset = $dataset;
        $this->orderable = $orderable;        
        $this->maxLength = null;
        $this->replaceLFByBR = false;
        $this->BeforeColumnRender = new Event();
        $this->escapeHTMLSpecialChars = false;
    }

    public function GetName() { return $this->fieldName; }
    public function GetDataset() { return $this->dataset; }
    public function GetData() { return $this->GetDataset()->GetFieldValueByName($this->fieldName); }

    public function GetMoreLink()
    {
        $result = $this->GetGrid()->CreateLinkBuilder();
        if ($this->GetFullTextWindowHandlerName() != null)
            $result->AddParameter('hname', $this->GetFullTextWindowHandlerName());
        else
        $result->AddParameter('hname', $this->fieldName . '_handler');
            
        AddPrimaryKeyParameters($result, $this->GetDataset()->GetPrimaryKeyValues());
        return $result->GetLink();
    }

    public function GetValue() { return $this->GetData(); }    
        
    public function Accept($renderer)
    {
        $renderer->RenderTextViewColumn($this);
    }
    
    public function IsNull()
    {
        $value = $this->GetData(); 
        return !isset($value);         
    }
    
    public function SetMaxLength($value) { $this->maxLength = $value; }
    public function GetMaxLength() { return $this->maxLength; }    
    
    public function SetReplaceLFByBR($value) { $this->replaceLFByBR = $value; }
    public function GetReplaceLFByBR() { return $this->replaceLFByBR; }    
    
    public function SetEscapeHTMLSpecialChars($value) { $this->escapeHTMLSpecialChars = $value; }
    public function GetEscapeHTMLSpecialChars() { return $this->escapeHTMLSpecialChars; }    
    
    
    public function SetOrderable($value) { $this->orderable = $value; }
    public function GetOrderable() { return $this->orderable; }    
    
    private function GetOrderByLink($currentOrderType = null)
    {
        $linkBuilder = $this->GetGrid()->CreateLinkBuilder();
        
        switch($currentOrderType)
        {
            case otAscending:
                $linkBuilder->AddParameter('order', GetOrderTypeCaption(otDescending) . $this->fieldName);
                break;
            case otDescending:
                $linkBuilder->AddParameter(OPERATION_PARAMNAME, 'resetorder');
                break;
            case null:
                $linkBuilder->AddParameter('order', GetOrderTypeCaption(otAscending) . $this->fieldName);
                break;                                
        }
        
        return $linkBuilder->GetLink();        
    }
    
    private function GetSortCaption($currentOrderType = null)
    {
        switch($currentOrderType)
        {
            case otAscending:
                return ' <img style="border: 0" src="images/sortasc.gif">';
                break;
            case otDescending:
                return ' <img style="border: 0" src="images/sortdesc.gif">';
                break;
            case null:
                return '';
                break;
        }
    }
   
    protected function CreateHeaderControl()
    {
        if ($this->orderable)
            return new HyperLink('HeaderControl', $this->GetCaption());
        else
            return parent::CreateHeaderControl();
    }       
    
    public function ProcessMessages()
    {
        if ($this->orderable)
        {
            $orderColumn = $this->GetGrid()->GetOrderColumnFieldName();
            if ($orderColumn == $this->fieldName)
            {
                $this->GetHeaderControl()->SetAfterLinkText($this->GetSortCaption($this->GetGrid()->GetOrderType()));
                $this->GetHeaderControl()->SetLink($this->GetOrderByLink($this->GetGrid()->GetOrderType()));
            }
            else
            {
                $this->GetHeaderControl()->SetLink($this->GetOrderByLink());
            }
        }
    }    
    
    private $fullTextWindowHandlerName;
    
    public function SetFullTextWindowHandlerName($value) { $this->fullTextWindowHandlerName = $value; }
    public function GetFullTextWindowHandlerName() { return $this->fullTextWindowHandlerName; }
    
    public function IsDataColumn() { return true; }
}

abstract class CustomFormatValueViewColumnDecorator extends CustomViewColumn
{
    private $innerField;

    public function __construct($innerField)
    {
        parent::__construct('');
        $this->innerField = $innerField;
        $this->Bold = null;
    }

    public function GetName() { return $this->innerField->GetName(); }
    public function GetData() { return $this->innerField->GetData(); }
    
    protected function GetInnerFieldValue()
    {
        return $this->innerField->GetValue();
    }

    protected function IsNull()
    {
        return $this->innerField->IsNull();     
    }
    
    public function GetInnerField()
    {
        return $this->innerField;
    }
    
    public function GetCaption() { return $this->innerField->GetCaption(); }

    public function SetGrid($value) { $this->innerField->SetGrid($value); }

    public function GetAfterRowControl() { return $this->innerField->GetAfterRowControl(); }

    public function GetHeaderControl() { return $this->innerField->GetHeaderControl(); }
    
    public function ProcessMessages()
    { 
        $this->innerField->ProcessMessages();
    }    
    
    public function IsDataColumn() { return $this->innerField->IsDataColumn(); }
}

class CheckBoxFormatValueViewColumnDecorator extends CustomFormatValueViewColumnDecorator
{
    private $trueValue;
    private $falseValue;

    public function GetValue()
    {
        $value = $this->GetInnerField()->GetDataset()->GetFieldValueByName($this->GetName());
        if (!isset($value))
            return $this->GetInnerFieldValue();
        else if (empty($value))
            return '<input type="checkbox" onclick="return false;">';
        else
            return '<input type="checkbox" checked="checked" onclick="return false;">';
    } 
    
    public function SetDisplayValues($trueValue, $falseValue)
    {
        $this->trueValue = $trueValue;
        $this->falseValue = $falseValue;
    }
    
    public function GetTrueValue() { return $this->trueValue; }
    public function GetFalseValue() { return $this->falseValue; }
    
    public function Accept($renderer)
    {
        $renderer->RenderCheckBoxViewColumn($this);
    }        
    
    public function IsDataColumn() { return false; }
}

class NumberFormatValueViewColumnDecorator extends CustomFormatValueViewColumnDecorator
{
    private $numberAfterDecimal;
    private $thousandsSeparator;
    private $decimalSeparator;
    
    public function __construct($innerField, $numberAfterDecimal, $thousandsSeparator, $decimalSeparator)
    {
        parent::__construct($innerField);
        $this->numberAfterDecimal = $numberAfterDecimal;
        $this->thousandsSeparator = $thousandsSeparator;
        $this->decimalSeparator = $decimalSeparator;
    }
    
    protected function GetNumberAfterDecimal() { return $this->numberAfterDecimal; }
    
    public function GetValue()
    {
        if (!$this->IsNull())
        return number_format($this->GetInnerFieldValue(), $this->numberAfterDecimal, $this->decimalSeparator, $this->thousandsSeparator);
        else
            return $this->GetInnerFieldValue();
    }
}

class CurrencyFormatValueViewColumnDecorator extends NumberFormatValueViewColumnDecorator
{
    private $currencySign;

    public function __construct($innerField, $numberAfterDecimal, $thousandsSeparator, $decimalSeparator, $currencySign = '$')
    {
        parent::__construct($innerField, $numberAfterDecimal, $thousandsSeparator, $decimalSeparator);
        $this->currencySign = $currencySign;
    }

    public function GetValue()
    {
        if (!$this->IsNull())
        return $this->currencySign . parent::GetValue();
        else
            return $this->GetInnerFieldValue();
    }
}

class StringFormatValueViewColumnDecorator extends CustomFormatValueViewColumnDecorator
{
    private $stringTransaformFunction;
    
    private function TransformString($string)
    {
        if (function_exists($this->stringTransaformFunction)) 
            return call_user_func($this->stringTransaformFunction, $string);
        else
            return $string;
    }
 
    public function __construct($innerField, $stringTransaformFunction)
    {
        parent::__construct($innerField);
        $this->stringTransaformFunction = $stringTransaformFunction;   
    }
 
    public function GetValue()
    {
        return $this->TransformString($this->GetInnerFieldValue());
    }     
}

class PercentFormatValueViewColumnDecorator extends NumberFormatValueViewColumnDecorator
{
    public function GetValue()
    {
        return parent::GetValue() . '%';
    }
}   

class DivTagViewColumnDecorator extends CustomViewColumn
{
    private $innerField;
    public $Bold;
    public $Italic;
    public $CustomAttributes;
    public $Align;

    public function __construct($innerField)
    {
        $this->Bold = null;
        $this->Italic = null;
        $this->CustomAttributes = null;
        $this->innerField = $innerField;
    }

    public function GetName() { return $this->innerField->GetName(); }
    public function GetData() { return $this->innerField->GetData(); }

    public function GetInnerField() { return $this->innerField; }
    
    public function GetValue()
    {
        $styles = '';
        if (isset($this->Bold))
            AddStr($styles, 'font-weight: ' . ($this->Bold ? 'bold' : 'normal'), '; ');
        if (isset($this->Italic))
            AddStr($styles, 'font-style: ' . ($this->Italic ? 'italic' : 'normal'), '; ');

        return '<div '. ($styles != '' ? ('style="' . $styles. '"') : '') .
            (isset($this->Align) ? ' align="' . $this->Align . '" ' : '') .
            (isset($this->CustomAttributes) ? $this->CustomAttributes . ' ' : '') . '>'. $this->innerField->GetValue() . '</div>';
    }

    public function GetCaption() { return $this->innerField->GetCaption(); }

    public function SetGrid($value) { $this->innerField->SetGrid($value); }

    public function GetAfterRowControl() { return $this->innerField->GetAfterRowControl(); }

    public function GetHeaderControl() { return $this->innerField->GetHeaderControl(); }
    
    public function ProcessMessages()
    { 
        $this->innerField->ProcessMessages();
    }        
    
    public function IsDataColumn() { return $this->innerField->IsDataColumn(); }
    
    public function Accept($renderer)
    {
        $renderer->RenderDivTagViewColumnDecorator($this);
    }    
}

class ExtendedHyperLinkColumnDecorator extends CustomFormatValueViewColumnDecorator
{
    private $template;
    private $target;
    private $dataset;
    public function __construct($innerField, $dataset, $template, $target = '_blank')
    {
        parent::__construct($innerField);
        $this->template = $template;
        $this->target = $target;
        $this->dataset = $dataset;
    } 
    
    private function GetLink()
    {
        return FormatDatasetFieldsTemplate($this->dataset, $this->template);
    }
    
    public function GetValue()
    {
        return sprintf('<a href="%s" target="%s">%s</a>',
            $this->GetLink(),
            $this->target,
            $this->GetInnerFieldValue());
    }    
}

class HyperLinkColumnDecorator extends CustomViewColumn
{
    private $innerField;
    private $hrefFieldName;
    private $dataset;

    public $Prefix;
    public $Suffix;
    public $Target;
            
    public function GetName() { return $this->innerField->GetName(); }
    public function GetData() { return $this->innerField->GetData(); }

    public function __construct($innerField, $hrefFieldName, $dataset)
    {
        $this->innerField = $innerField;
        $this->hrefFieldName = $hrefFieldName;
        $this->dataset = $dataset;
    }

    private function GetHrefValue()
    {
        return $this->Prefix . $this->GetDataset()->GetFieldValueByName($this->hrefFieldName) . $this->Suffix;
    }

    public function GetValue()
    {
        return '<a href="' . $this->GetHrefValue() . '"' . 
        (isset($this->Target) ? ('target = "' . $this->Target . '"') : '') . '>' . $this->innerField->GetValue() . '</a>';
    }

    public function GetDataset() { return $this->dataset; }

    public function GetCaption() { return $this->innerField->GetCaption(); }

    public function SetGrid($value) { $this->innerField->SetGrid($value); }

    public function GetAfterRowControl() { return $this->innerField->GetAfterRowControl(); }

    public function GetHeaderControl() { return $this->innerField->GetHeaderControl(); }
    
    public function ProcessMessages()
    { 
        $this->innerField->ProcessMessages();
    }    
    
    public function IsDataColumn() { return $this->innerField->IsDataColumn(); }
}

class DownloadDataColumn extends CustomViewColumn
{
    private $dataset;
    private $fieldName;
    private $linkInnerHtml;
 
    public function __construct($fieldName, $caption, $dataset, $linkInnerHtml = 'download')
    {
        parent::__construct($caption);
        $this->fieldName = $fieldName;
        $this->dataset = $dataset;
        $this->linkInnerHtml = $linkInnerHtml;
    }
    
    public function GetName() { return $this->fieldName; }
    public function GetDataset() { return $this->dataset; }
    public function GetData() { return $this->GetDataset()->GetFieldValueByName($this->fieldName); }
    public function GetValue() { return $this->GetData(); }
    public function GetLinkInnerHtml() { return $this->linkInnerHtml; }
    
    public function GetDownloadLink()
    {
        $result = $this->GetGrid()->CreateLinkBuilder();
        $result->AddParameter('hname', $this->fieldName . '_handler');
        AddPrimaryKeyParameters($result, $this->GetDataset()->GetPrimaryKeyValues());
        return $result->GetLink();
    }

    public function Accept($renderer)
    {
        $renderer->RenderDownloadDataColumn($this);
    }            
    
    public function IsDataColumn() { return false; }
}

class DownloadExternalDataColumn extends  CustomViewColumn
{
    private $fieldName;
    private $dataset;
    private $downloadTextTemplate;
    private $downloadLinkHintTemplate;

    private $sourcePrefix;
    private $sourceSuffix;
    

    public function __construct($fieldName, $caption, $dataset, $downloadTextTemplate, $downloadLinkHintTemplate = '')
    {
        parent::__construct($caption);
        $this->fieldName = $fieldName;
        $this->dataset = $dataset;
        $this->downloadTextTemplate = $downloadTextTemplate;
        $this->downloadLinkHintTemplate = $downloadLinkHintTemplate;
    }

    public function GetName() { return $this->fieldName; }
    public function GetDataset() { return $this->dataset; }
    public function GetData() { return $this->GetDataset()->GetFieldValueByName($this->fieldName); }

    public function SetSourcePrefix($value) { $this->sourcePrefix = $value; }
    public function GetSourcePrefix() { return $this->sourcePrefix; }
    
    public function SetSourceSuffix($value) { $this->sourceSuffix = $value; }
    public function GetSourceSuffix() { return $this->sourceSuffix; }    
    
    public function GetValue()
    {
        $fieldValue = $this->GetDataset()->GetFieldValueByName($this->fieldName);
        if ($fieldValue == null)
            return '<i><font color="#AAAAAA">NULL</font></i>';
        else
            return '<a title="'. FormatDatasetFieldsTemplate($this->dataset, $this->downloadLinkHintTemplate) .'" href="' . $this->sourcePrefix . $fieldValue . $this->sourceSuffix . '">' .
                FormatDatasetFieldsTemplate($this->dataset, $this->downloadTextTemplate) . '</a>';
    }
}

class ExternalImageColumn extends  CustomViewColumn
{
    private $fieldName;
    private $dataset;
    private $hintTemplate;
    private $sourcePrefix;
    private $sourceSuffix;

    public function __construct($fieldName, $caption, $dataset, $hintTemplate)
    {
        parent::__construct($caption);
        $this->fieldName = $fieldName;
        $this->dataset = $dataset;
        $this->hintTemplate = $hintTemplate;
        $this->sourcePrefix = '';
        $this->sourceSuffix = '';
    }

    public function SetSourcePrefix($value) { $this->sourcePrefix = $value; }
    public function GetSourcePrefix() { return $this->sourcePrefix; }
    
    public function SetSourceSuffix($value) { $this->sourceSuffix = $value; }
    public function GetSourceSuffix() { return $this->sourceSuffix; }
    
    public function GetName() { return $this->fieldName; }
    public function GetDataset() { return $this->dataset; }
    public function GetData() { return $this->GetDataset()->GetFieldValueByName($this->fieldName); }

    public function GetValue()
    {
        $fieldValue = $this->GetDataset()->GetFieldValueByName($this->fieldName);
        if ($fieldValue == null)
            return '<i><font color="#AAAAAA">NULL</font></i>';
        else
            return '<img alt="'. FormatDatasetFieldsTemplate($this->dataset, $this->hintTemplate) .
                '" src="' . $this->sourcePrefix . $fieldValue . $this->sourceSuffix . '">';
    }
}


class ImageViewColumn extends CustomViewColumn
{
    private $dataset;
    private $fieldName;
    private $imageHintTemplate;
    private $enablePictureZoom;
    private $handlerName;

    public function __construct($fieldName, $caption, $dataset, $enablePictureZoom = true, $handlerName)
    {
        parent::__construct($caption);
        $this->fieldName = $fieldName;
        $this->dataset = $dataset;
        $this->imageHintTemplate = null;
        $this->enablePictureZoom = $enablePictureZoom;
        $this->handlerName = $handlerName;
    }

    public function GetName() { return $this->fieldName; }
    public function GetDataset() { return $this->dataset; }
    public function GetData() { return $this->GetDataset()->GetFieldValueByName($this->fieldName); }
    public function GetValue() { return $this->GetData(); }

    public function GetEnablePictureZoom() { return $this->enablePictureZoom; }
    public function SetImageHintTemplate($value) { $this->imageHintTemplate = $value; }
    public function GetImageHintTemplate() { return $this->imageHintTemplate; }

    public function GetImageLink()
    {
        $result = $this->GetGrid()->CreateLinkBuilder();
        $result->AddParameter('hname', $this->handlerName);
        AddPrimaryKeyParameters($result, $this->GetDataset()->GetPrimaryKeyValues());
        return $result->GetLink();
    }

    public function GetFullImageLink()
    {
        $result = $this->GetGrid()->CreateLinkBuilder();
        $result->AddParameter('hname', $this->handlerName);
        $result->AddParameter('large', '1');
        AddPrimaryKeyParameters($result, $this->GetDataset()->GetPrimaryKeyValues());
        return $result->GetLink();
    }

    public function GetImageHint()
    {
        if (isset($this->imageHintTemplate))
            return FormatDatasetFieldsTemplate($this->dataset, $this->imageHintTemplate);
        else
            return $this->GetCaption();
    }

    public function Accept($renderer)
    {
        $renderer->RenderImageViewColumn($this);
    }                

    public function IsDataColumn() { return false; }   
}

class RowOperationByLinkColumn extends CustomViewColumn
{
    private $operationName;
    //
    public $OnShow;

    function __construct($caption, $operationName, $dataset)
    {
        parent::__construct($caption);
        $this->operationName = $operationName;
        $this->dataset = $dataset;
        //
        $this->OnShow = new Event();
    }

    public function GetName() { return $this->operationName; }
    public function GetData() { return $this->operationName; }
    
    private function GetLinkParametersForPrimaryKey()
    {
        $result = array();
        $keyValues = $this->dataset->GetPrimaryKeyValues();
        for($i = 0; $i < count($keyValues); $i++)
            $result["pk$i"] = $keyValues[$i];
        return $result;
    }

    public function SetGrid($value) 
    { 
        $this->grid = $value; 
    }    
    
    public function GetLink()
    {
        $result = $this->GetGrid()->CreateLinkBuilder();
        $result->AddParameter(OPERATION_PARAMNAME, $this->operationName);
        $result->AddParameters($this->GetLinkParametersForPrimaryKey());
        return $result->GetLink();
    }

    public function GetValue()
    {
        $showButton = true;
        $this->OnShow->Fire(array(&$showButton));
        
        if ($showButton)
        return '<a href="' . $this->GetLink() . '">' . $this->GetCaption() . '</a>';
        else
            return '';
    }
}

class DetailColumn extends CustomViewColumn
{
    private $masterKeyFields;
    private $separatePageHandlerName;
    private $inlinePageHandlerName;
    private $dataset;
    private $name;

    public function __construct($masterKeyFields, $name, $separatePageHandlerName, $inlinePageHandlerName, $dataset, $caption)
    {
        parent::__construct($caption);
        $this->masterKeyFields = $masterKeyFields;
        $this->name = $name;
        $this->separatePageHandlerName = $separatePageHandlerName;
        $this->inlinePageHandlerName = $inlinePageHandlerName;
        $this->dataset = $dataset;
    }

    public function GetName() { return ''; }
    public function GetDataset() { return $this->dataset; }
    public function GetData() { return null; }

    public function GetLink()
    {
	    $linkBuilder = $this->GetGrid()->CreateLinkBuilder();
	    $linkBuilder->AddParameter('detailrow', 'DetailContent_' . $this->name . '_' . $this->GetDataset()->GetCurrentRowIndex());
	    $linkBuilder->AddParameter('hname', $this->inlinePageHandlerName);
	    for($i = 0; $i < count($this->masterKeyFields); $i++)
	        $linkBuilder->AddParameter('fk' . $i, $this->GetDataset()->GetFieldValueByName($this->masterKeyFields[$i]));
        return $linkBuilder->GetLink();
    }

    public function GetSeparateViewLink()
    {
        $linkBuilder = $this->GetGrid()->CreateLinkBuilder();
        $linkBuilder->AddParameter('hname', $this->separatePageHandlerName);
        for($i = 0; $i < count($this->masterKeyFields); $i++)
            $linkBuilder->AddParameter('fk' . $i, $this->GetDataset()->GetFieldValueByName($this->masterKeyFields[$i]));
        return $linkBuilder->GetLink();
    }

    public function GetAfterRowControl()
    {
        return new CustomHtmlControl(
            '<iframe class="hidden"' .
            	' id="DetailFrame_' . $this->name . '_' . $this->GetDataset()->GetCurrentRowIndex() . '"' .
            	' name="DetailFrame_' . $this->name . '_' . $this->GetDataset()->GetCurrentRowIndex() . '"' .
            	' style="width:100%"></iframe>' .
            	'<div class="hidden" id="DetailContent_' . $this->name . '_' . $this->GetDataset()->GetCurrentRowIndex() . '"></div>'
            	);
    }
    
    public function GetValue()
    {
        return 
            '<a class="page_link" onclick="expand(' . 
            '\'DetailFrame_' . $this->name . '_' . $this->GetDataset()->GetCurrentRowIndex() . '\', ' .
            '\'DetailContent_' . $this->name . '_' . $this->GetDataset()->GetCurrentRowIndex() . '\', ' .           
            '\'ExpandImage_' . $this->name . '_' . $this->GetDataset()->GetCurrentRowIndex() . '\', ' .           
            'this);" href="' . $this->GetLink() . '">'.
            '<img id="ExpandImage_' . $this->name . '_' . $this->GetDataset()->GetCurrentRowIndex() . '" src="images/expand.gif" class="collapsed">' .
            '</a>&nbsp;' .
            '<a class="page_link" href="' . $this->GetSeparateViewLink() . '">' . $this->GetCaption() . '</a>';
    }
}

?>