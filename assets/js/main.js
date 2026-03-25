document.addEventListener('DOMContentLoaded', () => {
  /* CURSOR — fork */
  const c = document.getElementById('cursor');
  let mx=0,my=0;
  document.addEventListener('mousemove',e=>{ mx=e.clientX;my=e.clientY; if(c)c.style.transform=`translate(${mx-11}px,${my-2}px)`; });
  document.querySelectorAll('a,button,input,select,textarea').forEach(el=>{
    el.addEventListener('mouseenter',()=>{ if(c) c.classList.add('hovered'); });
    el.addEventListener('mouseleave',()=>{ if(c) c.classList.remove('hovered'); });
  });

  /* PROGRESS */
  const pb = document.getElementById('progress-bar');
  window.addEventListener('scroll',()=>{ if(pb) pb.style.transform=`scaleX(${window.scrollY/(document.documentElement.scrollHeight-window.innerHeight)})`; },{passive:true});

  /* CARD TILT */
  document.querySelectorAll('.mcard').forEach(card=>{
    card.addEventListener('mousemove',e=>{
      const r=card.getBoundingClientRect();
      card.style.transform=`perspective(900px) rotateX(${((e.clientY-r.top)/r.height-.5)*-5}deg) rotateY(${((e.clientX-r.left)/r.width-.5)*5}deg) translateY(-8px)`;
    });
    card.addEventListener('mouseleave',()=>card.style.transform='');
  });

  /* MIN DATE */
  const di=document.getElementById('rd');
  if(di){const t=new Date();di.min=`${t.getFullYear()}-${String(t.getMonth()+1).padStart(2,'0')}-${String(t.getDate()).padStart(2,'0')}`;}

  /* LAZY IMAGES */
  if('IntersectionObserver'in window){
    const io=new IntersectionObserver(en=>{en.forEach(e=>{if(e.isIntersecting){const i=e.target;i.style.cssText+='opacity:0;transition:opacity .6s ease';if(i.complete)i.style.opacity='1';else i.onload=()=>i.style.opacity='1';io.unobserve(i);}});});
    document.querySelectorAll('img[loading="lazy"]').forEach(i=>io.observe(i));
  }

  console.log('%cDineLocal%c ITC 6355 · Bootstrap 5','background:#C4551A;color:#FBF0DC;font-family:Georgia;font-weight:bold;padding:4px 12px;border-radius:4px','color:#E8A83E;padding:4px');
});