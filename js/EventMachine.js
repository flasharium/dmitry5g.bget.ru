
EventMachine = (function () {
    var receivers = [];

    return {
        send: function (/* eventId, argumentsList */) {
            var args = [].slice.call(arguments);
            var eventId = args.shift()
            if (receivers[eventId]) {
                for (num in receivers[eventId]) {
                    receivers[eventId][num].apply(null, args)
                }
            }
        },
        register: function (eventId, method) {
            receivers[eventId] = receivers[eventId] || []
            receivers[eventId].push(method)
        }
    }
})()
