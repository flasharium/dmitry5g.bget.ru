
EventMachine = (function(){
  var receivers = [];

  return {
    send: function(){
      var args = [].slice.call(arguments);
      var eventId = args.shift()
      if (receivers[eventId]) {
        for (method in receivers[eventId]) {
          method.apply(null, args)
        }
      }
    },
    register: function(eventId, method){
      receivers[eventId] = receivers[eventId] || []
      receivers[eventId].push(method)
    }
  }
})()
