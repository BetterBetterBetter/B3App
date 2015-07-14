EasyBlog.module("composer/panels/post", function($){

    var module = this;

    EasyBlog.Controller("Composer.Panels.Post", {
        defaultOptions: {

            // Title of the blog post
            "{title}": "[data-post-title]",

            // Permalink
            permalink: "",
            "{permalinkData}": "[data-permalink-data]",
            "{permalinkInput}": "[data-permalink-input]",
            "{savePermalink}": "[data-permalink-save]",
            "{editPermalink}": "[data-permalink-edit]",
            "{cancelEditPermalink}": "[data-permalink-edit-cancel]",
            "{permalinkEditor}": "[data-permalink-editor]",
            "{permalinkPreview}": "[data-permalink-preview]"
        }
    }, function(self, opts, base, composer) {

        return {

            init: function() {
                // Initilize the permalink value
                opts.permalink = self.permalinkInput().val();
            },

            hidePermalinkForm: function() {
                // Hide the preview
                self.permalinkEditor().addClass('hide');

                // Show the editor
                self.permalinkPreview().removeClass('hide');
            },

            showPermalinkForm: function() {
                // Hide the preview
                self.permalinkPreview().addClass('hide');

                // Show the editor
                self.permalinkEditor().removeClass('hide');
            },

            savePermalinkForm: function() {
                // Generate a proper permalink given the edited permalink value
                var value = self.permalinkInput().val();

                // Request from the server
                EasyBlog.ajax('site/views/composer/normalizePermalink', {
                    "permalink": value
                }).done(function(permalink) {

                    opts.permalink = permalink;

                    // Ensure that the input is always the same as the modified version
                    self.permalinkInput().val(opts.permalink);

                    // Update the preview
                    self.permalinkData().html(permalink);

                    // Hide the form
                    self.hidePermalinkForm();
                });
            },

            "{cancelEditPermalink} click": function(el, event) {
                // Reset to the original value.
                self.permalinkInput().val(opts.permalink);

                self.hidePermalinkForm();
            },

            "{permalinkInput} keyup": function(el, event) {
                var code = event.keyCode ? event.keyCode : event.which;

                if (code == 13) {

                    self.savePermalinkForm();
                }
            },

            "{savePermalink} click": function(el, event) {
                self.savePermalinkForm();
            },

            "{editPermalink} click": function(el, event) {
                self.showPermalinkForm();
            },

            "{title} change": function(el, event) {
                var value = $(el).val();

                // Update the permalink only if this entry has not been edited before
                if (opts.permalink != '') {
                    return false;
                }

                // Set the title as the permalink value
                self.permalinkInput().val(value);

                // Validate the permalink
                self.savePermalinkForm();
            },

            "{self} composerSelectTemplate": function(el, event, templateId, title, permalink, documentHtml) {

                // If title is not empty, set it here
                if (title) {
                    self.title().val(title);
                }

                if (permalink) {
                    self.permalinkInput().val(permalink);
                    self.savePermalinkForm();
                }
            }
        }
    });

    module.resolve();

});
