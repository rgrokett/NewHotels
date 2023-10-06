<?php
abstract class SearchColumn
{
    private $fieldName;
    private $editorControl;
    private $secondEditorControl;
    private $namePrefix;
    private $caption;

    private $applyNotOperator;
    private $filterIndex;
    private $firstValue;
    private $secondValue;

    protected $localizerCaptions;

    protected function GetApplyNotOperator() { return $this->applyNotOperator; }
    protected function SetApplyNotOperator($value) { $this->applyNotOperator = $value; }
    
    protected function GetFilterIndex() { return $this->filterIndex; }
    protected function SetFilterIndex($value) { $this->filterIndex = $value; }

    public function __construct($fieldName, $caption, $localizerCaptions)
    {
        $this->fieldName = $fieldName;
        $this->editorControl = $this->CreateEditorControl();
        $this->secondEditorControl = $this->CreateSecondEditorControl();
        $this->caption = $caption;
        $this->localizerCaptions = $localizerCaptions;
    }

    public function SetNamePrefix($value) { $this->namePrefix = $value; }

    public function GetCaption() { return $this->caption; }
    public function SetCaption($value) { $this->caption = $value; }

    protected abstract function CreateEditorControl();
    protected abstract function CreateSecondEditorControl();

    protected abstract function SetEditorControlValue($value);
    protected abstract function SetSecondEditorControlValue($value);

    public function GetFieldName() { return $this->fieldName; }

    public function GetAvailableFilterTypes() { return array(); }
    public function GetActiveFilterType() { return ''; }

    public function GetEditorControl() { return $this->editorControl; }
    public function GetSecondEditorControl() { return $this->secondEditorControl; }

    public function ExtractSearchValuesFromSession()
    {
        if (GetApplication()->IsSessionVariableSet($this->namePrefix . 'not_' . $this->GetFieldName()))
        {
            $this->applyNotOperator = GetApplication()->GetSessionVariable($this->namePrefix . 'not_' . $this->GetFieldName());
            $this->filterIndex = GetApplication()->GetSessionVariable($this->namePrefix . 'AdvSearch_FilterType_' . $this->GetFieldName());
            $this->firstValue = GetApplication()->GetSessionVariable($this->namePrefix . $this->GetEditorControl()->GetName());
            $this->secondValue = GetApplication()->GetSessionVariable($this->namePrefix . $this->GetSecondEditorControl()->GetName());

            $this->SetEditorControlValue($this->firstValue);
            $this->SetSecondEditorControlValue($this->secondValue);
        }
    }

    public function ExtractSearchValuesFromPost()
    {
        $this->applyNotOperator = GetApplication()->IsPOSTValueSet('not_' . $this->GetFieldName());;
        $this->filterIndex = GetApplication()->GetPOSTValue('AdvSearch_FilterType_' . $this->GetFieldName());
        $this->firstValue = $this->GetEditorControl()->ExtractsValueFromPost();
        $this->secondValue = $this->GetSecondEditorControl()->ExtractsValueFromPost();

        GetApplication()->SetSessionVariable($this->namePrefix . 'not_' . $this->GetFieldName(), $this->applyNotOperator);
        GetApplication()->SetSessionVariable($this->namePrefix . 'AdvSearch_FilterType_' . $this->GetFieldName(), $this->filterIndex);
        GetApplication()->SetSessionVariable($this->namePrefix . $this->GetEditorControl()->GetName(), $this->firstValue);
        GetApplication()->SetSessionVariable($this->namePrefix . $this->GetSecondEditorControl()->GetName(), $this->secondValue);

        $this->SetEditorControlValue($this->firstValue);
        $this->SetSecondEditorControlValue($this->secondValue);
    }

    public function GetFilterForField()
    {
        $result = null;
        if (isset($this->firstValue) && $this->firstValue != '')
        {
        if ($this->filterIndex == 'between')
            $result = new BetweenFieldFilter($this->firstValue, $this->secondValue);
            elseif ($this->filterIndex == 'STARTS')
                $result = new FieldFilter($this->firstValue.'%', 'LIKE');
            elseif ($this->filterIndex == 'ENDS')
                $result = new FieldFilter('%'.$this->firstValue, 'LIKE');
            elseif ($this->filterIndex == 'CONTAINS')
                $result = new FieldFilter('%'.$this->firstValue.'%', 'LIKE');
        else
            $result = new FieldFilter($this->firstValue, $this->filterIndex);
                
        if ($this->applyNotOperator)
            $result = new NotPredicateFilter($result);
        }
        return $result;
    }

    public function GetUserFriendlyCondition()
    {
        $result = '';
        $filterTypes = $this->GetAvailableFilterTypes();
        if (isset($this->firstValue) && $this->firstValue != '')
        {
            if ($this->filterIndex == 'between')
                $result = sprintf('between %s and %s', '<b>'.$this->firstValue.'</b>',  '<b>'.$this->secondValue.'</b>');
            else
                $result = $filterTypes[$this->filterIndex] . ' ' . '<b>'.$this->firstValue.'</b>';
            if ($this->applyNotOperator)
                $result = $this->localizerCaptions->GetMessageString('Not') . ' (' . $result . ')';  
        }
        return $result;    
    }
    
    public function IsFilterActive()
    {
        $result = false;
        if (isset($this->filterIndex))
        {
            $result = isset($this->firstValue) && $this->firstValue != '';
            if ($this->filterIndex == 'between')
                $result = $result && isset($this->secondValue) && $this->secondValue != '';
        }
        return $result;
    }

    public function ResetFilter()
    {
        $this->applyNotOperator = null;
        $this->filterIndex = null;
        $this->firstValue = null;
        $this->secondValue = null;

        GetApplication()->UnSetSessionVariable($this->namePrefix . 'not_' . $this->GetFieldName());
        GetApplication()->UnSetSessionVariable($this->namePrefix . 'AdvSearch_FilterType_' . $this->GetFieldName());
        GetApplication()->UnSetSessionVariable($this->namePrefix . $this->GetEditorControl()->GetName());
        GetApplication()->UnSetSessionVariable($this->namePrefix . $this->GetSecondEditorControl()->GetName());
    }

    public function GetActiveFilterIndex()
    {
        return $this->filterIndex;
    }

    public function GetFilterValue() { return $this->firstValue; }
    
    public function GetIsApplyNotOperatorIndex()
    {
        return $this->applyNotOperator;
    }
}

class IntegerSearchColumn extends SearchColumn
{
    protected function CreateEditorControl()
    {
        return new SpinEdit($this->GetFieldName() . '_value');
    }

    protected function CreateSecondEditorControl()
    {
        return new SpinEdit($this->GetFieldName() . '_secondvalue');
    }

    protected function SetEditorControlValue($value)
    {
        $this->GetEditorControl()->SetValue($value);
    }

    protected function SetSecondEditorControlValue($value)
    {
        $this->GetSecondEditorControl()->SetValue($value);
    }

    public function GetAvailableFilterTypes()
    {
        return array(
            'LIKE' => 'LIKE',
            'between' => 'Between');
    }
}

class BlobSearchColumn extends SearchColumn 
{
    protected function CreateEditorControl()
    {
        return new NullComponent($this->GetFieldName() . '_value');
    }

    protected function CreateSecondEditorControl()
    {
        return new NullComponent($this->GetFieldName() . '_secondvalue');
    }

    protected function SetEditorControlValue($value) { }
    protected function SetSecondEditorControlValue($value) { }

    public function GetAvailableFilterTypes()
    {
        return array(
            '' => '',
            'IS NULL' => $this->localizerCaptions->GetMessageString('isBlank'),
            'IS NOT NULL' => $this->localizerCaptions->GetMessageString('isNotBlank')
            );
    }    
    
    public function IsFilterActive()
    {        
        return $this->GetFilterIndex() != '';
    }    
    
    public function GetFilterForField()
    {
        $result = null;
        if ($this->GetFilterIndex() != '')
        {
            if ($this->GetFilterIndex() == 'IS NULL')
                $result = new IsNullFieldFilter();
            elseif ($this->GetFilterIndex() == 'IS NOT NULL')
                $result = new NotPredicateFilter(new IsNullFieldFilter());
            if ($this->GetApplyNotOperator())
                $result = new NotPredicateFilter($result);
        }
        return $result;
    }
    
}

class StringSearchColumn extends SearchColumn
{
    protected function CreateEditorControl()
    {
        return new TextEdit($this->GetFieldName() . '_value');
    }

    protected function CreateSecondEditorControl()
    {
        return new TextEdit($this->GetFieldName() . '_secondvalue');
    }

    protected function SetEditorControlValue($value)
    {
        $this->GetEditorControl()->SetValue($value);
    }

    protected function SetSecondEditorControlValue($value)
    {
        $this->GetSecondEditorControl()->SetValue($value);
    }

    public function GetAvailableFilterTypes()
    {
        return array(
            'LIKE' => $this->localizerCaptions->GetMessageString('Like'),
            'STARTS' => $this->localizerCaptions->GetMessageString('StartsWith'),
            'ENDS' => $this->localizerCaptions->GetMessageString('EndsWith'),
            'CONTAINS' => $this->localizerCaptions->GetMessageString('Contains'),
            'between' => $this->localizerCaptions->GetMessageString('between'),
            '='  => $this->localizerCaptions->GetMessageString('equals'),
            '<>' => $this->localizerCaptions->GetMessageString('doesNotEquals'),
            '>'  => $this->localizerCaptions->GetMessageString('isGreaterThan'),
            '>=' => $this->localizerCaptions->GetMessageString('isGreaterThanOrEqualsTo'),
            '<'  => $this->localizerCaptions->GetMessageString('isLessThan'),
            '<=' => $this->localizerCaptions->GetMessageString('isLessThanOrEqualsTo')
            );
    }
}

class DateTimeSearchColumn extends SearchColumn
{
    protected function CreateEditorControl()
    {
        return new DateTimeEdit($this->GetFieldName() . '_value', true);
    }

    protected function CreateSecondEditorControl()
    {
        return new DateTimeEdit($this->GetFieldName() . '_secondvalue', true);
    }

    protected function SetEditorControlValue($value)
    {
        $this->GetEditorControl()->SetValue($value);
    }

    protected function SetSecondEditorControlValue($value)
    {
        $this->GetSecondEditorControl()->SetValue($value);
    }

    public function GetAvailableFilterTypes()
    {
        return array(
            'LIKE' => 'LIKE',
            'between' => 'Between');
    }
}

class AdvancedSearchControl
{
    private $name;
    private $columns;
    private $dataset;
    private $applyAndOperator;

    public function __construct($name, $dataset)
    {
        $this->name = $name;
        $this->dataset = $dataset;
        $this->columns = array();
        $this->applyAndOperator = null;
        $this->isActive = false;
    }

    public function AddSearchColumn($column)
    {
        $column->SetNamePrefix($this->name);
        $this->columns[] = $column;
    }

    public function GetSearchColumns() { return $this->columns; }
    public function GetIsApplyAndOperator() { return $this->applyAndOperator; }

    public function Accept($renderer)
    {
        $renderer->RenderAdvancedSearchControl($this);
    }

    public function ResetFilter()
    {
        foreach($this->columns as $column)
            $column->ResetFilter();
        $this->applyAndOperator = null;
        GetApplication()->UnSetSessionVariable($this->name . 'SearchType');
    }

    public function ExtractValuesFromSession()
    {
        foreach($this->columns as $column)
            $column->ExtractSearchValuesFromSession();
        $this->applyAndOperator = GetApplication()->GetSessionVariable($this->name . 'SearchType');
    }

    public function ExtractValuesFromPost()
    {
        foreach($this->columns as $column)
            $column->ExtractSearchValuesFromPost();
        $this->applyAndOperator = GetApplication()->GetPOSTValue('SearchType') == 'and';
        GetApplication()->SetSessionVariable($this->name . 'SearchType', $this->applyAndOperator);
    }

    public function ApplyFilterToDataset()
    {
        $fieldNames = array();
        $fieldFilters = array();

        foreach($this->columns as $column)
            if ($column->IsFilterActive())
            {
                $fieldNames[] = $column->GetFieldName();
                $fieldFilters[] = $column->GetFilterForField();
            }

        if (count($fieldFilters) > 0)
        $this->dataset->AddCompositeFieldFilter(
            $this->applyAndOperator ? 'AND' : 'OR',
            $fieldNames,
            $fieldFilters);
        $this->isActive = (count($fieldFilters) > 0);
    }

    public function GetUserFriendlySearchConditions()
    {
        $result = array();
        //$operator = $this->applyAndOperator ? ' AND ' : ' OR ';
        
        foreach($this->columns as $column)
            if ($column->IsFilterActive())
            {
                $result[] = array(
                    'Caption' => $column->GetCaption(),
                    'Condition' => $column->GetUserFriendlyCondition()
                );
            }
        return $result;
    }

    public function IsActive() { return $this->isActive; }
    
    public function ProcessMessages()
    {
        if ((GetApplication()->IsPOSTValueSet('ResetFilter') && GetApplication()->GetPOSTValue('ResetFilter') == '1') || (GetApplication()->IsPOSTValueSet('operation') && GetApplication()->GetPOSTValue('operation') == 'ssearch'))
        {
            $this->ResetFilter();
        }
        else
        {
            if (GetApplication()->IsSessionVariableSet($this->name . 'SearchType'))
                $this->ExtractValuesFromSession();

            if (GetApplication()->IsPOSTValueSet('SearchType'))
                $this->ExtractValuesFromPost();

            if (isset($this->applyAndOperator))
                $this->ApplyFilterToDataset();
        }
    }
    
    public function GetHighlightedFields()
    {
        $result = array();
        foreach($this->columns as $column)
            if ($column->IsFilterActive() && (
                ($column->GetActiveFilterIndex() == 'LIKE') ||
                ($column->GetActiveFilterIndex() == '=') ||
                ($column->GetActiveFilterIndex() == 'STARTS') ||
                ($column->GetActiveFilterIndex() == 'ENDS') ||
                ($column->GetActiveFilterIndex() == 'CONTAINS')                  
                ))
                $result[] = $column->GetFieldName();
        return $result;
    }
        
    public function GetHighlightedFieldText()
    {
        $result = array();
        foreach($this->columns as $column)
            if ($column->IsFilterActive() && (
                ($column->GetActiveFilterIndex() == 'LIKE') ||
                ($column->GetActiveFilterIndex() == '=') ||
                ($column->GetActiveFilterIndex() == 'STARTS') ||
                ($column->GetActiveFilterIndex() == 'ENDS') ||
                ($column->GetActiveFilterIndex() == 'CONTAINS')                  
                ))
                $result[] = str_replace('%', '', $column->GetFilterValue());
        return $result;
    }
        
    public function GetHighlightedFieldOptions()
    {
        $result = array();
        foreach($this->columns as $column)
            if ($column->IsFilterActive() && (
                ($column->GetActiveFilterIndex() == 'LIKE') ||
                ($column->GetActiveFilterIndex() == '=')  ||
                ($column->GetActiveFilterIndex() == 'STARTS') ||
                ($column->GetActiveFilterIndex() == 'ENDS') ||
                ($column->GetActiveFilterIndex() == 'CONTAINS')  
                ))
            {
                $trimmed = trim($column->GetFilterValue());
                if ($column->GetActiveFilterIndex() == 'LIKE')
                {
                if ($trimmed[0] == '%' && $trimmed[strlen($trimmed) - 1] == '%')
                    $result[] =  'ALL';
                elseif ($trimmed[0] == '%')
                    $result[] =  'END';
                elseif ($trimmed[strlen($trimmed) - 1] == '%')
                    $result[] =  'START';
                }
                elseif ($column->GetActiveFilterIndex() == 'STARTS')
                    $result[] = 'START';
                elseif ($column->GetActiveFilterIndex() == 'ENDS')
                    $result[] = 'END';
                elseif ($column->GetActiveFilterIndex() == 'CONTAINS')
                    $result[] = 'ALL';
                else
                    $result[] =  'ALL';
            }
        return $result;
    }        
}
?>
