# apibridge
## This is an example of use with jQuery:
    var baseurl = 'http://your-api-url';  
    var endpoints = {  
      profile: '/objects/v1/profile'  
    };  
    var per_page = 20;  
    var currPage = 1;  
    $.ajax({  
		    type: 'GET',  
		    url: baseurl + '?object_id=' + objectId + '&type=' + objectType + '&endpoint=' + endpoints.profile + '&mediatype=picture',  
		    dataType: 'JSON',  
		    success: function(data,status,jqXHR){  
			    if(data.status == '1'){  
				    // parse data  
			    }  
		    }  
	    });  
