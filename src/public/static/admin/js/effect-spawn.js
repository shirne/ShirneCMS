function spawnParticle(vars){

    var p,ls;

    var pt={};

    p=Math.PI*2*Math.random();

    ls=Math.sqrt(Math.random()*vars.distributionRadius);

    pt.x=Math.sin(p)*ls;

    pt.y=-vars.vortexHeight/2;

    pt.vy=vars.initV/20+Math.random()/vars.initV;

    pt.z=Math.cos(p)/ls;

    pt.radius=200+800*Math.random();

    pt.color=pt.radius/1000+vars.frameNo/250;

    vars.points.push(pt);

}

function frame(vars) {

    if(vars === undefined){

        var vars={};

        vars.canvas = document.querySelector("canvas");

        vars.ctx = vars.canvas.getContext("2d");

        vars.canvas.width = document.body.clientWidth;

        vars.canvas.height = document.body.clientHeight;

        window.addEventListener("resize", function(){

            vars.canvas.width = document.body.clientWidth;

            vars.canvas.height = document.body.clientHeight;

            vars.cx=vars.canvas.width/2;

            vars.cy=vars.canvas.height/2;

        }, true);

        vars.frameNo=0;

        vars.camX = 0;

        vars.camY = 0;

        vars.camZ = -14;

        vars.pitch = elevation(vars.camX, vars.camZ, vars.camY) - Math.PI / 2;

        vars.yaw = 0;

        vars.cx=vars.canvas.width/2;

        vars.cy=vars.canvas.height/2;

        vars.bounding=10;

        vars.scale=500;

        vars.floor=26.5;

        vars.points=[];

        vars.initParticles=2000;

        vars.initV=.01;

        vars.distributionRadius=800;

        vars.vortexHeight=25;

    }

    vars.frameNo++;

    requestAnimationFrame(function() {

        frame(vars);

    });

    process(vars);

    draw(vars);

}

frame();