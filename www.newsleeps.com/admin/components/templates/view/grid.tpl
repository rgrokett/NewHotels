<div align="center" style="width: auto">
    <table class="grid" style="width: 500px">
        <tr><th class="even" colspan=2>
            {$Title}{* [{foreach key=FieldName item=FieldValue from=$PrimaryKeyMap name=PrimaryKeys}{$FieldName}: {$FieldValue}{if !$smarty.foreach.PrimaryKeys.last}; {/if}{/foreach}] *}
        </th></tr>
{section name=RowGrid loop=$ColumnCount}
        <tr class="{if $smarty.section.RowGrid.index is even}even{else}odd{/if}"{if $RowCssStyles[$smarty.foreach.RowsGrid.index] != ''} style="{$RowCssStyles[$smarty.foreach.RowsGrid.index]}"{/if}>
            <td class="odd" style="padding-left:20px;"><b>{$Columns[$smarty.section.RowGrid.index]->GetCaption()}</b></td>
            <td class="even" style="padding-left:10px;">
                {$Row[$smarty.section.RowGrid.index]}
            </td>
        </tr>
{/section}
        <tr height="40" class="editor_buttons"><td colspan="2" align="center" valign="middle">
            <input class="sm_button" type="button" value="{$Captions->GetMessageString('BackToList')}" onclick="window.location.href='{$Grid->GetReturnUrl()}'"/>
        </td></tr>
    </table>
</div>
