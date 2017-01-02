var Notify = {};

Notify.isGranted = function(){
	return Notification.permission === "granted";
};


Notify.request = function(){
	Notification.requestPermission();
};

Notify.prompt = function(title,options,callback,timeout){
	

	console.log(timeout);
	if(!Notify.isGranted()){
		Notify.request();

		return;
	}

	var notification = new Notification(title,options);

	notification.onclick = function(){
		callback();
	};


	setTimeout(function(){notification.close},timeout);
};

$(document).ready(function(){

	if(!Notify.isGranted()){
		Notify.request();

		return;
	}

});