/**
 * @author Martin Klang <mars@pingdynasty.com>
 * @author Sam Artuso <sam@highoctanedev.co.uk>
 */

/**
 * @namespace
 */
var HoxtonOwl;
if (!HoxtonOwl) {
    HoxtonOwl = {};
}

/**
 * Conveniently groups some utility functions to handle patches.
 * 
 * @namespace
 */
HoxtonOwl.patchManager = {

    /**
     * Fetches a file from GitHub.
     * 
     * @param {string} url
     *     The URL.
     * @param {Function} callback
     *     A callback returned data will be passed to.
     * @param {number} startLineNum
     *     Selection start.
     * @param {number} endLineNum
     *     Selection end.
     */
    getGithubFile: function(url, callback, startLineNum, endLineNum) {
        
        startLineNum = (typeof startLineNum == "undefined") ? 1 : startLineNum;
        endLineNum = (typeof endLineNum == "undefined") ? 0 : endLineNum;
        
        // input:
        // 
        // https://github.com/pingdynasty/OwlPatches/blob/master/Contest/ConnyPatch.hpp
        //                    [+++++++++] [++++++++]      [++++] [++++++++++++++++++++]
        //                    owner       repo            branch file path
        //                    
        // output:
        // https://api.github.com/repos/pingdynasty/OwlPatches/contents/Contest/ConnyPatch.hpp?ref=master
        //                              [+++++++++] [++++++++]          [++++++++++++++++++++]     [++++]
        //                              owner       repo                path                       branch
        
        var bits     = url.split('/');
        var repo     = bits.slice(3,5).join('/');
        var branch   = bits[6];
        var path     = bits.slice(7).join('/');
        var filename = bits[bits.length-1];
        var endpoint = 'https://api.github.com/repos/' + repo + '/contents/' + path + '?ref=' + branch;
        
        $.ajax({
            type:     "GET",
            url:      endpoint,
            dataType: "jsonp",
            success:  function(data) {
                HoxtonOwl.patchManager.getGithubFile.count++;
                if (typeof data.data.content != "undefined") {
                    if (data.data.encoding == "base64") {
                        var base64EncodedContent = data.data.content;
                        base64EncodedContent = base64EncodedContent.replace(/\n/g, "");
                        var content = window.atob(base64EncodedContent);
                        var contentArray = content.split("\n");
                        if (endLineNum == 0) {
                            endLineNum = contentArray.length;
                        }
                        callback(contentArray.slice(startLineNum - 1, endLineNum).join("\n"), filename);
                        return;
                    }
                }
                callback('// This file could not be fetched. Is it from a public GitHub repository?', filename);
            },
            error: function(data) {
                HoxtonOwl.patchManager.getGithubFile.count++;
                callback('// This file could not be fetched because of an unexpected error.', filename);
            }
        })
    },
    
    /**
     * Contains the code that operates the patches page.
     * 
     * @param {Object[]} patches
     *     An array of objects that represent patches.
     * @param {string[]} authors
     *     The authors.
     * @param {string[]} tags
     *     The tags.
     */
    main: function(patches, authors, tags) {
        
        var that = this;
        var pm = HoxtonOwl.patchManager;
        
        that.selectedPatch = ko.observable();       // currently selected patch
        that.patches = ko.observableArray(patches); // all patches
        that.authors = ko.observableArray(authors); // all authors
        that.tags = ko.observableArray(tags);       // all tags

        that.search = ko.observable();              // one of 'all', 'search', 'tag' or 'patch'
        that.searchItems = ko.observableArray();

        that.filteredPatches = ko.computed(function() {
            //console.log('filteredPatches');
            //console.log(that.searchItems());
            return ko.utils.arrayFilter(that.patches(), function(r) {
                //console.log(that.searchItems);
                if(that.searchItems.indexOf("All") > -1) {
                    return true;
                }
                if(that.search() === "tag") {
                    for (i=0; i<r.tags.length; ++i) {
                        if(that.searchItems.indexOf(r.tags[i]) > -1) {
                            return true;
                        }
                    }
                } else if(that.search() === "author") {
                    return that.searchItems.indexOf(r.author) > -1;
                }
                return false;
            });
        });

        that.selectAllPatches = function() {
            //console.log('selectAllPatches');
            pm.updateBreadcrumbs();
            
            that.selectedPatch(null);
            that.search('all');
            that.searchItems.removeAll();
            that.searchItems.push('All');
        };
        
        that.selectFilter = function(item) {
            //console.log("select filter "+item+" searching "+that.search());
            if(that.search() === "author") {
                return selectAuthor(item);
            } else {
                return selectTag(item);
            }
        };

        that.selectAuthor = function(author) {
            pm.updateBreadcrumbs();
            if(author.author) {
                author = author.author;
            }
            //console.log("select author "+author);
            that.selectedPatch(null);
            if(that.search() != "author") {
                that.search("author");
                that.searchItems.removeAll();
                that.searchItems.push(author);
            } else if (that.searchItems.indexOf(author) > -1) {
                that.searchItems.remove(author);
                if(that.searchItems().length === 0) {
                    that.searchItems.push("All");
                }
            } else {
                if (author === "All") {
                    that.searchItems.removeAll();
                    that.searchItems.push('All'); // added by Sam
                } else {
                    that.searchItems.remove("All"); 
                    that.searchItems.push(author);
                }
            }
        };

        that.selectTag = function(tag) {
            //console.log("select tag " + tag);
            that.selectedPatch(null);
            if (that.search() != "tag") {
                that.search("tag");
                that.searchItems.removeAll();
                that.searchItems.push(tag);
            } else if (that.searchItems.indexOf(tag) > -1) {
                that.searchItems.remove(tag);
                if(that.searchItems().length == 0) {
                    that.searchItems.push("All");
                }
            } else {
                if (tag === "All") {
                    that.searchItems.removeAll();
                    that.searchItems.push('All'); // added by Sam
                } else {
                    that.searchItems.remove("All"); 
                    that.searchItems.push(tag);
                }
            }
        };

        that.selectOnlyTag = function(tag) {
            //console.log('selectOnlyTag');
            that.searchItems.removeAll();
            selectTag(tag);
        };

        that.selectAllTags = function(tag) {
            //console.log('selectAllTags');
            pm.updateBreadcrumbs();
            selectTag('All');
        };

        that.selectOnlyAuthor = function(author) {
            //console.log('selectOnlyAuthor');
            that.searchItems.removeAll();
            selectAuthor(author);
        };

        that.selectAllAuthors = function(tag) {
            //console.log('selectAllAuthors');
            pm.updateBreadcrumbs();
            selectAuthor('All');
        };

        that.selectPatch = function(patch) {
            pm.updateBreadcrumbs(patch);
            
            var patchId = patch._id;
            var apiClient = new HoxtonOwl.ApiClient();
            apiClient.getSinglePatch(patchId, function(patch) {
                
                
                if (name.name) {
                    name = name.name;
                }
                //console.log("select patch "+name);
                that.search("patch");
                that.searchItems.removeAll();
                $("#gitsource").empty();
                that.selectedPatch(patch);
                
                //var url = "https://api.github.com/repos/" + that.selectedPatch().repo + "/contents/" + that.selectedPatch().github;
                
                $('#github-files').empty();
                if (that.selectedPatch().github) {
                    for (var i = 0, max = that.selectedPatch().github.length; i < max; i++) {
                        
                        pm.getGithubFile(that.selectedPatch().github[i], function(contents, filename) {
                            
                            var cnt;
                            
                            if (0 === $('#github-files > ul').length) {
                                $('#github-files').html('<ul></ul>');
                            }
                            cnt = $('#github-files > ul > li').length;
                            
                            cnt++;
                            $('#github-files > ul').append('<li><a href="#tabs-' + cnt + '">' + filename + '</a></li>');
                            $('#github-files').append('<div id="tabs-' + cnt + '"><pre class="prettyprint"></pre></div>');
                            $('#github-files pre.prettyprint').text(contents);
                            if (HoxtonOwl.patchManager.getGithubFile.count == max) {
                                // no more files to be loaded
                                prettyPrint();
                                $('#github-files').tabs({ active: 0 }); // jQuery-UI tabs
                            }
                            
                            //$("#gitsource").text(contents).removeClass("prettyprinted").parent();
                        });
                    }
                } else {
                    $('#github-files').append('<p>No code available.</p>');
                }
                
                knobify();
                
            });
        };

        that.soundcloud = ko.computed(function() {
            if(that.selectedPatch() && that.selectedPatch().soundcloud && that.selectedPatch().soundcloud.length) {
                
                var iframeSrcs = [];
                for (var i = 0, max = that.selectedPatch().soundcloud.length; i < max; i++) {
                    iframeSrcs.push(
                        "https://w.soundcloud.com/player/?url=" +
                        encodeURIComponent(that.selectedPatch().soundcloud[i]) +
                        "&amp;auto_play=false&amp;hide_related=false&amp;show_comments=true&amp;show_user=true&amp;show_reposts=false&amp;visual=true"
                    );
                }
                return iframeSrcs;
                
            } else {
                return "";
            }
        });

        ko.applyBindings(that);
        
        var url = location.pathname;
        var matches = url.match(/^\/patch-library\/patch\/.+\/?$/g);
        if (matches) {
            var seoName = url.split('/')[3];
            var apiClient = new HoxtonOwl.ApiClient();
            apiClient.getSinglePatchBySeoName(seoName, selectPatch);
        } else {
            selectTag("All");
            that.search("all");
        }
    },
    
    /**
     * Navigates to a patch page.
     */
    openPatch: function(patch) {
        location = '/patch-library/patch/' + patch.seoName;
    },
    
    /**
     * Navigates to the patch edit page.
     */
    editPatch: function(patch) {
        location = '/edit-patch/' + patch.seoName;
    },
    
    updateBreadcrumbs: function(patch) {
        $('#breadcrumbs li').slice(2).remove();
        if (patch) {
            document.title = 'The OWL | ' + patch.name;
            $('#breadcrumbs').append('<li><a href="/patch-library/" rel="category tag">Patches</a></li><li class="separator"> / </li><li>' + patch.name + '</li>');
        } else {
            $('#breadcrumbs').append('<li><strong>Patches</strong></li>');
            document.title = 'The OWL | Patch Library';
        }
    }
};

HoxtonOwl.patchManager.getGithubFile.count = 0;

$(function() {
    
    var pm = HoxtonOwl.patchManager;
    var apiClient = new HoxtonOwl.ApiClient();
    apiClient.getAllPatches(pm.main);
});

// EOF