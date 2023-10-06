<?php
    require_once 'security_info.php';
    require_once 'database_engine/engine.php';
    require_once 'components/common_utils.php';
    require_once 'components/dataset/dataset.php';
    require_once 'components/dataset/table_dataset.php';

    class TableBasedUserAuthorization extends AbstractUserAuthorization
    {
        private $rolesSecurityInfo;
        private $userRoles;
        private $usersTable;
        private $userNameFieldName;
        private $userIdFieldName;
        private $dataset;

        private $guestUserName;
	    private $defaultUserName;

        public function __construct($connectionFactory, $rolesSecurityInfo, $userRoles,
            $connectionOptions, $usersTable, $userNameFieldName, $userIdFieldName,
            $guestUserName = 'guest', 
    		$defaultUserName = 'defaultUser')
        {
            $this->rolesSecurityInfo = $rolesSecurityInfo;
            $this->userRoles = $userRoles;
            $this->usersTable = $usersTable;
            $this->userIdFieldName = $userIdFieldName;
            $this->userNameFieldName = $userNameFieldName;
	        $this->guestUserName = $guestUserName;
	        $this->defaultUserName = $defaultUserName;


            $this->dataset = new TableDataset(
                $connectionFactory,
                $connectionOptions,
                $usersTable);
            $field = new StringField($userNameFieldName);
            $this->dataset->AddField($field, true);
            $field = new StringField($userIdFieldName);
            $this->dataset->AddField($field, false);
        }

        public function GetRoleSecurityInfo($role)
        {
            return $this->rolesSecurityInfo[$role];
        }

        public function GetCurrentUserId()
        {
            $result = null;
            $this->dataset->AddFieldFilter(
                $this->userNameFieldName,
                new FieldFilter($this->GetCurrentUser(), '=', true));
            $this->dataset->Open();
            if ($this->dataset->Next())
                $result = $this->dataset->GetFieldValueByName($this->userIdFieldName);
            $this->dataset->Close();
            $this->dataset->ClearFieldFilters();
            return $result;
        }

        public function GetCurrentUser()
        {
            return GetCurrentUser();
        }

        public function IsCurrentUserLoggedIn()
        {
            return $this->GetCurrentUser() != 'guest';
        }

        public function GetUserRoles($userName)
        {
            if (isset($this->userRoles[$userName]))
                return $this->userRoles[$userName];
            else
	            return $this->userRoles[$this->defaultUserName];
        }
    }


    class TableBasedIdentityCheckStrategy
    {
        private $tableName;
        private $userNameFieldName;
        private $passwordFieldName;
        private $passwordEncryption;
        private $userIdFieldName;

        private $dataset;

        private function CheckPasswordEquals($actualPassword, $expectedPassword)
        {
            if ($this->passwordEncryption == ENCRYPTION_NONE)
                return $actualPassword == $expectedPassword;
            else if ($this->passwordEncryption == ENCRYPTION_MD5)
                return md5($actualPassword) == $expectedPassword;
            else if ($this->passwordEncryption == ENCRYPTION_SHA1)
                return sha1($actualPassword) == $expectedPassword;
            else
                return false;
        }

        public function __construct($connectionFactory, $connectionOptions, $tableName, $userNameFieldName, $passwordFieldName, $passwordEncryption = ENCRYPTION_NONE, $userIdFieldName = null)
        {
            $this->userNameFieldName = $userNameFieldName;
            $this->passwordFieldName = $passwordFieldName;
            $this->passwordEncryption = $passwordEncryption;

            $this->dataset = new TableDataset(
                $connectionFactory,
                $connectionOptions,
                $tableName);
            $field = new StringField($userNameFieldName);
            $this->dataset->AddField($field, true);
            $field = new StringField($passwordFieldName);
            $this->dataset->AddField($field, false);
        }

        public function CheckUsernameAndPassword($username, $password, &$errorMessage)
        {
            $this->dataset->AddFieldFilter(
                $this->userNameFieldName,
                new FieldFilter($username, '=', true));
            $this->dataset->Open();
            if ($this->dataset->Next())
            {
                $expectedPassword = $this->dataset->GetFieldValueByName($this->passwordFieldName);
                if ($this->CheckPasswordEquals($password, $expectedPassword))
                {
                    return true;
                }
                else
                {
                    $errorMessage = 'The username/password combination you entered was invalid.';
                    return false;
                }
            }
            else
            {
                $errorMessage = 'The username/password combination you entered was invalid.';
                return false;
            }
        }

    }

?>