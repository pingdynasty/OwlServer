<?php

wp_enqueue_script('jquery',                         get_template_directory_uri() . '/node_modules/jquery/dist/jquery.min.js');

wp_enqueue_script('midi-client',                    get_template_directory_uri() . '/js/midiclient.js');
wp_enqueue_script('owl-midi-control',               get_template_directory_uri() . '/js/OpenWareMidiControl.js');
wp_enqueue_script('owl-cmd',                        get_template_directory_uri() . '/js/owlcmd.js');

wp_enqueue_script('owl-device-page',				get_template_directory_uri() . '/page-owl-device/js/script.js');

Starkers_Utilities::get_template_parts( array( 'parts/shared/html-header', 'parts/shared/header' ) );

?>

<div class="wrapper flexbox">
    <div class="content-container">

		<p>MIDI In
			<select id="midiInputs" onchange="selectMidiInput(this.selectedIndex)">
				<option>...</option>
			</select>
		</p>

		<p>MIDI Out
			<select id="midiOutputs" onchange="selectMidiOutput(this.selectedIndex)">
				<option>...</option>
			</select>
		</p>


	</div>
</div>

<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/footer','parts/shared/html-footer') ); ?>
