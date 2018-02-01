/*	TL.Media.GitHub
    Load a GitHub resources. Currently implemented:
        - releases (https://api.github.com/repos/{user}/{repos}/releases/{release)
================================================== */

TL.Media.GitHub = TL.Media.extend({

    includes: [TL.Events],

    /*	Load the media
    ================================================== */
    _loadMedia: function() {
        var api_url,
            self = this;

        console.log(this.data);

        // Create Dom element
        this._el.content_item	= TL.Dom.create("div", "tl-media-item tl-media-github", this._el.content);
        this._el.content_container.className = "tl-media-content-container tl-media-content-container-text";

        // Get Media ID
        this.media_id = this.data.url.split("repos/")[1].split("/releases/").join("@");

        // API Call
        TL.ajax({
            type: 'GET',
            url: this.data.url,
            dataType: 'json', //json data type

            success: function(d){
                console.log(d);
                self.createMedia(d);
            },
            error:function(xhr, type){
                var error_text = "";
                error_text += "Unable to load github" + "<br/>" + self.media_id + "<br/>" + type;
                self.loadErrorDisplay(error_text);
            }
        });

    },

    createMedia: function(d) {
            var content = "";

            content		+=	"<span class='tl-icon-github'></span>";
            content		+=	"<div class='tl-wikipedia-title'><h4><a href='" + this.data.html_url + "' target='_blank'>" + d.name+ "</a></h4>";

            content		+=	d.body.substr(0,255) + " [...]";

            // Add to DOM
            this._el.content_item.innerHTML	= content;
            // After Loaded
            this.onLoaded();

    },

    updateMediaDisplay: function() {

    },

    _updateMediaDisplay: function() {

    }

});
