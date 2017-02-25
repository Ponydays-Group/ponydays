require('../css/index.scss')

// Function.prototype.bind = function(context) {
//   try {
//     var fn = this;
//     if(jQuery.type(fn) != 'function'){
//         throw new TypeError('Function.prototype.bind: call on non-function');
//     };
//     if(jQuery.type(context) == 'null'){
//         throw new TypeError('Function.prototype.bind: cant be bound to null');
//     };
//     return function() {
//         return fn.apply(context, arguments);
//     };
//   } catch(e) {
//     console.log(e);
//   }
// };
String.prototype.tr = function(a,p) {
    var k;
    var p = typeof(p)=='string' ? p : '';
    var s = this;
    jQuery.each(a,function(k){
        var tk = p?p.split('/'):[];
        tk[tk.length] = k;
        var tp = tk.join('/');
        if(typeof(a[k])=='object'){
            s = s.tr(a[k],tp);
        }else{
            s = s.replace((new RegExp('%%'+tp+'%%', 'g')), a[k]);
        };
    });
    return s;
};

let extend = function(self, obj) {
    for (var i in obj) {
        if (obj.hasOwnProperty(i)) {
            self[i] = obj[i];
        }
    }
};

import init from './template'

init()

import * as Ajax from './ajax'
import * as Blog from './blog'
import * as Subscribe from './subscribe'
import * as Settings from './settings'
import * as Comments from './comments'
import * as Lang from './lang'
import * as Userfeed from './userfeed'
import * as Stream from './stream'
import * as Toolbar from './toolbar'
import * as Poll from './poll'
import * as Timer from './timer'
import * as Topic from './topic'
import * as Userfield from './userfield'
import * as Tools from './tools'
import * as Vote from './vote'
import * as User from './user'
import * as Wall from './wall'
import * as Usernote from './usernote'
import * as Talk from './talk'
import * as Favourite from './favourite'
import * as Geo from './geo'
import * as Registry from './registry'
import * as Blocks from './blocks'
import * as Autocomplete from './autocomplete'
import * as Hook from './hook'
import * as Msg from './msg'

let ls = {
    ajax: Ajax,
    blog: Blog,
    subscribe: Subscribe,
    settings: Settings,
    comments: Comments,
    lang: Lang,
    userfeed: Userfeed,
    stream: Stream,
    toolbar: Toolbar,
    poll: Poll,
    timer: Timer,
    topic: Topic,
    userfield: Userfield,
    tools: Tools,
    vote: Vote,
    user: User,
    wall: Wall,
    usernote: Usernote,
    talk: Talk,
    favourite: Favourite,
    geo: Geo,
    registry: Registry,
    blocks: Blocks,
    autocomplete: Autocomplete,
    hook: Hook,
    msg: Msg,
}

console.log(ls)
window.ls = ls;

console.log("Hello, world!")
