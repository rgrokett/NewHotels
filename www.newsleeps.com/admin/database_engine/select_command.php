<?php

class JoinInfo
{
    public $JoinKind;
    public $Table;
    public $Field;
    public $LinkField;
    public $TableAlias;
    
    public function __construct($joinKind, $table, $field, $linkField, $tableAlias)
    {
        $this->JoinKind = $joinKind;
        $this->Table = $table;
        $this->Field = $field;
        $this->LinkField = $linkField;
        $this->TableAlias = $tableAlias;
    }
}

class CustomSelectCommand extends EngCommand
{
    private $sql;
    private $fields;
    private $joins; # List<JoinInfo>
    private $upLmit;
    private $limitCount;
    private $fieldFilters;    
    private $compositeFieldFilters;    
    private $customConditions;
    private $orederByFields;

    public function __construct($sql, $engCommandImp)
    {
        parent::__construct($engCommandImp);
        $this->sql = $sql;
        $this->fieldFilters = array();
        $this->compositeFieldFilters = array();
        $this->joins = array();
        $this->orederByFields = array();
        $this->customConditions = array();
    }

    public function AddField($tableName, $fieldName, $fieldType, $alias)
    {
        if (!isset($tableName) || $tableName == '')
            $this->fields[] = new FieldInfo('SM_SOURCE_SQL', $fieldName, $fieldType, $alias);
        else
            $this->fields[] = new FieldInfo($tableName, $fieldName, $fieldType, $alias);
    }
    
    public function GetFields() { return $this->fields; } 

    private function GetFieldByName($name)
    {
        foreach($this->fields as $field)
            if (isset($field->Alias) && $field->Alias != '' && $field->Alias == $name)
                return $field;
            elseif ($field->Name == $name)
                return $field;
        return null;
    }

    public function GetSQL()
    {
        $condition = $this->GetFieldFilterCondition();     
        if (!empty($condition) || count($this->joins) > 0 || count($this->orederByFields) > 0)
        {
        $fieldList = '';
        foreach($this->fields as $field)
            AddStr($fieldList, $this->GetCommandImp()->GetFieldAsSQLInSelectFieldList($field), ', ');
     
        $result = 'SELECT '.$fieldList.' FROM (' . $this->sql . ') SM_SOURCE_SQL';
        AddStr($result, $this->CreateJoinsClause(), ' ');
        AddStr($result, $condition, ' WHERE ');                
        
        foreach($this->orederByFields as $fieldName => $orderType)
            AddStr($result, $this->GetCommandImp()->GetFieldFullName($this->GetFieldByName($fieldName)) . ' ' . $orderType , ' ORDER BY  ');
        
        }
        else
            $result = $this->sql;
        ///echo $result;
        return $result;
    }

    private function CreateJoinsClause()
    {
        $result = '';
        foreach($this->joins as $joinInfo)
            AddStr($result, $this->GetCommandImp()->CreateJoinClause($joinInfo), ' ');
        return $result;
    }
    
    public function SetPrimaryKeyFilter($primaryKeyValues)
    {
        $this->primaryKeyFilter = $primaryKeyValues;
    }
    
    public function Execute($connection)
    {
        return $this->GetCommandImp()->ExecuteCustomSelectCommand($connection, $this);        
    }
    
    public function AddJoin($joinKind, $table, $fieldName, $linkField, $tableAlias = null)
    {
        $this->joins[] = new JoinInfo(
            $joinKind, $table,
            $this->GetFieldByName($fieldName), 
            $linkField,
            $tableAlias);
    }
    
    public function GetSelectRecordCountSQL()
    {
        $condition = $this->GetFieldFilterCondition();     
        $result = 'SELECT COUNT(*) FROM (' . $this->sql . ') SM_SOURCE_SQL';
        AddStr($result, $this->CreateJoinsClause(), ' ');
        AddStr($result, $condition, ' WHERE ');        
        return $result;
    }    
    
    public function GetUpLmit() { return $this->upLmit; }
    public function SetUpLimit($upLimit) { $this->upLmit = $upLimit; }
    
    public function GetLimitCount() { return $this->limitCount; }
    public function SetLimitCount($limitCount) { $this->limitCount = $limitCount; }
    
    private function GetFieldFilterCondition()
    {
        $result = '';
        foreach($this->fieldFilters as $fieldName => $filters)
            foreach($filters as $filter)
                AddStr($result,
                    $this->GetCommandImp()->GetFilterConditionGenerator()->CreateCondition(
                        $filter, $this->GetFieldByName($fieldName)),
                    ' AND ');

        foreach($this->compositeFieldFilters as $filter)
        {
            AddStr($result,
                '(' . $this->GetCommandImp()->GetFilterConditionGenerator()->CreateCondition($filter, null) . ')',
                ' AND ');
        }

        foreach($this->customConditions as $condition)
            AddStr($result, '(' . $condition . ')', ' AND ');

        return $result;
    }
    
    public function SetOrderBy($fieldName, $orderType)
    {
        $this->orederByFields[$fieldName] = $orderType;
    }    
    
    public function AddFieldFilter($fieldName, $fieldFilter)
    {
        $this->fieldFilters[$fieldName][] = $fieldFilter;
    }    
    
    public function RemoveFieldFilter($fieldName, $fieldFilter)
    {
        unset($this->fieldFilters[$fieldName][array_search($fieldFilter, $this->fieldFilters[$fieldName])]);
    }

    public function AddCompositeFieldFilter($filterLinkType, $fieldNames, $fieldFilters)
    {
        $compositeFilter = new CompositeFilter($filterLinkType);
        for($i = 0; $i < count($fieldNames); $i++)
            $compositeFilter->AddFilter(
                $this->GetFieldByName($fieldNames[$i]),
                $fieldFilters[$i]);
        $this->compositeFieldFilters[] = $compositeFilter;
    }    

    public function AddCustomCondition($condition)
    {
        $this->customConditions[] = $condition;
    }
}

class SelectCommand extends EngCommand
{
    private $fields; # List<FieldInfo>
    private $joins; # List<JoinInfo>
    //
    private $sourceTable;
    private $sourceTableAlias;
    //
    private $primaryKeyFilter;
    private $fieldFilters;
    private $compositeFieldFilters;
    private $upLmit;
    private $limitCount;
    private $customConditions;
    private $orederByFields;

    public function __construct($engCommandImp)
    {
        parent::__construct($engCommandImp);
        $this->fields = array();
        $this->primaryKeyFilter = array();
        $this->fieldFilters = array();
        $this->compositeFieldFilters = array();
        $this->orederByFields = array();
        $this->joins = array();
        $this->customConditions = array();
    }

    public function GetUpLmit() { return $this->upLmit; }
    public function SetUpLimit($upLimit) { $this->upLmit = $upLimit; }
    
    public function GetLimitCount() { return $this->limitCount; }
    public function SetLimitCount($limitCount) { $this->limitCount = $limitCount; }
    
    public function SetSourceTableName($sourceTable, $sourceTableAlias = null)
    {
        $this->sourceTable = $sourceTable;
        $this->sourceTableAlias = $sourceTableAlias;
    }

    public function AddField($tableName, $fieldName, $fieldType, $alias)
    {
        $this->fields[] = new FieldInfo($tableName, $fieldName, $fieldType, $alias);
    }
    
    public function GetFields() { return $this->fields; } 

    private function GetFieldByName($name)
    {
        foreach($this->fields as $field)
            if (isset($field->Alias) && $field->Alias != '' && $field->Alias == $name)
                return $field;
            elseif ($field->Name == $name)
                return $field;
        return null;
    }

    private function GetFieldFilterCondition()
    {
        $result = '';
        foreach($this->fieldFilters as $fieldName => $filters)
            foreach($filters as $filter)
                AddStr($result,
                    $this->GetCommandImp()->GetFilterConditionGenerator()->CreateCondition(
                        $filter, $this->GetFieldByName($fieldName)),
                    ' AND ');

        foreach($this->compositeFieldFilters as $filter)
        {
            AddStr($result,
                '(' . $this->GetCommandImp()->GetFilterConditionGenerator()->CreateCondition($filter, null) . ')',
                ' AND ');
        }

        foreach($this->customConditions as $condition)
            AddStr($result, '(' . $condition . ')', ' AND ');

        return $result;
    }

    protected function DoGetLimitClause($limitCount, $upLimit)
    {
        return $this->GetCommandImp()->GetLimitClause($limitCount, $upLimit);
    }

    public function GetLimitClause()
    {
        $result = '';
        if (isset($this->upLmit) && isset($this->limitCount))
        {
            if($this->limitCount <= 0)
                $this->limitCount = 1;
            if ($this->upLmit < 0)
                $this->upLmit = 0;                  
            $result = $this->DoGetLimitClause($this->limitCount, $this->upLmit);
        }
        return $result;
    }

    private function CreateJoinsClause()
    {
        $result = '';
        foreach($this->joins as $joinInfo)
            AddStr($result, $this->GetCommandImp()->CreateJoinClause($joinInfo), ' ');
        return $result;
    }

    public function GetSQL()
    {
        $fieldList = '';
        foreach($this->fields as $field)
            AddStr($fieldList, $this->GetCommandImp()->GetFieldAsSQLInSelectFieldList($field), ', ');

        $condition = $this->GetFieldFilterCondition();
        $afterSelectSql = $this->GetCommandImp()->GetAfterSelectSQL($this);
        if ($afterSelectSql != '')
            $afterSelectSql = ' ' . $afterSelectSql;

        $result = "SELECT$afterSelectSql $fieldList FROM " . $this->GetCommandImp()->QuoteTableIndetifier($this->sourceTable) .
            ((isset($this->sourceTableAlias) && $this->sourceTableAlias != '') ? ' ' . $this->sourceTableAlias : '');
        AddStr($result, $this->CreateJoinsClause(), ' ');
        AddStr($result, $condition, ' WHERE ');

        foreach($this->orederByFields as $fieldName => $orderType)
            AddStr($result, $this->GetCommandImp()->GetFieldFullName($this->GetFieldByName($fieldName)) . ' ' . $orderType , ' ORDER BY  ');

        AddStr($result, $this->GetLimitClause(), ' ');
        return $result;
    }

    public function GetSelectRecordCountSQL()
    {
        $condition = '';
        AddStr($condition, $this->GetFieldFilterCondition(), ' AND ');

        $result = 'SELECT COUNT(*) FROM ' . $this->GetCommandImp()->QuoteTableIndetifier($this->sourceTable);
        AddStr($result, $this->CreateJoinsClause(), ' ');
        AddStr($result, $condition, ' WHERE ');

        return $result;
    }

    public function AddFieldFilter($fieldName, $fieldFilter)
    {
        $this->fieldFilters[$fieldName][] = $fieldFilter;
    }

    public function ClearFieldFilters()
    {
        foreach($this->fieldFilters as $fieldName => $filterArray)
            unset($this->fieldFilters[$fieldName]);
    }

    public function AddCustomCondition($condition)
    {
        $this->customConditions[] = $condition;
    }

    public function RemoveFieldFilter($fieldName, $fieldFilter)
    {
        unset($this->fieldFilters[$fieldName][array_search($fieldFilter, $this->fieldFilters[$fieldName])]);
    }

    public function AddCompositeFieldFilter($filterLinkType, $fieldNames, $fieldFilters)
    {
        $compositeFilter = new CompositeFilter($filterLinkType);
        for($i = 0; $i < count($fieldNames); $i++)
            $compositeFilter->AddFilter(
                $this->GetFieldByName($fieldNames[$i]),
                $fieldFilters[$i]);
        $this->compositeFieldFilters[] = $compositeFilter;
    }

    public function SetOrderBy($fieldName, $orderType)
    {
        $this->orederByFields[$fieldName] = $orderType;
    }

    public function AddJoin($joinKind, $table, $fieldName, $linkField, $tableAlias = null)
    {
        $this->joins[] = new JoinInfo(
            $joinKind, $table,
            $this->GetFieldByName($fieldName),
            $linkField,
            $tableAlias);
    }
    
    public function Execute($connection)
    {
        return $this->GetCommandImp()->ExecuteSelectCommand($connection, $this);
    }
} 
?>