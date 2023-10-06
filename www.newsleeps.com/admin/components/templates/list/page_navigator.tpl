<!-- <Pages> -->
<div class="page_navigator">
{if $PageNavigator->GetPageCount() > 1}
    <span id="current_page_text">
    {assign var="current_page" value=$PageNavigator->CurrentPageNumber()}
    {assign var="page_count" value=$PageNavigator->GetPageCount()}
    {assign var="current_page_info_template" value=$Captions->GetMessageString('PageNumbetOfCount')}
    {eval var=$current_page_info_template}
    </span>
{foreach item=PageNavigatorPage from=$PageNavigatorPages}
{if $PageNavigatorPage->IsCurrent()}
                        <span id="current_page" title="{$PageNavigatorPage->GetHint()}">{$PageNavigatorPage->GetPageCaption()}</span>
{else}
                        <a href="{$PageNavigatorPage->GetPageLink()}" class="page_link" title="{$PageNavigatorPage->GetHint()}">{$PageNavigatorPage->GetPageCaption()}</a>
{/if}
{/foreach}
{/if}
</div>
<script>
    {if $PageNavigator->HasPreviosPage()}
    BindPageDecrementShortCut('{$PageNavigator->PreviosPageLink()}');
    {/if}
    {if $PageNavigator->HasNextPage()}
    BindPageIncrementShortCut('{$PageNavigator->NextPageLink()}');
    {/if}
</script>
