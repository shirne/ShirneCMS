function del(obj, msg) {
    dialog.confirm(msg, function () {
        location.href = $(obj).attr('href');
    });
    return false;
}

function lang(key) {
    if (window.language && window.language[key]) {
        return window.language[key];
    }
    return key;
}

function randomString(len, charSet) {
    charSet = charSet || 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var str = '', allLen = charSet.length;
    for (var i = 0; i < len; i++) {
        var randomPoz = Math.floor(Math.random() * allLen);
        str += charSet.substring(randomPoz, randomPoz + 1);
    }
    return str;
}

function copy_obj(arr) {
    return JSON.parse(JSON.stringify(arr));
}

function isObjectValueEqual(a, b) {
    if (!a && !b) return true;
    if (!a || !b) return false;

    // Of course, we can do it use for in
    // Create arrays of property names
    var aProps = Object.getOwnPropertyNames(a);
    var bProps = Object.getOwnPropertyNames(b);

    // If number of properties is different,
    // objects are not equivalent
    if (aProps.length != bProps.length) {
        return false;
    }

    for (var i = 0; i < aProps.length; i++) {
        var propName = aProps[i];

        // If values of same property are not equal,
        // objects are not equivalent
        if (a[propName] !== b[propName]) {
            return false;
        }
    }

    // If we made it this far, objects
    // are considered equivalent
    return true;
}

function array_combine(a, b) {
    var obj = {};
    for (var i = 0; i < a.length; i++) {
        if (b.length < i + 1) break;
        obj[a[i]] = b[i];
    }
    return obj;
}