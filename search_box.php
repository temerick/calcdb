<?
include("connect.php");
?>

<input class="search_box" type="text" name="q" id="tags" value="Search for a Tag" style="color:gray;" onfocus="if(this.value=='Search for a Tag'){this.value='';this.style.color='black'} javascript:close_everything_but('')" onclick="javascript:close_everything_but('')" onkeydown="if (event.keyCode==13) { javascript:close_everything_but('');search_query(document.getElementById('tags').value); }" size="15"/>




<? 

$all_tags=optimized_tags();

$list_of_tags=implode(",",array_keys($all_tags));
?>

<script type="text/javascript">
//<![CDATA[


var a1;

// function InitMonths() {
// 	a2.setOptions({lookup: '<?php echo $list_of_tags; ?>'.split(',') });
// }

a1 = $('#tags').autocomplete({
  width: 132,
  delimiter: /(,|;|:)\s*/,
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
