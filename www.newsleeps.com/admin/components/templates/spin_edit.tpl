<div align="center" style="width: 155;">
<div id="{$SpinEdit->GetName()}_Container" align="center" style="width: 155;"></div>
</div>
<input type="hidden" id="{$SpinEdit->GetName()}" name="{$SpinEdit->GetName()}" value="{$SpinEdit->GetValue()}">
<script type="text/javascript">
    function {$SpinEdit->GetName()}_listener(sender, newVal)
    {ldelim}
        document.getElementById('{$SpinEdit->GetName()}').value = newVal;
    {rdelim}

    var spinCtrl = new SpinControl();
    spinCtrl.GetAccelerationCollection().Add(new SpinControlAcceleration(1, 500));
    spinCtrl.GetAccelerationCollection().Add(new SpinControlAcceleration(5, 1750));
    spinCtrl.SetCurrentValue({$SpinEdit->GetValue()});
    spinCtrl.SetWidth(155);
    spinCtrl.SetInputName('{$SpinEdit->GetName()}_Input');

    var el = document.getElementById('{$SpinEdit->GetName()}_Container');
    el.appendChild(spinCtrl.GetContainer());
    spinCtrl.AttachValueChangedListener({$SpinEdit->GetName()}_listener);

    spinCtrl.StartListening();
</script>