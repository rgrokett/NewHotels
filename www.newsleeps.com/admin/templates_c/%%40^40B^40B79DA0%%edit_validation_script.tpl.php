<?php /* Smarty version 2.6.19, created on 2009-12-25 14:44:58
         compiled from common/edit_validation_script.tpl */ ?>
<script language="javascript">
    var adaptersCreated = false;
    
    var ControlsToValidate;
    var ArrayOnInvalidActions;
    var ArrayOnValidActions;
    var ControlValidators;
    
    function CreateAdaptersIfNeed()
    {
        if (!adaptersCreated)
        {
            adaptersCreated = true;
        ControlsToValidate =
        [
<?php $_from = $this->_tpl_vars['Columns']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['ControlsAdapters'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['ControlsAdapters']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['column']):
        $this->_foreach['ControlsAdapters']['iteration']++;
?>
            <?php echo $this->_tpl_vars['column']->GetCreateJSControlAdapter(); ?>
<?php if (! ($this->_foreach['ControlsAdapters']['iteration'] == $this->_foreach['ControlsAdapters']['total'])): ?>,<?php endif; ?>

<?php endforeach; endif; unset($_from); ?>
        ];

        ArrayOnInvalidActions =
        [
<?php $_from = $this->_tpl_vars['Columns']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['OnInvalidActions'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['OnInvalidActions']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['column']):
        $this->_foreach['OnInvalidActions']['iteration']++;
?>
            function (controlAdapter) { controlAdapter.SetBackgroundColor('#FFAAAA'); }<?php if (! ($this->_foreach['OnInvalidActions']['iteration'] == $this->_foreach['OnInvalidActions']['total'])): ?>,<?php endif; ?>

<?php endforeach; endif; unset($_from); ?>
        ];

        ArrayOnValidActions =
        [
<?php $_from = $this->_tpl_vars['Columns']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['OnValidActions'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['OnValidActions']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['column']):
        $this->_foreach['OnValidActions']['iteration']++;
?>
            function (controlAdapter) { controlAdapter.ResetBackgroundColor(); }<?php if (! ($this->_foreach['OnValidActions']['iteration'] == $this->_foreach['OnValidActions']['total'])): ?>,<?php endif; ?>

<?php endforeach; endif; unset($_from); ?>
        ];

        ControlValidators =
        [
<?php $_from = $this->_tpl_vars['Columns']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['Controls'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['Controls']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['column']):
        $this->_foreach['Controls']['iteration']++;
?>
            [
<?php $_from = $this->_tpl_vars['column']->GetValidators(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['Validators'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['Validators']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['validator']):
        $this->_foreach['Validators']['iteration']++;
?>
                <?php echo $this->_tpl_vars['validator']->GetCreateJSValidator(); ?>
<?php if (! ($this->_foreach['Validators']['iteration'] == $this->_foreach['Validators']['total'])): ?>,<?php endif; ?>

<?php endforeach; endif; unset($_from); ?>
            ]<?php if (! ($this->_foreach['Controls']['iteration'] == $this->_foreach['Controls']['total'])): ?>,<?php endif; ?>

<?php endforeach; endif; unset($_from); ?>
        ];
        }
    }

    function ValidateControls()
    {
        CreateAdaptersIfNeed();
        var errorMessages = [];

        var isAllControlsValid = true;
        for(var controlIndex = 0; controlIndex < ControlsToValidate.length; controlIndex++)
        {
            var isControlValid = true;
            if (ControlsToValidate[controlIndex].IsSetToDefault() || ControlsToValidate[controlIndex].IsSetToNull())
                isControlValid = true;
            else
            {
                for(var validatorIndex = 0; validatorIndex < ControlValidators[controlIndex].length; validatorIndex++)
                {
                    if(!ControlValidators[controlIndex][validatorIndex].Validate(ControlsToValidate[controlIndex].GetValue()))
                    {
                        errorMessages.push(ControlValidators[controlIndex][validatorIndex].GetErrorMessage());
                        isControlValid = false;
                    }
                }
            }
            if (!isControlValid)
            {
                isAllControlsValid = false;
                ArrayOnInvalidActions[controlIndex](ControlsToValidate[controlIndex]);
            }
            else
                ArrayOnValidActions[controlIndex](ControlsToValidate[controlIndex]);
        }
        if (!isAllControlsValid)
        {
            document.getElementById('errorMessagesRow').style.display = '';
            document.getElementById('errorMessages').innerHTML = CreateListByArray(errorMessages);
        }
        else
            document.getElementById('errorMessagesRow').style.display = 'none';
        return isAllControlsValid;
    }
</script>