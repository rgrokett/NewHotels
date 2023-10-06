<?php /* Smarty version 2.6.19, created on 2009-12-25 14:44:14
         compiled from common/page_header.tpl */ ?>
<head>
    <meta http-equiv="content-type" content="text/html<?php if ($this->_tpl_vars['Page']->GetContentEncoding() != null): ?>; charset=<?php echo $this->_tpl_vars['Page']->GetContentEncoding(); ?>
<?php endif; ?>">
    <meta name="generator" content="Maestro PHP Generator">
    <title><?php echo $this->_tpl_vars['Page']->GetCaption(); ?>
</title>
    <!--[if lte IE 6]>
    <style>
        th { behavior: url('iepngfix.htc'); }
        div.site_header_pad
        {
            margin: 0px;
            display: none;
        }
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
    <?php echo $this->_tpl_vars['Page']->GetCustomPageHeader(); ?>

    
	<script type="text/javascript">
	   $(document).ready(function(){
	           $("a[rel=zoom]").lightbox();
	       });
	   $(document).ready(function(){
	      $('span.more_hint').each(function(i){
	          $(this).qtip({container:('tip_' + i), content:($(this).children('div.box_hidden').html()), position:'center'});
	      }); 
	   });
    
    sideBarHidden = GetCookie('sideBarHidden') == 'true';

    function ApplySideBarPosition()
    {
        if (sideBarHidden)
            $('#right_side_bar').hide();
    }
       
    function ToogleSideBar()
    {
        if (sideBarHidden)
            $('#right_side_bar').show('normal');
        else
            $('#right_side_bar').hide('normal');
        sideBarHidden = !sideBarHidden;
        SetCookie('sideBarHidden', sideBarHidden ? 'true' : 'false');
    }
    
	</script>

</head>