var Util = {
    addClass: function(element, className) {
        var classes = element.className.split(' ');

        if (classes.indexOf(className) === -1) {
            classes.push(className);
        }

        element.className = classes.join(' ');
    },

    removeClass: function(element, className) {
        var classes = element.className.split(' ');

        if (classes.indexOf(className) !== -1) {
            delete classes[classes.indexOf(className)];
        }

        element.className = classes.join(' ');
    },

    hasClass: function(element, className) {
        var classes = element.className.split(' ');

        if (classes.indexOf(className) !== -1) {
            return true;
        }

        return false;
    },

    delegate: function(type, container, item, cb) {
        container.addEventListener(type, function(e) {
            if (this.matches(e.target, item)) {
                if (typeof cb == 'function') {
                    cb(e.target);
                }
            }
        }.bind(this));
    },

    matches: function(element, selector) {
    	var p = Element.prototype;
    	var f = p.matches || p.webkitMatchesSelector || p.mozMatchesSelector || p.msMatchesSelector || function(s) {
    		return [].indexOf.call(document.querySelectorAll(s), this) !== -1;
    	};

    	return f.call(element, selector);
    }
};
