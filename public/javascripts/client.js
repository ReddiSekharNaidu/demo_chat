var webcam_videos = [];
var chat_users = [];
var webcam_width = 130;
var webcam_height = 90;
var peerConnection;		
var websocketChat = {
		send: function (message) {
			rtc._socket.send(message);
		},
		recv: function (message) {
			return message;
		},
		event: 'receive_chat_msg'
};
var dataChannelChat = {
		broadcast: function(message) {
			for(var connection in rtc.dataChannels) {
				var channel = rtc.dataChannels[connection];
				if (rtc.connection_ok_to_send[connection]) {
					channel.send(message);
				} else {
					console.log("unable to send message to " + connection);
				}
			}
		},
		send: function(connection, message) {
			var channel = rtc.dataChannels[connection];
			if (rtc.connection_ok_to_send[connection]) {
				channel.send(message);
			} else {
				console.log("unable to send message to " + connection);
			}
		},
		recv: function(channel, message) {
			return JSON.parse(message).data;
		},
		event: 'data stream data'
};

var ppChat = {
	init:function(){
		$('#chat-wrapper').window('open');				
	},
	addmsg_to_board : function(username, msg, color) {
			var messages = $('#msgboard');
			msg = ppChat.sanitize(msg);
			if(color) {
				msg = '<span style="color: ' + color + '; padding-left: 15px;">' + username + ' : </span>' + '<span style="color: ' + color + '; padding-left: 10px">' + msg + '</span>';
			} else {
				msg = '<span style="color: ' + color + '; padding-left: 15px;">' + username + ' : </span>' + '<span><strong style="padding-left: 10px">' + msg + '</strong></span>';
			}
			messages.html(messages.html() + msg + '<br />');
			messages.scrollTop(10000);
	},
	dispsystemmessage : function(msg) {
		var messages = $('#msgboard');
		msg = ppChat.sanitize(msg);
		msg = '<strong class="small" style="padding-left: 15px">' + msg + '</strong>';
		messages.html(messages.html() + msg + '<br />');
		messages.scrollTop(10000);
	},
	sanitize : function(msg) {
		/* this isn't actual security, just avoids accidential html input */
		return msg.replace(/</g, '&lt;');
	},
	sendmessage : function(chat, color) {
		var room = window.location.hash.slice(1);
		var username = $("#username").val();
		chat.broadcast(JSON.stringify({
			"eventName": "chat_msg",
			"data": {
			  "messages": $("#sendtext").val(),
			  "room": room,
			  "color": color
			}
	    }));		
		ppChat.addmsg_to_board(username, $("#sendtext").val());
		$("#sendtext").val("");
	},
	initChat : function() {
		  var chat;
		  if (rtc.dataChannelSupport) {
				console.log('initializing data channel chat');
				chat = dataChannelChat;
		  } else {
				console.log('initializing websocket chat');
				chat = websocketChat;
		  }		  		  
		  var color = "#" + ((1 << 24) * Math.random() | 0).toString(16);
		  $('#btnSend').bind('click', function(event){
				ppChat.sendmessage(chat, color);
		  });
		  $('#sendtext').bind('keypress', function(event){
				var key = event.which || event.keyCode;
				if((key === 13 || key === 10) && event.ctrlKey) {
					ppChat.sendmessage(chat, color);
					event.preventDefault();
				}
		  });
		  $('#shutter').bind('click', function(event) {
			    var canvas       = document.querySelector('#canvas');
				var photo        = document.querySelector('#photo');
				var video        = document.querySelector('#you');
				canvas.width = webcam_width;
				canvas.height = webcam_height;
				canvas.getContext('2d').drawImage(video, 0, 0, webcam_width, webcam_height);
				var data = canvas.toDataURL('image/png');
				$.ajax({
					type : 'POST',
					url  : '/upload',
					data : { shutter_image : data, username : $('#username').val() },
					success : function(data){
							alert('uploaded successfully!');
							console.log('uploaded successfully!');
						}});
		  });
		  
		  /* this function is called with every data packet recieved */
		  rtc.on(chat.event, function(conn, data, id, username) {
				/* decode and append to data */
				data = chat.recv.apply(this, arguments);
				data.id = id;
				data.username = username;			
				/* pass data along */
				if (data.messages) {
					/* chat */
					ppChat.addmsg_to_board(data.username, data.messages, data.color.toString(16));
				} else {
					/* data */
					process_data(data);
				}
		  });
	},
	WebRTC: {		
		init: function() {
			 if (window.chrome)
			 {
				peerConnection = window.PeerConnection || window.webkitPeerConnection00 || window.webkitRTCPeerConnection;
			 }else{
				peerConnection = mozRTCPeerConnection;
			 }
			 if(peerConnection){
				rtc.createStream({"video": true, "audio": false}, function(stream) {
				  $('#you').attr('src', URL.createObjectURL(stream));
				  webcam_videos.push($('#you'));
				  rtc.attachStream(stream, 'you');
				  ppChat.WebRTC.displaywebcamVideos();
				});
			 }else {
				ppChat.Errors.displayErrors('Your browser is not supported or you have to turn on flags. In chrome you go to chrome://flags and turn on Enable PeerConnection remember to restart chrome');
			 }
			 var room = window.location.hash.slice(1);
			 if (room != 0) {
				  rtc.connect("ws:" + window.location.href.substring(window.location.protocol.length).split('#')[0], room, $("#username").val());
				  rtc.on('ready', function(my_socket, usernames) {
					 //to expand for ready event of WebRTC
				  });
				  /* when a new user's data channel is opened and we are offering a file, tell them */
				  rtc.on('data stream open', function(id, username) {
						ppChat.dispsystemmessage('now connected to ' + username);
						ppChat.WebRTC.cloneUser('useryou', id, username);
				  });
				  rtc.on('add remote stream', function(stream, socketId) {
						console.log("adding remote stream...");
						var clone = ppChat.WebRTC.cloneVideo('you', socketId);
						$('#' + clone.attr('id')).attr("class", "");
						rtc.attachStream(stream, clone.attr('id'));
						ppChat.WebRTC.displaywebcamVideos();
				  });
				  /* when another user disconnects */
				  rtc.on('disconnect stream', function(disconnecting_socket, disconnecting_username) {
						ppChat.dispsystemmessage(disconnecting_username + " has left the room");						
						ppChat.WebRTC.removeVideo(disconnecting_socket);
						ppChat.WebRTC.removeUser(disconnecting_socket);
				  });
				  ppChat.initChat();
			 }
		},
		calcNumPerRow : function() {
			var len = webcam_videos.length;
			var biggest;  
			len++;
			biggest = Math.ceil(Math.sqrt(len));
			while (len % biggest !== 0) {
				biggest++;
			}
			return biggest;
		},
		displaywebcamVideos : function() {
			var perRow = ppChat.WebRTC.calcNumPerRow();
			var numInRow = 0;
			for (var i = 0, len = webcam_videos.length; i < len; i++) {
				var video = webcam_videos[i];
				ppChat.WebRTC.setwebcamSize(video);
				numInRow = (numInRow + 1) % perRow;
			}
		},
		setwebcamSize : function(video) {
			var perRow = ppChat.WebRTC.calcNumPerRow();
			var perColumn = Math.ceil(webcam_videos.length / perRow);
			var width = webcam_width;
			var height = webcam_height;
			video.attr('width', webcam_width);
			video.attr('height', webcam_height);
			video.attr('style','position:relative;left:0px;top:0px;');			
		},
		cloneUser : function(domId, socketId, username) {
			var user = $('#' + domId);
			var clone = user.clone();
			clone.attr('id',"uremote" + socketId);
			$('#users').append(clone);
			$('#' + clone.attr('id')).attr("class", "");
			$('#' + clone.attr('id')).html(username);
			chat_users.push(username);
			return clone;
		},
		cloneVideo : function(domId, socketId) {
			var video = $('#' + domId);
			var clone = video.clone();
			clone.attr('id',"vremote" + socketId);
			$('#webcam-videos').append(clone);
			webcam_videos.push(clone);
			return clone;
		},
		removeVideo : function(socketId) {
			var video = $('#vremote' + socketId);
			if (video) {
				webcam_videos.splice(webcam_videos.indexOf(video), 1);
				video.remove();
			}
		},
		removeUser : function(socketId) {
			var user = $('#uremote' + socketId);
			if (user) {
				chat_users.splice(chat_users.indexOf(user), 1);
				user.remove();
			}
		}
	},
	Errors: {
		displayErrors : function(errMsg) {
			alert(errMsg);
		}
	}
}

window.onresize = function(event) {
	ppChat.WebRTC.displaywebcamVideos();
};
