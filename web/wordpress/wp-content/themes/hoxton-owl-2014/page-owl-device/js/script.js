function onMidiInitialised(){

    for (var o = 0; o < HoxtonOwl.midiClient.midiOutputs.length; o++) {
    	var out = HoxtonOwl.midiClient.midiOutputs[o];
        $('select#midiOutputs')
			.append($("<option></option>")
			.val(out.id)
			.text(out.name + " (" + out.manufacturer + ") " + out.id));
    }

    for (var i = 0; i < HoxtonOwl.midiClient.midiInputs.length; i++) {
    	var input = HoxtonOwl.midiClient.midiInputs[i];
        $('select#midiInputs')
			.append($("<option></option>")
			.val(input.id)
			.text(input.name + " (" + input.manufacturer + ") " + input.id));    	
    }

}

HoxtonOwl.midiClient.initialiseMidi(onMidiInitialised);
