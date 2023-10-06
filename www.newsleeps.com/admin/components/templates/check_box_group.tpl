{foreach key=Value item=Name from=$CheckBoxGroup->GetValues()}
<label><input type="checkbox" name="{$CheckBoxGroup->GetName()}[]" value="{$Value}" {if $CheckBoxGroup->IsValueSelected($Value)} checked="checked"{/if}>{$Name}</label><br/>
{/foreach}