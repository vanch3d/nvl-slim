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

	 return self;

 }( jNVL || {}, jQuery ));


