
// this variable holds the latest hash (page view). it is set to init (a dummy value) so that we can enter the site via a predefined hash, for example by navigating directly to #cart or #restorecart:uidlist
var recentHash = "init";



// index.php's onload function

function page_load()
{
	// function to check the page hash to see if anything has changed and update appropriately
	// this is delicate machinery. don't emerick it up!
	var check_hash = function  () 
	{
		var hash = document.location.hash.substr(1);
		
		//hasharray = hash.split(":");
		//operator = hasharray[0];
		//hasharray.splice(0,1); // remove the first element of the array
		//args = decodeURI(hasharray.join(":")); // merge all of them back together to account for not: operators		
			
		if (decodeURIComponent(hash) == recentHash) { // recentHash is assumed to always be decoded
		        return;
		}
		//alert("hash changed from " +recentHash + " to " + hash + ". ver1");
		recentHash = decodeURIComponent(hash); // recentHash is assumed to always be decoded
		load_content(hash);
	}
	setInterval(check_hash, 500); // need to check hash very often to detect back button presses. 1000 = 1 sec.
}






// function to be called whenever a new dynamic page is "loaded."
// rules for use:
// use this to set a new hash in a function which creates a page from which you should be able to hit "back" and expect proper behavior.
// syntax: you need to call manual_hash("operator:args"), where operator is:
// 	qt: query_tags
//	qu: query_uids
// etc. see the load_content function for all operators.
// generally this won't be necessary, since every ajax_magic function will have its own operator, and you'll only need to add one or call it when you're writing a new function that gives a "full page"
// none of this probably makes sense. sorry.

// EXECUTIVE SUMMARY: if you do an AJAX request into d_query, you should probably add a manual_hash to the end of the AJAX request, and add the corresponding entry in the load_content function.


function manual_hash(hash)
{
	recentHash = decodeURIComponent(hash); // recentHash is assumed to always be decoded
	document.location.hash = hash;
}




// function called when hash changes, so we can load new content appropriately
// this function could also probably be used as a sitemap

function load_content(hash)
{
	//alert("loading content for hash: " + hash);
	
	hasharray = hash.split(":");
	operator = hasharray[0];
	hasharray.splice(0,1); // remove the first element of the array
	args = decodeURI(hasharray.join(":")); // merge all of them back together to account for not: operators
	
	//alert("operator is " + operator);
	
	// here we goooooo. these should _roughly_ correspond to the order the functions appear in this file
	switch (operator)
	{
		case "":
			home(); break;
		
		case "sq":
			search_query(args); break;
			
		case "qt":
			query_tags(args); break;
			
		case "qu":
			query_uids(args); break;
			
		case "restorecart":
			restore_cart(args); break;
			
		case "cart":
			query_cart(); break;
			
		case "listtags":
			list_tags(); break;
			
		case "helpout":
			help_out(); break;
			
		case "userinfo":
			user_info(); break;
			
		case "savedcarts":
			saved_carts(); break;
			
		case "sharedcarts":
			shared_carts(); break;
			
		case "taggame":
			play_tag_game(); break;
			
		case "myprobs":
			query_my_probs(); break;
	
	}
}






// This will be called from the search box. Currently, it only handles a single uid input, or a tag input.
function search_query(item) 
{
	
	xmlhttp=new XMLHttpRequest();

	xmlhttp.onreadystatechange=function() { 
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			manual_hash("sq:"+item);
			document.getElementById("d_query").innerHTML=xmlhttp.responseText;
			MathJax.Hub.Queue(["Typeset",MathJax.Hub,"d_query"]); // re-renders page with MathJax
		}
	}
	
	if(!isNaN(parseInt(item))) {
		// If we have a number...
		xmlhttp.open("GET","query_uids.php?uids="+item); 
		xmlhttp.send();
		document.getElementById("d_query").innerHTML=splines();
	} else {
		// If we have a tag...
		xmlhttp.open("GET","query_tags.php?tags="+encodeURI(item)); 
		xmlhttp.send();
		document.getElementById("d_query").innerHTML=splines();
	}
	
}


// the following are the various query functions

function query_tags(taglist)
{
	xmlhttp=new XMLHttpRequest();

	xmlhttp.onreadystatechange=function() { 
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			manual_hash("qt:" + encodeURI(taglist));
			window.last_query_type = "tags";
			window.last_query_value = taglist;
			document.getElementById("d_query").innerHTML=xmlhttp.responseText;
			MathJax.Hub.Queue(["Typeset",MathJax.Hub,"d_query"]); // re-renders page with MathJax
		}
	}
	
	xmlhttp.open("GET","query_tags.php?tags="+encodeURI(taglist)); 
	xmlhttp.send();
	document.getElementById("d_query").innerHTML=splines();
}

function query_uids(uidlist)
{
	xmlhttp=new XMLHttpRequest();

	xmlhttp.onreadystatechange=function() { 
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			manual_hash("qu:" + encodeURI(uidlist));
			window.last_query_type = "uids";
			window.last_query_value = uidlist;
			document.getElementById("d_query").innerHTML=xmlhttp.responseText;
			MathJax.Hub.Queue(["Typeset",MathJax.Hub,"d_query"]); // re-renders page with MathJax
		}
	}
	
	xmlhttp.open("GET","query_uids.php?uids="+uidlist); 
	xmlhttp.send();
	document.getElementById("d_query").innerHTML=splines();
}

function query_cart()
{
	xmlhttp=new XMLHttpRequest();

	xmlhttp.onreadystatechange=function() { 
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			manual_hash("cart");
			window.last_query_type = "cart";
			window.last_query_value = "";
			document.getElementById("d_query").innerHTML=xmlhttp.responseText;
			MathJax.Hub.Queue(["Typeset",MathJax.Hub,"d_query"]); // re-renders page with MathJax
		}
	}
	
	xmlhttp.open("GET","query_cart.php"); 
	xmlhttp.send();
	document.getElementById("d_query").innerHTML=splines();
}

function query_all()
{

}

function query_last()
{

	if (window.last_query_type == "tags")
	{
		query_tags(window.last_query_value);
	}
	else if (window.last_query_type == "uids")
	{
		query_uids(window.last_query_value);
	}
	else if (window.last_query_type == "cart")
	{
		query_cart();
	}
	else if (window.last_query_type == "myprobs")
	{
		query_my_probs();
	}
	else
	{
	
	}
}


// tag box functions
function load_tag_box(uid)
{

	xmlhttp=new XMLHttpRequest();

	xmlhttp.onreadystatechange=function() { 
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			document.getElementById("tagarea"+uid).innerHTML=xmlhttp.responseText;
			document.getElementById('text'+uid).focus();
		}
	}
	
	xmlhttp.open("GET","tag_box.php?uid="+uid); 
	xmlhttp.send();
	document.getElementById("tagarea"+uid).innerHTML="Loading";
	
}

function add_tag(uid,tag)
{
	regex=/^[0-9a-zA-Z ,]+$/;
	if (regex.test(tag))
	{
		xmlhttp=new XMLHttpRequest();

		xmlhttp.onreadystatechange=function() { 
			if (xmlhttp.readyState==4 && xmlhttp.status==200) {
				document.getElementById("taglist"+uid).innerHTML=xmlhttp.responseText;
			}
		}
		
		xmlhttp.open("GET","tag_problem.php?uid="+uid+"&tag="+tag);
		xmlhttp.send();
		document.getElementById("taglist"+uid).innerHTML="Loading";
		document.getElementById("text"+uid).value="";
	}
	else
	{
		alert("Only letters, numbers and spaces are allowed in tag names.");
	}
}



// function to add a problem to the cart by uid

function add_to_cart(uid)
{
	xmlhttp[uid]=new XMLHttpRequest();

	xmlhttp[uid].onreadystatechange=function() { 
		if (xmlhttp[uid].readyState==4 && xmlhttp[uid].status==200) {
			document.getElementById("button"+uid).innerHTML=xmlhttp[uid].responseText;
			update_cart_count();
		}
	}

	xmlhttp[uid].open("GET","addtocart.php?uid="+uid, true);
	xmlhttp[uid].send();
	document.getElementById("button"+uid).innerHTML="<img src='img/ajax-loader.gif'>";
}


// Shares or unshares a cart from the carts menus
function share_cart(cartnumber,personid) {
	xmlhttp=new XMLHttpRequest();
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			document.getElementById("cart"+cartnumber+"_"+personid).innerHTML=xmlhttp.responseText;
		}
	}
	
	xmlhttp.open("GET","cart_share.php?cart="+cartnumber+"&personid="+personid+"&change=true", true);
	xmlhttp.send();
}


// function that accepts multiple uids to add to cart, forcing each one and looping only on completion
// this prevents an overload of ajax requests all at once, which evidently is bad form, and also doesn't work.
function add_to_cart_bulk()
{
	var args = Array.prototype.slice.call(arguments);  

	xmlhttp=new XMLHttpRequest();

	xmlhttp.onreadystatechange=function() { 
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			document.getElementById("button"+args[0]).innerHTML=xmlhttp.responseText;
			args.shift();
			if (args[0] !== undefined)
			{
				add_to_cart_bulk.apply(null, args); // continue the loop of adding problems to cart
			} else {
				update_cart_count(); // if there's nothing left, update the cart count.
			}
		}
	}

	xmlhttp.open("GET","addtocart.php?uid="+args[0]+"&force=true", true);
	xmlhttp.send();
	document.getElementById("button"+args[0]).innerHTML="<img src='img/ajax-loader.gif'>";

}



// function to update the cart count

function update_cart_count()
{
	xmlhttp=new XMLHttpRequest();

	xmlhttp.onreadystatechange=function() { 
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			document.getElementById("cartcount").innerHTML=xmlhttp.responseText;
		}
	}

	xmlhttp.open("GET","print_cart_count.php", true);
	xmlhttp.send();
}


// empty the cart
function empty_cart()
{
	ans=confirm("You are about to remove all your selected problems. This action cannot be undone. Are you sure?");
	if (ans) {
		xmlhttp=new XMLHttpRequest();

		xmlhttp.onreadystatechange=function() { 
			if (xmlhttp.readyState==4 && xmlhttp.status==200) {
				document.getElementById("d_query").innerHTML=xmlhttp.responseText;
				update_cart_count();
				manual_hash("");
			}
		}

		xmlhttp.open("GET","empty_cart.php?header=emptycart", true);
		xmlhttp.send();
	}
}



// restore a cart
function restore_cart(uid_list)
{
	ans=confirm("You are restoring a list of problems. This will REPLACE your currenly selected problems and cannot be undone. Do you wish to continue?");
	if (ans) {
		xmlhttp=new XMLHttpRequest();

		xmlhttp.onreadystatechange=function() { 
			if (xmlhttp.readyState==4 && xmlhttp.status==200) {
				//manual_hash("restorecart:"+uid_list);
				document.getElementById("d_query").innerHTML=xmlhttp.responseText;
				update_cart_count();
				query_cart();
			}
		}

		xmlhttp.open("GET","index_restore_cart.php?restore_cart="+uid_list, true);
		xmlhttp.send();
	}
}


// function to toggle solution display

function toggle_sol_disp()
{
	xmlhttp_sol=new XMLHttpRequest();

	xmlhttp_sol.onreadystatechange=function() { 
		if (xmlhttp_sol.readyState==4 && xmlhttp_sol.status==200) {
			document.getElementById("sol_disp").innerHTML=xmlhttp_sol.responseText;
			query_last();
		}
	}

	xmlhttp_sol.open("GET","print_sol_disp_pref.php?toggle=true", true);
	xmlhttp_sol.send();
}

// function to toggle the display of solutions with the checkbox.

function toggle_sol_disp_checkbox()
{
	xmlhttp_sol=new XMLHttpRequest();

	xmlhttp_sol.onreadystatechange=function() { 
		if (xmlhttp_sol.readyState==4 && xmlhttp_sol.status==200) {
			document.getElementById("solutions_topbar").innerHTML=xmlhttp_sol.responseText;
			conditional_reload();
		}
	}

	xmlhttp_sol.open("GET","toggle.php?q=solutions", true);
	xmlhttp_sol.send();
}


// function to toggle the display of tags with the checkbox.

function toggle_tag_disp_checkbox()
{
	xmlhttp_sol=new XMLHttpRequest();

	xmlhttp_sol.onreadystatechange=function() { 
		if (xmlhttp_sol.readyState==4 && xmlhttp_sol.status==200) {
			document.getElementById("tags_topbar").innerHTML=xmlhttp_sol.responseText;
			conditional_reload();
		}
	}

	xmlhttp_sol.open("GET","toggle.php?q=tags", true);
	xmlhttp_sol.send();
}


// function for the above two checkbox functions, to avoid reloading on pages that don't need it

function conditional_reload()
{
	curhash = document.location.hash.substr(1);
	if (curhash != "" && curhash != "sharedcarts" && curhash != "savedcarts" && curhash != "helpout" && curhash != "userinfo" && curhash != "listtags")
	{
		location.reload(true);
	}
}


// function to toggle tag display

function toggle_tag_disp()
{
	xmlhttp_sol=new XMLHttpRequest();

	xmlhttp_sol.onreadystatechange=function() { 
		if (xmlhttp_sol.readyState==4 && xmlhttp_sol.status==200) {
			document.getElementById("tag_disp").innerHTML=xmlhttp_sol.responseText;
			query_last();
		}
	}

	xmlhttp_sol.open("GET","print_tag_disp_pref.php?toggle=true", true);
	xmlhttp_sol.send();
}



// function to toggle preferences for a given tag

function toggle_tag_pref(tag)
{
	xmlhttp_sol=new XMLHttpRequest();

	xmlhttp_sol.onreadystatechange=function() { 
		if (xmlhttp_sol.readyState==4 && xmlhttp_sol.status==200) {
			document.getElementById("error_box").innerHTML=xmlhttp_sol.responseText;
		}
	}
	
	xmlhttp_sol.open("GET","toggle_tag_pref.php?tag="+encodeURIComponent(tag), true);
	xmlhttp_sol.send();
}


// show the tag preferences page

function list_tags()
{
	xmlhttp_add=new XMLHttpRequest();

	xmlhttp_add.onreadystatechange=function() { 
		if (xmlhttp_add.readyState==4 && xmlhttp_add.status==200) {
			manual_hash("listtags");
			document.getElementById("d_query").innerHTML=xmlhttp_add.responseText;
		}
	}

	xmlhttp_add.open("GET","list_tags.php", true);
	xmlhttp_add.send();
}

// show the help out page

function help_out()
{
	xmlhttp_add=new XMLHttpRequest();

	xmlhttp_add.onreadystatechange=function() { 
		if (xmlhttp_add.readyState==4 && xmlhttp_add.status==200) {
			manual_hash("helpout");
			document.getElementById("d_query").innerHTML=xmlhttp_add.responseText;
		}
	}

	xmlhttp_add.open("GET","help_out.php", true);
	xmlhttp_add.send();
}

// Show the home page

function home()
{
	xmlhttp=new XMLHttpRequest();

	xmlhttp.onreadystatechange=function() { 
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			manual_hash("");
			document.getElementById("d_query").innerHTML=xmlhttp.responseText;
		}
	}

	xmlhttp.open("GET","index_default_content.php", true);
	xmlhttp.send();
	document.getElementById("d_query").innerHTML=splines();
}



// show the user info page

function user_info()
{
	xmlhttp_add=new XMLHttpRequest();

	xmlhttp_add.onreadystatechange=function() { 
		if (xmlhttp_add.readyState==4 && xmlhttp_add.status==200) {
			manual_hash("userinfo");
			document.getElementById("d_query").innerHTML=xmlhttp_add.responseText;
		}
	}

	xmlhttp_add.open("GET","user_info.php", true);
	xmlhttp_add.send();
}

// show the saved carts page

function saved_carts()
{
	xmlhttp_add=new XMLHttpRequest();

	xmlhttp_add.onreadystatechange=function() { 
		if (xmlhttp_add.readyState==4 && xmlhttp_add.status==200) {
			manual_hash("savedcarts");
			document.getElementById("d_query").innerHTML=xmlhttp_add.responseText;
		}
	}

	xmlhttp_add.open("GET","save_cart.php", true);
	xmlhttp_add.send();
}

// show the shared carts page

function shared_carts()
{
	xmlhttp_add=new XMLHttpRequest();

	xmlhttp_add.onreadystatechange=function() { 
		if (xmlhttp_add.readyState==4 && xmlhttp_add.status==200) {
			manual_hash("sharedcarts");
			document.getElementById("d_query").innerHTML=xmlhttp_add.responseText;
		}
	}

	xmlhttp_add.open("GET","shared_carts.php", true);
	xmlhttp_add.send();
}

// Saves a new password.
function save_new_pw()
{
	xmlhttp=new XMLHttpRequest();

	xmlhttp.onreadystatechange=function() { 
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			document.getElementById("d_query").innerHTML=xmlhttp.responseText;
		}
	}

	params=
		"oldpw="+encodeURIComponent(document.edit_pw.oldpw.value)
		+"&newpw1="+encodeURIComponent(document.edit_pw.newpw1.value)
		+"&newpw2="+encodeURIComponent(document.edit_pw.newpw2.value);
	xmlhttp.open("POST","user_info.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.send(params);
}



function save_my_cart()
{
	xmlhttp=new XMLHttpRequest();

	xmlhttp.onreadystatechange=function() { 
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			manual_hash("savedcarts");
			document.getElementById("d_query").innerHTML=xmlhttp.responseText;
		}
	}

	params=
		"cartname="+encodeURIComponent(document.save_cart.cart_name.value);
	xmlhttp.open("POST","save_cart.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.send(params);
}


// allows people to change the course they are currently associated to
function change_current_course(course) {
	xmlhttp=new XMLHttpRequest();

	xmlhttp.onreadystatechange=function() { 
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			document.getElementById("current_selected_course").innerHTML=xmlhttp.responseText;
		}
	}
	
	close_everything_but('');
	
	xmlhttp.open("GET","change_current_course.php?course="+encodeURIComponent(course),true); 
	xmlhttp.send();
}


// play tag game!

function play_tag_game()
{
	xmlhttp=new XMLHttpRequest();

	xmlhttp.onreadystatechange=function() { 
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			manual_hash("taggame");
			document.getElementById("d_query").innerHTML=xmlhttp.responseText;
			MathJax.Hub.Queue(["Typeset",MathJax.Hub,"d_query"]); // re-renders page with MathJax
		}
	}

	xmlhttp.open("GET","tag_game.php",true); 
	xmlhttp.send();
	document.getElementById("d_query").innerHTML=splines();
}

function addTagRevert(uid) {
	document.getElementById("tagarea"+uid).innerHTML="<small>Tags:&nbsp;(<a class='xbutton' href=\"javascript:load_tag_box("+uid+")\">add&nbsp;tag</a>)</small>";
}

// This is only used on the "My carts" page at the moment...
function yesToNo(personid,cartnumber)
{
	if(document.getElementById( cartnumber+"_"+personid ).innerHTML=="Yes") {
		document.getElementById( cartnumber+"_"+personid ).innerHTML="No";
	} else {
		document.getElementById( cartnumber+"_"+personid ).innerHTML="Yes";
	}
}

function switchMenu(obj) {
	var el = document.getElementById(obj);
	if ( el.style.display != "none" ) {
		el.style.display = 'none';
	}
	else {
		el.style.display = '';
	}
}

function popup_image(filename) { // this doesn't seem to be working for the moment...
	window.open( "problem_images/"+filename, "myWindow", "status = 1, height = 300, width = 300, resizable = 0" );
}

function visibility_toggle(div) {
	var current=document.getElementById(div).style.display;
	if(current=="none") {
		document.getElementById(div).style.display="block";
	} else if(current=="block") {
		document.getElementById(div).style.display="none";
	} else {
		alert("Something went wrong."+current);
	}
}

// This closes all currently known divs except for a fixed one.
function close_everything_but(div) {
	//if(div!="list_of_current_course_options") {
	//	document.getElementById('list_of_current_course_options').style.display="none";
	//}
	//if(div!="cart_pop_down") {
	//	document.getElementById('cart_pop_down').style.display="none";
	//}
	if(div!="login_pop_down_options") {
		document.getElementById('login_pop_down_options').style.display="none";
	}

}


// More link for related.
function related_more_toggle() {
	if(document.getElementById('related_search').style.height=="60px") { // need to open.
		document.getElementById('related_search').style.height="auto";
		document.getElementById('related_search_more').innerHTML="<a href='javascript:related_more_toggle();' style='color: black; text-decoration: none;'>Less</a>";
		document.getElementById('related_search_more_td').vAlign="bottom";
	} else { // need to close.
		document.getElementById('related_search').style.height="60px";
		document.getElementById('related_search_more').innerHTML="<a href='javascript:related_more_toggle();' style='color: black; text-decoration: none;'>More</a>";
		document.getElementById('related_search_more_td').vAlign="top";
	}
}

// Query my problems.
function query_my_probs()
{
	xmlhttp_add=new XMLHttpRequest();

	xmlhttp_add.onreadystatechange=function() { 
		if (xmlhttp_add.readyState==4 && xmlhttp_add.status==200) {
			manual_hash("myprobs");
			window.last_query_type = "myprobs";
			document.getElementById("d_query").innerHTML=xmlhttp_add.responseText;
			MathJax.Hub.Queue(["Typeset",MathJax.Hub,"d_query"]); // re-renders page with MathJax
		}
	}

	xmlhttp_add.open("GET","query_my_probs.php", true);
	xmlhttp_add.send();
}


// returns the text for a loading page
function splines()
{
	return "<br><br><center>Reticulating splines...</center>";
}
