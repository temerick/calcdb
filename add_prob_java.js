

// called when the text boxes change

function boxes_changed()
{
	document.getElementById("add_preview").disabled = false;
	document.getElementById("add_revert").disabled = false;
	document.getElementById("add_copy").disabled = true;
	document.getElementById("add_save").disabled = true;
	document.getElementById("add_add").disabled = true;
	document.getElementById("add_delete").disabled = true;
}




// called when directions dropdown is changed

function directions_changed() 
{
	if (document.add_type_select.type.value == "create_new_type")
	{
		//disable_everything();
		//document.getElementById("add_boxes").style.display = "none";
		//document.getElementById("add_controls").style.display = "none";
		xmlhttp=new XMLHttpRequest();

		xmlhttp.onreadystatechange=function() { 
			if (xmlhttp.readyState==4 && xmlhttp.status==200) {
				document.getElementById("new_directions").innerHTML=xmlhttp.responseText;
			}
		}

		xmlhttp.open("GET","add_type_form.php",true); 
		xmlhttp.send();
	} else {
		document.getElementById("new_directions").innerHTML="";
	}
}




// function to call when the preview button is hit
function previewed()
{
	document.getElementById("add_preview").disabled = true;
	document.getElementById("add_copy").disabled = false;
	document.getElementById("add_save").disabled = false;
	document.getElementById("add_add").disabled = false;
}


// disables the textboxes to await a different action
function disable_everything()
{
	document.getElementById("prob_box").disabled = true;
	document.getElementById("answer_box").disabled = true;
	document.getElementById("type_box").disabled = true;
	document.getElementById("add_preview").disabled = true;
	document.getElementById("add_revert").disabled = true;
	document.getElementById("add_copy").disabled = true;
	document.getElementById("add_save").disabled = true;
	document.getElementById("add_add").disabled = true;
	document.getElementById("add_delete").disabled = true;
}







// PREVIEW the preview pane of the add problem form

function add_form_preview(uid)
{
	// going to search for $$ and warn about them, because they suck and shouldn't be used for a variety of reasons
	// (regex searching for $ is silly.) -- also check for \displaystyle
	if (document.add_prob.prob.value.search("\\$\\$") == -1 && document.add_prob.answer.value.search("\\$\\$") == -1 && document.add_prob.prob.value.search("\\\\displaystyle") == -1 && document.add_prob.answer.value.search("\\\\displaystyle") == -1 && (document.add_prob.prob.value.split("$").length-1)%2==0 && (document.add_prob.answer.value.split("$").length-1)%2==0)
	{
		xmlhttp=new XMLHttpRequest();

		xmlhttp.onreadystatechange=function() { 
			if (xmlhttp.readyState==4 && xmlhttp.status==200) {
				document.getElementById("prob_preview").innerHTML=xmlhttp.responseText;
				MathJax.Hub.Queue(["Typeset",MathJax.Hub,"prob_preview"]); // re-renders page with MathJax
				previewed();
			}
		}

		params=
			"baseuid="+uid
			+"&type="+encodeURIComponent(document.add_type_select.type.value)
			+"&prob="+encodeURIComponent(document.add_prob.prob.value)
			+"&sol="+encodeURIComponent(document.add_prob.answer.value)
			+"&preview=true";
		xmlhttp.open("POST","prob_preview.php", true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.setRequestHeader("Content-length", params.length);
		xmlhttp.setRequestHeader("Connection", "close");
		xmlhttp.send(params);
	} else {
		alert("Do not use $$ in your LaTeX. Please use \\[ and \\] instead. The use of \\displaystyle is unnecessary, they are added automatically. Also, dollar signs should only be used to delimit math, not as a literal dollar sign.");
		boxes_changed();
	}
}

// inital run of preview called when box is first opened
function add_form_preview_init(uid)
{

	xmlhttp=new XMLHttpRequest();

	xmlhttp.onreadystatechange=function() { 
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			document.getElementById("prob_preview").innerHTML=xmlhttp.responseText;
			MathJax.Hub.Queue(["Typeset",MathJax.Hub,"prob_preview"]); // re-renders page with MathJax
		}
	}

	params="uid="+uid;
	xmlhttp.open("POST","prob_preview.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.send(params);
}


// REVERT the preview pane of the add problem form.... just reloads the page, baby.

function add_form_revert(uid)
{

	window.location.reload()

}



// UPDATE a problem in the database

function add_form_save_to_db(uid)
{
	xmlhttp=new XMLHttpRequest();

	xmlhttp.onreadystatechange=function() { 
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			document.getElementById("add_controls").innerHTML=xmlhttp.responseText;

			disable_everything();
		}
	}

	params=
		"uid="+uid
		+"&type="+encodeURIComponent(document.add_type_select.type.value)
		+"&prob="+encodeURIComponent(document.add_prob.prob.value)
		+"&sol="+encodeURIComponent(document.add_prob.answer.value);
	xmlhttp.open("POST","database_update.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.send(params);
}



// ADD a problem in the database

function add_form_add_to_db(uid)
{
	xmlhttp=new XMLHttpRequest();

	xmlhttp.onreadystatechange=function() { 
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			document.getElementById("add_controls").innerHTML=xmlhttp.responseText;
			disable_everything();		
		}
	}
	
	if(document.add_type_select.type.value=="create_new_type") {
		params="typename="+encodeURIComponent(document.add_type.type_name.value)
			+"&directions="+encodeURIComponent(document.add_type.directions_text.value)
			+"&prob="+encodeURIComponent(document.add_prob.prob.value)
			+"&sol="+encodeURIComponent(document.add_prob.answer.value);
	} else {
		params=
			"baseuid="+uid
			+"&type="+encodeURIComponent(document.add_type_select.type.value)
			+"&prob="+encodeURIComponent(document.add_prob.prob.value)
			+"&sol="+encodeURIComponent(document.add_prob.answer.value);
	}
	xmlhttp.open("POST","database_insert.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.send(params);
}


// DELETE a problem in the database

function add_form_delete_from_db(uid)
{
	ans=confirm("This will PERMANENTLY DELETE this problem from the database. Are you sure?");
	if (ans) {
		xmlhttp=new XMLHttpRequest();

		xmlhttp.onreadystatechange=function() { 
			if (xmlhttp.readyState==4 && xmlhttp.status==200) {
				document.getElementById("add_controls").innerHTML=xmlhttp.responseText;

				disable_everything();
			}
		}

		params="uid="+uid;
		xmlhttp.open("POST","database_delete.php", true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.setRequestHeader("Content-length", params.length);
		xmlhttp.setRequestHeader("Connection", "close");
		xmlhttp.send(params);
	}
}

// refresh the directions list. Can this be deleted?
function refresh_directions() 
{
	xmlhttp=new XMLHttpRequest();

	xmlhttp.onreadystatechange=function() { 
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			document.getElementById("directions_box").innerHTML=xmlhttp.responseText;
		}
	}

	xmlhttp.open("GET","add_prob_directions_box.php",true); 
	xmlhttp.send();
}



// Remove a tag from a given problem.
function remove_tag(probuid, probtaguid) {
	xmlhttp=new XMLHttpRequest();

	xmlhttp.onreadystatechange=function() { 
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			document.getElementById("tag_list").innerHTML=xmlhttp.responseText;
		}
	}
	
	params="probuid="+probuid+"&probtaguid="+probtaguid;
	xmlhttp.open("POST","add_prob_tag_box.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.send(params);
}


// called when a type is attempted to be created. Can this be deleted?!?

function create_type()
{
		xmlhttp=new XMLHttpRequest();

		xmlhttp.onreadystatechange=function() { 
			if (xmlhttp.readyState==4 && xmlhttp.status==200) {
				document.getElementById("new_directions").innerHTML=xmlhttp.responseText;
			}
		}

		params="typename="+encodeURIComponent(document.add_type.type_name.value)+"&directions="+encodeURIComponent(document.add_type.directions_text.value);
		xmlhttp.open("POST","add_type_form.php", true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.setRequestHeader("Content-length", params.length);
		xmlhttp.setRequestHeader("Connection", "close");
		xmlhttp.send(params);
} 






// called when type creation is cancelled.

function cancel_create_type()
{
	document.getElementById("prob_preview").innerHTML="";
	document.getElementById("prob_box").disabled = false;
	document.getElementById("answer_box").disabled = false;
	document.getElementById("type_box").disabled = false;
	document.getElementById("type_box").options.selectedIndex = 0;
	document.getElementById("add_preview").disabled = false;
	document.getElementById("add_revert").disabled = false;
	document.getElementById("add_copy").disabled = true;
	document.getElementById("add_save").disabled = true;
	document.getElementById("add_add").disabled = true;
	document.getElementById("add_delete").disabled = true;
	document.getElementById("add_boxes").style.display = "";
	document.getElementById("add_controls").style.display = "";
} 




// called when type creation is finished.

function type_created(type_name, directions)
{

	document.getElementById("prob_box").disabled = false;
	document.getElementById("answer_box").disabled = false;
	document.getElementById("type_box").disabled = false;

	var select = document.getElementById("type_box");
	select.options[select.options.length] = new Option(directions,type_name);

	document.getElementById("type_box").options.selectedIndex = select.options.length-1;

	document.getElementById("prob_preview").innerHTML="";
	document.getElementById("add_preview").disabled = false;
	document.getElementById("add_revert").disabled = false;
	document.getElementById("add_copy").disabled = true;
	document.getElementById("add_save").disabled = true;
	document.getElementById("add_add").disabled = true;
	document.getElementById("add_delete").disabled = true;
	document.getElementById("add_boxes").style.display = "";
	document.getElementById("add_controls").style.display = "";
} 
