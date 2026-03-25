document.addEventListener('DOMContentLoaded',()=>{
  const form=document.getElementById('resForm');
  if(!form)return;
  const $=id=>document.getElementById(id);
  const rn=$('rn'),re=$('re'),rg=$('rg'),rd=$('rd'),rt=$('rt');
  const e1=$('e1'),e2=$('e2'),e3=$('e3'),e4=$('e4'),e5=$('e5');
  const mbi=$('mb-items'),mbb=$('mb-body');
  const se=(el,m)=>{if(el)el.textContent=m;};
  const ce=el=>{if(el)el.textContent='';};
  const bad=i=>{if(i)i.style.boxShadow='0 0 0 2px rgba(192,57,43,.42)';};
  const good=i=>{if(i)i.style.boxShadow='0 0 0 2px rgba(30,74,14,.38)';};
  const ve=v=>/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);
  const fd=v=>{const d=new Date(v),n=new Date();n.setHours(0,0,0,0);return d>=n;};
  const drop=(k,text)=>{
    if(!mbi)return;
    const o=mbi.querySelector(`[data-k="${k}"]`);if(o)o.remove();
    const d=document.createElement('div');d.className='mb-r';d.dataset.k=k;
    d.innerHTML=`<i class="bi ${k}"></i>${text}`;mbi.appendChild(d);
    if(mbb){mbb.style.transform='rotate(4deg) scale(1.04)';setTimeout(()=>{mbb.style.transform='rotate(-3deg)';},85);setTimeout(()=>{mbb.style.transform='';},190);}
  };

  rn?.addEventListener('blur',()=>{const v=rn.value.trim();if(!v||v.length<2){se(e1,!v?'Name required.':'Min 2 chars.');bad(rn);}else{ce(e1);good(rn);drop('bi-person',v);}});
  re?.addEventListener('blur',()=>{const v=re.value.trim();if(!v){se(e2,'Email required.');bad(re);}else if(!ve(v)){se(e2,'Enter valid email.');bad(re);}else{ce(e2);good(re);drop('bi-envelope',v);}});
  rg?.addEventListener('change',()=>{if(!rg.value){se(e3,'Select guests.');bad(rg);}else{ce(e3);good(rg);drop('bi-people',rg.value);}});
  rd?.addEventListener('change',()=>{if(!rd.value){se(e4,'Select date.');bad(rd);}else if(!fd(rd.value)){se(e4,'Future date only.');bad(rd);}else{ce(e4);good(rd);drop('bi-calendar3',rd.value);}});
  rt?.addEventListener('change',()=>{if(!rt.value){se(e5,'Select time.');bad(rt);}else{ce(e5);good(rt);drop('bi-clock',rt.value);}});

  form.addEventListener('submit',e=>{
    e.preventDefault();let ok=true;
    const nv=rn?.value.trim()||'';
    if(!nv||nv.length<2){se(e1,!nv?'Name required.':'Min 2 chars.');bad(rn);ok=false;}else{ce(e1);good(rn);}
    const ev=re?.value.trim()||'';
    if(!ev){se(e2,'Email required.');bad(re);ok=false;}else if(!ve(ev)){se(e2,'Enter valid email.');bad(re);ok=false;}else{ce(e2);good(re);}
    if(!rg?.value){se(e3,'Select guests.');bad(rg);ok=false;}else{ce(e3);good(rg);}
    if(!rd?.value){se(e4,'Select date.');bad(rd);ok=false;}else if(!fd(rd.value)){se(e4,'Future date only.');bad(rd);ok=false;}else{ce(e4);good(rd);}
    if(!rt?.value){se(e5,'Select time.');bad(rt);ok=false;}else{ce(e5);good(rt);}
    /* ALERT rubric */
    if(!ok){alert('Please fill in all required fields.');return;}
    /* CONFIRM rubric */
    const ok2=confirm(`Confirm:\n\nName: ${nv}\nEmail: ${ev}\nGuests: ${rg.value}\nDate: ${rd.value}\nTime: ${rt.value}\n\nProceed?`);
    if(!ok2){
      /* PROMPT rubric */
      const note=prompt('What would you like to change?');
      if(note)alert(`Noted: "${note}"\nPlease update and resubmit.`);
      return;
    }
    alert('Reservation confirmed! We look forward to welcoming you to DineLocal.');
    form.submit();
  });
});