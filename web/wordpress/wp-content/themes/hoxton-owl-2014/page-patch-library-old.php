<?php
/**
 * @author Sam Artuso <sam@highoctanedev.co.uk>
 *
 * This is the template for the 'Patches' page.
 *
 * Note that this file is named after the page's slug, following the Wordpress
 * convention "page-{page_slug}.php".
 */

$resUri = get_template_directory_uri() . '/page-patch-library/';

// <link rel="stylesheet"> tags to be placed in <head>
wp_enqueue_style('owl-patches-page_style_css', $resUri . 'style.css', array(), '5.0.1');
wp_enqueue_style('jquery-ui-style', get_template_directory_uri() . '/js/jquery-ui-1.11.4.custom/jquery-ui.min.css');
wp_enqueue_style('jquery-ui-style-structure', get_template_directory_uri() . '/js/jquery-ui-1.11.4.custom/jquery-ui.structure.min.css');
wp_enqueue_style('jquery-ui-style-theme', get_template_directory_uri() . '/js/jquery-ui-1.11.4.custom/jquery-ui.theme.min.css');

// <script> tags to be placed in <head>
wp_enqueue_script('owl-patches-page_knockout',      $resUri . 'js3rdparty/knockout-2.0.0.js');
wp_enqueue_script('jquery',                         $resUri . 'js3rdparty/jquery-1.7.1.min.js');
wp_enqueue_script('jquery-ui',                      get_template_directory_uri() . '/js/jquery-ui-1.11.4.custom/jquery-ui.min.js', array('jquery'));
wp_enqueue_script('owl-patches-page_jquery_knob',   $resUri . 'js3rdparty/jquery.knob.min.js', array('jquery'));

wp_enqueue_script('owl-patches-page_prettify',      'https://cdn.rawgit.com/google/code-prettify/master/loader/prettify.js');
wp_enqueue_script('owl-patches-page_run_prettify',  'https://cdn.rawgit.com/google/code-prettify/master/loader/run_prettify.js');

wp_enqueue_script('midi-client',                    $resUri . 'js/midiclient.js');
wp_enqueue_script('owl-midi-control',               $resUri . 'js/OpenWareMidiControl.js');
wp_enqueue_script('owl-cmd',                        $resUri . 'js/owlcmd.js');

// <script> tags to be placed just before </body>
wp_enqueue_script('owl-api-client',                 get_template_directory_uri() . '/js/hoxtonowl-api-client.js', array('jquery'), false, true);
wp_enqueue_script('owl-patch',                      get_template_directory_uri() . '/js/hoxtonowl-patch.js', array(), false, true);
wp_enqueue_script('owl-patches-page_patch_manager', $resUri . 'js/patchManager.js', array('jquery', 'jquery-ui', 'owl-patch', 'owl-api-client'), '5.0.1', true);
wp_enqueue_script('pd-fileutils',                   $resUri . 'js3rdparty/pd-fileutils-latest.min.js', ['owl-patches-page_patch_manager'], false, true);

?>

<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/html-header', 'parts/shared/header' ) ); ?>


<div id="secondary-nav-bar">
    <div id="secondary-nav-bar-content">
        <div class="secondary-nav-item" id="sort-patches-by-CreationTimeUtc" data-bind="css: { active: (search() === 'all' && patchSortOrder() === 'creationTimeUtc') }, click: selectAllPatches">
          <img src="<?= $resUri ?>images/latest-icon.png" width="40" height="40" alt="icon">
          <p>Latest</p>
        </div>
        <!-- <div class="secondary-nav-item"> -->
        <!--   <img src="<?= $resUri ?>images/popular-icon.png" width="40" height="40" alt="icon"> -->
        <!--   <p>Popular</p> -->
        <!-- </div> -->
        <div class="secondary-nav-item" data-bind="css: { active: search() === 'tag' }, click: selectAllTags">
            <img src="<?= $resUri ?>images/tags-icon.png" width="40" height="40" alt="icon">
            <p>Tags</p>
        </div>
        <div class="secondary-nav-item" data-bind="css: { active: search() === 'author' }, click: selectAllAuthors">
            <img src="<?= $resUri ?>images/authors-icon.png" width="40" height="40" alt="icon">
            <p>Authors</p>
        </div>
        <div class="secondary-nav-item" id="sort-patches-by-Name" data-bind="css: { active: (search() === 'all' && patchSortOrder() === 'name') }, click: selectAllPatches">
            <img src="<?= $resUri ?>images/browse-all-icon.png" width="40" height="40" alt="icon">
            <p>All</p>
        </div>
        <?php if (is_user_logged_in()): ?>
        <div class="secondary-nav-item" data-bind="css: { active: search() === 'myPatches' }, click: selectMyPatches">
            <img src="<?= $resUri ?>images/my-patches-icon.png" width="40" height="40" alt="icon">
            <p>My patches</p>
        </div>
        <?php endif; ?>
        <!-- <div class="secondary-nav-item"> -->
        <!--     <img src="<?= $resUri ?>images/my-rigs-icon.png" width="40" height="40" alt="icon"> -->
        <!--     <p>My rigs</p> -->
        <!-- </div> -->
    </div>
</div>

<div id="filter-bar" data-bind="if: search() != 'patch' && search() != 'all' && search() != 'myPatches'">
    <div id="filter-wrapper" class="wrapper" data-bind="foreach: search() === 'author' ? authors : tags">
        <div class="tag-filter-button" data-bind="text: $data, click: selectFilter, css: { active: searchItems.indexOf($data) > -1 }"></div>
    </div>
</div>

<div class="wrapper flexbox">
    <div class="content-container">
        <!-- ko if: selectedPatch -->

        <!-- #one-third -->
        <div id="one-third" data-bind="with: selectedPatch" class="patch-library">
            <div class="patch-tile-small" id="sticker">
                <table class="patch-title-controls">
                    <tr>
                        <td><span class="patch-title no-pseudo-link" data-bind="text: name"></span></td>
                        <?php if (current_user_can('administrator')): ?>
                        <td width="80">
                            <div class="patch-button" data-bind="click: HoxtonOwl.patchManager.editPatch"><img src="<?= $resUri ?>images/pencil.png" alt="Edit patch" /></div>
                            <div class="patch-button" data-bind="click: HoxtonOwl.patchManager.deletePatch"><img src="<?= $resUri ?>images/bin.png" alt="Delete patch" /></div>
                        </td>
                        <?php else: ?>
                        <!-- ko if: userAllowedToEditPatch(selectedPatch()) -->
                        <td width="80">
                            <div class="patch-button" data-bind="click: HoxtonOwl.patchManager.editPatch"><img src="<?= $resUri ?>images/pencil.png" alt="Edit patch" /></div>
                            <div class="patch-button" data-bind="click: HoxtonOwl.patchManager.deletePatch"><img src="<?= $resUri ?>images/bin.png" alt="Delete patch" /></div>
                        </td>
                        <!-- /ko -->
                        <?php endif; ?>
                    </tr>
                    <tr>
                        <td class="patch-author">
                            <span class="author-name" data-bind="text: author.name, click: selectOnlyAuthor"></span>
                        </td>
                        <td>&nbsp;</td>
                    </tr>
                </table>
            </div>
            <div class="patch-description">
                <h2>Description</h2>
                <p data-bind="text: description"></p>
            </div>
            <!-- ko if: instructions -->
            <div class="patch-instructions">
                <h2>Instructions</h2>
                <p data-bind="text: instructions"></p>
            </div>
            <!-- /ko -->
            <div id="selected-patch-id" data-bind="visible: false, text: _id"></div>
            <!-- <div class="video-wrapper1">
                <iframe src="//www.youtube.com/embed/HAfODHJFkjE" frameborder="0" allowfullscreen></iframe>
            </div> -->
            <div class="patch-stats" data-bind="with: selectedPatch">
                <table width="100%">
                    <tr>
                        <td width="30%"><span class="parameter-label">Channels</span></td>
                        <td><p data-bind="text: (inputs + ' in / ' + outputs + ' out')"></p></td>
                    </tr>
                    <!-- ko if: cycles -->
                    <tr>
                        <td><span class="parameter-label">CPU</span></td>
                        <td><p data-bind="text: cycles + '%'"></p></td>
                    </tr>
                    <!-- /ko -->
                    <!-- ko if: bytes -->
                    <tr>
                        <td><span class="parameter-label">Memory</span></td>
                        <td>
                            <p data-bind="text: (selectedPatch().bytesToHuman(bytes) + ' / 1Mb')"></p>
                        </td>
                    </tr>
                    <!-- /ko -->
                    <!-- ko if: sysExAvailable -->
                    <tr>
                        <td><span class="parameter-label">SysEx</span></td>
                        <td>
                            <p>
                                <a class="sysExDownloadLink" href="#">Download</a>
                                <!-- ko if: sysExLastUpdated -->
                                (built on <span data-bind="text: new Date(selectedPatch().sysExLastUpdated).toLocaleString()"></span>)
                                <!-- /ko -->
                            </p>
                        </td>
                    </tr>
                    <!-- /ko -->
                    <!-- ko if: jsAvailable -->
                    <tr>
                        <td><span class="parameter-label">JS</span></td>
                        <td>
                            <p>
                                <a class="jsDownloadLink" href="#">Download</a>
                                <!-- ko if: jsLastUpdated -->
                                (built on <span data-bind="text: new Date(selectedPatch().jsLastUpdated).toLocaleString()"></span>)
                                <!-- /ko -->
                            </p>
                        </td>
                    </tr>
                    <!-- ko if: downloadCount -->
                    <tr>
                        <td><span class="parameter-label">Downloads</span></td>
                        <td><p data-bind="text: downloadCount"></p></td>
                    </tr>
                    <!-- /ko -->
                    <!-- /ko -->
                    <tr class="compile-patch-container">
                       <td><span class="parameter-label">Build</span></td>
                       <td><p><a class="compileLink sysex" href="#">Compile Patch</a></p></td>
                    </tr>
                </table>
            </div>
            <!-- ko foreach: tags -->
            <div class="tag"><span data-bind="text: $data, click: selectOnlyTag"></span></div>
            <!-- /ko -->
        </div>
        <!-- /#one-third -->

        <!-- #two-thirds -->
        <div id="two-thirds" class="patch-library">
            <div data-bind="visible: location.hostname != 'hoxtonowl.localhost' && selectedPatch().soundcloud.length > 0">
                <div class="patch-soundcloud" data-bind="foreach: soundcloud">
                    <iframe width="100%" height="250" scrolling="no" frameborder="no" data-bind="attr: { src: $data }"></iframe>
                </div>
            </div>
            <!-- <div class="video-wrapper2"> -->
            <!--     <iframe src="//www.youtube.com/embed/HAfODHJFkjE" frameborder="0" allowfullscreen></iframe> -->
            <!-- </div> -->
            <!-- <div class="clear"></div> -->
            <div class="patch-stats2" data-bind="with: selectedPatch">
                <span class="parameter-label">Channels</span>
                <span class="parameter-value" data-bind="text: (inputs + ' in / ' + outputs + ' out')"></span>
                <!-- ko if: cycles -->
                <span class="parameter-label">CPU</span>
                <span class="parameter-value" data-bind="text: cycles + '%'"></span>
                <!-- /ko -->
                <!-- ko if: bytes -->
                <span class="parameter-label">Memory</span>
                <span class="parameter-value" data-bind="text: (selectedPatch().bytesToHuman(bytes) + ' / 1Mb')"></span>
                <!-- /ko -->
                <!-- ko if: downloadCount -->
                <span class="parameter-label">Downloads</span>
                <span class="parameter-value" data-bind="text: downloadCount"></span>
                <!-- /ko -->
                <!-- ko if: sysExAvailable -->
                <span class="parameter-value"><button class="sysExDownloadLink" href="#">Download SysEx</button></span>
                <!-- /ko -->
                <!-- ko if: jsAvailable -->
<!--                <span class="parameter-value"><a class="jsDownloadLink" href="#">Download JS</a></span> -->
                <!-- /ko -->
                <!-- ko foreach: tags -->
                <div class="tag"><span data-bind="text: $data, click: selectOnlyTag"></span></div>
                <!-- /ko -->

                <span class="parameter-value compile-patch-container"><a class="compileLink sysex" href="#">Compile Patch</a></span>
            </div>
            <div class="white-box2" data-bind="with: selectedPatch">
                <div id="patch-tab-header-container">
                    <h2 class="bolder patch-tab-header selected" id="patch-tab-header-info"><a href="#">Info</a></h2>
                    <h2 class="bolder patch-tab-header" id="patch-tab-header-test"><a href="#">Audio</a></h2>
                    <h2 class="bolder patch-tab-header" id="patch-tab-header-midi"><a href="#" onclick="connectToOwl();">MIDI</a></h2>
                </div>
                <div class="flexbox flex-center patch-tab-container">
                    <div class="knob-container" id="patch-parameter-a">
                        <input class="knob" data-displayPreviousValue="true" data-angleOffset="-125" data-angleArc="250" data-displayInput="true" data-readOnly="true" data-fgColor="#aaa" data-linecap="round" data-width="100%" data-rotation="clockwise" value="35">
                        <p class="parameter-label" data-bind="text: parameters.a"></p>
                    </div>
                    <div class="knob-container" id="patch-parameter-b">
                        <input class="knob" data-displayPreviousValue="true" data-angleOffset="-125" data-angleArc="250" data-displayInput="true" data-readOnly="true" data-fgColor="#aaa" data-linecap="round" data-width="100%" data-rotation="clockwise" value="35">
                        <p class="parameter-label" data-bind="text: parameters.b"></p>
                    </div>
                    <div class="knob-container" id="patch-parameter-c">
                        <input class="knob" data-displayPreviousValue="true" data-angleOffset="-125" data-angleArc="250" data-displayInput="true" data-readOnly="true" data-fgColor="#aaa" data-linecap="round" data-width="100%" data-rotation="clockwise" value="35">
                        <p class="parameter-label" data-bind="text: parameters.c"></p>
                    </div>
                    <div class="knob-container" id="patch-parameter-d">
                        <input class="knob" data-displayPreviousValue="true" data-angleOffset="-125" data-angleArc="250" data-displayInput="true" data-readOnly="true" data-fgColor="#aaa" data-linecap="round" data-width="100%" data-rotation="clockwise" value="35">
                        <p class="parameter-label" data-bind="text: parameters.d"></p>
                    </div>
                    <div class="knob-container" id="patch-parameter-e">
                        <input class="knob" data-displayPreviousValue="true" data-angleOffset="-125" data-angleArc="250" data-displayInput="true" data-readOnly="true" data-fgColor="#aaa" data-linecap="round" data-width="100%" data-rotation="clockwise" value="35">
                        <p class="parameter-label" data-bind="text: parameters.e"></p>
                    </div>
                </div>
                <div class="flexbox flex-center patch-tab-container" id="patch-tab-info">

                </div>
                <div class="flexbox flex-center patch-tab-container" id="patch-tab-test">

                    <div id="patch-tab-test-message-1" class="patch-tab-test-message">
                        <strong>Error:</strong> Your browser does not support the HTML5 Web Audio API.<br><br>
                        Try and upgrade your browser. Here's a <a target="_blank" href="http://caniuse.com/#feat=audio-api">list</a> of browsers that do support the Web Audio API.
                    </div>
                    <div id="patch-tab-test-message-2" class="patch-tab-test-message">
                        <strong>Error:</strong> JavaScript build not available for this patch.
                        <span id="patch-tab-test-message-3" class="patch-tab-test-message"><a class="compileLink sysex" href="#">Build this patch now</a></span>
                    </div>
                    <div id="patch-tab-test-loading" class="patch-tab-test-message">
                        Loading patch...
                    </div>

                    <div id="patch-test-inner-container">

                        <input type="button" value="Pushbutton" id="patch-test-pushbutton" />
                        <br/>
                        <label for="patch-test-source">Source:</label>
                        <select id="patch-test-source">
                            <option value="_clear" selected>No Input</option>
                            <option value="_mic">Microphone</option>
                            <option disabled>──────────</option>
                            <option value="gtr-jazz">Jazz Guitar</option>
                            <option value="rock-beat">Rock Beat</option>
                            <option value="synth">Synth</option>
                            <option value="white-noise">White Noise</option>
                        </select>
                        <input type="button" value="Start" id="patch-test-start-stop" />

                        <audio id="patch-test-audio" controls loop preload="auto">
                            Your browser does not support the <code>&lt;audio&gt;</code> element.
                        </audio>
                    </div>
                </div>
                <div class="flexbox flex-center patch-tab-container" id="patch-tab-midi" style="display:none;">

                    <div style="width:100%; text-align:center;">
                        <p id="ourstatus">...</p>
                        <p id="patchstatus"></p>
                        <p id="patchmessage"></p>
                    </div>

                    <!-- ko if: sysExAvailable -->
                    <div>
                        <button id="load-owl-button"/>Load patch onto OWL device<img src="<?php echo get_stylesheet_directory_uri(); ?>/page-patch-library/images/sendfile.png" /></button>
                    </div>
                    <!-- /ko -->
                    <!-- ko ifnot: sysExAvailable -->
                    <div>
                        <strong>Error:</strong> SysEx build not available for this patch.
                    </div>
                    <!-- /ko -->
                    
                </div>
            </div>
            <div class="white-box2" id="git-code">
                <h2 class="bolder">Patch code</h2>
                <div id="github-files">
                    <!--
                    <ul>
                        <li><a href="#tabs-1">Nunc tincidunt</a></li>
                    </ul>
                    <div id="tabs-1">
                        <pre class="prettyprint"></pre>
                    </div>
                    -->
                </div>
            </div>
        </div>
        <!-- /#two-thirds -->
        <!-- /ko -->

        <div id="patch-counter" data-bind="visible: search() !== 'patch'">
            <div data-bind="visible: search() !== 'myPatches'">
                <span data-bind="text: filteredPatches().length">0</span> public
                patch<span data-bind="text: filteredPatches().length == 1 ? '' : 'es'"></span> by
                <span data-bind="text: filteredPatchAuthorNo()">0</span>
                author<span data-bind="text: filteredPatchAuthorNo() == 1 ? '' : 's'"></span>.
            </div>
            <div data-bind="visible: search() === 'myPatches'">
                <span data-bind="text: filteredPatches().length">0</span>
                patch<span data-bind="text: filteredPatches().length == 1 ? '' : 'es'"></span>
            </div>
        </div>

        <!-- ko foreach: filteredPatches -->
        <!-- div.patch-tile -->
        <div class="patch-tile">
            <div class="patch-title-controls">
                <div>
                    <span class="patch-title" data-bind="text: name, click: HoxtonOwl.patchManager.openPatch, attr: { 'data-patch-id': _id }"></span>
                    <div class="patch-visibility" data-bind="visible: !published"><img src="<?= $resUri ?>images/lock.png"> PRIVATE</div>
                </div>

                <!-- ko if: search() === 'myPatches' -->
                <div>
                    <div class="patch-button" data-bind="click: HoxtonOwl.patchManager.editPatch"><img src="<?= $resUri ?>images/pencil.png" alt="Edit patch" /></div>
                    <div class="patch-button" data-bind="click: HoxtonOwl.patchManager.deletePatch"><img src="<?= $resUri ?>images/bin.png" alt="Delete patch" /></div>
                </div>
                <!-- /ko -->
       
                <div class="patch-author">
                    <span class="author-name" data-bind="visible: search() !== 'myPatches', text: author.name, click: selectOnlyAuthor"></span>
                </div>
                <span class="patch-description-list-view" data-bind="text: description"></span>     
            </div>
            <div class="patch-baseline">
                <!-- ko foreach: tags -->
                <div class="tag"><span data-bind="text: $data, click: selectOnlyTag"></span></div>
                <!-- /ko -->
                <!-- <span class="add-counter">215</span> -->
            </div>
        </div>
        <!-- /div.patch-tile -->
        <!-- /ko -->

        <!-- ko if: search() === 'myPatches' -->
        <div class="patch-tile patch-tile-new" data-bind="click: HoxtonOwl.patchManager.addPatch">
            <table class="patch-title-controls patch-title-controls-new">
                <tbody>
                    <tr>
                        <td><span class="patch-title">Add a new patch</span></td>
                    </tr>
                </tbody>
            </table>
            <div class="patch-baseline"></div>
        </div>
        <!-- /ko -->

    </div> <!-- /div.content-container -->
</div> <!-- /div.wrapper.flexbox -->

<div id="compile-dialog" title="Compile patch">
    <div id="compile-tabs">
        <ul>
            <li><a href="#tabs-msg">Result</a></li>
            <li><a href="#tabs-stdout">stdout</a></li>
            <li><a href="#tabs-stderr">stderr</a></li>
        </ul>
        <div id="tabs-msg">
            <textarea readonly></textarea>
        </div>
        <div id="tabs-stdout">
            <textarea readonly></textarea>
        </div>
        <div id="tabs-stderr">
            <textarea readonly></textarea>
        </div>
    </div>
    <div id="compile-dialog-button-container">
        <button id="compile-dialog-btn-done">Done</button>
    </div>
</div>

<?php
global $current_user;

if (is_user_logged_in()): ?>
<div style="display: none;">
    <div id="wordpress-username"><?= $current_user->user_login; ?></div>
    <div id="wordpress-user-id"><?= $current_user->ID; ?></div>
    <div id="wordpress-display-name"><?= $current_user->display_name ?></div>
    <div id="wordpress-user-is-admin"><?= current_user_can('administrator') ? 1 : 0 ?></div>
</div>
<?php endif; ?>

<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/footer','parts/shared/html-footer') ); ?>
