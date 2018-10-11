function getFeed(){
	$('#mainFeed').html('<div class="text-center"><i class="fas fa-cog fa-spin fa-8x" ></i></div>');
	$.ajax({
    url: "api.php",
    error: function(err){
        $('#mainFeed').html('<div class="alert alert-danger text-center" role="alert">Something went wrong.</div>');
    },
    success: function(res){
		if(res){
			let myObj = JSON.parse(res);
			if(Object.keys(myObj.data).length > 0){
				$('#mainFeed').html('');
				printFeed(myObj)
			}
			else {
				$('#mainFeed').html('<div class="alert alert-primary" role="alert">No posts found.</div>');
			}
		}
		else {
				$('#mainFeed').html('<div class="alert alert-danger text-center" role="alert">Something went wrong.</div>');
		}
    },
    timeout: 3000 // sets timeout to 3 seconds
	});
}

function getMore(sid){
	
	$.ajax({
    url: "api.php?next=true&sid="+sid,
    error: function(err){
        $('#mainFeed').html('<div class="alert alert-danger text-center" role="alert">Something went wrong.</div>');
    },
    success: function(res){
		let myObj = JSON.parse(res);
		$('#mainFeedButton').remove();
		if(Object.keys(myObj.data).length > 0){
				printFeed(myObj)
		}
		else {
			$('#mainFeedButton').html('<div class="alert alert-primary" role="alert">Δεν βρέθηκαν δημοσιευσεις.</div>');
		}
    },
    timeout: 3000 // sets timeout to 3 seconds
	});
}

function printFeed(myObj){
	let data = myObj.data;
	Object.keys(data).forEach(function(key) {
				let img='';
				if(data[key].full_picture)
					img=data[key].full_picture;
				
				$('#mainFeed').append(''+ 
				'<div class="card mycard" >'+
				'  <div class="card-body">'+
				'    <h5 class="card-title"><i class="fab fa-facebook"></i> '+data[key].from.name+'</h5>'+
				'    <p class="card-text">'+data[key].message+'</p>'+
				'    <div class="text-center"><img src="'+img+'" /></div>'+
				'    <br><footer class="blockquote-footer">'+new Date(data[key].created_time*1000).toLocaleString()+'</footer>'+
				'  </div>'+
				'</div>');
				});
			if(myObj.next){
				$('#mainFeed').append('<div id="mainFeedButton"><br><button type="button" class="btn btn-primary btn-lg btn-block" onclick="getMore(\''+myObj.sid+'\')">Load More</button></div>');
			}
}