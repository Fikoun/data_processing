
var url_base = "http://data.processing/"

function serverCommand(command) {
	switch(command) {
		case 'start':
			$.ajax({ url: url_base + "server/start" }).done(function( data ) {
			      console.log( "data:", data.slice( 0, 100 ) );
			  });
		break;
		case 'stop':
			$.ajax({ url: url_base + "server/stop" }).done(function( data ) {
			      console.log( "data:", data.slice( 0, 100 ) );
			  });
		break;
		case 'status':
			$.ajax({ url: url_base + "server/status" }).done(function( data ) {
			      console.log( "data:", data.slice( 0, 100 ) );
			  });
		break;
	}
}