<?php

include_once("engine.php");
include_once("pdo_engine.php");

class MyConnectionFactory extends ConnectionFactory
{
    public function CreateConnection($AConnectionParams)
    {
        return new MyConnection($AConnectionParams);
    }

    public function CreateDataset($AConnection, $ASQL)
    {
        return new MyDataReader($AConnection, $ASQL);
    }

    function CreateEngCommandImp()
    {
        return new MyCommandImp($this);
    }
}

class MySqlIConnectionFactory extends ConnectionFactory
{
    public function CreateConnection($AConnectionParams)
    {
        return new MySqlIConnection($AConnectionParams);
    }

    public function CreateDataset($AConnection, $ASQL)
    {
        return new MySqlIDataReader($AConnection, $ASQL);
    }

    function CreateEngCommandImp()
    {
        return new MyCommandImp($this);
    }

    function CreateCustomUpdateCommand($sql)
    {
        if (is_array($sql))
            return new MultiStatementUpdateCommand($sql, $this->CreateEngCommandImp());
        else
            return parent::CreateCustomUpdateCommand($sql);
    }

    function CreateCustomInsertCommand($sql)
    {
        if (is_array($sql))
            return new MultiStatementInsertCommand($sql, $this->CreateEngCommandImp());
        else
            return parent::CreateCustomInsertCommand($sql);
    }

    function CreateCustomDeleteCommand($sql)
    {
        if (is_array($sql))
            return new MultiStatementDeleteCommand($sql, $this->CreateEngCommandImp());
        else
            return parent::CreateCustomDeleteCommand($sql);
    }
}

class MyPDOConnectionFactory extends ConnectionFactory
{
    public function CreateConnection($AConnectionParams)
    {
        return new MyPDOConnection($AConnectionParams);
    }

    public function CreateDataset($AConnection, $ASQL)
    {
        return new PDODataReader($AConnection, $ASQL);
    }

    function CreateEngCommandImp()
    {
        return new MyCommandImp($this);
    }

    function CreateCustomUpdateCommand($sql)
    {
        if (is_array($sql))
            return new MultiStatementUpdateCommand($sql, $this->CreateEngCommandImp());
        else
            return parent::CreateCustomUpdateCommand($sql);
    }

    function CreateCustomInsertCommand($sql)
    {
        if (is_array($sql))
            return new MultiStatementInsertCommand($sql, $this->CreateEngCommandImp());
        else
            return parent::CreateCustomInsertCommand($sql);
    }

    function CreateCustomDeleteCommand($sql)
    {
        if (is_array($sql))
            return new MultiStatementDeleteCommand($sql, $this->CreateEngCommandImp());
        else
            return parent::CreateCustomDeleteCommand($sql);
    }
}

class MyCommandImp extends EngCommandImp
{
    protected function GetDateTimeFieldAsSQLForSelect($fieldInfo)
    {
        $result = sprintf('DATE_FORMAT(%s, \'%s\')', $this->GetFieldFullName($fieldInfo), '%Y-%m-%d %H:%i:%S');
        return $result;
    }

    public function EscapeString($string)
    {
        return mysql_escape_string($string);
    }

    public function QuoteIndetifier($identifier)
    {
        return '`'.$identifier.'`';
    }

    public function DoExecuteCustomSelectCommand($connection, $command)
    {
        $upLimit = $command->GetUpLmit();
        $limitCount = $command->GetLimitCount();

        if (isset($upLimit) && isset($limitCount))
        {
            $sql = sprintf('SELECT * FROM (%s) a LIMIT %s, %s',
                $command->GetSQL(),
                $upLimit,
                $limitCount
            );
            $result = $this->GetConnectionFactory()->CreateDataset($connection, $sql);
            $result->Open();
            return $result;
        }
        else
        {
            return parent::DoExecuteSelectCommand($connection, $command);
        }
    }
}

class MyConnection extends EngConnection
{
    private $FServerConnecionLink;

    protected function DoConnect()
    {
        $this->FServerConnecionLink = @mysql_connect(
            $this->ConnectionParam('server') . ':' . $this->ConnectionParam('port'),
            $this->ConnectionParam('username'),
            $this->ConnectionParam('password'));

        if ($this->FServerConnecionLink)
            if (@mysql_select_db($this->ConnectionParam('database')))
            {
                if ($this->ConnectionParam('client_encoding') != '')
                    $this->ExecSQL('SET NAMES \''.$this->ConnectionParam('client_encoding').'\'');
                return true;
            }
        return false;
    }

    protected function DoDisconnect()
    {
        @mysql_close($this->FServerConnecionLink);
    }

    public function GetServerConnecionLink()
    {
        return $this->FServerConnecionLink;
    }

    public function DoExecSQL($ASQL)
    {
        if (@mysql_query($ASQL, $this->GetServerConnecionLink()))
            return true;
        else
            return false;
    }

    public function ExecScalarSQL($ASQL)
    {
        $queryHandle = @mysql_query($ASQL, $this->GetServerConnecionLink());
        $queryResult = @mysql_fetch_array($queryHandle, MYSQL_NUM);
        @mysql_free_result($queryHandle);
        return $queryResult[0];
    }

    public function ExecQueryToArray($sql, &$array)
    {
        $queryHandle = @mysql_query($sql, $this->GetServerConnecionLink());
        while($row = @mysql_fetch_array($queryHandle, MYSQL_BOTH))
            $array[] = $row;
        @mysql_free_result($queryHandle);
    }

    public function LastError()
    {
        if ($this->FServerConnecionLink)
            return mysql_error($this->FServerConnecionLink);
        else
            return mysql_error();
    }
}

class MyDataReader extends EngDataReader
{
# private
    private $FQueryResult;
    private $FLastFetchedRow;

    private $FFieldList;
    private $CurrentField;

    # protected

    function FetchField()
    {
        $Field = @mysql_fetch_field($this->FQueryResult);
        if ($Field)
            return $Field->name;
        else
            return null;
    }

    function DoOpen()
    {
        $this->FQueryResult = @mysql_query($this->GetSQL(), $this->GetConnection()->GetServerConnecionLink());
        if ($this->FQueryResult)
            return true;
        else
            return false;
    }

    # public
    function MyDataset($FMyConnection, $ASQL)
    {
        parent::EngDataset($FMyConnection, $ASQL);
        $this->FQueryResult = null;
    }

    function Opened()
    {
        return $this->FQueryResult ? true : false;
    }

    function Seek($ARowIndex)
    {
        mysql_data_seek($this->FQueryResult, $ARowIndex);
    }

    function Next()
    {
        $this->FLastFetchedRow = mysql_fetch_array($this->FQueryResult);
        return $this->FLastFetchedRow ? true : false;
    }

    function GetFieldValueByName($AFieldName)
    {
        return $this->GetActualFieldValue($AFieldName, $this->FLastFetchedRow[$AFieldName]);
    }

    function CursorPosition()
    {}
    function Prev()
    {}
    function First()
    {}
    function Last()
    {}
}


class MySqlIConnection extends EngConnection
{
    private $connectionHandle;

    protected function DoConnect()
    {
        $this->connectionHandle = @mysqli_connect(
            $this->ConnectionParam('server'),
            $this->ConnectionParam('username'),
            $this->ConnectionParam('password'),
            $this->ConnectionParam('database'),
            $this->ConnectionParam('port')
        );

        if ($this->connectionHandle)
        {
            if ($this->ConnectionParam('client_encoding') != '')
                $this->ExecSQL('SET NAMES \''.$this->ConnectionParam('client_encoding').'\'');
            return true;
        }
        return false;
    }

    protected function DoDisconnect()
    {
        @mysqli_close($this->connectionHandle);
    }

    public function GetServerConnecionLink()
    {
        return $this->connectionHandle;
    }

    public function DoExecSQL($sql)
    {
        if (@mysqli_query($this->GetServerConnecionLink(), $sql))
            return true;
        else
            return false;
    }

    public function ExecScalarSQL($sql)
    {
        $queryHandle = @mysqli_query($this->GetServerConnecionLink(), $sql);
        $queryResult = @mysqli_fetch_array($queryHandle, MYSQLI_NUM);
        @mysqli_free_result($queryHandle);
        return $queryResult[0];
    }

    public function ExecQueryToArray($sql, &$array)
    {
        $queryHandle = @mysqli_query($this->GetServerConnecionLink(), $sql);
        while($row = @mysqli_fetch_array($queryHandle, MYSQLI_BOTH))
            $array[] = $row;
        @mysqli_free_result($queryHandle);
    }

    public function LastError()
    {
        if ($this->connectionHandle)
            return mysqli_error($this->connectionHandle);
        else
            return mysqli_error();
    }
}

class MySqlIDataReader extends EngDataReader
{
# private
    private $queryResult;
    private $lastFetchedRow;

    # protected

    function FetchField()
    {
        $field = @mysqli_fetch_field($this->FQueryResult);
        if ($field)
            return $field->name;
        else
            return null;
    }

    function DoOpen()
    {
        $this->queryResult = @mysqli_query($this->GetConnection()->GetServerConnecionLink(), $this->GetSQL());
        if ($this->queryResult)
            return true;
        else
            return false;
    }

    #
    function MySqlIDataReader($connection, $sql)
    {
        parent::EngDataReader($connection, $sql);
        $this->queryResult = null;
    }

    function Opened()
    {
        return $this->queryResult ? true : false;
    }

    function Seek($rowIndex)
    {
        mysqli_data_seek($this->queryResult, $ARowIndex);
    }

    function Next()
    {
        $this->lastFetchedRow = mysqli_fetch_array($this->queryResult);
        return $this->lastFetchedRow ? true : false;
    }

    function GetFieldValueByName($fieldName)
    {
        return $this->GetActualFieldValue($fieldName, $this->lastFetchedRow[$fieldName]);
    }

    function CursorPosition()
    {}
    function Prev()
    {}
    function First()
    {}
    function Last()
    {}
}


class MyPDOConnection extends PDOConnection
{
    protected function CreatePDOConnection()
    {
        return new PDO(
        sprintf('mysql:host=%s;port=%s;dbname=%s',
        $this->ConnectionParam('server'),
        $this->ConnectionParam('port'),
        $this->ConnectionParam('database')),
        $this->ConnectionParam('username'),
        $this->ConnectionParam('password'));
    }

    protected function DoAfterConnect()
    {
        if ($this->ConnectionParam('client_encoding') != '')
            $this->ExecSQL('SET NAMES \'' . $this->ConnectionParam('client_encoding').'\'');
    }
}
?>