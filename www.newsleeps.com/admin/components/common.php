<?php
class Event
{
    private $listeners;

    public function __construct()
    {
        $this->listeners = array();
    }

    public function AddListener($functionName, $object = null)
    {
        if ($object == null)
            $this->listeners[] = $functionName;
        else
            $this->listeners[] = array($object, $functionName);
    }

    public function Fire()
    {
        $argumets = func_get_args();
        foreach($this->listeners as $listener)
            call_user_func_array($listener, $argumets[0]);
    }

    public function GetListenerCount()
    {
        return count($this->listeners);
    }
}

class SMDateTime
{
    private $timestamp;

    public function __construct($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    public static function Parse($stringValue, $format)
    {
        return new SMDateTime(strtotime($stringValue));
    }

    public static function Now()
    {
        return new SMDateTime(time());
    }

    public function ToString($format)
    {
        return @date($format, $this->timestamp);
    }
}

$formatsMap = array(
    'd' => 'd',
    'e' => 'j',
    'a' => 'D',
    'A' => 'l',
    'V' => 'W',

    'B' => 'F',
    'b' => 'M',
    'm' => 'm',
    'g' => 'o',
    'Y' => 'Y',
    'y' => 'y',

    'H' => 'H',
    'I' => 'h',
    'l' => 'g',
    'M' => 'i',
    'S' => 's',
    'p' => 'A',
    'P' => 'a');

function OSFormatToDateFormat($osFormat)
{   
    global $formatsMap;
    $result = $osFormat;
    foreach($formatsMap as $osId => $dateId)
        $result = str_replace('%' . $osId, $dateId, $result);
    return $result;
}

function DateFormatToOSFormat($dateFormat)
{   
    global $formatsMap;
    $result = $dateFormat;
    foreach($formatsMap as $osId => $dateId)
        $result = str_replace($dateId, '%' . $osId, $result);
    return $result;
}

class SMDate
{

}

class LinkBuilder
{
    private $targetPage;
    private $parameters;
    public function __construct($targetPage)
    {
        $this->targetPage = $targetPage;
        $this->parameters = array();
    }

    public function AddParameter($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    public function AddParameters($parameters)
    {
        foreach($parameters as $name => $value)
            $this->AddParameter($name, $value);
    }

    public function RemoveParameter($name)
    {
        unset($this->parameters[$name]);
    }

    public function GetParameters()
    {
        return $this->parameters;
    }

    public function GetLink()
    {
        $parameterList = '';
        foreach($this->parameters as $name => $value)
            AddStr($parameterList, $name . '=' . $value, '&' );
        return $this->targetPage . ($parameterList != '' ? '?' : '') . $parameterList;
    }

    public function CloneLinkBuilder()
    {
        $result = new LinkBuilder($this->targetPage);
        $result->AddParameters($this->GetParameters());
        return $result;
    }
}

abstract class ImageFilter
{
    abstract function ApplyFilter(&$imageString);
}

class NullFilter extends ImageFilter
{
    function ApplyFilter(&$imageString)
    {
        return $imageString;
    }
}

class ImageFitByWidthResizeFilter extends ImageFilter
{
    private $width;

    function __construct($width)
    {
        $this->width= $width;
    }

    function GetTransformedSize($imageSize)
    {
        $imageWidth = $imageSize[0];
        $imageHeight = $imageSize[1];

        $result = array(
            $imageWidth * $this->width / $imageWidth,
            $imageHeight * $this->width / $imageWidth);

        return $result;
    }

    function echobig($string, $bufferSize = 8192)
    {
        for ($chars=strlen($string)-1,$start=0;$start <= $chars;$start += $bufferSize)
            echo substr($string,$start,$bufferSize);
    }


    function ApplyFilter(&$imageString)
    {
        $image = imagecreatefromstring($imageString);
        $imageSize = array(imagesx($image), imagesy($image));
        $imageWidth = $imageSize[0];
        $imageHeight = $imageSize[1];

        $newImageSize = $this->GetTransformedSize($imageSize);
        $newImageWidth = $newImageSize[0];
        $newImageHeight = $newImageSize[1];

        $result = imagecreatetruecolor($newImageWidth, $newImageHeight);
        imageantialias($result, true);
        imagecopyresampled($result, $image, 0, 0, 0, 0, $newImageWidth, $newImageHeight, $imageWidth, $imageHeight);

        return imagejpeg($result);
    }

}

class ImageFitByHeightResizeFilter extends ImageFilter
{
    private $height;

    function __construct($Height)
    {
        $this->height = $Height;
    }

    function GetTransformedSize($imageSize)
    {
        $imageWidth = $imageSize[0];
        $imageHeight = $imageSize[1];

        $result = array(
            $imageWidth * $this->height / $imageHeight,
            $imageHeight * $this->height / $imageHeight);

        return $result;
    }

    function echobig($string, $bufferSize = 8192)
    {
        for ($chars=strlen($string)-1,$start=0;$start <= $chars;$start += $bufferSize)
            echo substr($string,$start,$bufferSize);
    }


    function ApplyFilter(&$imageString)
    {
        $image = imagecreatefromstring($imageString);
        $imageSize = array(imagesx($image), imagesy($image));
        $imageWidth = $imageSize[0];
        $imageHeight = $imageSize[1];

        $newImageSize = $this->GetTransformedSize($imageSize);
        $newImageWidth = $newImageSize[0];
        $newImageHeight = $newImageSize[1];

        $result = imagecreatetruecolor($newImageWidth, $newImageHeight);
        imageantialias($result, true);
        imagecopyresampled($result, $image, 0, 0, 0, 0, $newImageWidth, $newImageHeight, $imageWidth, $imageHeight);

        return imagejpeg($result);
    }
}

abstract class HTTPHandler
{
    private $name;

    public function __construct($Name)
    {
        $this->name = $Name;
    }

    public function GetName()
    {
        return $this->name;
    }

    public abstract function Render($renderer);
}

class DownloadHTTPHandler extends HTTPHandler
{
    private $dataset;
    private $fieldName;
    private $contentType;
    private $downloadFileName;

    public function __construct($dataset, $fieldName, $name, $contentType, $downloadFileName)
    {
        parent::__construct($name);
        $this->dataset = $dataset;
        $this->fieldName = $fieldName;
        $this->contentType = $contentType;
        $this->downloadFileName = $downloadFileName;
    }

    public function Render($renderer)
    {
        $primaryKeyValues = array();
        ExtractPrimaryKeyValues($primaryKeyValues, METHOD_GET);

        $this->dataset->SetSingleRecordState($primaryKeyValues);
        $this->dataset->Open();
        if ($this->dataset->Next())
            $result = $this->dataset->GetFieldValueByName($this->fieldName);
        $this->dataset->Close();

        header('Content-type: ' . $this->contentType);
        header('Content-Disposition: attachment; filename="' . $this->downloadFileName . '"');
        echo $result;
    }
}

class ImageHTTPHandler extends HTTPHandler
{
    private $dataset;
    private $fieldName;
    private $imageFilter;

    public function __construct($Dataset, $FieldName, $Name, $ImageFilter)
    {
        parent::__construct($Name);
        $this->dataset = $Dataset;
        $this->fieldName = $FieldName;
        $this->imageFilter = $ImageFilter;
    }

    function TransformImage(&$imageString)
    {
        echo $this->imageFilter->ApplyFilter($imageString);
    }

    public function Render($renderer)
    {
        $result = '';
        header('Content-type: image');

        $primaryKeyValues = array ( );
        ExtractPrimaryKeyValues($primaryKeyValues, METHOD_GET);

        $this->dataset->SetSingleRecordState($primaryKeyValues);
        $this->dataset->Open();

        if ($this->dataset->Next())
            $result = $this->dataset->GetFieldValueByName($this->fieldName);
        $this->dataset->Close();

        if (GetApplication()->IsGETValueSet('large'))
        {
            echo $result;
            return '';//$result;
        }
        else
        {
        //echo $result;
            $this->TransformImage($result);
            return '';
        }
    }
}

class ShowTextBlobHandler extends HTTPHandler
{
    private $dataset;
    private $fieldName;
    private $parentPage;
    private $caption;
    private $column;

    public function __construct($dataset, $parentPage, $name, $column)
    {
        parent::__construct($name);
        $this->dataset = $dataset;
        $this->parentPage = $parentPage;
        $this->column = $column;
    }

    public function Render($renderer)
    {
        echo $renderer->Render($this);
    }

    public function Accept($renderer)
    {
        $renderer->RenderTextBlobViewer($this);
    }

    public function GetParentPage()
    { return $this->parentPage; }
    public function GetCaption()
    { return $this->column->GetCaption(); }

    public function GetValue($renderer)
    {
        $result = '';
        $primaryKeyValues = array ( );
        ExtractPrimaryKeyValues($primaryKeyValues, METHOD_GET);

        $this->dataset->SetSingleRecordState($primaryKeyValues);
        $this->dataset->Open();
        if ($this->dataset->Next())
        {
            if ($this->column == null)
            ;//$result = $this->dataset->GetFieldValueByName($this->fieldName);
            else
                $result = $renderer->Render($this->column);
        }
        $this->dataset->Close();
        return $result;
    }
}

class Path
{
    public static $PathDelimiter = '/';

    public static function IsPathDelimiter($character)
    {
        return $character == Path::$PathDelimiter;
    }

    public static function IsAbsolutePath($path)
    {
        return Path::IsPathDelimiter($path[0]);
    }

    public static function IncludeTralligPathDelimiter($path)
    {
        $result = $path;
        if (!Path::IsPathDelimiter($result[strlen($result) - 1]))
            $result .= Path::$PathDelimiter;
        return $result;
    }

    public static function Combine($prefix, $suffix)
    {
        if (Path::IsAbsolutePath($suffix) || !isset($prefix) || empty($prefix))
            return $suffix;
        else
            return Path::IncludeTralligPathDelimiter($prefix) . $suffix;
    }

}

?>