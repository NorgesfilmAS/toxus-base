/**
 * the nice menu's
 */
/*================================================================*/
/*	DESKTOP MENU
/*================================================================*/
if (document.documentElement.clientWidth > 767) { //if client width is greater than 767px

ddsmoothmenu.init({
	mainmenuid: "main_menu", 
	orientation: 'h',
	contentsource: "markup",
	showhidedelay: {showdelay: 300, hidedelay: 100} //set delay in milliseconds before sub menus appear and disappear, respectively
})

} // end if 


