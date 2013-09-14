<?



?>

<head>
	<link href="styles.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="jquery.autocomplete.js"></script>
</head>

<input type="text" name="q" id="tags" value="Search for a Tag"/>

<? 
include("../connect.php");
include("../functions.php");

$list_of_tags=implode(",",array_keys(tag_list()));
?>

<script type="text/javascript">
//<![CDATA[


var a2;

// function InitMonths() {
// 	a2.setOptions({lookup: '<?php echo $list_of_tags; ?>'.split(',') });
// }

a2 = $('#tags').autocomplete({
  width: 300,
  delimiter: /(,|;)\s*/,
  lookup: '<?php echo $list_of_tags; ?>'.split(',')
});

jQuery(function() {
$('#navigation a').each(function() {
  $(this).click(function(e) {
    var element = $(this).attr('href');
    $('html').animate({ scrollTop: $(element).offset().top }, 300, null, function() { document.location = element; });
    e.preventDefault();
  });
});
});

//]]>
</script>