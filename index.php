<?
ob_start("ob_gzhandler"); // Gzip should speed up website load times.

include("cookie_check.php");
include("cleanup/autoclean.php");
include("functions.php");

if($_COOKIE['username']=="tre8a" || $_COOKIE['username']=="svd5d") {
	$experimental=true;
} else {
	$experimental=false;
}

?>

<html class="main">
<head>
<title>Calculus Problem Database</title>

<link href="jquery/styles.css" rel="stylesheet" type="text/css" />
<link href="greybox/gb_styles.css" rel="stylesheet" type="text/css" media="all" />
<link href="css/style.css" rel="stylesheet" type="text/css" media="all" />

<script type="text/javascript" defer="defer"> var GB_ROOT_DIR = "./greybox/"; </script>
<script type="text/javascript" src="greybox/AJS.js" defer="defer"></script>
<script type="text/javascript" src="greybox/AJS_fx.js" defer="defer"></script>
<script type="text/javascript" src="greybox/gb_scripts.js" defer="defer"></script>
<script type="text/javascript" src="jquery/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="jquery/jquery.autocomplete.js"></script>
<!--<script src="toggle.js" type="text/javascript" defer="defer"></script>-->
<script src="ajax_magic.js" type="text/javascript" defer="defer"></script>
<script type="text/x-mathjax-config">
        MathJax.Hub.Config({"HTML-CSS": { preferredFont: "TeX", availableFonts: ["STIX","TeX"] },
                         tex2jax: { inlineMath: [ ["$", "$"], ["\\\\(","\\\\)"] ], displayMath: [ ["$$","$$"], ["\\[", "\\]"] ], processEscapes: true, ignoreClass: "tex2jax_ignore|dno" },
                         TeX: { noUndefined: { attributes: { mathcolor: "red", mathbackground: "#FFEEEE", mathsize: "90%" } } },
                         messageStyle: "none"
        });
    </script>    
    <script type="text/javascript" src="http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS_HTML"></script>
  

<script type="text/javascript" defer="defer">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-24994476-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>
<body class="main" onload="javascript:page_load();">

<? include("topbar.php"); ?>

<table width=100%>
<tr>
<td valign=top>
<span id="d_query">
</span> <? // end d_query span ?>


</td>
</tr>
</table>
