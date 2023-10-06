{foreach key=Value item=Name from=$RadioEdit->GetValues()}
<label><input id="{$RadioEdit->GetName()}" name="{$RadioEdit->GetName()}" value="{$Value}"{if $RadioEdit->GetSelectedValue() eq $Value} checked="checked"{/if} type="radio">{$Name}</label> 
{/foreach}