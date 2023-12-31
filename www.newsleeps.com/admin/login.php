<?php
    require_once 'components/page.php';
    require_once 'components/renderers/renderer.php';
    require_once 'components/renderers/list_renderer.php';
    require_once 'authorization.php';
    require_once 'settings.php';

    class LoginControl
    {
        private $identityCheckStrategy;
        private $urlToRedirectAfterLogin;
        private $errorMessage;
        private $lastUserName;
        private $lastSaveidentity;
        private $loginAsGuestLink;

        public function __construct($identityCheckStrategy, $urlToRedirectAfterLogin)
        {
            $this->identityCheckStrategy = $identityCheckStrategy;
            $this->urlToRedirectAfterLogin = $urlToRedirectAfterLogin;
            $this->errorMessage = '';
            $this->lastSaveidentity = false;
        }

        public function Accept($renderer)
        {
            $renderer->RenderLoginControl($this);
        }

        public function GetErrorMessage() { return $this->errorMessage; }

        public function GetLastUserName() { return $this->lastUserName; }
        public function GetLastSaveidentity() { return $this->lastSaveidentity; }
        public function CanLoginAsGuest() { return false; }
        
        public function GetLoginAsGuestLink() { return $this->urlToRedirectAfterLogin; }

        public function CheckUsernameAndPassword($username, $password, &$errorMessage)
        {
            try
            {
                return $this->identityCheckStrategy->CheckUsernameAndPassword($username, $password, $errorMessage);
            }
            catch(Exception $e)
            {
                $errorMessage = $e->getMessage();
                return false;
            }
        }

        public function SaveUserIdentity($username, $password, $saveidentity)
        {
            $expire = $saveidentity ? time() + 3600 * 24 * 365 : 0;
            setcookie('username', $username, $expire);
            setcookie('password', $password, $expire);
        }

        public function ClearUserIdentity()
        {
            setcookie('username', '', time() - 3600);
            setcookie('password', '', time() - 3600);
        }

        public function ProcessMessages()
        {
            if (isset($_GET[OPERATION_PARAMNAME]) && $_GET[OPERATION_PARAMNAME] == 'logout')
            {
                $this->ClearUserIdentity();
            }
            elseif (isset($_COOKIE['username']) && isset($_COOKIE['password']) && !(isset($_POST['username']) && isset($_POST['password'])))
            {
                /*$username = $_COOKIE['username'];
                $password = $_COOKIE['password'];

                if ($this->CheckUsernameAndPassword($username, $password, $this->errorMessage))
                {
                    header('Location: ' . $this->urlToRedirectAfterLogin );
                }
                else
                {
                }*/
            }
            elseif (isset($_POST['username']) && isset($_POST['password']))
            {
                $username = $_POST['username'];
                $password = $_POST['password'];
                $saveidentity = isset($_POST['saveidentity']);

                if ($this->CheckUsernameAndPassword($username, $password, $this->errorMessage))
                {
                    $this->SaveUserIdentity($username, $password, $saveidentity);
                    header('Location: ' . $this->urlToRedirectAfterLogin );
                    exit;
                }
                else
                {
                    $this->lastUserName = $username;
                    $this->lastSaveidentity = $saveidentity;
                }
            }
        }
    }

    class LoginPage extends CustomLoginPage
    {
        private $loginControl;
        private $renderer;
        private $header;
        private $footer;

        public function __construct($loginControl)
        {
            parent::__construct();
            $this->loginControl = $loginControl;
            $this->renderer = new ViewAllRenderer(GetCaptions('UTF-8'));
        }

        public function GetLoginControl()
        {
            return $this->loginControl;
        }

        public function Accept($renderer)
        {
            $renderer->RenderLoginPage($this);
        }

        public function GetContentEncoding() { return 'UTF-8'; }
        
        public function GetCaption() { return 'Login'; }
        
        public function SetHeader($value) { $this->header = $value; }
        public function GetHeader() { return $this->RenderText($this->header); }
        
        public function SetFooter($value) { $this->footer = $value; }
        public function GetFooter() { return $this->RenderText($this->footer); }

        public function BeginRender()
        {
            $this->loginControl->ProcessMessages();
        }

        public function EndRender()
        {
            echo $this->renderer->Render($this);
        }
    }

    $loginPage = new LoginPage(
        new LoginControl(
            GetIdentityCheckStrategy(),
            'affiliate_data.php'));

    $loginPage->SetHeader(GetPagesHeader());
    $loginPage->SetFooter(GetPagesFooter());
    $loginPage->BeginRender();
    $loginPage->BeginRender();
    $loginPage->EndRender();
?>
