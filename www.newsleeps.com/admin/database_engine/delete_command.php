<?php

class MultiStatementDeleteCommand extends EngCommand
{
    private $deleteCommands;
 
    public function __construct($statements, $engCommandImp)
    {
        parent::__construct($engCommandImp);
        $this->deleteCommands = array();
        foreach($statements as $statement)
            $this->deleteCommands[] = new CustomDeleteCommand($statement, $engCommandImp);
    }
    
    public function AddField($field, $fieldType)
    {
        foreach($this->deleteCommands as $deleteCommand)
            $deleteCommand->AddField($field, $fieldType);
    }

    public function SetKeyFieldValue($keyFieldName, $value) 
    { 
        foreach($this->deleteCommands as $deleteCommand)
            $deleteCommand->SetKeyFieldValue($keyFieldName, $value);
    }
    
    public function GetFields() 
    { 
        return $this->deleteCommands[0]->GetFields();
    }        
    public function GetValues() 
    { 
        return $this->deleteCommands[0]->GetValues();
    }    

    public function GetSQL()
    {
        $result = '';
        foreach($this->deleteCommands as $deleteCommand)
            AddStr($result, $deleteCommand->GetSQL(), ' ');
        return $result;
    }
    // </Query Building>    
    
    public function Execute($connection)
    { 
        foreach($this->deleteCommands as $deleteCommand)
            $deleteCommand->Execute($connection);
    }    
}

class CustomDeleteCommand extends EngCommand
{
    private $sql;
    private $keyFields;
    private $keyFieldValues;
 
    public function __construct($sql, $engCommandImp)
    {
        parent::__construct($engCommandImp);
        $this->sql = $sql;
        $this->keyFields = array();
        $this->keyFieldValues = array();        
    }
    
    public function AddField($field, $fieldType)
    {
        $fieldInfo = new FieldInfo('', $field, $fieldType, '');
        $this->keyFields[] = $fieldInfo;
    }

    public function SetKeyFieldValue($keyFieldName, $value) { $this->keyFieldValues[$keyFieldName] = $value; }
    
    public function GetFields() { return $this->fields; }    
    public function GetValues() { return $this->fieldValues; }    

    // <Query Building>
    private function GetFieldByName($name)
    {
        foreach($this->keyFields as $field)
            if ($field->Name == $name)
                return $field;
        return null;
    }
    
    public function GetSQL()
    {
        assert(count($this->keyFieldValues) > 0);
        $result = $this->sql;
        foreach($this->keyFieldValues as $fieldName => $value)
        {
            $result = ReplaceFirst($result, 
                ':' . $fieldName, 
                $this->GetCommandImp()->GetFieldValueAsSQLForDelete($this->GetFieldByName($fieldName), $value));
        }
        return $result;
    }
    // </Query Building>    
    
    public function Execute($connection)
    { 
        $this->GetCommandImp()->ExecuteCustomDeleteCommand($connection, $this);
    }    
}

class DeleteCommand extends EngCommand
{
    private $tableName;
    private $keyFields;
    private $keyFieldValues;

    private $setToDefaultFields;

    public function __construct($engCommandImp)
    {
        parent::__construct($engCommandImp);    
        $this->keyFields = array();
        $this->keyFieldValues = array();
    }

    public function AddField($field, $fieldType)
    {
        $fieldInfo = new FieldInfo('', $field, $fieldType, '');
        $this->keyFields[] = $fieldInfo;
    }

    public function SetKeyFieldValue($keyFieldName, $value) { $this->keyFieldValues[$keyFieldName] = $value; }
    
    public function SetTableName($value) { $this->tableName = $value; }
    public function GetTableName() { return $this->tableName; }    

    public function GetFields() { return $this->fields; }    
    public function GetValues() { return $this->fieldValues; }    

    // <Query Building>
    private function GetFieldByName($name)
    {
        foreach($this->keyFields as $field)
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

    private function GetKeyFieldCondition()
    {
        $result = '';
        foreach($this->keyFieldValues as $fieldName => $value)
        {
            AddStr($result,
                $this->GetCommandImp()->GetFilterConditionGenerator()->CreateCondition(
                    new FieldFilter($value, '='), $this->GetFieldByName($fieldName)
                    ), ' AND ');
        }
        return $result;
    }

    public function GetSQL()
    {
        assert(count($this->keyFieldValues) > 0);
        
        $result = sprintf(
            'DELETE FROM %s WHERE %s',
            $this->GetCommandImp()->QuoteTableIndetifier($this->tableName),
            $this->GetKeyFieldCondition()
        ); 
        return $result;
    }
    // </Query Building>    
    
    public function Execute($connection)
    { 
        $this->GetCommandImp()->ExecuteDeleteCommand($connection, $this);
    }    
}

?>