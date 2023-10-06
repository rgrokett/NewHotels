<?php

require_once 'renderer.php';

class EditRenderer extends Renderer
{
    function RowOperationByLinkColumn($column)
    {
        $this->result = '';
    }

    function RenderPage($Page)
    {
        $this->SetHTTPContentTypeByPage($Page);
        $Page->BeforePageRender->Fire(array(&$Page));

        $this->DisplayTemplate('edit/page.tpl',
            array('Page' => $Page),
            array('Grid' => $this->Render($Page->GetGrid())
        ));
    }

    function RenderDetailPageEdit($Page)
    {
        $this->DisplayTemplate('edit/page.tpl',
            array('Page' => $Page),
            array(
            'Grid' => $this->Render($Page->GetGrid())
        ));
    }

    function RenderImageUploader($imageUploader)
    {
        $this->result = '';
        if ($imageUploader->GetShowImage())
            $this->result =  '<img src="' . $imageUploader->GetLink() .'"><br/>';
        $this->result = $this->result .
            '<input checked="checked" type="radio" value="Keep" name="' . $imageUploader->GetName() . '_action">'.$this->Captions()->GetMessageString('KeepImage') .
            '<input type="radio" value="Remove" name="' . $imageUploader->GetName() . '_action">' . $this->Captions()->GetMessageString('RemoveImage').
            '<input type="radio" value="Replace" name="' . $imageUploader->GetName() . '_action">'.$this->Captions()->GetMessageString('ReplaceImage').'<br>' .
            '<input type="file" name="' . $imageUploader->GetName() . '_filename" ' .
            'onchange="if (this.form.'. $imageUploader->GetName() . '_action[2]) this.form.' . $imageUploader->GetName() . '_action[2].checked=true;">';
    }

    function RenderGrid($Grid)
    {
        $hiddenValues = array(OPERATION_PARAMNAME => OPERATION_COMMIT);
        AddPrimaryKeyParametersToArray($hiddenValues, $Grid->GetDataset()->GetPrimaryKeyValues());        
        
        $primaryKeyMap = $Grid->GetDataset()->GetPrimaryKeyValuesMap();
        
        $this->DisplayTemplate('edit/grid.tpl',
            array(
            'Title' => $Grid->GetPage()->GetShortCaption(),
            'Grid' => $Grid,
            'Columns' => $Grid->GetEditColumns(),
            'PrimaryKeyMap' => $primaryKeyMap),
            array(
            'HiddenValues' => $hiddenValues)
        );
    }
}
?>