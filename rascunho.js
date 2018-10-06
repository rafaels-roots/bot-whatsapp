
function triggerMouseEvent(node, eventType) {
    var event = document.createEvent('MouseEvents');
    event.initEvent(eventType, true, true);
    node.dispatchEvent(event);
}


setInterval(function()
{
	var interval = true;
	var chat = document.querySelectorAll(".chat"); 
	var nome = document.querySelectorAll(".chat-title");
	var lastmsg = document.querySelectorAll("._2_LEW");
	var warray =  [];
	var wobj = {};
	
	if(interval == true) 
		{ 	
			interval = false;
			var len = chat.length;
			
			for (var i = 0; i < chat.length; i++) 
			{
					
				if(chat[i].className.indexOf("unread") >= 0  && len == chat.length) 
					{
						
						wobj["nome"] = nome[i].firstChild.title;
						wobj["last_msg"] = lastmsg[i].title;
						wobj["indice"] = i;
						warray.push(wobj);
						wobj = {};
					}
			}
			sessionStorage.setItem("news" , JSON.stringify(warray) );
			interval = true; 
          
		}
},10000);


setInterval(function()
{
	var newss = JSON.parse( sessionStorage.getItem("news") ); 
	if(newss != null && newss.length != 0) 
		{
			setTimeout(function()
				{
					var input = document.querySelector('.block-compose .pluggable-input-body');
					for(var i = 0, len = newss.length; i < len; i++) 
                        {
                            //document.getElementsByClassName("chat-title")[ newss[i]["indice"]].click();
                            triggerMouseEvent( document.getElementsByClassName("chat-title")[ newss[i]["indice"] ] , "mousedown");
                            //triggerMouseEvent( document.getElementsByClassName("chat-title")[ newss[i]["indice"] ] , "mouseup");
	
                            input.innerHTML = ".";
                            input.dispatchEvent(new Event('input', {bubbles: true}));
                            setTimeout(function()
                                {
                                    var button = document.querySelector('.block-compose .compose-btn-send');
                                    button.click();
                                }, 800);
                            sessionStorage.removeItem("news");
                        }
				}, 1000);
			
		}
}, 5000);