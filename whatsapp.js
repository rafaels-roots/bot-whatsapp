var sql = "";
var myWindow;

setInterval(function()
{
	var interval = true;
	var chat = document.querySelectorAll(".chat"); 
	//var nome = document.querySelectorAll(".chat-title");
	//var lastmsg = document.querySelectorAll("._2_LEW");
	//var wdele =  "";
	
	if(interval == true) 
		{ 	
			interval = false;
			var len = chat.length;
			
			for (var i = 0; i < chat.length; i++) 
			{
					
				if(chat[i].className.indexOf("unread") >= 0  && len == chat.length) 
					{
		
						sql += document.querySelectorAll(".chat-title")[i].firstChild.title + String.fromCharCode(174) +
							   document.querySelectorAll("._2_LEW")[i].title + String.fromCharCode(169)	;
					}
			}
			openWin();
			console.log(sql);
			sql = "";
			interval = true; 
			closeWin();
		}
},10000);

function openWin() {
	if(sql != "") 
	{
		myWindow = window.open("http://www.sisleq.com.br/processa.php?sql="+sql , "_blank", "width=100, height=100");
		myWindow.blur();	
	}
    
}
function closeWin() {
    if(myWindow){
       setTimeout(function() { myWindow.close() } , 3000); 
    }
}