<?php
class SimpleSearch
{
    private $name;
    private $fieldsForFilter;
    private $filterTypes;
    private $dataset;
    private $fieldCaptions;

    private $activeFilterTypeName;
    private $activeFieldName;
    private $activeFilterText;
    private $localizerCaptions;

    public function __construct($name, $dataset, $fieldsForFilter, $fieldCaptions, $filterTypes, $localizerCaptions)
    {
        $this->name = $name;
        $this->fieldsForFilter = $fieldsForFilter;
        $this->filterTypes = $filterTypes;
        $this->dataset = $dataset;
        $this->fieldCaptions = $fieldCaptions;
        $this->localizerCaptions = $localizerCaptions;
        $this->activeFieldName = 'Any field';
        $this->activeFilterTypeName = 'LIKE';
    }
    
    private function IsRequestParameterSet($name)
    {
        return GetApplication()->IsPOSTValueSet($name);
    }
    
    private function GetRequestParameter($name)
    {
        if (GetApplication()->IsPOSTValueSet($name))
            return GetApplication()->GetPOSTValue($name);
        else
            return null;
    }

    public function ResetFilter()
    {
        GetApplication()->UnSetSessionVariable($this->name . 'SearchField');
        GetApplication()->UnSetSessionVariable($this->name . 'FilterType');
        GetApplication()->UnSetSessionVariable($this->name . 'FilterText');
        $this->activeFieldName = 'Any field';
        $this->activeFilterTypeName = 'LIKE';
        $this->activeFilterText = '';
    }

    public function ExtractValuesFromSession()
    {
        $this->activeFieldName = GetApplication()->GetSessionVariable($this->name . 'SearchField');
        $this->activeFilterTypeName = GetApplication()->GetSessionVariable($this->name . 'FilterType');
        $this->activeFilterText = GetApplication()->GetSessionVariable($this->name . 'FilterText');
    }

    public function ExtractValuesFromPost()
    {
        $this->activeFieldName = $this->GetRequestParameter('SearchField');
        $this->activeFilterTypeName = $this->GetRequestParameter('FilterType');
        $this->activeFilterText = $this->GetRequestParameter('FilterText');
        GetApplication()->SetSessionVariable($this->name . 'SearchField', $this->activeFieldName);
        GetApplication()->SetSessionVariable($this->name . 'FilterType', $this->activeFilterTypeName);
        GetApplication()->SetSessionVariable($this->name . 'FilterText', $this->activeFilterText);
    }
    
    public function CreateFieldFilter($ignoreDataType)
    {
        if ($this->activeFilterTypeName == 'STARTS')
            return new FieldFilter($this->activeFilterText.'%', 'LIKE', $ignoreDataType);
        elseif ($this->activeFilterTypeName == 'ENDS')
            return new FieldFilter('%'.$this->activeFilterText, 'LIKE', $ignoreDataType);
        elseif ($this->activeFilterTypeName == 'CONTAINS')
            return new FieldFilter('%'.$this->activeFilterText.'%', 'LIKE', $ignoreDataType);
        else
            return new FieldFilter($this->activeFilterText, $this->activeFilterTypeName, $ignoreDataType);
    }
    
    public function ApplyFilterToDataset()
    {
        if ($this->activeFieldName == 'Any field')
        {
            $fieldNames = array();
            $fieldFilters = array();
    
            foreach($this->fieldsForFilter as $fieldName)
            {
                $fieldNames[] = $fieldName;
                $fieldFilters[] = $this->CreateFieldFilter(true);
            }

            if (count($fieldFilters) > 0)
                $this->dataset->AddCompositeFieldFilter('OR', $fieldNames, $fieldFilters);
        }
        else
        {
            $this->dataset->AddFieldFilter(
                $this->activeFieldName,
                $this->CreateFieldFilter(false));
        }
    }

    private function IsSearchVariablesComletelySet()
    {
        return isset($this->activeFieldName) && (isset($this->activeFilterTypeName)) &&
            (isset($this->activeFilterText) && trim($this->activeFilterText) != '');
    }

    public function ProcessMessages()
    {
        if (($this->IsRequestParameterSet('ResetFilter') && $this->GetRequestParameter('ResetFilter') == '1') ||
            GetOperation() == 'resetall' ||  (GetApplication()->IsPOSTValueSet('operation') && GetApplication()->GetPOSTValue('operation') == 'asearch'))
        {
            $this->ResetFilter();
        }
        else
        {
            if (GetApplication()->IsSessionVariableSet($this->name . 'SearchField'))
                $this->ExtractValuesFromSession();

            if ($this->IsRequestParameterSet('SearchField'))
                $this->ExtractValuesFromPost();

            if ($this->IsSearchVariablesComletelySet())
                $this->ApplyFilterToDataset();
        }
    }

    public function GetActiveFilterText()
    {
        return $this->activeFilterText;
    }

    public function GetActiveFilterTypeName()
    {
        return $this->activeFilterTypeName;
    }

    public function GetActiveFieldName()
    {
        return $this->activeFieldName;
    }

    public function GetFilteredFields()
    {
        $result = array();
        for($i = 0; $i < count($this->fieldsForFilter); $i++)
            $result[$this->fieldsForFilter[$i]] = $this->fieldCaptions[$i];
        $result['Any field'] = $this->localizerCaptions->GetMessageString('AnyField');
        return $result;
        
    }

    public function GetFilterTypes()
    {
        return $this->filterTypes;
    }

    public function Accept($Renderer)
    {
        $Renderer->RenderSimpleSearch($this);
    }
    
    public function GetHighlightedFields()
    {
        if ($this->activeFieldName == 'Any field')
            return $this->fieldsForFilter;
        else
            return $this->activeFieldName;
    }
    
    public function UseTextHighlight()
    {
        return (
            ($this->activeFilterTypeName == 'LIKE') ||
            ($this->activeFilterTypeName == '=') ||
            ($this->activeFilterTypeName == 'STARTS') ||
            ($this->activeFilterTypeName == 'ENDS') ||
            ($this->activeFilterTypeName == 'CONTAINS'))
        && ($this->activeFilterText != '');
    }
    
    public function GetTextForHighlight()
    {
        return str_replace('%', '', $this->activeFilterText);
    }
    
    public function GetHighlightOption()
    {
        $trimmed = trim($this->activeFilterText);
        if ($this->activeFilterTypeName == 'LIKE')
        {
        if ($trimmed[0] == '%' && $trimmed[strlen($trimmed) - 1] == '%')
            return 'ALL';
        elseif ($trimmed[0] == '%')
            return 'END';
        elseif ($trimmed[strlen($trimmed) - 1] == '%')
            return 'START';
    }
        elseif ($this->activeFilterTypeName == 'STARTS')
            return 'START';
        elseif ($this->activeFilterTypeName == 'ENDS')
            return 'END';
        elseif ($this->activeFilterTypeName == 'CONTAINS')
            return 'ALL';
        else
            return 'ALL';
    }
}
?>
