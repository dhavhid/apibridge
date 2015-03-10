# apibridge
<p>This is a library intented to be a bridge between any API and your own application writen with a Javascript Framework.</P>  
<p>It handles basic authentication and was originally written for and API based in Laravel.</p>  
<p>This is the list of parameters accepted by default:</p>
* object_id: Which is the Id of the object to be queried in the API
* type: Represents the type of the object, for example: car, motorcycle, etc.
* endpoint: It is the piece of the url that folows your base API url, for example: /profile  


### This is an example of use with jQuery:
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
