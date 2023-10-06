<?php
class InsertRenderer extends EditRenderer
{
	function RenderImageUploader($imageUploader)
    {     
        $this->result = '';
        $this->result = $this->result .
            '<input checked="checked" type="radio" value="Keep" name="' . $imageUploader->GetName() . '_action">'.$this->Captions()->GetMessageString('KeepImage') .
            '<input type="radio" value="Remove" name="' . $imageUploader->GetName() . '_action">'.$this->Captions()->GetMessageString('RemoveImage') .
            '<input type="radio" value="Replace" name="' . $imageUploader->GetName() . '_action">'.$this->Captions()->GetMessageString('ReplaceImage').'<br>' .
            '<input type="file" name="' . $imageUploader->GetName() . '_filename" ' .
            'onchange="if (this.form.'. $imageUploader->GetName() . '_action[2]) this.form.' . $imageUploader->GetName() . '_action[2].checked=true;">';
	}

    function RenderGrid($Grid)
    {
        $hiddenValues = array(OPERATION_PARAMNAME => OPERATION_COMMIT_INSERT);

        $this->DisplayTemplate('insert/grid.tpl',
            array(
                'Title' => $Grid->GetPage()->GetShortCaption(),
                'Grid' => $Grid,
                'Columns' => $Grid->GetInsertColumns()),
            array(
                'HiddenValues' => $hiddenValues)
        );
    }
}
?>
