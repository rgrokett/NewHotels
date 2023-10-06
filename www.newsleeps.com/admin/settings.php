<?php
    function GetGlobalConnectionOptions()
{
  return array(
  'server' => 'localhost',
  'port' => '3306',
  'username' => 'newsleeps',
  'password' => 'YourPassword',
  'database' => 'newsleepsDB'
);
}
function GetPagesHeader()
    {
    return
    '<H2>New Sleeps</H2>';
}

function GetPagesFooter()
    {
    return
        '<I>Copyright 2010 Kinetic Designs</I>'; 
    }
function ApplyCommonPageSettings($page, $grid)
{
    $page->SetShowUserAuthBar(true);
    $grid->OnBeforeDataChange->AddListener('Global_BeforeDataChangeHandler');
}

function GetAnsiEncoding() { return 'windows-1252'; }

function Global_BeforeDataChangeHandler($page, $rowData, $cancel, $message)
{

}

?>
