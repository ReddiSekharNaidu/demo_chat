/*
*	Routes for home page
*/
exports.index = function(req, res) {
	console.log("index page");
    res.render('index');
};
exports.login = function(req, res) {
	console.log("post action in index page");
	if(req.body.username == "" || req.body.roomname == "")
	{
		res.render('index');
	}else{
		res.render('main', 
		      {	
				username: req.body.username,
				roomname: req.body.roomname
			  });
	}
};