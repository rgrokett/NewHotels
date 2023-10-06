{if !$ComboBox->GetReadOnly()}<select id="{$ComboBox->GetName()}" name="{$ComboBox->GetName()}">
{foreach key=Value item=Name from=$ComboBox->GetValues()}
    <option value="{$Value}"{if $ComboBox->GetSelectedValue() eq $Value} selected{/if}>{$Name}</option>
{/foreach}
</select>{else}
{foreach key=Value item=Name from=$ComboBox->GetValues()}
{if $ComboBox->GetSelectedValue() eq $Value}{$Name}{/if}
{/foreach}
{/if}