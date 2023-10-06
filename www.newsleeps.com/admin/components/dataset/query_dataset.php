<?php

class QueryDataset extends Dataset
{
    private $sql;
    private $insertSql;
    private $updateSql;

    function GetSelectQuery()
    { }

    function __construct($ConnectionFactory, $ConnectionParams, $sql, $insertSql, $updateSql, $deleteSql)
    {
        $this->sql = $sql;
        $this->insertSql = $insertSql;
        $this->updateSql = $updateSql;
        $this->deleteSql = $deleteSql;
        Dataset::__construct($ConnectionFactory, $ConnectionParams);
    }

    function CreateSelectCommand()
    {
        return $this->GetConnectionFactory()->CreateCustomSelectCommand($this->sql);
    }

    protected function DoCreateUpdateCommand()
    {
        $result = $this->GetConnectionFactory()->CreateCustomUpdateCommand($this->updateSql);
        foreach($this->GetFields() as $field)
            $result->AddField($field->GetName(), $field->GetEngFieldType(), $this->IsFieldPrimaryKey($field->GetName()));
        return $result;
    }

    protected function DoCreateInsertCommand()
    {
        $result = $this->GetConnectionFactory()->CreateCustomInsertCommand($this->insertSql);
        foreach($this->GetFields() as $field)
            $result->AddField($field->GetName(), $field->GetEngFieldType());
        return $result;
    }

    protected function DoCreateDeleteCommand()
    {
        $result = $this->GetConnectionFactory()->CreateCustomDeleteCommand($this->deleteSql);
        foreach($this->GetFields() as $field)
            if ($this->IsFieldPrimaryKey($field->GetName()))
                $result->AddField($field->GetName(), $field->GetEngFieldType());
        return $result;
    }

    protected function DoAddField($field)
    {
        $this->GetSelectCommand()->AddField($field->GetSourceTable(), $field->GetName(), $field->GetEngFieldType(), $field->GetAlias());
    }

    function GetFieldValueAsSQLByNameForInsert($fieldName, $value)
    {
        return $this->GetFieldByName($fieldName)->GetValueForSql($value);
    }

    function GetFieldValueAsSQLByNameForUpdate($fieldName, $value, $setToDefault = false)
    {
        return $this->GetFieldByName($fieldName)->GetValueForSql($value);
    }

    public function AddFieldFilter($fieldName, $fieldFilter)
    {
        $this->GetSelectCommand()->AddFieldFilter($fieldName, $fieldFilter);
    }

    public function AddCompositeFieldFilter($filterLinkType, $fieldNames, $fieldFilters)
    {
        $this->GetSelectCommand()->AddCompositeFieldFilter($filterLinkType, $fieldNames, $fieldFilters);
    }

    public function SetOrderBy($fieldName, $orderType)
    {
        $this->GetSelectCommand()->SetOrderBy($fieldName, $orderType);
    }

    public function AddLookupField($fieldName, $lookUpTable, $lookUpLinkField, $lookupDisplayField, $lookUpTableAlias )
    {
        parent::AddLookupField($fieldName, $lookUpTable, $lookUpLinkField, $lookupDisplayField, $lookUpTableAlias);
        $this->AddField($lookupDisplayField);
        //$this->GetSelectCommand()->AddField($lookUpTable, $lookupDisplayField->GetName(), $lookupDisplayField->GetEngFieldType(), $lookupDisplayField->GetAlias());
        $this->GetSelectCommand()->AddJoin(jkLeftOuter,
            $lookUpTable,
            $fieldName,
            $lookUpLinkField->GetName(),
            $lookUpTableAlias
        );
    }

}
?>