var upload_folder = "uploads/";

exports.index = function(req, res) {
	console.log("main page");
    if(req.body.username == "" || req.body.roomname == "")
	{
		res.render('index');
	}else{
		res.render('main', {	
			username: req.body.username,
			roomname: req.body.roomname
		});
	}	
};
exports.upload = function(req, res){	
	var shutter_img = req.body.shutter_image;
	var data = shutter_img.replace(/^data:image\/\w+;base64,/, "");
	var buf = new Buffer(data, 'base64');
	var fs = require('fs');
	fs.writeFile(upload_folder + req.body.username + ".png",buf, function(err){
		if(err) return console.log(err);
	});
	console.log("saved file successfully.!");
	res.writeHead(200, {'Content-Type' : 'text/html'});
	res.end('');
};