<style>
	* { font-family:tahoma; font-size:12px; padding:0px; margin:0px; }
	.p { line-height:18px; }
	#box { width:500px; margin-left:auto; margin-right:auto;}
	#con { padding:5px; background:#ddd; border-radius:5px; overflow-y: scroll;
			   border:1px solid #CCC; margin-top:10px; height: 160px; }
	#input { border-radius:2px; border:1px solid #ccc;
			 margin-top:10px; padding:5px; width:400px;  }
	#status { width:88px; display:block; float:left; margin-top:15px; }
	#lag { color:green; }
</style>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

<script type="text/javascript">
	$(function () {
		"use strict";
	 
		// for better performance - to avoid searching in DOM
		var con = $('#con');
		var input = $('#input');
		var status = $('#status');
		var lagLabel = $('#lag');
		var tenBtn = $('#ten');
		
		// display rate
		var rate=4;
		var count=0;
	 
		// my color assigned by the server
		var myColor = false;
		// my name sent to the server
		var myName = false;
	 
		// if user is running mozilla then use it's built-in WebSocket
		window.WebSocket = window.WebSocket || window.MozWebSocket;
	 
		// if browser doesn't support WebSocket, just show some notification and exit
		if (!window.WebSocket) {
			con.html($('<p>', { text: 'Sorry, but your browser doesn\'t '
										+ 'support WebSockets.'} ));
			input.hide();
			$('span').hide();
			return;
		}
	 
		// open connection
		var connection = new WebSocket('ws://127.0.0.1:1337');
	 
		connection.onopen = function () {
			// first we want users to enter their names
			input.removeAttr('disabled');
			status.text('Choose name:');
		};
	 
		connection.onerror = function (error) {
			// just in there were some problems with conenction...
			con.html($('<p>', { text: 'Sorry, but there\'s some problem with your '
										+ 'connection or the server is down.' } ));
		};
	 
		// most important part - incoming messages
		connection.onmessage = function (message) {
			// try to parse JSON message. Because we know that the server always returns
			// JSON this should work without any problem but we should make sure that
			// the massage is not chunked or otherwise damaged.
			try {
				var json = JSON.parse(message.data);
			} catch (e) {
				console.log('This doesn\'t look like a valid JSON: ', message.data);
				return;
			}
	 
			// NOTE: if you're not sure about the JSON structure
			// check the server source code above
			if (json.type === 'color') { // first response from the server with user's color
				myColor = json.data;
				status.text(myName + ': ').css('color', myColor);
				input.removeAttr('disabled').focus();
				// from now user can start sending messages
			} else if (json.type === 'history') { // entire message history
				// insert every single message to the chat window
				for (var i=0; i < json.data.length; i++) {
					addMessage(json.data[i].author, json.data[i].text,
							   json.data[i].color, new Date(json.data[i].time));
				}
			} else if (json.type === 'message') { // it's a single message
				// for timing
				if(sended&&json.data.author==myName){
					var lag=window.performance.now()-before;
					// show the lag
					if(count%rate==0){
						lagLabel.val(lag+' milliseconds');
					}
					sended=false;
					count+=1;
				}
				input.removeAttr('disabled'); // let the user write another message
				//addMessage(json.data.author, json.data.text,
				//		   json.data.color, new Date(json.data.time));
			} else {
				console.log('Hmm..., I\'ve never seen JSON like this: ', json);
			}
		};
	 
		// flag for timing
		var sended=false;
		var before;
		/**
		 * Send mesage when user presses Enter key
		 */
		input.keydown(function(e) {
			if (e.keyCode === 13) {
				var msg = $(this).val();
				if (!msg) {
					return;
				}
				// for timing
				before=window.performance.now();
				sended=true;
				// send the message as an ordinary text
				connection.send(msg);
				
				$(this).val('');
				// disable the input field to make the user wait until server
				// sends back response
				input.attr('disabled', 'disabled');
	
				// we know that the first message sent from a user their name
				if (myName === false) {
					myName = msg;
				}
			}
		});
		
		/** send ten times per second */
		tenBtn.click(function(){
			var msg = input.val();
				if (!msg) {
					return;
			}
			setInterval(function() {
				// for timing
				before=window.performance.now();
				sended=true;
				// send the message as an ordinary text
				connection.send(msg);
				// disable the input field to make the user wait
				input.attr('disabled', 'disabled');
			}, 1000);
		});
	 
		/**
		 * This method is optional. If the server wasn't able to respond to the
		 * in 3 seconds then show some error message to notify the user that
		 * something is wrong.
		 */
		setInterval(function() {
			if (connection.readyState !== 1) {
				status.text('Error');
				input.attr('disabled', 'disabled').val('Unable to comminucate '
													 + 'with the WebSocket server.');
			}
		}, 3000);
	 
		/**
		 * Add message to the chat window
		 */
		function addMessage(author, message, color, dt) {
			con.prepend('<p><span style="color:' + color + '">' + author + '</span> @ ' +
				 + (dt.getHours() < 10 ? '0' + dt.getHours() : dt.getHours()) + ':'
				 + (dt.getMinutes() < 10 ? '0' + dt.getMinutes() : dt.getMinutes())
				 + ': ' + message + '</p>');
		}
	});
</script>

<h1><?php echo h($chatroom['Chatroom']['title']); ?></h1>
<p class="p"><small>Created: <?php echo $chatroom['Chatroom']['created']; ?></small></p>
<p class="p"><?php echo h($chatroom['Chatroom']['description']); ?></p>	
	
<div id="con"></div>
<div id="box">
	<span id="status">Connecting...</span>
	<input type="text" id="input" disabled="disabled" />
</div>
<span>Round trip time</span>
<input type="text" id="lag" disabled="disabled" />
<span>test case</span>
<div>
<button id="ten">send 10 times per second</button>
</div>
