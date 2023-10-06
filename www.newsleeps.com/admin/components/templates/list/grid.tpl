{if $UseFilter}
{strip}
    <div class="grid grid_menu" style="width: auto; padding: 10px; margin-top: 10px;">
        {$SearchControl}
    </div>
    <br/>
{/strip}
{/if}

{if $AllowDeleteSelected}
<form name="selectedRecords" method="POST" action="{$Grid->GetDeleteSelectedLink()}">
    <input type="hidden" name="operation" value="delsel">
    <input type="hidden" name="recordCount" value="{$RecordCount}">
{/if}

{if $Grid->GetHighlightRowAtHover()}<script> EnableHighlightRowAtHover('.grid'); </script>{/if}

{* if true}
<div class="under_grid_menu" align="right">
<a id="highligth_all_link" class="under_grid_link" href="#" onclick="ToggleHighligthAllSearches($(this)); return false;">Highligth all</a>
<script>{literal}
    $(document).ready(function()
    {
        /* RestoreDisplayingHighlightSearches($('#highligth_all_link')); */
    });
{/literal}</script>
</div>
{/if *}

<table class="grid">
{if $Grid->GetShowAddButton() or $AllowDeleteSelected or $Grid->GetShowUpdateLink()}
    <tr>
        {strip}
        <td colspan="{$ColumnCount}" class="grid_menu">
                {counter start=0 assign="grid_menu_links"}
                {if $Grid->GetShowAddButton()}
                    {if $grid_menu_links > 0}|{/if}
                    <a class="grid_menu_link" href="{$Grid->GetAddRecordLink()}">{$Captions->GetMessageString('AddNewRecord')}</a>
                    {counter assign="grid_menu_links"}
                {/if}
                {if $AllowDeleteSelected}
                    {if $grid_menu_links > 0}|{/if}
                    <a class="grid_menu_link" href="" onclick="if (confirm('Delete records?')) document.selectedRecords.submit(); return false;">{$Captions->GetMessageString('DeleteSelected')}</a>
                    {counter assign="grid_menu_links"}
                {/if}
                {if $Grid->GetShowUpdateLink()}
                    {if $grid_menu_links > 0}|{/if}
                    <a class="grid_menu_link" href="{$Grid->GetUpdateLink()}">{$Captions->GetMessageString('Refresh')}</a>
                    {counter assign="grid_menu_links"}
                {/if}
        </td>
        {/strip}
    </tr>
{/if}
    {if $Grid->GetErrorMessage() != ''}
    <tr><td class="odd grid_error_row" colspan="{$ColumnCount}" >
        <div class="grid_error_message">
        <strong>{$Captions->GetMessageString('ErrorsDuringDeleteProcess')}</strong><br><br>
        {$Grid->GetErrorMessage()}
        </div>
    </td></tr>
    {/if}

    <!-- <Grid Head> -->
    <tr>
        {if $AllowDeleteSelected}
            <th class="odd">
                <input type="checkbox" name="rec{$smarty.foreach.RowsGrid.index}" onClick="var i; for(i = 0; i < {$RecordCount}; i++) document.getElementById('rec' + i).checked = this.checked">
            </th>
        {/if}
        <!-- <Grid Head Columns> -->
        {foreach item=Column from=$Columns name=Header}
            {strip}
            <th class="{if $smarty.foreach.Header.index is even}even{else}odd{/if}"{if $HeadColumnsStyles[$smarty.foreach.Header.index] != ''} style="{$HeadColumnsStyles[$smarty.foreach.Header.index]}"{/if}>
                {$Renderer->Render($Column->GetHeaderControl())}
            </th>
            {/strip}
        {/foreach}
        <!-- </Grid Head Columns> -->
    </tr>

    <!-- </Grid Head> -->
{if count($Rows) > 0}
    {foreach item=Row from=$Rows name=RowsGrid}
    <tr class="{if $smarty.foreach.RowsGrid.index is even}even{else}odd{/if}"{if $RowCssStyles[$smarty.foreach.RowsGrid.index] != ''} style="{$RowCssStyles[$smarty.foreach.RowsGrid.index]}"{/if}>
        {if $AllowDeleteSelected}
        {strip}
        <td class="odd" {if $RowCssStyles[$smarty.foreach.RowsGrid.index] != ''} style="{$RowCssStyles[$smarty.foreach.RowsGrid.index]}"{/if}>
            <input type="checkbox" name="rec{$smarty.foreach.RowsGrid.index}" id="rec{$smarty.foreach.RowsGrid.index}">
            {foreach item=PkValue from=$RowPrimaryKeys[$smarty.foreach.RowsGrid.index] name=CPkValues}
                <input type="hidden" name="rec{$smarty.foreach.RowsGrid.index}_pk{$smarty.foreach.CPkValues.index}" value="{$PkValue}">
            {/foreach}
        </td>
        {/strip}
        {/if}

        {foreach item=RowColumn from=$Row name=RowColumns}
        {strip}
            <td data-column-name="{$ColumnsNames[$smarty.foreach.RowColumns.index]}" char="{$RowColumnsChars[$smarty.foreach.RowsGrid.index][$smarty.foreach.RowColumns.index]}" class="{if $smarty.foreach.RowColumns.index is even}even{else}odd{/if}"{if $RowColumnsCssStyles[$smarty.foreach.RowsGrid.index][$smarty.foreach.RowColumns.index] != ''} style="{$RowColumnsCssStyles[$smarty.foreach.RowsGrid.index][$smarty.foreach.RowColumns.index]}"{/if}>
                {$RowColumn}
            </td>
        {/strip}
        {/foreach}
    </tr>
    
    {strip}
    <tr style="border: none; height: 0px;">
        <td colspan="{$ColumnCount}" style="border: none; padding: 0px; height: 0px;">
            {foreach item=AfterRow from=$AfterRows[$smarty.foreach.RowsGrid.index]}
                {$AfterRow}
            {/foreach}
        </td>
    </tr>
    {/strip}
    
    {/foreach}
{else} {* count($Rows) > 0 *}
{strip}
    <tr>
        <td colspan="{$ColumnCount}" class="emplygrid">
            {$Captions->GetMessageString('NoDataToDisplay')}
        </td>
    </tr>
{/strip}
{/if} {* count($Rows) > 0 *}
{if $Grid->GetShowAddButton() or $AllowDeleteSelected or $Grid->GetShowUpdateLink()}
    <tr>
        {strip}
        <td colspan="{$ColumnCount}" class="grid_menu">
                {counter start=0 assign="grid_menu_links"}
                {if $Grid->GetShowAddButton()}
                    {if $grid_menu_links > 0}|{/if}
                    
                    <a class="grid_menu_link" href="{$Grid->GetAddRecordLink()}">{$Captions->GetMessageString('AddNewRecord')}</a>
                    
                    {counter assign="grid_menu_links"}
                {/if}
                {if $AllowDeleteSelected}
                    {if $grid_menu_links > 0}|{/if}
                    
                    <a class="grid_menu_link" href="" onclick="if (confirm('Delete records?')) document.selectedRecords.submit(); return false;">{$Captions->GetMessageString('DeleteSelected')}</a>
                    
                    {counter assign="grid_menu_links"}
                {/if}
                {if $Grid->GetShowUpdateLink()}
                    {if $grid_menu_links > 0}|{/if}
                    
                    <a class="grid_menu_link" href="{$Grid->GetUpdateLink()}">{$Captions->GetMessageString('Refresh')}</a>
                    
                    {counter assign="grid_menu_links"}
                {/if}
        </td>
        {/strip}
    </tr>
{/if}
</table>

{if $AllowDeleteSelected}</form>{/if}