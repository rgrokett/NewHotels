<html>
{include file='common/page_header.tpl'}
{include file='common/list_pages_body_header.tpl'}
<div align="center" style="padding-bottom: 5px;">
    <table border=0 cellpadding=0 cellspacing=0 class=main_table_border2 width=100%><tr><td>
        <table class="main_table_border" cellspacing="0" cellpadding="0" width=100% border=0>
            <tr><td height="20" valign=middle align=center style="padding-top:0px;">
                <table class="data" border="0" cellspacing="0" cellpadding="3" width="100%">
                    <tr class="blackshade" valign="top">
                        <td class="headerlist">
                            {$Captions->GetMessageString('MasterRecord')}
                            (<a href="{$Page->GetParentPageLink()}">{$Captions->GetMessageString('ReturnFromDetailToMaster')}</a>)
                        </td>
                    </tr>
                </table>
            </td></tr>
         </table>
     </td></tr></table>
</div>
{html_indent value=1 text=$MasterGrid}<br />
{html_indent value=1 text=$Grid}
{include file='common/list_pages_body_footer.tpl'}
</html>