<head>
    <meta http-equiv="content-type" content="text/html{if $Page->GetContentEncoding() != null}; charset={$Page->GetContentEncoding()}{/if}">
    <meta name="generator" content="Maestro PHP Generator">
    <title>{$Page->GetCaption()}</title>
    <!--[if lte IE 6]>
    <style>
        th {ldelim} behavior: url('iepngfix.htc'); {rdelim}
        div.site_header_pad
        {ldelim}
            margin: 0px;
            display: none;
        {rdelim}
    </style>
    <![endif]-->
    <link rel="stylesheet" type="text/css" href="common_style.css" />
    <link rel="stylesheet" type="text/css" href="phpgen.css" />
    <link rel="stylesheet" type="text/css" href="grid.css" />
    <link rel="stylesheet" type="text/css" href="libs/jquery/css/lightbox.css" media="screen" />

    <script type="text/javascript" src="libs/jquery/jquery.js"></script>        
    <script type="text/javascript" src="libs/jquery/jquery.lightbox.js"></script>
    <script type="text/javascript" src="libs/jquery/jquery.highlight-3.js"></script>
    <script type="text/javascript" src="libs/jquery/jquery.hotkeys-0.7.9.min.js"></script>
    <script type="text/javascript" src="libs/jquery/jquery.qtips.js"></script>
    
    <script type="text/javascript" src="phpgen.js"></script>
    <link rel="stylesheet" type="text/css" href="libs/spinedit/spincontrol.css" media="screen" />
    <script type="text/javascript" src="libs/spinedit/spincontrol.js"></script>

    <link rel="stylesheet" type="text/css" media="all" href="libs/calendar/calendar-win2k-cold-1.css" title="win2k-cold-1" />
    
    <script type="text/javascript" src="libs/calendar/calendar.js"></script>
    <script type="text/javascript" src="libs/calendar/lang/calendar-en.js"></script>
    <script type="text/javascript" src="libs/calendar/calendar-setup.js"></script>
    {$Page->GetCustomPageHeader()}
    
	<script type="text/javascript">
	   $(document).ready(function(){ldelim}
	           $("a[rel=zoom]").lightbox();
	       {rdelim});
	   $(document).ready(function(){ldelim}
	      $('span.more_hint').each(function(i){ldelim}
	          $(this).qtip({ldelim}container:('tip_' + i), content:($(this).children('div.box_hidden').html()), position:'center'{rdelim});
	      {rdelim}); 
	   {rdelim});
    
    sideBarHidden = GetCookie('sideBarHidden') == 'true';

    function ApplySideBarPosition()
    {ldelim}
        if (sideBarHidden)
            $('#right_side_bar').hide();
    {rdelim}
       
    function ToogleSideBar()
    {ldelim}
        if (sideBarHidden)
            $('#right_side_bar').show('normal');
        else
            $('#right_side_bar').hide('normal');
        sideBarHidden = !sideBarHidden;
        SetCookie('sideBarHidden', sideBarHidden ? 'true' : 'false');
    {rdelim}
    
	</script>

</head>