import EventEmitter from "eventemitter3"

const Emitter = new EventEmitter();

Emitter.meta = function(meta_id, ...events) {
    events.forEach(function(el) {
       Emitter.on(el, function() { Emitter.emit(meta_id, arguments); });
    });
};

export default Emitter;
