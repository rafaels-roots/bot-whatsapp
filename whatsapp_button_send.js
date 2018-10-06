var input = document.querySelector('.block-compose .pluggable-input-body');
setTimeout(function(){
    for(var j = 0; j < 1; j++) {
        input.innerHTML = "Hello";
        input.dispatchEvent(new Event('input', {bubbles: true}));
        var button = document.querySelector('.block-compose .compose-btn-send');
        button.click();
    }
},1000);

var input = document.querySelector('.block-compose .pluggable-input-body');
setTimeout(function(){
    for(var j = 0; j < 1; j++) {
        input.innerHTML = "testando button send . . .";
        input.dispatchEvent(new Event('input', {bubbles: true}));
       setTimeout(function(){ 
			var button = document.querySelector('.block-compose .compose-btn-send');
        	button.click();
		},500);    
}
},1000);


/********/

window.InputEvent = window.Event || window.InputEvent;
var d = new Date();
var event = new InputEvent('input', {bubbles: true});

    //var textbox = $('div.input');  Class Name changed to> "div.pluggable-input-body.copyable-text.selectable-text"
    var textbox = document.querySelector('#main > footer > div.block-compose > div.input-container > div.pluggable-input.pluggable-input-compose > div.pluggable-input-body.copyable-text.selectable-text');

textbox.textContent = "t";
textbox.dispatchEvent(event);
document.querySelector("button.compose-btn-send").click();