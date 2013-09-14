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
	window.open( "problem_images/"+filename, "myWindow", "status = 1, height = 300, width = 300, resizable = 0" )
}
