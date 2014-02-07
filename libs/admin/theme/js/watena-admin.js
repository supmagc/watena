
var oWatena = new (function() {
	this.sSearchDefault = '';
})();

function loaderCallback() {
	requestContent();

	oWatena.sSearchDefault = $("#search_txt").val();
	$("#search_txt").focus(function() {
		if(this.value == oWatena.sSearchDefault)
			this.value = "";
	});
	$("#search_txt").blur(function() {
		if(this.value == "")
			this.value = oWatena.sSearchDefault;
	});
}

function displayLogin() {
}