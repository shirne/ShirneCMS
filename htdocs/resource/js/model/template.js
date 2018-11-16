
Number.prototype.format=function(fix){
    if(fix===undefined)fix=2;
    var num=this.toFixed(fix);
    var z=num.split('.');
    var format=[],f=z[0].split(''),l=f.length;
    for(var i=0;i<l;i++){
        if(i>0 && i % 3==0){
            format.unshift(',');
        }
        format.unshift(f[l-i-1]);
    }
    return format.join('')+(z.length==2?'.'+z[1]:'');
};
if(!String.prototype.trim){
    String.prototype.trim=function () {
        return this.replace(/(^\s+|\s+$)/g,'');
    }
}
String.prototype.compile=function(data,list){

    if(list){
        var temps=[];
        for(var i in data){
            temps.push(this.compile(data[i]));
        }
        return temps.join("\n");
    }else{
        return this.replace(/\{if\s+([^\}]+)\}(.*?){\/if}/g,function(all, condition, cont){
            var operation;
            if(operation=condition.match(/\s+(=+|<|>)\s+/)){
                operation=operation[0];
                var part=condition.split(operation);
                if(part[0].indexOf('@')===0){
                    part[0]=data[part[0].replace('@','')];
                }
                if(part[1].indexOf('@')===0){
                    part[1]=data[part[1].replace('@','')];
                }
                operation=operation.trim();
                var result=false;
                switch (operation){
                    case '==':
                        result = part[0] == part[1];
                        break;
                    case '===':
                        result = part[0] === part[1];
                        break;
                    case '>':
                        result = part[0] > part[1];
                        break;
                    case '<':
                        result = part[0] < part[1];
                        break;
                }
                if(result){
                    return cont;
                }
            }else {
                if (data[condition.replace('@','')]) return cont;
            }
            return '';
        }).replace(/\{@([\w\d\.]+)(?:\|([\w\d]+)(?:\s*=\s*([\w\d,\s#]+))?)?\}/g,function(all,m1,func,args){

            if(m1.indexOf('.')>0){
                var keys=m1.split('.'),val=data;
                for(var i=0;i<keys.length;i++){
                    if(val[keys[i]]!==undefined && val[keys[i]]!==null){
                        val=val[keys[i]];
                    }else{
                        val = '';
                        break;
                    }
                }
                return callfunc(val,func,args);
            }else{
                return callfunc(data[m1],func,args,data);
            }
        });
    }
};

function tostring(obj) {
    if(obj && obj.toString){
        return obj.toString();
    }
    return '';
}

function callfunc(val,func,args,thisobj){
    if(!args){
        args=[val];
    }else{
        if(typeof args==='string')args=args.split(',');
        var argidx=args.indexOf('###');
        if(argidx>=0){
            args[argidx]=val;
        }else{
            args=[val].concat(args);
        }
    }

    return window[func]?window[func].apply(thisobj,args):((val===undefined||val===null)?'':val);
}

function iif(v,m1,m2){
    if(v==='0')v=0;
    return v?m1:m2;
}