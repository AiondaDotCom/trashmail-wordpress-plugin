"use strict";

document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("submit-tm").onclick = function(e) {
		wpNavMenu.registerChange();

        let text = document.getElementById("tm-menu-text");
        let spinner = document.querySelector("#tm-div .spinner");
        spinner.style.visibility = "visible";
        let id = /menu-item\[([^\]]*)/.exec(text.name);
        id = typeof id[1] == 'undefined' ? 0 : parseInt(id[1], 10);

        let menu_items = {id:
            {"menu-item-title": text.value, "menu-item-type": "tm-contact-me",
             "menu-item-object-id": "-1", "menu-item-object": "tm-contact-me"}
        };

        if (menu_items[id]["menu-item-title"] === "")
            menu_items[id]["menu-item-title"] = "Contact Me";

        wpNavMenu.addItemToMenu(menu_items, wpNavMenu.addMenuItemToBottom, function() {
            spinner.style.visibility = "hidden";
        });
    };
});
