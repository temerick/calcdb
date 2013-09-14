<?
include("../connect.php");
include("../functions.php");
$t=$_GET['t'];
if($t=="") $query="SELECT * FROM problems ORDER BY RAND() LIMIT 1";
else $query="SELECT * FROM problems WHERE type='$t' ORDER BY RAND() LIMIT 1";

$random_problem = mysql_fetch_array(mysql_query($query));
$type=$random_problem['type'];
$directions = mysql_fetch_array(mysql_query("SELECT directions FROM directions WHERE type='$type'")); 
?>
<html>
   <head>
      <title>Random Calculus Problem</title>
      <style type="text/css">
         html, body   { height: 100%; margin: 0; padding: 0;}
         div#centered { border: 0; height: 50%; width: 50%;
                        position: absolute; left: 25%; top: 25%; }
         div#problem  { text-align: center; }
         div#buttons  { text-align: right; }
      </style>

<script type="text/x-mathjax-config">
        MathJax.Hub.Config({"HTML-CSS": { preferredFont: "TeX", availableFonts: ["STIX","TeX"] },
                         tex2jax: { inlineMath: [ ["$", "$"], ["\\\\(","\\\\)"] ], displayMath: [ ["$$","$$"], ["\\[", "\\]"] ], processEscapes: true, ignoreClass: "tex2jax_ignore|dno" },
                         TeX: { noUndefined: { attributes: { mathcolor: "red", mathbackground: "#FFEEEE", mathsize: "90%" } } },
                         messageStyle: "none"
        });
    </script>    
    <script type="text/javascript" src="http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS_HTML"></script>

    <script type="text/javascript" src="../toggle.js">
    </script>

   </head>
   <body>
      <div id="centered">
	<? echo $directions['directions']."<br><br><div id='problem'>".build_prob($random_problem['uid'],$random_problem['prob'],0)."</div><br><div id='answer' style='display:none;'><b>Answer:</b>  ".build_prob($random_problem['uid'],$random_problem['answer'],0,"","a")."</div><div id='buttons'><a href=\"javascript:switchMenu('answer')\">Show me the answer</a><br><a href='index.php?t=$type'>Similar problem</a><br><a href='index.php'>New Problem</a></div>"; ?>
      </div>
   </body>
</html>
