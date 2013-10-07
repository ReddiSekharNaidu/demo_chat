var express = require('express');
var app = express();
var server = require('http').createServer(app);
var webRTC = require('./modules/webrtc.io').listen(server);
var env = require('./config.js').server_config;
var index = require('./routes/index');
var chat_main = require('./routes/main');

server.listen(env.server_port);
console.log('Starting chat server on port ' + env.server_port);

// ***** App Configuration
app.configure(function () {
	app.set('views', __dirname + '/views');
	app.engine('.html', require('ejs').renderFile);
	app.set('view engine', 'html');
	app.set('view options',{ layout:false});
	app.use(
        express.static(__dirname + '/public')
    );
	app.use(express.bodyParser());
	app.use(app.router);	
	app.use(express.methodOverride());
});

// ***** App Routes
app.get('/', index.index);
app.get('/enter', chat_main.index);
app.post('/', index.login);
app.post('/enter', chat_main.index);
app.post('/upload', chat_main.upload);