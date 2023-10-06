{if not $TextEdit->GetReadOnly()}<input class="sm_text" id="{$TextEdit->GetName()}" name="{$TextEdit->GetName()}" value="{$TextEdit->GetHTMLValue()}" {if $TextEdit->GetSize() != null}size="{$TextEdit->GetSize()}" style="width: auto;"{/if} {if $TextEdit->GetMaxLength() != null}maxlength="{$TextEdit->GetMaxLength()}"{/if}>{else}
{$TextEdit->GetValue()}{/if}
