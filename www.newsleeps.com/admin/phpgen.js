    function expand(frameName, contentName, expandImageName, a)
    {
        var nodeEl = document.getElementById(contentName);
        var expandImage = document.getElementById(expandImageName);

        if (nodeEl.className == 'hidden')
        {
            if (a && a.href != "javascript:;")
            {
	            nodeEl.className = 'shown';
	            nodeEl.innerHTML = 'Loading...';
			    a.target = frameName;
			    expandImage.src = 'images/collapse.gif';
            }
            else
            {
	            nodeEl.className = 'shown';
	        	$('#' + contentName).slideDown();
	        	expandImage.src = 'images/collapse.gif';
            }

        }
        else
        {
            if (a)
                a.href = "javascript:;";
			$('#' + contentName).slideUp();
            nodeEl.className = 'hidden';
            expandImage.src = 'images/expand.gif';
        }
        return true;
    }

    function LoadDetail(node, content)
    {
    	contentControl = document.getElementById(node);
        contentControl.innerHTML = content.innerHTML;
    	$('#' + node).hide();
    	$('#' + node).slideDown();
        contentControl.className = 'shown';
    }

    function GetTextEditValue(formName, textEditName)
    {
        var textEdit;
        if (formName)
            textEdit = document.forms[formName].elements[textEditName];
        else
            textEdit = document.getElementById(textEditName);
        return textEdit.value;
    }


    function AbstractValidator(aMessage)
    {
        var errorMessage = aMessage;

        this.GetErrorMessage = function() { return errorMessage; }
    }

    function IntegerValidator(aMessage)
    {
        this.parent = AbstractValidator;
        this.parent(aMessage);

        this.Validate = function(value)
        {
            return isInteger(value);
        }
    }

    function NotEmptyValidator(aMessage)
    {
        this.parent = AbstractValidator;
        this.parent(aMessage);

        this.Validate = function(value)
        {
            return value != '';
        }
    }

    function AbstractAdapder(fieldName)
    {
        var _this = this;
        var _fieldName = fieldName;

        this.GetFieldName = function()
        {
            return _fieldName;
        }

        this.IsSetToDefault = function()
        {
            var name = _fieldName + '_def';
            var defCheckBox = document.getElementById(name);

            if (defCheckBox)
            {
                return defCheckBox.checked;
            }
            else
                return false;
        }

        this.IsSetToNull = function()
        {
            var name = _fieldName + '_null';
            var nullCheckBox = document.getElementById(name);
            if (nullCheckBox)
            {
                return nullCheckBox.checked;
            }
            else
                return false;
        }
    }

    function ComboBoxAdapter(name, fieldName)
    {
        this.parent = AbstractAdapder;
        this.parent(fieldName);
        var _this = this;
        var comboBoxInput = document.getElementById(name);
        if (comboBoxInput)
            var oldColor = comboBoxInput.style.backgroundColor;

        this.SetBackgroundColor = function(color)
        {
            if (comboBoxInput)
                comboBoxInput.style.backgroundColor = color;
        }

        this.ResetBackgroundColor = function()
        {
            if (comboBoxInput)
                comboBoxInput.style.backgroundColor = oldColor;
        }

        this.GetValue = function()
        {
            if (comboBoxInput)
                return comboBoxInput.Value;
            else
                return '';
        }
    }

    function CheckBoxGroup(name, fieldName)
    {
        this.parent = AbstractAdapder;
        this.parent(fieldName);
        var _this = this;

        this.SetBackgroundColor = function(color)
        {
        }

        this.ResetBackgroundColor = function()
        {
        }

        this.GetValue = function()
        {
            return 'dummy';
        }
    }

    function ImageEditorAdapter(name, fieldName)
    {
        this.parent = AbstractAdapder;
        this.parent(fieldName);

        this.SetBackgroundColor = function(color)
        {
            //editInput.style.backgroundColor = color;
        }

        this.ResetBackgroundColor = function()
        {

        }

        this.GetValue = function()
        {
            return 'dummy';
        }
    }

    function TextEditAdapter(name, fieldName)
    {
        this.parent = AbstractAdapder;
        this.parent(fieldName);
        var _this = this;
        var editInput = document.getElementById(name);
        if (editInput)
            var oldColor = editInput.style.backgroundColor;

        this.SetBackgroundColor = function(color)
        {
            if (editInput)
                editInput.style.backgroundColor = color;
        }

        this.ResetBackgroundColor = function()
        {
            if (editInput)
                editInput.style.backgroundColor = oldColor;
        }

        this.GetValue = function()
        {
            if (editInput)
                return editInput.value;
        }

    }

    function SpinEditAdapter(name, fieldName)
    {
        this.parent = AbstractAdapder;
        this.parent(fieldName);
        var _this = this;
        var spinEditInput = document.getElementById(name + '_Input');
        if (spinEditInput)
            var oldColor = spinEditInput.style.backgroundColor;

        this.SetBackgroundColor = function(color)
        {
            if (spinEditInput)
                spinEditInput.style.backgroundColor = color;
        }

        this.ResetBackgroundColor = function()
        {
            if (spinEditInput)
                spinEditInput.style.backgroundColor = oldColor;
        }

        this.GetValue = function()
        {
            if (spinEditInput)
                return spinEditInput.value;
        }
    }

    function CheckBoxEditAdapter(name, fieldName)
    {
        this.parent = AbstractAdapder;
        this.parent(fieldName);

        this.SetBackgroundColor = function(color)
        {
        }

        this.ResetBackgroundColor = function()
        {
        }

        this.GetValue = function()
        {
            return 'dummy';
        }
    }

    function CreateListByArray(valuesList)
    {
        result = '';
        for(var i = 0; i < valuesList.length; i++)
            result = result + valuesList[i] + '<br/>'
        return result;
    }
    
    function SetCookie(name, stages)
    { 
    	var time = new Date();
    	time.setTime(time.getTime() + 30 * 24 * 60 * 60 * 1000);
    	document.cookie = name + '=' + stages + '; expires=' + time.toGMTString() +'; path=/'; 
    }
    function GetCookie(name)
    {
    	var prefix = name + '=';
    	var indexBeg = document.cookie.indexOf(prefix);
    	if (indexBeg == -1)
    	   return false;
    	var indexEnd = document.cookie.indexOf(';', indexBeg + prefix.length);
    	if (indexEnd == -1) 
    	   indexEnd = document.cookie.length;
    	return unescape(document.cookie.substring(indexBeg + prefix.length, indexEnd)); 
    }    
    
    inputControlFocuced = false;
        
    $(document).ready(function()
    {    
        $('input').blur(function() { inputControlFocuced = false; });
        $('input').focus(function() { inputControlFocuced = true; });
    });
            
    function navigate_to(link)
    {
        window.location.href = link;
    }
    
    function BindPageDecrementShortCut(prevPageLink)
    {
        $(document).ready(function()
        {    
            $(document).bind('keydown', 'Ctrl+left', function()
            {
                if (!inputControlFocuced)
                    navigate_to(prevPageLink);
            });
        });
    }
    
    function BindPageIncrementShortCut(nextPageLink)
    {
        $(document).ready(function()
        {    
            $(document).bind('keydown', 'Ctrl+right', function()
            {
                if (!inputControlFocuced)
                    navigate_to(nextPageLink);
            });   
        });
    }
    
    function EnableHighlightRowAtHover(gridSelector)
    {
        $(document).ready(function()
        {
            $(".grid tr.even").mouseover(function()
            {
                $(this).addClass("highlited");
            });
            $(".grid tr.odd").mouseover(function()
            {
                $(this).addClass("highlited");
            });        
            $(".grid tr.odd").mouseout(function()
            {
                $(this).removeClass("highlited");
            });
            $(".grid tr.even").mouseout(function()
            {
                $(this).removeClass("highlited");
            });
        });
    }

    var highlightFunctions = new Array();
    
    function HighlightTextInGrid(gridSelector, fieldName, text, opt, a_hint)
    {
        var hint_text = '';
        if (a_hint)
            hint_text = a_hint;
        
        // highlightFunctions.push(function ()
        // {
            $(gridSelector + " tr td.even[char=data][data-column-name='"+fieldName+"']").highlight(text, opt, {hint: hint_text});
            $(gridSelector + " tr td.odd[char=data][data-column-name='"+fieldName+"']").highlight(text, opt, {hint: hint_text});
        // });
    }
    
    function ShowHighligthAllSearches()
    {
        for(i = 0; i < highlightFunctions.length - 1; i++)
            highlightFunctions[i]();
    }
    
    function HideHighligthAllSearches()
    {
        $('.grid td').removeHighlight();
    }
    
    function ToggleHighligthAllSearches(highlightAllLink)
    {
        if (highlightAllLink.hasClass('pressed'))
        {
            HideHighligthAllSearches();
            highlightAllLink.removeClass('pressed');
        }
        else
        {
            ShowHighligthAllSearches();
            highlightAllLink.addClass('pressed');
        }
    }
