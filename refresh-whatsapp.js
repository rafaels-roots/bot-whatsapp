event = document.createEvent("UIEvents");
                event.initUIEvent("input", true, true, window, 1);
                document.querySelector('.pluggable-input-body').dispatchEvent(event);