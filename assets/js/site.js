(function(){
  var reduce=window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  function $(id){return document.getElementById(id)}

  /* Alte Anker der früheren Einzeldatei auf die neuen Seiten umleiten */
  if(location.hash==='#impressum'||location.hash==='#datenschutz'){location.replace(location.hash.slice(1)+'/');return}

  /* Header & Menü */
  var top=$('top'),burger=$('burger'),nav=$('nav'),bIcon=$('bIcon');
  function onScroll(){top.classList.toggle('solid',window.scrollY>40)}
  window.addEventListener('scroll',onScroll,{passive:true});onScroll();
  function setMenu(o){nav.classList.toggle('open',o);burger.setAttribute('aria-expanded',o);burger.setAttribute('aria-label',o?'Menü schließen':'Menü öffnen');bIcon.setAttribute('d',o?'M6 6l12 12M18 6L6 18':'M4 7h16M4 12h16M4 17h10');document.body.style.overflow=o?'hidden':''}
  burger.addEventListener('click',function(){setMenu(!nav.classList.contains('open'))});
  nav.querySelectorAll('a').forEach(function(a){a.addEventListener('click',function(){setMenu(false)})});
  document.addEventListener('keydown',function(e){if(e.key==='Escape'&&nav.classList.contains('open')){setMenu(false);burger.focus()}});

  /* Reveal */
  var els=document.querySelectorAll('.reveal');
  if('IntersectionObserver' in window){
    var io=new IntersectionObserver(function(es){es.forEach(function(e){if(e.isIntersecting){e.target.classList.add('in');io.unobserve(e.target)}})},{threshold:.12});
    els.forEach(function(el){io.observe(el)});
  } else els.forEach(function(el){el.classList.add('in')});

  /* Anfrage-Konfigurator */
  var cfg=$('cfg');
  if(cfg){
    var state={leistung:[],umfang:'',zeit:''},waNr=cfg.dataset.wa,mail=cfg.dataset.mail,greet=cfg.dataset.greeting||'Hallo,';
    cfg.querySelectorAll('.chips').forEach(function(g){
      var key=g.dataset.group,multi=!!g.dataset.multi;
      g.querySelectorAll('.chip').forEach(function(c){c.addEventListener('click',function(){
        var val=c.childNodes[0].textContent.trim(),on=c.getAttribute('aria-pressed')==='true';
        if(multi){c.setAttribute('aria-pressed',!on);state[key]=on?state[key].filter(function(x){return x!==val}):state[key].concat(val)}
        else{g.querySelectorAll('.chip').forEach(function(o){o.setAttribute('aria-pressed','false')});c.setAttribute('aria-pressed',!on);state[key]=on?'':val}
        upd();
      })});
    });
    var msg=function(){
      var n=$('cName').value.trim(),o=$('cOrt').value.trim(),m=$('cMsg').value.trim();
      var t=greet+'\n';
      t+=state.leistung.length?'ich interessiere mich für: '+state.leistung.join(', ')+'.':'ich interessiere mich für ein Projekt.';
      if(state.umfang)t+='\nUmfang: '+state.umfang;
      if(state.zeit)t+='\nZeitraum: '+state.zeit;
      if(o)t+='\nOrt: '+o;
      if(m)t+='\n\n'+m;
      t+='\n\nViele Grüße'+(n?'\n'+n:'');
      return t;
    };
    var upd=function(){
      var t=msg();$('preview').textContent=t;
      if($('sendWa'))$('sendWa').href='https://wa.me/'+waNr+'?text='+encodeURIComponent(t);
      if($('sendMail'))$('sendMail').href='mailto:'+mail+'?subject='+encodeURIComponent('Anfrage'+(state.leistung.length?': '+state.leistung.join(', '):''))+'&body='+encodeURIComponent(t);
    };
    ['cName','cOrt','cMsg'].forEach(function(id){$(id).addEventListener('input',upd)});
    upd();
  }

  /* Icons: Zeichen-Effekt vorbereiten */
  document.querySelectorAll('.svc .ic *').forEach(function(el){el.setAttribute('pathLength','1')});

  /* Grashalme erzeugen */
  var bl=$('blades');
  if(bl){
    var bh='';
    for(var gx=2;gx<558;gx+=3.2){var hgt=10+Math.random()*16,lean=(Math.random()-.5)*8,sh=Math.random();bh+='<path d="M'+gx.toFixed(1)+' 213q'+(lean/2).toFixed(1)+' -'+(hgt/2).toFixed(1)+' '+lean.toFixed(1)+' -'+hgt.toFixed(1)+'" stroke="'+(sh<.33?'#5f7a2e':sh<.66?'#7a9a3a':'#8fb046')+'" stroke-width="2.2" fill="none" stroke-linecap="round"/>'}
    bl.innerHTML=bh;
  }

  /* Scroll-Szenen (nur bei Scroll/Resize neu berechnen, nicht in jedem Frame) */
  var layers=[].slice.call(document.querySelectorAll('.soil .ly')),psteps=[].slice.call(document.querySelectorAll('.psteps li')),pbar=$('pbar'),prof=$('profil'),steps=$('steps');
  function clamp(v){return v<0?0:v>1?1:v}
  function scenes(){
    var vh=window.innerHeight;
    if(prof){
      var r=prof.getBoundingClientRect(),p=reduce?1:clamp(-r.top/(r.height-vh)),act=0;
      layers.forEach(function(g,i){
        var n=layers.length,t=reduce?1:clamp((p-i*(.88/n)-.02)/(.62/n));
        var e=1-Math.pow(1-t,3);
        g.style.transform='translateY('+((1-e)*-140)+'px)';g.style.opacity=t;
        if(t>.5)act=i;
      });
      psteps.forEach(function(li,i){li.classList.toggle('on',i===act)});
      pbar.style.transform='scaleX('+p+')';
    }
    if(steps){
      var rs=steps.getBoundingClientRect(),q=reduce?1:clamp((vh*.75-rs.top)/(rs.height));
      steps.style.setProperty('--p',q.toFixed(3));
    }
  }
  var sceneQueued=false;
  function queueScenes(){if(!sceneQueued){sceneQueued=true;requestAnimationFrame(function(){sceneQueued=false;scenes()})}}
  window.addEventListener('scroll',queueScenes,{passive:true});
  window.addEventListener('resize',queueScenes);
  scenes();

  /* Laufband – läuft nur, solange es sichtbar ist */
  var mq=$('mq');
  if(mq&&!reduce){
    var mx=0,lastY=window.scrollY,vel=0,mqOn=true;
    var marquee=function(){
      if(!mqOn)return;
      var y=window.scrollY;vel+=((y-lastY)-vel)*.1;lastY=y;
      mx-=(.6+Math.min(Math.abs(vel)*.25,8));var half=mq.scrollWidth/2;if(-mx>=half)mx+=half;
      mq.style.transform='translateX('+mx+'px)';
      requestAnimationFrame(marquee);
    };
    requestAnimationFrame(marquee);
    if('IntersectionObserver' in window){new IntersectionObserver(function(e){var v=e[0].isIntersecting;if(v&&!mqOn){mqOn=true;lastY=window.scrollY;requestAnimationFrame(marquee)}mqOn=v}).observe(mq)}
  }

  /* Einsatzgebiet: Linie zeichnen */
  var area=document.querySelector('.area');
  if(area){if('IntersectionObserver' in window){new IntersectionObserver(function(e,o){if(e[0].isIntersecting){area.classList.add('in');o.disconnect()}},{threshold:.3}).observe(area)}else area.classList.add('in')}

  /* Vorher/Nachher */
  document.querySelectorAll('.ba').forEach(function(ba){
    var rng=ba.querySelector('input');
    rng.addEventListener('input',function(){ba.style.setProperty('--x',rng.value+'%')});
  });

  /* Galerien + Bildansicht */
  var lb=$('lb');
  if(lb){
    var lbImg=lb.querySelector('img'),lbCap=lb.querySelector('p'),list=[],cur=0,opener=null;
    var show=function(i){
      cur=(i+list.length)%list.length;var b=list[cur],im=b.querySelector('img');
      lbImg.src=b.dataset.full||im.src;lbImg.alt=im.alt;
      lbCap.textContent=(cur+1)+' / '+list.length+(im.alt?' – '+im.alt:'');
    };
    var close=function(){lb.classList.remove('open');document.body.style.overflow='';if(opener)opener.focus()};
    document.querySelectorAll('.proj-item').forEach(function(item){
      var strip=item.querySelector('.strip');if(!strip)return;
      var shots=[].slice.call(strip.querySelectorAll('.shot'));
      var stepW=function(){return shots[0].getBoundingClientRect().width+18};
      var pv=item.querySelector('.s-prev'),nx=item.querySelector('.s-next');
      if(pv)pv.addEventListener('click',function(){strip.scrollBy({left:-stepW(),behavior:'smooth'})});
      if(nx)nx.addEventListener('click',function(){strip.scrollBy({left:stepW(),behavior:'smooth'})});
      shots.forEach(function(b,i){b.addEventListener('click',function(){
        list=shots;opener=b;show(i);
        lb.querySelector('.pv').hidden=lb.querySelector('.nx').hidden=shots.length<2;
        lb.classList.add('open');document.body.style.overflow='hidden';lb.querySelector('.x').focus();
      })});
    });
    lb.querySelector('.x').onclick=close;lb.querySelector('.pv').onclick=function(){show(cur-1)};lb.querySelector('.nx').onclick=function(){show(cur+1)};
    lb.addEventListener('click',function(e){if(e.target===lb)close()});
    document.addEventListener('keydown',function(e){
      if(!lb.classList.contains('open'))return;
      if(e.key==='Escape')close();
      if(e.key==='ArrowRight'&&list.length>1)show(cur+1);
      if(e.key==='ArrowLeft'&&list.length>1)show(cur-1);
      if(e.key==='Tab'){ /* Fokus im Dialog halten */
        var f=[].slice.call(lb.querySelectorAll('button:not([hidden])')),i=f.indexOf(document.activeElement);
        e.preventDefault();f[(i+(e.shiftKey?-1:1)+f.length)%f.length].focus();
      }
    });
    var sx0=null;lb.addEventListener('touchstart',function(e){sx0=e.touches[0].clientX},{passive:true});
    lb.addEventListener('touchend',function(e){if(sx0===null)return;var dx=e.changedTouches[0].clientX-sx0;if(Math.abs(dx)>50&&list.length>1)show(cur+(dx<0?1:-1));sx0=null});
  }

  /* Animiertes Gelände: Höhenlinien (Marching Squares über Rauschen) */
  var cv=$('terrain');
  if(!cv)return;
  var ctx=cv.getContext('2d');
  var W,H,dpr,cell,cols,rows,field;
  var perm=new Uint8Array(512);(function(){var p=[];for(var i=0;i<256;i++)p[i]=i;for(i=255;i>0;i--){var j=Math.floor(Math.random()*(i+1)),t=p[i];p[i]=p[j];p[j]=t}for(i=0;i<512;i++)perm[i]=p[i&255]})();
  function fade(t){return t*t*t*(t*(t*6-15)+10)}
  function lerp(a,b,t){return a+(b-a)*t}
  function grad(h,x,y,z){var u=(h&15)<8?x:y,v=(h&15)<4?y:((h&15)===12||(h&15)===14?x:z);return((h&1)?-u:u)+((h&2)?-v:v)}
  function noise(x,y,z){var X=Math.floor(x)&255,Y=Math.floor(y)&255,Z=Math.floor(z)&255;x-=Math.floor(x);y-=Math.floor(y);z-=Math.floor(z);var u=fade(x),v=fade(y),w=fade(z);
    var A=perm[X]+Y,AA=perm[A]+Z,AB=perm[A+1]+Z,B=perm[X+1]+Y,BA=perm[B]+Z,BB=perm[B+1]+Z;
    return lerp(lerp(lerp(grad(perm[AA],x,y,z),grad(perm[BA],x-1,y,z),u),lerp(grad(perm[AB],x,y-1,z),grad(perm[BB],x-1,y-1,z),u),v),lerp(lerp(grad(perm[AA+1],x,y,z-1),grad(perm[BA+1],x-1,y,z-1),u),lerp(grad(perm[AB+1],x,y-1,z-1),grad(perm[BB+1],x-1,y-1,z-1),u),v),w)}
  var mouse={x:-9999,y:-9999,a:0};
  function size(){var r=cv.getBoundingClientRect();dpr=Math.min(window.devicePixelRatio||1,2);W=r.width;H=r.height;cv.width=W*dpr;cv.height=H*dpr;ctx.setTransform(dpr,0,0,dpr,0,0);cell=W<700?16:14;cols=Math.ceil(W/cell)+1;rows=Math.ceil(H/cell)+1;field=new Float32Array(cols*rows)}
  var LEVELS=14;
  function draw(t){
    var s=W<700?.0042:.0026,z=t*.00005;
    for(var j=0;j<rows;j++)for(var i=0;i<cols;i++){
      var x=i*cell,y=j*cell,v=noise(x*s,y*s,z)*.9+noise(x*s*2.3,y*s*2.3,z*1.6)*.35;
      if(mouse.a>0){var dx=x-mouse.x,dy=y-mouse.y;v+=Math.exp(-(dx*dx+dy*dy)/(2*110*110))*.55*mouse.a}
      field[j*cols+i]=v;
    }
    ctx.clearRect(0,0,W,H);
    for(var l=0;l<LEVELS;l++){
      var th=-.7+l*(1.5/LEVELS),major=l%4===0;
      ctx.beginPath();
      for(j=0;j<rows-1;j++)for(i=0;i<cols-1;i++){
        var a=field[j*cols+i],b=field[j*cols+i+1],c=field[(j+1)*cols+i+1],d=field[(j+1)*cols+i];
        var k=(a>th?8:0)|(b>th?4:0)|(c>th?2:0)|(d>th?1:0);if(k===0||k===15)continue;
        var x0=i*cell,y0=j*cell;
        var T=[x0+cell*(th-a)/(b-a),y0],R=[x0+cell,y0+cell*(th-b)/(c-b)],Bt=[x0+cell*(th-d)/(c-d),y0+cell],L=[x0,y0+cell*(th-a)/(d-a)];
        var segs;
        switch(k){case 1:case 14:segs=[L,Bt];break;case 2:case 13:segs=[Bt,R];break;case 3:case 12:segs=[L,R];break;case 4:case 11:segs=[T,R];break;case 6:case 9:segs=[T,Bt];break;case 7:case 8:segs=[L,T];break;case 5:segs=[L,T,Bt,R];break;case 10:segs=[L,Bt,T,R];break}
        for(var q=0;q<segs.length;q+=2){ctx.moveTo(segs[q][0],segs[q][1]);ctx.lineTo(segs[q+1][0],segs[q+1][1])}
      }
      ctx.strokeStyle=major?'rgba(201,174,127,.55)':'rgba(236,231,220,.14)';
      ctx.lineWidth=major?1.3:.9;ctx.stroke();
    }
  }
  var last=0,running=true;
  function loop(t){if(!running)return;if(t-last>40){mouse.a+=((mouse.x>-999?1:0)-mouse.a)*.06;draw(t);last=t}requestAnimationFrame(loop)}
  size();
  if(reduce){draw(0)}else{requestAnimationFrame(loop)}
  window.addEventListener('resize',function(){size();if(reduce||!running)draw(last)});
  var hero=document.querySelector('.hero');
  function pt(e){var r=cv.getBoundingClientRect(),p=e.touches?e.touches[0]:e;mouse.x=p.clientX-r.left;mouse.y=p.clientY-r.top}
  hero.addEventListener('pointermove',pt);hero.addEventListener('touchmove',pt,{passive:true});
  hero.addEventListener('pointerleave',function(){mouse.x=-9999});
  if(!reduce&&'IntersectionObserver' in window){new IntersectionObserver(function(e){var v=e[0].isIntersecting;if(v&&!running){running=true;requestAnimationFrame(loop)}running=v}).observe(hero)}
})();
