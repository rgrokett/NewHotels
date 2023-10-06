<?php
    class Component
    {
        private $name;

        public function __construct($name)
        {
            $this->name = $name;
        }

        function GetName()
        {
            return $this->name;
        }

        public function ProcessMessages()
        { }

        protected function GetJSControlAdapterClass()
        {
            throw new Exception('Operation does not supported');
        }

        public function GetCreateJSControlAdapter($fieldName)
        {
            return 'new ' . $this->GetJSControlAdapterClass() . '(\'' . $this->GetName() . '\', \'' . $fieldName . '\')';
        }
    }

    class NullComponent extends Component
    {
        public function Accept($Renderer)
        {
            $Renderer->RenderComponent($this);
        }
        
        public function ExtractsValueFromPost()
        {
            return '';   
        }
    }

    class TextAreaEdit extends Component
    {
        private $value;
        private $columnCount;
        private $rowCount;
        private $customAttributes = null;
        private $allowHtmlCharacters = true;
     
        public function __construct($name, $columnCount = null, $rowCount = null, $customAttributes = null)
        {
            parent::__construct($name);
            $this->columnCount = $columnCount;
            $this->rowCount = $rowCount;
            $customAttributes = $customAttributes;
        }
        
        private $readOnly = false;
        public function GetReadOnly() { return $this->readOnly; }
        public function SetReadOnly($value) { $this->readOnly = $value; }
        
        public function GetValue() { return $this->value; }
        public function SetValue($value) { $this->value = $value; }
        
        public function SetColumnCount($value) { $this->columnCount = $value; }
        public function GetColumnCount() { return $this->columnCount; }
        
        public function SetRowCount($value) { $this->rowCount = $value; }
        public function GetRowCount() { return $this->rowCount; }

        public function SetCustomAttributes($value) { $this->customAttributes = $value; }
        public function GetCustomAttributes() { return $this->customAttributes; }
                
        public function GetAllowHtmlCharacters() { return $this->allowHtmlCharacters; }
        public function SetAllowHtmlCharacters($value) { $this->allowHtmlCharacters = $value; }

        public function ExtractsValueFromPost()
        {
            $value = GetApplication()->IsPOSTValueSet($this->GetName()) ? GetApplication()->GetPOSTValue($this->GetName()) : null;
            if (isset($value) && !$this->allowHtmlCharacters)
                $value = htmlspecialchars($value, ENT_QUOTES);
            return $value;
        }

        public function Accept($renderer)
        {
            $renderer->RenderTextAreaEdit($this);
        }

        public function GetValueClientFunction()
        {
            return 'GetTextEditValue(null, ' . $this->GetName() . ')';
        }

        protected function GetJSControlAdapterClass()
        {
            return 'TextEditAdapter';
        }
    }

    class CustomEditor extends Component
    {
        private $customAttributes = null;

        public function __construct($name, $customAttributes = null)
        {
            parent::__construct($name);
            $this->customAttributes = $customAttributes;
        }

        public function Accept($Renderer)
        {
            assert(false);
        }

        public function SetCustomAttributes($value) { $this->customAttributes = $value; }
        public function GetCustomAttributes() { return $this->customAttributes; }
    }

    class TextEdit extends CustomEditor
    {
        private $value;
        private $size = null;
        private $maxLength = null;
        private $allowHtmlCharacters = true;

        public function __construct($name, $size = null, $maxLength = null, $customAttributes = null)
        {
            parent::__construct($name, $customAttributes);
            $this->size = $size;
            $this->maxLength = $maxLength;
        }

        private $readOnly = false;
        public function GetReadOnly() { return $this->readOnly; }
        public function SetReadOnly($value) { $this->readOnly = $value; }

        public function SetSize($value) { $this->size = $value; }
        public function GetSize() { return $this->size; }

        public function SetMaxLength($value) { $this->maxLength = $value; }
        public function GetMaxLength() { return $this->maxLength; }

        public function GetValue() { return $this->value; }
        public function SetValue($value) { $this->value = $value; }

        public function GetHTMLValue() { return str_replace('"', '&quot;', $this->value); }

        public function GetAllowHtmlCharacters() { return $this->allowHtmlCharacters; }
        public function SetAllowHtmlCharacters($value) { $this->allowHtmlCharacters = $value; }

        public function ExtractsValueFromPost()
        {
            $value = GetApplication()->IsPOSTValueSet($this->GetName()) ? GetApplication()->GetPOSTValue($this->GetName()) : null;
            if (isset($value) && !$this->allowHtmlCharacters)
                $value = htmlspecialchars($value, ENT_QUOTES);
            return $value;
        }

        public function Accept($Renderer)
        {
            $Renderer->RenderTextEdit($this);
        }

        public function GetValueClientFunction()
        {
            return 'GetTextEditValue(null, ' . $this->GetName() . ')';
        }

        protected function GetJSControlAdapterClass()
        {
            return 'TextEditAdapter';
        }
    }

    class SpinEdit extends Component
    {
        private $value;
        private $minValue;
        private $maxValue;

        private $readOnly = false;

        public function GetReadOnly() { return $this->readOnly; }
        public function SetReadOnly($value) { $this->readOnly = $value; }

        public function GetMaxValue() { return $this->maxValue; }
        public function SetMaxValue($value) { $this->maxValue = $value; }

        public function GetMinValue() { return $this->minValue; }
        public function SetMinValue($value) { $this->minValue = $value; }

        public function GetValue() { return $this->value; }
        public function SetValue($value) { $this->value = $value; }

        public function ExtractsValueFromPost()
        {
            return GetApplication()->IsPOSTValueSet($this->GetName()) ? GetApplication()->GetPOSTValue($this->GetName()) : null;
        }

        public function Accept($Renderer)
        {
            $Renderer->RenderSpinEdit($this);
        }

        protected function GetJSControlAdapterClass()
        {
            return 'SpinEditAdapter';
        }
    }

    class CheckBox extends Component
    {     
        private $value;
        
        private $readOnly = false;
        public function GetReadOnly() { return $this->readOnly; }
        public function SetReadOnly($value) { $this->readOnly = $value; }
        
        public function GetValue() { return $this->value; }
        public function SetValue($value) { $this->value = $value; }
        
        public function ExtractsValueFromPost()
        {
            return GetApplication()->IsPOSTValueSet($this->GetName()) ? '1' : '0';
        }

        public function Accept($renderer)
        {
            $renderer->RenderCheckBox($this);
        }
        
        public function Checked()
        {
            return (isset($this->value) && !empty($this->value));
        }
        
        protected function GetJSControlAdapterClass()
        {
            return 'CheckBoxEditAdapter';
        }        
    }

    class DateTimeEdit extends Component
    {
        private $value;
        private $showsTime;
        private $format;

        public function __construct($name, $showsTime = false, $format = null)
        {
            parent::__construct($name);
            $this->showsTime = $showsTime;
            
            if (!isset($format))
                $this->format = $this->showsTime ? 'Y-m-d H:i:s' : 'Y-m-d';
            else
                $this->format = $format;
        }

        private $readOnly = false;
        public function GetReadOnly() { return $this->readOnly; }
        public function SetReadOnly($value) { $this->readOnly = $value; }

        public function GetValue() 
        {
            if (isset($this->value))
                return $this->value->ToString($this->format); 
            else
                return '';
        }
        
        public function SetValue($value) 
        { 
            if (isset($value))
                 $this->value = SMDateTime::Parse($value, $this->showsTime ? 'Y-m-d H:i:s' : 'Y-m-d'); 
            else
                $this->value = null;
        }

        public function GetFormat() { return DateFormatToOSFormat($this->format); }
        public function SetFormat($value) { $this->format = $value; }
        
        public function GetShowsTime() { return $this->showsTime; }
        public function SetShowsTime($value) { $this->showsTime = $value; }
        
        public function Accept($renderer)
        {
            $renderer->RenderDateTimeEdit($this);
        }
        
        public function ExtractsValueFromPost()
        {
            return GetApplication()->IsPOSTValueSet($this->GetName()) ? GetApplication()->GetPOSTValue($this->GetName()) : null;;
        }

        public function GetValueClientFunction()
        {
            return 'GetTextEditValue(null, ' . $this->GetName() . ')';
        }

        protected function GetJSControlAdapterClass()
        {
            return 'TextEditAdapter';
        }
    }    

    class ComboBox extends Component
    {
        private $values;
        private $selectedValue;

        public function __construct($name)
        {
            parent::__construct($name);
            $this->values = array();
            $this->selectedValue = null;
        }

        private $readOnly = false;
        public function GetReadOnly() { return $this->readOnly; }
        public function SetReadOnly($value) { $this->readOnly = $value; }

        public function GetSelectedValue() { return $this->selectedValue; }
        public function SetSelectedValue($selectedValue) { $this->selectedValue = $selectedValue; }

        public function AddValue($value, $name) { $this->values[$value] = $name; }
        public function GetValues() { return $this->values; }

        public function GetValue() { return $this->selectedValue; }
        public function SetValue($value) { $this->selectedValue = $value; }

        public function ExtractsValueFromPost()
        {
            return GetApplication()->IsPOSTValueSet($this->GetName()) ? GetApplication()->GetPOSTValue($this->GetName()) : null;;
        }

        public function Accept($Renderer)
        {
            $Renderer->RenderComboBox($this);
        }

        protected function GetJSControlAdapterClass()
        {
            return 'ComboBoxAdapter';
        }
    }
        
    class RadioEdit extends Component
    {
        private $values;
        private $selectedValue;

        public function __construct($name)
        {
            parent::__construct($name);
            $this->values = array();
            $this->selectedValue = null;
        }

        private $readOnly = false;
        public function GetReadOnly() { return $this->readOnly; }
        public function SetReadOnly($value) { $this->readOnly = $value; }

        public function GetSelectedValue() { return $this->selectedValue; }
        public function SetSelectedValue($selectedValue) { $this->selectedValue = $selectedValue; }

        public function AddValue($value, $name) { $this->values[$value] = $name; }
        public function GetValues() { return $this->values; }

        public function GetValue() { return $this->selectedValue; }
        public function SetValue($value) { $this->selectedValue = $value; }

        public function ExtractsValueFromPost()
        {
            return GetApplication()->IsPOSTValueSet($this->GetName()) ? GetApplication()->GetPOSTValue($this->GetName()) : null;;
        }

        public function Accept($Renderer)
        {
            $Renderer->RenderRadioEdit($this);
        }

        protected function GetJSControlAdapterClass()
        {
            return 'ComboBoxAdapter';
        }
    }
    
    class CheckBoxGroup extends Component
    {
        private $values;
        private $selectedValues;

        public function __construct($name)
        {
            parent::__construct($name);
            $this->values = array();
            $this->selectedValues = array();
        }

        private $readOnly = false;
        public function GetReadOnly() { return $this->readOnly; }
        public function SetReadOnly($value) { $this->readOnly = $value; }

        public function IsValueSelected($value) 
        { 
            echo $value;
            return in_array($value, $this->selectedValues); 
        }
        
        public function AddValue($value, $name) { $this->values[$value] = $name; }
        public function GetValues() { return $this->values; }
        
        public function GetValue()  
        { 
            $result = '';
            foreach($this->selectedValues as $selectedValue)
                AddStr($result, $selectedValue, ',');
            return $result;
        }
        
        public function SetValue($value) 
        { 
            $this->selectedValues = explode(',', $value); 
        }

        public function ExtractsValueFromPost()
        {
            $valuesArray = GetApplication()->IsPOSTValueSet($this->GetName()) ? GetApplication()->GetPOSTValue($this->GetName()) : array();
            $result = '';
            foreach($valuesArray as $value)
                AddStr($result, $value, ',');
            return $result;            
        }

        public function Accept($Renderer)
        {
            $Renderer->RenderCheckBoxGroup($this);
        }

        protected function GetJSControlAdapterClass()
        {
            return 'CheckBoxGroup';
        }     
    }

    class FileUploader extends Component
    {
     
    }

    class ImageUploader extends Component
    {
        private $showImage;
        private $imageLink;

        private $readOnly = false;
        public function GetReadOnly() { return $this->readOnly; }
        public function SetReadOnly($value) { $this->readOnly = $value; }

        public function __construct($name)
        {
            parent::__construct($name);
            $this->showImage = false;
        }

        public function GetShowImage() { return $this->showImage; }
        public function SetShowImage($value) { $this->showImage = $value; }

        public function GetLink() { return $this->imageLink; }
        public function SetLink($value) { $this->imageLink = $value; }

        public function ExtractsValueFromPost(&$valueChanged)
        {
            $action =GetApplication()->GetPOSTValue($this->GetName() . "_action");
            $filename = $_FILES[$this->GetName() . "_filename"]["tmp_name"];

            if ($action == REMOVE_IMAGE_ACTION)
            {
                $valueChanged = true;
                return null;
            }
            elseif ($action == REPLACE_IMAGE_ACTION)
            {
                $valueChanged = true;
                return $filename;
            }
            else
            {
                $valueChanged = false;
                return null;
            }
        }

        protected function GetJSControlAdapterClass()
        {
            return 'ImageEditorAdapter';
        }

        public function Accept($renderer)
        {
            $renderer->RenderImageUploader($this);
        }
    }

    class TextBox extends Component
    {
        private $caption;

        public function GetCaption()
        {
            return $this->caption;
        }

        public function SetCaption($caption)
        {
            $this->caption = $caption;
        }

        public function __construct($name, $caption)
        {
            Component::__construct($name);
            $this->caption = $caption;
        }

        public function Accept($Renderer)
        {
            $Renderer->RenderTextBox($this);
        }
    }

    class Image extends Component
    {
        private $source;

        public function GetSource()
        {
            return $this->source;
        }

        public function SetSource($source)
        {
            $this->source = $source;
        }

        public function Accept($Renderer)
        {
            $Renderer->RenderImage($this);
        }

        public function __construct($source)
        {
            $this->source = $source;
        }
    }

    class CustomHtmlControl extends Component
    {
        private $html;

        public function GetHtml()
        {
            return $this->html;
        }

        public function SetHtml($html)
        {
            $this->html = $html;
        }

        public function __construct($html)
        {
            $this->html = $html;
        }

        public function Accept($renderer)
        {
            $renderer->RenderCustomHtmlControl($this);
        }

    }
    
    class HyperLink extends Component
    {
        private $innerText; 
        private $afterLinkText; 
        private $link;
        
        public function __construct($name, $innerText, $link = '#')
        {
            parent::__construct($name);
            $this->innerText = $innerText;  
            $this->link = $link;            
        }            
        
        public function GetAfterLinkText() { return $this->afterLinkText; } 
        public function SetAfterLinkText($value) { $this->afterLinkText = $value; }
        
        public function GetInnerText() { return $this->innerText; } 
        public function SetInnerText($value) { $this->innerText = $value; }
         
        public function GetLink() { return $this->link; }
        public function SetLink($value) { $this->link = $value; }
        
        public function Accept($renderer)
        {
            $renderer->RenderHyperLink($this);
        }        
    }
?>