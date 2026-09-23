(function(){
  var csrf=document.body.dataset.csrf;

  /* Sicherheitsabfragen vor dem Löschen */
  document.querySelectorAll('form[data-confirm]').forEach(function(f){
    f.addEventListener('submit',function(e){if(!confirm(f.dataset.confirm))e.preventDefault()});
  });
  document.querySelectorAll('button[data-confirm]').forEach(function(b){
    b.addEventListener('click',function(e){if(!confirm(b.dataset.confirm))e.preventDefault()});
  });

  /* ---------- Bilder hochladen ---------- */
  var form=document.getElementById('projectForm'),input=document.getElementById('fileInput');
  if(form&&input){
    var slot='gallery',prog=document.getElementById('progress'),bar=prog.querySelector('i'),label=prog.querySelector('span');

    document.querySelectorAll('[data-upload]').forEach(function(el){
      function pick(){slot=el.dataset.upload;input.multiple=slot==='gallery';input.value='';input.click()}
      el.addEventListener('click',pick);
      el.addEventListener('keydown',function(e){if(e.key==='Enter'||e.key===' '){e.preventDefault();pick()}});
    });
    input.addEventListener('change',function(){if(input.files.length)run([].slice.call(input.files))});

    var drop=document.querySelector('.drop');
    ['dragenter','dragover'].forEach(function(t){drop.addEventListener(t,function(e){e.preventDefault();drop.classList.add('over')})});
    ['dragleave','drop'].forEach(function(t){drop.addEventListener(t,function(e){e.preventDefault();drop.classList.remove('over')})});
    drop.addEventListener('drop',function(e){
      var files=[].slice.call(e.dataTransfer.files).filter(function(f){return /^image\//.test(f.type)});
      if(files.length){slot='gallery';run(files)}
    });

    /* Große Handyfotos schon im Browser verkleinern – spart Upload-Zeit und umgeht Server-Limits */
    function shrink(file){
      return new Promise(function(done){
        if(!/^image\/(jpeg|png|webp)$/.test(file.type))return done(file);
        var url=URL.createObjectURL(file),img=new Image();
        img.onload=function(){
          var max=2400,s=Math.min(1,max/Math.max(img.naturalWidth,img.naturalHeight));
          if(s===1&&file.size<3e6){URL.revokeObjectURL(url);return done(file)}
          var c=document.createElement('canvas');c.width=Math.round(img.naturalWidth*s);c.height=Math.round(img.naturalHeight*s);
          var x=c.getContext('2d');x.fillStyle='#fff';x.fillRect(0,0,c.width,c.height);x.drawImage(img,0,0,c.width,c.height);
          URL.revokeObjectURL(url);
          c.toBlob(function(b){done(b?new File([b],file.name.replace(/\.[^.]+$/,'')+'.jpg',{type:'image/jpeg'}):file)},'image/jpeg',.9);
        };
        img.onerror=function(){URL.revokeObjectURL(url);done(file)};
        img.src=url;
      });
    }

    function send(file,onProgress){
      return new Promise(function(done){
        var fd=new FormData();
        fd.append('csrf',csrf);fd.append('action','upload');fd.append('id',form.dataset.id);fd.append('slot',slot);fd.append('file',file);
        var xhr=new XMLHttpRequest();
        xhr.open('POST','./?p=upload');
        xhr.upload.onprogress=function(e){if(e.lengthComputable)onProgress(e.loaded/e.total)};
        xhr.onload=function(){
          var r;try{r=JSON.parse(xhr.responseText)}catch(err){r={ok:false,error:xhr.status===413?'Die Datei ist zu groß für den Server.':'Unerwartete Antwort vom Server ('+xhr.status+').'}}
          done(r);
        };
        xhr.onerror=function(){done({ok:false,error:'Keine Verbindung zum Server.'})};
        xhr.send(fd);
      });
    }

    function run(files){
      var ok=0,errors=[],i=0;
      prog.hidden=false;
      (function next(){
        if(i>=files.length){
          if(errors.length)alert('Nicht alle Bilder konnten hochgeladen werden:\n\n'+errors.join('\n'));
          if(ok){form.elements.uploaded.value=ok;form.submit()}else{prog.hidden=true}
          return;
        }
        var f=files[i];
        label.textContent='Bild '+(i+1)+' von '+files.length+' wird vorbereitet …';
        shrink(f).then(function(small){
          label.textContent='Bild '+(i+1)+' von '+files.length+' wird hochgeladen …';
          return send(small,function(p){bar.style.width=((i+p)/files.length*100)+'%'});
        }).then(function(r){
          if(r.ok)ok++;else errors.push(f.name+': '+(r.error||'Fehler'));
          i++;bar.style.width=(i/files.length*100)+'%';next();
        });
      })();
    }
  }

  /* ---------- Texteditor für Rechtstexte ---------- */
  var ed=document.getElementById('editor'),src=document.getElementById('html'),lf=document.getElementById('legalForm');
  if(ed&&src&&lf){
    var htmlMode=false,dirty=false;
    try{document.execCommand('defaultParagraphSeparator',false,'p')}catch(e){}
    document.querySelectorAll('.toolbar button').forEach(function(b){
      b.addEventListener('mousedown',function(e){e.preventDefault()}); // Auswahl im Editor behalten
      b.addEventListener('click',function(){
        var cmd=b.dataset.cmd;
        if(cmd==='html'){
          htmlMode=!htmlMode;
          if(htmlMode){src.value=ed.innerHTML}else{ed.innerHTML=src.value}
          ed.hidden=htmlMode;src.hidden=!htmlMode;b.classList.toggle('active',htmlMode);
          document.querySelectorAll('.toolbar button:not([data-cmd=html])').forEach(function(o){o.disabled=htmlMode});
          return;
        }
        ed.focus();
        if(cmd==='createLink'){
          var url=prompt('Link-Adresse (z. B. https://… oder mailto:…):','https://');
          if(!url||url==='https://')return;
          document.execCommand('createLink',false,url);
        }else if(cmd==='formatBlock'){
          document.execCommand('formatBlock',false,'<'+b.dataset.val+'>');
        }else{
          document.execCommand(cmd,false,null);
        }
        dirty=true;
      });
    });
    ed.addEventListener('input',function(){dirty=true});
    src.addEventListener('input',function(){dirty=true});
    lf.addEventListener('submit',function(){if(!htmlMode)src.value=ed.innerHTML;dirty=false});
    window.addEventListener('beforeunload',function(e){if(dirty){e.preventDefault();e.returnValue=''}});
  }
})();
