<?php

require_once 'components/common_utils.php';
require_once 'components/error_utils.php';
require_once 'database_engine/commands.php';

abstract class ConnectionFactory
{
    abstract function CreateConnection($AConnectionParams);

    abstract function CreateDataset($AConnection, $ASQL);

    function CreateBlobEngField($fieldName)
    {
        return new BlobEngField($fieldName);
    }

    function CreateEngCommandImp()
    {
        return new EngCommandImp($this);
    }

    function CreateSelectCommand()
    {
        return new SelectCommand($this->CreateEngCommandImp());
    }

    function CreateCustomSelectCommand($sql)
    {
        return new CustomSelectCommand($sql, $this->CreateEngCommandImp());
    }

    function CreateUpdateCommand()
    {
        return new UpdateCommand($this->CreateEngCommandImp());
    }

    function CreateInsertCommand()
    {
        return new InsertCommand($this->CreateEngCommandImp());
    }

    function CreateDeleteCommand()
    {
        return new DeleteCommand($this->CreateEngCommandImp());
    }

    function CreateCustomUpdateCommand($sql)
    {
        if (is_array($sql))
            return new MultiStatementUpdateCommand($sql, $this->CreateEngCommandImp());
        else
        return new CustomUpdateCommand($sql, $this->CreateEngCommandImp());
    }

    function CreateCustomInsertCommand($sql)
    {
        if (is_array($sql))
            return new MultiStatementInsertCommand($sql, $this->CreateEngCommandImp());
        else
        return new CustomInsertCommand($sql, $this->CreateEngCommandImp());
    }

    function CreateCustomDeleteCommand($sql)
    {
        if (is_array($sql))
            return new MultiStatementDeleteCommand($sql, $this->CreateEngCommandImp());
        else
        return new CustomDeleteCommand($sql, $this->CreateEngCommandImp());
    }
}

abstract class EngConnection
{
    private $FConnectionParams;
    private $FConnected;

    public $OnAfterConnect;

    protected abstract function DoConnect();

    protected abstract function DoDisconnect();

    function ConnectionParam($paramName)
    {
        return isset($this->FConnectionParams[$paramName]) ? $this->FConnectionParams[$paramName] : '';
    }

    protected function FormatConnectionParams()
    {
        return $this->ConnectionParam('server');
    }

    public function EngConnection($AConnectionParams)
    {
        $this->FConnectionParams = $AConnectionParams;
        $this->OnAfterConnect = new Event();
    }

    public function IsDriverSupported()
    {
        return true;
    }

    public function GetDriverNotSupportedMessage()
    {
        return '';
    }

    public function GetClientEncoding()
    { return $this->clientEncoding; }
    public function SetClientEncoding($value)
    { $this->clientEncoding = $value; }

    public function Connected()
    { return $this->FConnected; }

    public function DoExecSQL($ASQL)
    { }

    public function ExecSQL($ASQL)
    {
        if (!$this->DoExecSQL($ASQL))
            RaiseError($this->LastError());
    }

    public abstract function ExecScalarSQL($ASQL);

    public abstract function ExecQueryToArray($sql, &$array);

    public function Connect()
    {
        if (!$this->Connected())
        {
            if (!$this->IsDriverSupported())
            {
                RaiseError(sprintf('Could not connect to %s: %s',
                    $this->FormatConnectionParams(),
                    $this->LastError()
                ));
            }
            else
            {
                $this->FConnected = $this->DoConnect();
                if(!$this->FConnected)
                {
                    RaiseError( sprintf('Could not connect to %s: %s',
                        $this->FormatConnectionParams(),
                        $this->LastError()
                    ));
                }
                else
                {
                    $this->OnAfterConnect->Fire(array(&$this));
                }
            }
        }
    }

    public function Disconnect()
    {
        if ($this->Connected())
        {
            $this->DoDisconnect();
            $this->FConnected = false;
        }
    }

    public function DoLastError()
    {
        return '';
    }

    public function LastError()
    {
        if (!$this->IsDriverSupported())
            return $this->GetDriverNotSupportedMessage();
        else
            return $this->DoLastError();
    }
}

class EngDataReader
{
    private $FSQL;
    private $FConnection;
    private $FFieldList;
    private $rowLimit;
    private $fieldInfos;

    function ClearFields()
    {
        $this->FFieldList = array();
    }

    protected function GetFieldIndexByName($fieldName)
    {
        return array_search($fieldName, $this->FFieldList);
    }

    function AddField($AField)
    {
        $this->FFieldList[] = $AField;
    }

    function AddFieldInfo($fieldInfo)
    {
        if (isset($fieldInfo->Alias))
            $this->fieldInfos[$fieldInfo->Alias] = $fieldInfo;
        else
            $this->fieldInfos[$fieldInfo->Name] = $fieldInfo;
    }

    function GetFieldInfoByFieldName(&$fieldName)
    {
        if (isset($this->fieldInfos[$fieldName]))
            return $this->fieldInfos[$fieldName];
        else
            return null;
    }

    #protected
    function DoOpen() # virtual; abstract;

    { }

    function DoClose() # virtual; abstract;

    { }

    function FetchField() # virtual; abstract;

    { }

    function FetchFields()
    {
        $Field = $this->FetchField();
        while ($Field)
        {
            $this->AddField($Field);
            $Field = $this->FetchField();
        }
    }

    #public
    function EngDataReader($AConnection, $ASQL)
    {
        $this->fieldInfos = array();
        $this->FConnection = $AConnection;
        $this->FSQL = $ASQL;
        $this->FFieldList = array();
        $this->rowLimit = -1;
    }

    function GetSQL()
    {
        return $this->FSQL;
    }

    function SetSQL($ASQL)
    {
        $this->FSQL = $ASQL;
    }

    function GetConnection()
    {
        return $this->FConnection;
    }

    function Open()
    {
        if (!$this->Opened())
        {
            $this->ClearFields();
            if (!$this->DoOpen())
            {
                RaiseError($this->LastError());
            }
            if ($this->Opened())
            {
                $this->FetchFields();
            }
        }
    }

    function Close()
    {
        if ($this->Opened())
            $this->DoClose();
    }

    function Opened() # virtual; abstract;

    { }

    function FieldCount()
    {
        return count($this->FFieldList);
    }

    function GetField($AIndex)
    {
        return $this->FFieldList[$AIndex];
    }

    protected function LastError()
    {
        return $this->GetConnection()->LastError();
    }

    function GetDateTimeFieldValueByName(&$value)
    {
        if (isset($value))
            return SMDateTime::Parse($value, '%Y-%m-%d %H:%M:%S');
        else
            return null;
    }

    function GetDateFieldValueByName(&$value)
    {
        if (isset($value))
            return SMDateTime::Parse($value, '%Y-%m-%d');
        else
            return null;
    }

    function GetTimeFieldValueByName(&$value)
    {
        if (isset($value))
            return SMDateTime::Parse($value, '%H:%M:%S');
        else
            return null;
    }

    function GetActualFieldValue(&$fieldName, $value)
    {
        $fieldInfo = $this->GetFieldInfoByFieldName($fieldName);
        if (!isset($fieldInfo))
            return $value;
        if ($fieldInfo->FieldType == ftDateTime)
            return $this->GetDateTimeFieldValueByName($value);
        elseif ($fieldInfo->FieldType == ftDate)
            return $this->GetDateFieldValueByName($value);
        elseif ($fieldInfo->FieldType == ftTime)
            return $this->GetTimeFieldValueByName($value);
        else
        {
            return $value;
        }
    }

    function SetRowLimit($value)
    { $this->rowLimit = $value; }
    function GetRowLimit()
    { return $this->rowLimit; }

    function CursorPosition()
    {}
    function Next()
    {}
    function Prev()
    {}
    function First()
    {}
    function Last()
    {}
}

class EngField
{
    private $fieldName;

    public function __construct($fieldName)
    {
        $this->sourceTable = $sourceTable;
        $this->fieldName = $fieldName;
        $this->alias = $alias;
    }

    private function GetName()
    {
        return $this->fieldName;
    }

    public function AsSQL()
    {
        return $this->GetName();
    }

    public function MakeEqualCondition($value)
    {
        return $this->fieldName . ' = ' . $value;
    }

    public function GetValueAsSQL($value)
    {
        if (isset($value))
            return $value;
        else
            return 'NULL';
    }

    public function GetSetValueClause($value, $default = false)
    {
        if ($value == null)
        {
            if ($default)
                return $this->AsSQL() . ' = DEFAULT';
            else
                return $this->AsSQL() .' = NULL';
        }
        else
            return $this->AsSQL() . ' = ' . $this->GetValueAsSQL($value);
    }

}

?>