<?php /* Smarty version 2.6.19, created on 2009-12-25 14:44:23
         compiled from list/page_navigator.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'eval', 'list/page_navigator.tpl', 8, false),)), $this); ?>
<!-- <Pages> -->
<div class="page_navigator">
<?php if ($this->_tpl_vars['PageNavigator']->GetPageCount() > 1): ?>
    <span id="current_page_text">
    <?php $this->assign('current_page', $this->_tpl_vars['PageNavigator']->CurrentPageNumber()); ?>
    <?php $this->assign('page_count', $this->_tpl_vars['PageNavigator']->GetPageCount()); ?>
    <?php $this->assign('current_page_info_template', $this->_tpl_vars['Captions']->GetMessageString('PageNumbetOfCount')); ?>
    <?php echo smarty_function_eval(array('var' => $this->_tpl_vars['current_page_info_template']), $this);?>

    </span>
<?php $_from = $this->_tpl_vars['PageNavigatorPages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['PageNavigatorPage']):
?>
<?php if ($this->_tpl_vars['PageNavigatorPage']->IsCurrent()): ?>
                        <span id="current_page" title="<?php echo $this->_tpl_vars['PageNavigatorPage']->GetHint(); ?>
"><?php echo $this->_tpl_vars['PageNavigatorPage']->GetPageCaption(); ?>
</span>
<?php else: ?>
                        <a href="<?php echo $this->_tpl_vars['PageNavigatorPage']->GetPageLink(); ?>
" class="page_link" title="<?php echo $this->_tpl_vars['PageNavigatorPage']->GetHint(); ?>
"><?php echo $this->_tpl_vars['PageNavigatorPage']->GetPageCaption(); ?>
</a>
<?php endif; ?>
<?php endforeach; endif; unset($_from); ?>
<?php endif; ?>
</div>
<script>
    <?php if ($this->_tpl_vars['PageNavigator']->HasPreviosPage()): ?>
    BindPageDecrementShortCut('<?php echo $this->_tpl_vars['PageNavigator']->PreviosPageLink(); ?>
');
    <?php endif; ?>
    <?php if ($this->_tpl_vars['PageNavigator']->HasNextPage()): ?>
    BindPageIncrementShortCut('<?php echo $this->_tpl_vars['PageNavigator']->NextPageLink(); ?>
');
    <?php endif; ?>
</script>