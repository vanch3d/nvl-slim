/*
 * A wrapper for all the meaningful site-wide operations
 */

 var jNVL = (function (self,$) {

	 var	self = {},
	 		privateVariable = 1;

	 /**
	  *
	  */
	 self.moduleProperty = 1;

	 /**
	  *
	  */
	 function _privateMethod()
	 {
		 console.log("private");
	 }

     self.initSite = function()
     {
         $('.topcontrol a').click(function () {
             $('html, body, .scroller').animate({scrollTop: '0px'}, 800);
             return false;
         });
         console.log("Site controller loaded")
     };


     /**
	  *
	  */
	 self.moduleMethod = function ()
	 {
		 var obj = {
				 "flammable": "inflammable",
				 "duh": "no duh"
				 };
				 $.each( obj, function( key, value ) {
					 console.log( key + ": " + value );
				 });
		 _privateMethod();
		console.log("public");
	 };

     /**
      *
      * @param url
      * @returns {{}}
      */
     self.getProjectsJSON = function(url)
     {
         console.log("getProjectsJSON - " + url);
         var ret = null;
         $.getJSON(url, function(data)
         {
             data.projects = data.projects || {};
             ret = data.projects;
         });
         return ret;
     }

     /**
      * Check the page for references generated by CSL (csl-title) to insert the URL
      * to the PubReader version (<li data-reader=".....">)
      *
      * @todo[vanch3d] check if this could be done directly at the level of CSL transformation
      */
     self.addPubReaderLinks = function()
	 {
         $("li[data-reader]").each(function(idx,obj){
             var elt= $(obj).find("span.csl-title");
             if (!elt) return;
             var newLink = $("<a/>", {
                 href : $(obj).attr("data-reader"),
                 text : elt.text(),
                 title : "Read the full text online"
             });
             elt.empty().append(newLink);
         });

	 }

	 return self;

 }( jNVL || {}, jQuery ));


