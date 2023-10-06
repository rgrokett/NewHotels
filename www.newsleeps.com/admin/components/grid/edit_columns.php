<?php

class SMException extends Exception
{
    public function getLocalizedMessage($captions)
    {
        return $this->getMessage();
    }
}

class FileSizeExceedMaxSize extends SMException
{
    private $fieldName;
    private $actualFileSize;
    private $maxSize;

    public function  __construct($fieldName, $actualFileSize, $maxSize)
    {
        parent::__construct('', 0);
        $this->fieldName = $fieldName;
        $this->actualFileSize = $actualFileSize;
        $this->maxSize = $maxSize;
    }

    public function GetFieldName()
    {
        return $this->fieldName;
    }

    public function getLocalizedMessage($captions)
    {
        return sprintf($captions->GetMessageString('FileSizeExceedMaxSizeForField'), $this->fieldName, $this->actualFileSize, $this->maxSize);
    }
}

class ImageSizeExceedMaxSize extends SMException
{
    private $fieldName;
    private $actualWidth;
    private $actualHeight;
    private $maxWidth;
    private $maxHeight;

    public function  __construct($fieldName, $actualWidth, $actualHeight, $maxWidth, $maxHeight)
    {
        parent::__construct('', 0);
        $this->fieldName = $fieldName;
        $this->actualWidth = $actualWidth;
        $this->actualHeight = $actualHeight;
        $this->maxWidth = $maxWidth;
        $this->maxHeight = $maxHeight;
    }

    public function GetFieldName()
    {
        return $this->fieldName;
    }

    public function getLocalizedMessage($captions)
    {
        return sprintf($captions->GetMessageString('ImageSizeExceedMaxSizeForField'), $this->fieldName, $this->actualWidth, $this->actualHeight, $this->maxWidth, $this->maxHeight);
    }
}

abstract class AsbtractValidator
{
    private $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function GetMessage()
    { return $this->message; }

    abstract public function GetJSValidatorClass();

    public function GetCreateJSValidator()
    {
        return sprintf('new %s(\'%s\')', $this->GetJSValidatorClass(), $this->GetMessage());
    }

}

class NotEmptyValidator extends AsbtractValidator
{
    public function GetJSValidatorClass()
    {
        return 'NotEmptyValidator';
    }
}

class CustomEditColumn
{
    private $caption;
    private $editControl;
    private $fieldName;
    private $dataset;
    private $grid;
    private $validators;

    private $allowSetToNull;
    private $allowSetToDefault;
    private $insertDefaultValue;

    private $commitOperations = array(OPERATION_COMMIT, OPERATION_COMMIT_INSERT);
    private $editOperations = array(OPERATION_EDIT, OPERATION_INSERT, OPERATION_COPY);
    private $fieldIsReadOnly;

    public function __construct($caption, $fieldName, $editControl, $dataset, $allowSetToNull = false, $allowSetToDefault = false)
    {
        $this->caption = $caption;
        $this->editControl = $editControl;
        if ($dataset->GetFieldByName($fieldName) != null)
            $this->fieldIsReadOnly = $dataset->GetFieldByName($fieldName)->GetReadOnly();
        else
            $this->fieldIsReadOnly = true;
        $this->editControl->SetReadOnly($this->fieldIsReadOnly);

        $this->fieldName = $fieldName;
        $this->dataset = $dataset;
        $this->allowSetToNull = $allowSetToNull;
        $this->allowSetToDefault = $allowSetToDefault;
        $this->validators = array();
    }

    public function GetFieldName()
    { return $this->fieldName; }
    public function GetCaption()
    { return $this->caption; }
    protected function GetEditControl()
    { return $this->editControl; }
    public function GetDataset()
    { return $this->dataset; }

    public function GetAllowSetToNull()
    { return $this->allowSetToNull && !$this->fieldIsReadOnly; }
    public function SetAllowSetToNull($value)
    { $this->allowSetToNull = $value; }

    public function GetAllowSetToDefault()
    { return $this->allowSetToDefault && !$this->fieldIsReadOnly; }
    public function SetAllowSetToDefault($value)
    { $this->allowSetToDefault = $value; }

    public function GetInsertDefaultValue()
    { return $this->insertDefaultValue; }
    public function SetInsertDefaultValue($value)
    { $this->insertDefaultValue = $value; }

    public function GetGrid()
    { return $this->grid; }
    public function SetGrid($value)
    {
        $this->grid = $value;
        $this->caption = $this->grid->GetPage()->RenderText($this->caption);
    }

    public function Accept($renderer)
    {
        $this->editControl->Accept($renderer);
    }

    public function GetSetToNullFromPost()
    {
        return GetApplication()->IsPOSTValueSet($this->GetFieldName() . '_null') && GetApplication()->GetPOSTValue($this->GetFieldName() . '_null') == 1;;
    }

    public function GetSetToDefaultFromPost()
    {
        return GetApplication()->IsPOSTValueSet($this->GetFieldName() . '_def') && GetApplication()->GetPOSTValue($this->GetFieldName() . '_def') == 1;;
    }

    public function SetControlValuesFromPost()
    {
        $valueChanged = true;
        $value = $this->editControl->ExtractsValueFromPost($valueChanged);
        $this->editControl->SetValue($value);
    }

    public function PrepareEditorControl()
    { }

    protected function CheckValueIsCorrect($value)
    { }

    public function SetDatasetValuesFromPost()
    {
        $valueChanged = true;
        $value = $this->editControl->ExtractsValueFromPost($valueChanged);
        // TODO : Операция не на месте
        $this->SetControlValuesFromPost();

        $this->CheckValueIsCorrect($value);

        if ($valueChanged)
        {
            if ($this->GetSetToNullFromPost())
                $this->dataset->SetFieldValueByName($this->GetFieldName(), null);
            elseif ($this->GetSetToDefaultFromPost())
                $this->dataset->SetFieldValueByName($this->GetFieldName(), null, true);
            else
                $this->dataset->SetFieldValueByName($this->GetFieldName(), $value);
        }
    }

    public function IsValueNull()
    {
        if (GetOperation() == OPERATION_INSERT)
            return false;
        else
        {
            $value = $this->dataset->GetFieldValueByName($this->GetFieldName());
            return !isset($value);
        }
    }

    public function IsValueSetToDefault()
    {
        return $this->GetDataset()->GetFieldByName($this->GetFieldName())->GetIsAutoincrement();
    }

    public function SetControlValuesFromDataset()
    {
        if (!$this->dataset->GetFieldByName($this->fieldName)->GetReadOnly())
        {
            if (GetOperation() == OPERATION_EDIT)
            { 
                $this->editControl->SetValue(
                    $this->dataset->GetFieldValueByName($this->GetFieldName())
                );
            }
            elseif (GetOperation() == OPERATION_COPY)
            {
                $this->editControl->SetValue(
                    $this->dataset->GetFieldValueByName($this->GetFieldName())
                );
                $masterFieldValue = $this->dataset->GetMasterFieldValueByName($this->fieldName);
                if (isset($masterFieldValue))
                    $this->editControl->SetValue($masterFieldValue);

            }
            elseif (GetOperation() == OPERATION_INSERT)
            {
                $masterFieldValue = $this->dataset->GetMasterFieldValueByName($this->fieldName);
                if (!isset($masterFieldValue))
                    $this->editControl->SetValue($this->GetInsertDefaultValue());
                else
                    $this->editControl->SetValue($masterFieldValue);
            }
        }
        else
        {
            $this->editControl->SetValue(
                $this->dataset->GetFieldByName($this->fieldName)->GetDefaultValue());
        }
    }

    public function ProcessMessages()
    {
        $operation = GetOperation();
        if (in_array($operation, $this->commitOperations))
            $this->SetDatasetValuesFromPost();
        elseif(in_array($operation, $this->editOperations))
            $this->SetControlValuesFromDataset();
    }

    public function GetCreateJSControlAdapter()
    {
        return $this->GetEditControl()->GetCreateJSControlAdapter($this->fieldName);
    }

    public function GetValidators()
    { return $this->validators; }
    public function AddValidator($value)
    { $this->validators[] = $value; }
}

class LookUpEditColumn extends CustomEditColumn
{
    private $linkFieldName;
    private $displayFieldName;
    private $lookUpDataset;

    public function __construct($caption, $fieldName, $editControl, $dataset,
        $linkFieldName, $displayFieldName, $lookUpDataset)
    {
        parent::__construct($caption, $fieldName, $editControl, $dataset);
        $this->linkFieldName = $linkFieldName;
        $this->displayFieldName = $displayFieldName;
        $this->lookUpDataset = $lookUpDataset;
    }

    private function GetLookupValues()
    {
        $result = array();
        $this->lookUpDataset->Open();
        while ($this->lookUpDataset->Next())
        {
            $result[$this->lookUpDataset->GetFieldValueByName($this->linkFieldName)] =
                $this->lookUpDataset->GetFieldValueByName($this->displayFieldName);
        }
        $this->lookUpDataset->Close();

        return $result;
    }

    public function IsValueNull()
    {
        if (GetOperation() == OPERATION_INSERT)
            return false;
        else
        {
            $value = $this->GetDataset()->GetFieldValueByName($this->GetFieldName());
            return !isset($value);
        }
    }

    public function PrepareEditorControl()
    {
        foreach($this->GetLookupValues() as $name => $value)
            $this->GetEditControl()->AddValue($name, $value);
    }

    public function SetControlValuesFromDataset()
    {
        $this->PrepareEditorControl();
        parent::SetControlValuesFromDataset();
    }
}

class FileUploadingColumn extends CustomEditColumn
{
    private $handlerName;
    private $sizeCheckEnabled;
    private $imageSizeCheckEnabled;
    private $maxSize;
    private $maxWidth;
    private $maxHeight;

    public function __construct($caption, $fieldName, $editControl, $dataset, $allowSetToNull = false, $allowSetToDefault = false, $handlerName = '')
    {
        parent::__construct($caption, $fieldName, $editControl, $dataset, $allowSetToNull, $allowSetToDefault);
        $this->handlerName = $handlerName;
        $this->sizeCheckEnabled = false;
        $this->maxSize = 0;

        $this->imageSizeCheckEnabled = false;
        $this->maxWidth = 0;
        $this->maxHeight = 0;
    }

    public function GetFullImageLink()
    {
        if (GetOperation() == OPERATION_EDIT)
        {
            $result = $this->GetGrid()->CreateLinkBuilder();
            $result->AddParameter('hname', $this->handlerName);
            $result->AddParameter('large', '1');
            AddPrimaryKeyParameters($result, $this->GetDataset()->GetPrimaryKeyValues());
            return $result->GetLink();
        }
    }

    public function SetFileSizeCheckMode($enabled, $maxSize = 0)
    {
        if ($enabled && $maxSize <= 0)
            $this->sizeCheckEnabled = false;
        else
        {
            $this->sizeCheckEnabled = $enabled;
            $this->maxSize = $maxSize;
        }
    }

    public function SetImageSizeCheckMode($enabled, $maxWidth, $maxHeight)
    {
        if ($enabled && ($maxWidth <= 0) || ($maxHeight <= 0))
        {
            $this->imageSizeCheckEnabled = false;
            $this->maxWidth = 0;
            $this->maxHeight = 0;
        }
        else
        {
            $this->imageSizeCheckEnabled = $enabled;
            $this->maxWidth = $maxWidth;
            $this->maxHeight = $maxHeight;
        }
    }

    protected function CheckValueIsCorrect($value)
    {
        $filename = $value;
        if ($this->sizeCheckEnabled)
        {
            if (filesize($filename) > $this->maxSize)
                throw new FileSizeExceedMaxSize($this->GetFieldName(), filesize($filename), $this->maxSize);
        }
        if ($this->imageSizeCheckEnabled)
        {
            if (!CheckImageSize($filename, $this->maxWidth, $this->maxHeight))
            {
                list($actualWidth, $actualHeight) = SMGetImageSize($filename);
                throw new ImageSizeExceedMaxSize($this->GetFieldName(), $actualWidth, $actualHeight, $this->maxWidth, $this->maxHeight);
            }
        }
    }

    public function IsValueNull()
    {
        return false;
    }

    public function SetControlValuesFromPost()
    {
        $this->GetEditControl()->SetLink($this->GetFullImageLink());
    }

    public function PrepareEditorControl()
    {
        if (GetOperation() == OPERATION_EDIT)
            $this->GetEditControl()->SetLink($this->GetFullImageLink());
    }

    public function SetControlValuesFromDataset()
    {
        $this->PrepareEditorControl();
    }
}

class UploadFileToFolderColumn extends CustomEditColumn
{
    private $targetFolder;
    public $OnCustomFileName;

    public function __construct($caption, $fieldName, $editControl, $dataset, $allowSetToNull = false, $allowSetToDefault = false, $targetFolder = '', $fileExtension = '')
    {
        parent::__construct($caption, $fieldName, $editControl, $dataset, $allowSetToNull, $allowSetToDefault);
        $this->targetFolder = $targetFolder;
        $this->OnCustomFileName = new Event();
    }

    public function GetFullImageLink()
    {
        if (GetOperation() == OPERATION_EDIT)
        {
            $value = $this->GetDataset()->GetFieldValueByName($this->GetFieldName());
            return $value;
        }
    }

    public function IsValueNull()
    { return false; }

    private function GetNewFileName()
    {
        $result = '';
        $handled = false;
        $this->OnCustomFileName->Fire(array(&$result, &$handled));

        if (!$handled)
        {
            $filename = rand();
            $result = Path::Combine($this->targetFolder, $filename);

            while (file_exists($result))
            {
                $filename = rand();
                $result = Path::Combine($this->targetFolder, $filename);
            }
        }

        return $result;
    }

    public function SetDatasetValuesFromPost()
    {
        $valueChanged = true;

        $value = $this->GetEditControl()->ExtractsValueFromPost($valueChanged);
        $target = $this->GetNewFileName();

        if ($valueChanged)
        {
            move_uploaded_file($value, $target);

            if ($this->GetSetToNullFromPost())
                $this->GetDataset()->SetFieldValueByName($this->GetFieldName(), null);
            elseif ($this->GetSetToDefaultFromPost())
                $this->GetDataset()->SetFieldValueByName($this->GetFieldName(), null, true);
            else
                $this->GetDataset()->SetFieldValueByName($this->GetFieldName(), $target);
        }

    }

    public function SetControlValuesFromPost()
    {
        $this->GetEditControl()->SetLink($this->GetFullImageLink());
    }

    public function PrepareEditorControl()
    {
        if (GetOperation() == OPERATION_EDIT)
            $this->GetEditControl()->SetLink($this->GetFullImageLink());
    }
    
    public function SetControlValuesFromDataset()
    {
        $this->PrepareEditorControl();
    }
}

?>
