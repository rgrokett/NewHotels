<?php

require_once 'components/renderers/renderer.php';
require_once 'components/component.php';

define('otAscending', 1);
define('otDescending', 2);

$orderTypeCaptions = array(
    otAscending  => 'a',
    otDescending => 'd');

abstract class GridState
{
    protected $grid;

    public function __construct($Grid)
    {
        $this->grid = $Grid;
    }

    protected function GetPage()
    {
        return $this->grid->GetPage();
    }

    protected function ChangeState($stateIdentifier)
    {
        GetApplication()->SetOperation($stateIdentifier);
        $this->grid->SetState($stateIdentifier);
    }

    protected function ApplyState($stateIdentifier)
    {
        $this->ChangeState($stateIdentifier);
        $this->grid->GetState()->ProcessMessages();
    }

    protected function GetDataset()
    {
        return $this->grid->GetDataset();
    }

    protected function SetGridErrorMessage($exception)
    {
        $message = $exception->getMessage();
        if (defined('DEBUG_LEVEL') && DEBUG_LEVEL > 0)
            $message .= '<br>Program trace: <br>'.FormatExceptionTrace($exception);
        $this->grid->SetErrorMessage($message);
    }

    protected function SetGridErrorMessages($exceptions)
    {
        $message = '';
        foreach($exceptions as $exception);
        {
            if (is_subclass_of($exception, 'SMException'))
                AddStr($message, $exception->getLocalizedMessage($this->GetPage()->GetLocalizerCaptions()), '<br><br>');
            else
                AddStr($message, $exception->getMessage(), '<br><br>');

            if (defined('DEBUG_LEVEL') && DEBUG_LEVEL > 0)
                $message .= '<br>Program trace: <br>'.FormatExceptionTrace($exception);
        }
        $this->grid->SetErrorMessage($message);
    }

    protected function DoCanChangeData(&$rowValues, &$message)
    {
        return true;
    }

    protected function CanChangeData(&$rowValues, &$message)
    {
        $cancel = false;
        $this->grid->OnBeforeDataChange->Fire(array($this->grid->GetPage(), &$rowValues, &$cancel, &$message));
        return !$cancel && $this->DoCanChangeData($rowValues, $message);
    }

    public abstract function ProcessMessages();
}

class DeleteSelectedGridState extends GridState
{
    protected function DoCanChangeData(&$rowValues, &$message)
    {
        $cancel = false;
        $this->grid->BeforeDeleteRecord->Fire(array(&$rowValues, &$cancel, &$message));
        return !$cancel;
    }

    public function ProcessMessages()
    {
        $primaryKeysArray = array();
        for($i = 0; $i < GetApplication()->GetPOSTValue('recordCount'); $i++)
        {
            if (GetApplication()->IsPOSTValueSet('rec'.$i))
            {
                // TODO : move GetPrimaryKeyFieldNames function to private
                $primaryKeys = array();
                $primaryKeyNames = $this->grid->GetDataset()->GetPrimaryKeyFieldNames();
                for($j = 0; $j < count($primaryKeyNames); $j++)
                    $primaryKeys[] = GetApplication()->GetPOSTValue('rec' . $i . '_pk' . $j);
                $primaryKeysArray[] = $primaryKeys;
            }
        }

        foreach($primaryKeysArray as $primaryKeyValues)
        {
            $this->grid->GetDataset()->SetSingleRecordState($primaryKeyValues);
            $this->grid->GetDataset()->Open();

            if ($this->grid->GetDataset()->Next())
            {
                $rowValues = $this->grid->GetDataset()->GetFieldValues();
                $cancel = false;
                $message = '';

                $fieldValues = $this->grid->GetDataset()->GetCurrentFieldValues();
                if ($this->CanChangeData($fieldValues, $message))
                {
                    try
                    {
                        $this->grid->GetDataset()->Delete();
                    }
                    catch(Exception $e)
                    {
                        $this->grid->GetDataset()->SetAllRecordsState();
                        $this->ChangeState(OPERATION_VIEWALL);
                        $this->SetGridErrorMessage($e);
                        return;
                    }
                }
                else
                {
                    $this->grid->GetPage()->SetMessage($message);
                    $this->grid->GetDataset()->Close();
                    break;
                }
            }
            $this->grid->GetDataset()->Close();
        }
        
        $this->ApplyState(OPERATION_VIEWALL);
    }
}

class ViewAllGridState extends GridState
{
    function ProcessMessages()
    {
        $orderColumn = $this->grid->GetOrderColumnFieldName();
        $orderType = $this->grid->GetOrderType();
        if (isset($orderType) && isset($orderColumn))
            $this->grid->GetDataset()->SetOrderBy($orderColumn, $orderType == otAscending? 'ASC' : 'DESC');

        foreach( $this->grid->GetViewColumns() as $Column )
            $Column->ProcessMessages();
    }
}

class EditGridState extends GridState
{
    function ProcessMessages()
    {
        $primaryKeyValues = $this->grid->GetPrimaryKeyValuesFromGet();

        $this->grid->GetDataset()->SetSingleRecordState($primaryKeyValues);
        $this->grid->GetDataset()->Open();

        if ($this->grid->GetDataset()->Next())
        {
            $columns = $this->grid->GetEditColumns();
            array_walk($columns, create_function('$column', '$column->ProcessMessages();'));
        }
        $this->grid->GetDataset()->Close();
    }
}

class CopyGridState extends GridState
{
    function ProcessMessages()
    {
        $primaryKeyValues = $this->grid->GetPrimaryKeyValuesFromGet();

        $this->grid->GetDataset()->SetSingleRecordState($primaryKeyValues);
        $this->grid->GetDataset()->Open();

        if ($this->grid->GetDataset()->Next())
            foreach($this->grid->GetInsertColumns() as $column)
                $column->ProcessMessages();
    }
}

class InsertGridState extends GridState
{
    function ProcessMessages()
    {
        foreach($this->grid->GetInsertColumns() as $column)
            $column->ProcessMessages();
    }
}

class CommitNewValuesGridState extends GridState
{
    protected function DoCanChangeData(&$rowValues, &$message)
    {
        $cancel = false;
        $this->grid->BeforeInsertRecord->Fire(array(&$rowValues, &$cancel, &$message));
        return !$cancel;
    }

    function ProcessMessages()
    {
        $this->grid->GetDataset()->Insert();

        $exceptions = array();
        foreach($this->grid->GetInsertColumns() as $ñolumn)
        {
            try
            {
                $ñolumn->ProcessMessages();
            }
            catch(Exception $e)
            {
                $exceptions[] = $e;
            }
        }
            
        $message = '';
        $fieldValues = $this->grid->GetDataset()->GetCurrentFieldValues();
        if ($this->CanChangeData($fieldValues, $message))
        {
            if (count($exceptions) > 0)
            {
                $this->ChangeState(OPERATION_INSERT);
                $this->SetGridErrorMessages($exceptions);
                return;
            }
            try
            {
                $this->GetDataset()->Post();
            }
            catch(Exception $e)
            {
                $this->ChangeState(OPERATION_INSERT);
                $this->SetGridErrorMessage($e);
                return;
            }
        }
        else
            $this->grid->GetPage()->SetMessage($message);

        $this->ApplyState(OPERATION_VIEWALL);
    }
}

class CommitGridState extends GridState
{
    protected function DoCanChangeData(&$rowValues, &$message)
    {
        $cancel = false;
        $this->grid->BeforeUpdateRecord->Fire(array(&$rowValues, &$cancel, &$message));
        return !$cancel;
    }

    public function ProcessMessages()
    {
        $primaryKeyValues = array();
        ExtractPrimaryKeyValues($primaryKeyValues, METHOD_POST);

        $this->GetDataset()->SetSingleRecordState($primaryKeyValues);
        $this->GetDataset()->Open();
        
        if ($this->GetDataset()->Next())
        {
            $this->GetDataset()->Edit();

            $exceptions = array();
            foreach($this->grid->GetEditColumns() as $column)
            {
                try
                {
                    $column->ProcessMessages();
                }
                catch(Exception $e)
                {
                    $exceptions[] = $e;
                }
            }

            $message = '';
            $fieldValues = $this->grid->GetDataset()->GetCurrentFieldValues();
            if ($this->CanChangeData($fieldValues, $message))
            {
                if (count($exceptions) > 0)
                {
                    $this->ChangeState(OPERATION_EDIT);
                    $this->SetGridErrorMessages($exceptions);
                    $columns = $this->grid->GetEditColumns();
                    array_walk($columns, create_function('$column', '$column->PrepareEditorControl();'));
                    $this->grid->GetDataset()->Close();
                    return;
                }
                try
                {
                    $this->grid->GetDataset()->Post();
                }
                catch(Exception $e)
                {
                    $this->ChangeState(OPERATION_EDIT);
                    $this->SetGridErrorMessage($e);
                    $columns = $this->grid->GetEditColumns();
                    array_walk($columns, create_function('$column', '$column->PrepareEditorControl();'));
                    $this->grid->GetDataset()->Close();
                    return;
                }
            }
            else
                $this->grid->GetPage()->SetMessage($message);
                
            $this->grid->GetDataset()->Close();
        }

        $this->ApplyState(OPERATION_VIEWALL);
    }
}

class DeleteGridState extends GridState
{
    public function ProcessMessages()
    {
        $primaryKeyValues = array();
        ExtractPrimaryKeyValues($primaryKeyValues, METHOD_GET);
        $this->grid->GetDataset()->SetSingleRecordState($primaryKeyValues);
    }
}

class ViewGridState extends GridState
{
    public function ProcessMessages()
    {
        $primaryKeyValues = array();
        ExtractPrimaryKeyValues($primaryKeyValues, METHOD_GET);
        $this->grid->GetDataset()->SetSingleRecordState($primaryKeyValues);
    }
}

class CommitDeleteGridState extends GridState
{
    protected function DoCanChangeData(&$rowValues, &$message)
    {
        $cancel = false;
        $this->grid->BeforeDeleteRecord->Fire(array(&$rowValues, &$cancel, &$message));
        return !$cancel;
    }

    public function ProcessMessages()
    {
        $primaryKeyValues = array();
        ExtractPrimaryKeyValues($primaryKeyValues, METHOD_POST);

        $this->grid->GetDataset()->SetSingleRecordState($primaryKeyValues);
        $this->grid->GetDataset()->Open();
        if ($this->grid->GetDataset()->Next())
        {
            $message = '';
            $fieldValues = $this->grid->GetDataset()->GetCurrentFieldValues();
            if ($this->CanChangeData($fieldValues, $message))
            {
                try
                {
                    $this->grid->GetDataset()->Delete();
                }
                catch(Exception $e)
                {
                    
                    $this->ChangeState(OPERATION_DELETE);
                    $this->SetGridErrorMessage($e);
                    return;
                }
            }
            else
                $this->grid->GetPage()->SetMessage($message);
        }
        $this->grid->GetDataset()->Close();

        $this->grid->GetDataset()->Open();
        $this->ApplyState(OPERATION_VIEWALL);
    }
}

class Grid
{
    private $name;
    private $editColumns;
    private $viewColumns;
    private $printColumns;
    private $insertColumns;
    private $exportColumns;
    private $singleRecordViewColumns;
    //
    private $dataset;
    private $gridState;
    private $page;
    private $showAddButton;
    private $message;
    private $allowDeleteSelected;
    //
    public $Width;
    public $Margin;
    //
    public $SearchControl;
    public $UseFilter;
    //
    private $orderColumnFieldName;
    private $orderType;
    private $highlightRowAtHover;
    private $errorMessage;
    //
    public $OnCustomRenderColumn;
    public $OnCustomDrawCell;
    public $BeforeShowRecord;
    public $BeforeUpdateRecord;
    public $BeforeInsertRecord;
    public $BeforeDeleteRecord;
    public $OnBeforeDataChange;
    public $OnCustomDrawCell_Simple;

    function __construct($page, $dataset, $name)
    {
        $this->page = $page;
        $this->dataset = $dataset;
        $this->name = $name;
        //
        $this->editColumns = array();
        $this->viewColumns = array();
        $this->printColumns = array();
        $this->insertColumns = array();
        $this->exportColumns = array();
        $this->singleRecordViewColumns = array();
        //
        $this->SearchControl = new NullComponent('Search');
        $this->UseFilter = false;
        //
        $this->showAddButton = false;
        //
        $this->OnCustomDrawCell = new Event();
        $this->BeforeShowRecord = new Event();
        $this->BeforeUpdateRecord = new Event();
        $this->BeforeInsertRecord = new Event();
        $this->BeforeDeleteRecord = new Event();
        $this->OnCustomDrawCell_Simple = new Event();
        $this->OnCustomRenderColumn = new Event();
        $this->OnBeforeDataChange = new Event();
        //
        $this->SetState(OPERATION_VIEWALL);
        $this->allowDeleteSelected = false;
        $this->highlightRowAtHover = false;
    }

    private function SetSessionVariable($name, $value)
    {
        GetApplication()->SetSessionVariable($this->GetName() . '_' . $name, $value);
    }

    private function UnSetSessionVariable($name)
    {
        GetApplication()->UnSetSessionVariable($this->GetName() . '_' . $name);
    }

    private function IsSessionVariableSet($name)
    {
        return GetApplication()->IsSessionVariableSet($this->GetName() . '_' . $name);
    }

    private function GetSessionVariable($name)
    {
        return GetApplication()->GetSessionVariable($this->GetName() . '_' . $name);
    }

    public function SetErrorMessage($value) { $this->errorMessage = $value; }
    public function GetErrorMessage() { return $this->errorMessage; }

    public function GetName()
    { return $this->name; }

    public function GetOrderColumnFieldName()
    { return $this->orderColumnFieldName; }
    public function SetOrderColumnFieldName($value)
    { $this->orderColumnFieldName = $value; }

    public function GetOrderType()
    { return $this->orderType; }
    public function SetOrderType($value)
    { $this->orderType = $value; }

    public function SetGridMessage($value)
    { $this->message = $value; }
    public function GetGridMessage()
    { return $this->message; }

    public function SetShowAddButton($value)
    { $this->showAddButton = $value; }
    public function GetShowAddButton()
    { return $this->showAddButton; }

    function GetPage()
    { return $this->page; }

    function GetDataset()
    { return $this->dataset; }

    function GetEditColumns()
    { return $this->editColumns; }

    function GetViewColumns()
    { return $this->viewColumns; }

    function GetPrintColumns()
    { return $this->printColumns; }

    function GetInsertColumns()
    { return $this->insertColumns; }

    function GetExportColumns()
    { return $this->exportColumns; }

    function GetSingleRecordViewColumns()
    { return $this->singleRecordViewColumns; }

    public function GetAddRecordLink()
    {
        $result = $this->CreateLinkBuilder();
        $result->AddParameter(OPERATION_PARAMNAME, OPERATION_INSERT);
        return $result->GetLink();
    }

    function GetDeleteSelectedLink()
    {
        $result = $this->CreateLinkBuilder();
        return $result->GetLink();
    }


    function CreateLinkBuilder()
    {
        return $this->GetPage()->CreateLinkBuilder();
    }

    private function DoAddColumn($column)
    {
        $column->SetGrid($this);
    }

    private $verticalLines = array();

    function GetVerticalLineBeforeWidth($column)
    {
        if (is_subclass_of($column, 'CustomViewColumn'))
            return $column->GetVerticalLine();
    }

    function AddVericalLine($style)
    {
        if (count($this->viewColumns) > 0)
            $this->viewColumns[count($this->viewColumns) - 1]->SetVerticalLine($style);
    }

    function AddSingleRecordViewColumn($column)
    {
        $this->singleRecordViewColumns[] = $column;
        $this->DoAddColumn($column);
        return $column;
    }

    function AddViewColumn($column)
    {
        $this->viewColumns[] = $column;
        $this->DoAddColumn($column);
        return $column;
    }

    function AddEditColumn($column)
    {
        $this->editColumns[] = $column;
        $this->DoAddColumn($column);
        return $column;
    }

    function AddPrintColumn($column)
    {
        $this->printColumns[] = $column;
        $this->DoAddColumn($column);
        return $column;
    }

    function AddInsertColumn($column)
    {
        $this->insertColumns[] = $column;
        $this->DoAddColumn($column);
        return $column;
    }

    function AddExportColumn($column)
    {
        $this->exportColumns[] = $column;
        $this->DoAddColumn($column);
        return $column;
    }

    function Accept($Renderer)
    {
        $Renderer->RenderGrid($this);
    }

    function SetState($StateName)
    {
        switch($StateName)
        {
            case OPERATION_VIEW :
                $this->gridState = new ViewGridState($this);
                break;
            case OPERATION_EDIT :
                $this->gridState = new EditGridState($this);
                break;
            case OPERATION_VIEWALL :
                $this->gridState = new ViewAllGridState($this);
                break;
            case OPERATION_COMMIT :
                $this->gridState = new CommitGridState($this);
                break;
            case OPERATION_INSERT:
                $this->gridState = new InsertGridState($this);
                break;
            case OPERATION_COPY:
                $this->gridState = new CopyGridState($this);
                break;
            case OPERATION_COMMIT_INSERT:
                $this->gridState = new CommitNewValuesGridState($this);
                break;
            case OPERATION_DELETE:
                $this->gridState = new DeleteGridState($this);
                break;
            case OPERATION_COMMIT_DELETE:
                $this->gridState = new CommitDeleteGridState($this);
                break;
            case OPERATION_DELETE_SELECTED:
                $this->gridState = new DeleteSelectedGridState($this);
                break;
        }
    }

    function GetState()
    {
        return $this->gridState;
    }

    function GetEditPageAction()
    {
        $linkBuilder = $this->CreateLinkBuilder();
        return $linkBuilder->GetLink();
    }

    function GetReturnUrl()
    {
        $linkBuilder = $this->CreateLinkBuilder();
        $linkBuilder->AddParameter(OPERATION_PARAMNAME, 'return');
        return $linkBuilder->GetLink();
    }

    private function ExtractOrderValues()
    {
        if (GetApplication()->IsGETValueSet('order'))
        {
            $orderValue = GetApplication()->GetGETValue('order');
            $this->orderColumnFieldName = substr($orderValue, 1, strlen($orderValue) - 1);
            $this->orderType = $orderValue[0] == 'a' ? otAscending : otDescending;
            $this->SetSessionVariable('orderColumnFieldName', $this->orderColumnFieldName);
            $this->SetSessionVariable('orderType', $this->orderType);
        }
        elseif(GetOperation() == 'resetorder')
        {
            $this->UnSetSessionVariable('orderColumnFieldName');
            $this->UnSetSessionVariable('orderType');
            $this->orderColumnFieldName = null;
            $this->orderType = null;
        }
        elseif ($this->IsSessionVariableSet('orderColumnFieldName'))
        {

            $this->orderColumnFieldName = $this->GetSessionVariable('orderColumnFieldName');
            $this->orderType = $this->GetSessionVariable('orderType');
        }
    }

    private $showUpdateLink = true;

    function GetUpdateLink()
    { return $this->CreateLinkBuilder()->GetLink(); }
    function GetShowUpdateLink()
    { return $this->showUpdateLink; }
    function SetShowUpdateLink($value)
    { $this->showUpdateLink = $value; }

    function ProcessMessages()
    {
        $this->ExtractOrderValues();
        $this->SearchControl->ProcessMessages();
        $this->gridState->ProcessMessages();
    }

    function GetPrimaryKeyValuesFromGet()
    {
        $primaryKeyValues = array();
        ExtractPrimaryKeyValues($primaryKeyValues, METHOD_GET);
        return $primaryKeyValues;
    }

    function SetAllowDeleteSelected($value)
    { $this->allowDeleteSelected = $value; }
    function GetAllowDeleteSelected()
    { return $this->allowDeleteSelected; }


    function GetHighlightRowAtHover()
    { return $this->highlightRowAtHover; }
    function SetHighlightRowAtHover($value)
    { $this->highlightRowAtHover = $value; }
}
?>