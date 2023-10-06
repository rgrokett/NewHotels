<?php

require_once "commands.php";

class MultiStatementUpdateCommand extends EngCommand
{
    private $updateCommands;

    public function __construct($statements, $engCommandImp)
    {
        parent::__construct($engCommandImp);
        $this->updateCommands = array();
        foreach($statements as $statement)
            $this->updateCommands[] = new CustomUpdateCommand($statement, $engCommandImp);
    }

    public function AddField($field, $fieldType, $isKeyField = false)
    {
        foreach($this->updateCommands as $updateCommand)
            $updateCommand->AddField($field, $fieldType, $isKeyField);
    }

    public function SetParameterValue($fieldName, $value, $setToDefault = false)
    {
        foreach($this->updateCommands as $updateCommand)
            $updateCommand->SetParameterValue($fieldName, $value, $setToDefault);
    }

    public function SetKeyFieldValue($keyFieldName, $value)
    {
        foreach($this->updateCommands as $updateCommand)
            $updateCommand->SetKeyFieldValue($keyFieldName, $value);
    }

    public function GetSQL()
    {
        $result = '';
        foreach($this->updateCommands as $updateCommand)
            AddStr($result, $updateCommand->GetSQL(), ' ');
        return $result;
    }

    public function Execute($connection)
    {
        foreach($this->updateCommands as $updateCommand)
            $this->GetCommandImp()->ExecutCustomUpdateCommand($connection, $updateCommand);
    }
}

class CustomUpdateCommand extends EngCommand
{
    private $sql;

    private $fields;
    private $keyFields;

    private $fieldValues;
    private $keyFieldValues;

    private $setToDefaultFields;

    public function __construct($sql, $engCommandImp)
    {
        parent::__construct($engCommandImp);
        $this->sql = $sql;
        $this->fields = array();
        $this->keyFields = array();
        $this->fieldValues = array();
        $this->keyFieldValues = array();
        $this->setToDefaultFields = array();
    }

    public function GetCustomSql()
    {
        return $this->sql;
    }

    public function SetCustomSql($value)
    {
        $this->sql = $value;
    }

    public function AddField($field, $fieldType, $isKeyField = false)
    {
        $fieldInfo = new FieldInfo('', $field, $fieldType, '');
        $this->fields[] = $fieldInfo;
        if ($isKeyField)
            $this->keyFields[] = $fieldInfo;
    }

    public function SetParameterValue($fieldName, $value, $setToDefault = false)
    {
        if ($setToDefault)
            $this->setToDefaultFields[$fieldName] = true;
        $this->fieldValues[$fieldName] = $value;
    }

    public function SetKeyFieldValue($keyFieldName, $value)
    { $this->keyFieldValues[$keyFieldName] = $value; }

    private function GetFieldByName($name)
    {
        foreach($this->fields as $field)
            if ($field->Name == $name)
                return $field;
        return null;
    }

    public function GetSQL()
    {
        $result = $this->sql;
        
        foreach($this->keyFieldValues as $fieldName => $value)
        {
            $fieldInfo = $this->GetFieldByName($fieldName);
            if (isset($fieldInfo) && isset($this->keyFieldValues[$fieldInfo->Name]))
            {
                $result = ReplaceFirst($result,
                    ':OLD_' . $fieldName,
                    $this->GetCommandImp()->GetFieldValueAsSQLForUpdate(
                    $fieldInfo,
                    $this->keyFieldValues[$fieldInfo->Name],
                    isset($this->setToDefaultFields[$fieldName])));
            }
        }

        foreach($this->fields as $fieldInfo)
        {
            if (isset($this->fieldValues[$fieldInfo->Name]))
                $result = ReplaceFirst($result, ':' . $fieldInfo->Name,
                    $this->GetCommandImp()->GetFieldValueAsSQLForUpdate(
                    $fieldInfo,
                    $this->fieldValues[$fieldInfo->Name],
                    isset($this->setToDefaultFields[$fieldName])));
        }
        return $result;
    }

    public function Execute($connection)
    {
        $this->GetCommandImp()->ExecutCustomUpdateCommand($connection, $this);
    }
}

class UpdateCommand extends EngCommand
{
    private $tableName;

    private $fields;
    private $keyFields;

    private $fieldValues;
    private $keyFieldValues;

    private $setToDefaultFields;

    public function __construct($engCommandImp)
    {
        parent::__construct($engCommandImp);
        $this->fields = array();
        $this->keyFields = array();
        $this->fieldValues = array();
        $this->keyFieldValues = array();
        $this->setToDefaultFields = array();
    }

    public function AddField($field, $fieldType, $isKeyField = false)
    {
        $fieldInfo = new FieldInfo('', $field, $fieldType, '');
        $this->fields[] = $fieldInfo;
        if ($isKeyField)
            $this->keyFields[] = $fieldInfo;
    }

    public function SetParameterValue($fieldName, $value, $setToDefault = false)
    {
        if ($setToDefault)
            $this->setToDefaultFields[$fieldName] = true;
        $this->fieldValues[$fieldName] = $value;
    }

    public function SetKeyFieldValue($keyFieldName, $value)
    { $this->keyFieldValues[$keyFieldName] = $value; }

    public function SetTableName($value)
    { $this->tableName = $value; }
    public function GetTableName()
    { return $this->tableName; }

    public function GetFields()
    { return $this->fields; }
    public function GetValues()
    { return $this->fieldValues; }


    // <Query Building>
    private function GetFieldByName($name)
    {
        foreach($this->fields as $field)
            if ($field->Name == $name)
                return $field;
        return null;
    }

    private function GetSetFieldValueClause($fieldName)
    {
        return $this->GetCommandImp()->GetSetFieldValueClause(
        $this->GetFieldByName($fieldName),
        $this->fieldValues[$fieldName],
        isset($this->setToDefaultFields[$fieldName]));
    }

    private function GetSetClause()
    {
        $result = '';
        foreach($this->fieldValues as $fieldName => $value)
            AddStr($result, $this->GetSetFieldValueClause($fieldName), ', ');
        return $result;
    }

    private function GetKeyFieldCondition()
    {
        $result = '';
        foreach($this->keyFieldValues as $fieldName => $value)
            AddStr($result,
                $this->GetCommandImp()->GetFilterConditionGenerator()->CreateCondition(
                new FieldFilter($value, '='), $this->GetFieldByName($fieldName)
                ), ' AND ');
        return $result;
    }

    public function GetSQL()
    {
        assert(count($this->keyFieldValues) > 0);

        $result = sprintf(
            'UPDATE %s SET %s WHERE %s',
            $this->GetCommandImp()->QuoteTableIndetifier($this->tableName),
            $this->GetSetClause(),
            $this->GetKeyFieldCondition()
        );
        return $result;
    }
    // </Query Building>

    public function Execute($connection)
    {
        $this->GetCommandImp()->ExecuteUpdateCommand($connection, $this);
    }
}

?>