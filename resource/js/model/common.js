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

function parseObject(str) {
    try {
        var result = JSON.parse(str)
        if (result) {
            return result
        }
    } catch (e) {
        console.error(e)
    }
    return {};
}

function parseArray(str) {
    try {
        var result = JSON.parse(str)
        if (result instanceof Array) {
            return result
        }
    } catch (e) {
        console.error(e)
    }
    return [];
}

function copy_obj(arr) {
    return JSON.parse(JSON.stringify(arr));
}

function isObjectValueEqual(a, b) {
    if (!a && !b) return true;
    if (!a || !b) return false;

    var aProps = Object.getOwnPropertyNames(a);
    var bProps = Object.getOwnPropertyNames(b);

    if (aProps.length != bProps.length) {
        return false;
    }

    for (var i = 0; i < aProps.length; i++) {
        var propName = aProps[i];

        if (a[propName] !== b[propName]) {
            return false;
        }
    }

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