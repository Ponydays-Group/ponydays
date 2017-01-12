require('../css/index.scss')

let extend = function(self, obj) {
    for (var i in obj) {
        if (obj.hasOwnProperty(i)) {
            self[i] = obj[i];
        }
    }
};

window.ls = window.ls || {};

extend(ls, require("./hook.js"))
extend(ls, require("./main.js"))
extend(ls, require("./blocks.js"))
extend(ls, require("./blog.js"))
//require("./build.js")
extend(ls, require("./comments.js"))
extend(ls, require("./favourite.js"))
extend(ls, require("./geo.js"))
extend(ls, require("./infobox.js"))
extend(ls, require("./photoset.js"))
extend(ls, require("./poll.js"))
extend(ls, require("./settings.js"))
extend(ls, require("./stream.js"))
extend(ls, require("./subscribe.js"))
extend(ls, require("./talk.js"))
extend(ls, require("./toolbar.js"))
extend(ls, require("./topic.js"))
extend(ls, require("./user.js"))
extend(ls, require("./userfeed.js"))
extend(ls, require("./userfield.js"))
extend(ls, require("./usernote.js"))
extend(ls, require("./vote.js"))
extend(ls, require("./wall.js"))
extend(ls, require("./template.js"))


window.ls = ls;

console.log(ls)

console.log("Hello, world!")