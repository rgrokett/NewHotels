<input type="text" name="{$DateTimeEdit->GetName()}" id="{$DateTimeEdit->GetName()}" value="{$DateTimeEdit->GetValue()}">
<button type="reset" id="{$DateTimeEdit->GetName()}_trigger">...</button>
<script type="text/javascript">
    Calendar.setup({ldelim}
        inputField     :    "{$DateTimeEdit->GetName()}",
        ifFormat       :    "{$DateTimeEdit->GetFormat()}",
        showsTime      :    {if $DateTimeEdit->GetShowsTime()}true{else}false{/if},
        button         :    "{$DateTimeEdit->GetName()}_trigger",
        singleClick    :    true,
        step           :    1
    {rdelim});
</script>