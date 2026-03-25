<?php
// Welcome page — no session needed
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Welcome to DineLocal</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&family=Inter:wght@300;400;500&display=swap" rel="stylesheet"/>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
  <style>
    *{margin:0;padding:0;box-sizing:border-box;}
    html,body{width:100%;height:100%;overflow:hidden;background:#080301;}
    body{font-family:'Inter',sans-serif;user-select:none;cursor:pointer;}

    #stage{position:relative;width:100vw;height:100vh;overflow:hidden;}

    /* Dark wood table */
    #table-bg{
      position:absolute;inset:0;z-index:0;
      background:
        radial-gradient(ellipse 70% 55% at 50% 58%, rgba(130,75,22,.18) 0%, transparent 65%),
        repeating-linear-gradient(87deg,transparent,transparent 58px,rgba(255,255,255,.005) 58px,rgba(255,255,255,.005) 59px),
        repeating-linear-gradient(4deg,transparent,transparent 42px,rgba(0,0,0,.028) 42px,rgba(0,0,0,.028) 43px),
        linear-gradient(165deg,#1d1208 0%,#140c04 50%,#0e0801 100%);
    }
    #table-bg::after{
      content:'';position:absolute;inset:0;
      background:radial-gradient(ellipse 110% 110% at 50% 50%,transparent 18%,rgba(0,0,0,.68) 100%);
    }

    /* ── Food plates (circular, real photos) ── */
    .plate{
      position:absolute;border-radius:50%;overflow:hidden;z-index:5;
      box-shadow:0 22px 60px rgba(0,0,0,.82),0 6px 20px rgba(0,0,0,.6),
                 inset 0 0 0 2px rgba(255,255,255,.04);
    }
    .plate::before{
      content:'';position:absolute;inset:0;border-radius:50%;
      box-shadow:inset 0 0 0 12px rgba(242,234,220,.88);
      z-index:2;pointer-events:none;
    }
    .plate img{width:100%;height:100%;object-fit:cover;display:block;transform:scale(1.07);}

    /* Desktop plate sizes + positions */
    #p1{width:195px;height:195px;left:1%;top:4%;}
    #p2{width:220px;height:220px;right:2%;top:2%;}
    #p3{width:185px;height:185px;left:0%;top:39%;}
    #p4{width:198px;height:198px;right:0%;top:37%;}
    #p5{width:205px;height:205px;left:2%;bottom:3%;}
    #p6{width:200px;height:200px;right:1%;bottom:4%;}

    /* ── Brand ── */
    #brand{
      position:absolute;top:5%;left:50%;transform:translateX(-50%);
      text-align:center;z-index:20;
    }
    #brand .eyebrow{
      font-size:.55rem;letter-spacing:.32em;color:rgba(232,168,62,.5);
      text-transform:uppercase;margin-bottom:.5rem;
    }
    #brand h1{
      font-family:'Cormorant Garamond',serif;
      font-size:clamp(2.6rem,4.8vw,4.2rem);
      font-weight:700;color:#FBF0DC;letter-spacing:.13em;
    }
    .orn{display:flex;align-items:center;justify-content:center;gap:.65rem;margin:.45rem 0;}
    .orn-line{width:52px;height:1px;background:linear-gradient(to right,transparent,rgba(232,168,62,.38));}
    .orn-line.r{background:linear-gradient(to left,transparent,rgba(232,168,62,.38));}
    .orn-gem{width:5px;height:5px;background:rgba(232,168,62,.42);transform:rotate(45deg);}
    #brand .sub{font-size:.54rem;letter-spacing:.22em;color:rgba(251,240,220,.2);text-transform:uppercase;}

    /* ── Centre scene: plate + utensils ── */
    #scene{
      position:absolute;top:50%;left:50%;
      transform:translate(-50%,-46%);
      z-index:10;
      /* wide enough for fork + plate + knife */
      width:700px;height:340px;
    }

    /* Everything inside scene sits at its own absolute position */
    #plate-glow{
      position:absolute;top:50%;left:50%;
      transform:translate(-50%,-50%);
      width:390px;height:390px;border-radius:50%;
      background:radial-gradient(circle,rgba(196,85,26,.28) 0%,rgba(232,168,62,.07) 50%,transparent 70%);
      pointer-events:none;z-index:9;
    }

    #main-plate{
      position:absolute;top:50%;left:50%;
      transform:translate(-50%,-50%);
      z-index:11;
    }

    /* Fork & knife: centred in scene by default; GSAP offsets them */
    #fork-wrap{
      position:absolute;top:50%;left:50%;
      transform:translate(-50%,-50%);
      z-index:12;width:68px;height:270px;
    }
    #knife-wrap{
      position:absolute;top:50%;left:50%;
      transform:translate(-50%,-50%);
      z-index:12;width:56px;height:270px;
    }

    /* ── CTA ── */
    #cta-block{
      position:absolute;bottom:6.5%;left:50%;transform:translateX(-50%);
      text-align:center;z-index:20;white-space:nowrap;
    }
    #cta-block h2{
      font-family:'Cormorant Garamond',serif;
      font-size:clamp(2.6rem,5.2vw,4.4rem);
      font-weight:300;font-style:italic;color:#FBF0DC;letter-spacing:-.01em;line-height:1.1;
    }
    #cta-block h2 em{color:#E8A83E;font-style:italic;}
    #cta-block p{
      font-size:.58rem;letter-spacing:.22em;color:rgba(251,240,220,.25);
      text-transform:uppercase;margin:.3rem 0;
    }
    #cta-btn{
      display:inline-flex;align-items:center;gap:.65rem;margin-top:.9rem;
      background:linear-gradient(135deg,#C4551A,#9E3A0E);
      color:#FBF0DC;padding:.84rem 2.4rem;border-radius:9999px;
      font-size:.88rem;font-weight:600;letter-spacing:.07em;border:none;cursor:pointer;
      box-shadow:0 8px 30px rgba(196,85,26,.55);transition:transform .2s,box-shadow .2s;
    }
    #cta-btn:hover{transform:translateY(-3px);box-shadow:0 12px 40px rgba(196,85,26,.65);}
    .btn-dot{width:7px;height:7px;border-radius:50%;background:#E8A83E;animation:pd 1.5s ease-in-out infinite;}
    @keyframes pd{0%,100%{opacity:1;transform:scale(1);}50%{opacity:.15;transform:scale(.4);}}

    /* hint */
    #hint{
      position:absolute;bottom:1.8%;left:50%;transform:translateX(-50%);
      font-size:.5rem;letter-spacing:.28em;color:rgba(251,240,220,.16);
      text-transform:uppercase;z-index:20;white-space:nowrap;pointer-events:none;
    }

    canvas#fx{position:absolute;inset:0;z-index:3;pointer-events:none;}
    #flash{position:fixed;inset:0;background:#FBF0DC;opacity:0;pointer-events:none;z-index:999;}

    /* ══════════════════════════════
       MOBILE  (≤ 680px)
    ══════════════════════════════ */
    @media(max-width:680px){
      #scene{width:360px;height:220px;transform:translate(-50%,-50%);}
      #plate-glow{width:240px;height:240px;}
      /* hide mid plates, shrink others */
      #p3,#p4{display:none;}
      #p1{width:115px;height:115px;left:0%;top:5%;}
      #p2{width:125px;height:125px;right:0%;top:4%;}
      #p5{width:118px;height:118px;left:0%;bottom:5%;}
      #p6{width:115px;height:115px;right:0%;bottom:6%;}
      #brand h1{font-size:2.2rem;}
      #cta-block h2{font-size:2rem;}
      #cta-btn{padding:.7rem 1.8rem;font-size:.8rem;}
    }

    /* SMALL mobile (≤ 400px) */
    @media(max-width:400px){
      #scene{width:280px;height:180px;}
      #p1,#p2,#p5,#p6{width:90px;height:90px;}
      #brand h1{font-size:1.8rem;}
    }
  </style>
</head>
<body>
<div id="stage">
<div id="table-bg"></div>
<canvas id="fx"></canvas>

<!-- ══ 6 food plates (carousel cascade from top) ══ -->
<div class="plate" id="p1">
  <img src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=400&h=400&fit=crop&auto=format" alt="salad"/>
</div>
<div class="plate" id="p2">
  <img src="https://images.unsplash.com/photo-1563379926898-05f4575a45d8?w=400&h=400&fit=crop&auto=format" alt="pasta"/>
</div>
<div class="plate" id="p3">
  <img src="https://images.unsplash.com/photo-1547592166-23ac45744acd?w=400&h=400&fit=crop&auto=format" alt="soup"/>
</div>
<div class="plate" id="p4">
  <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&h=400&fit=crop&auto=format" alt="bowl"/>
</div>
<div class="plate" id="p5">
  <img src="https://images.unsplash.com/photo-1558030006-450675393462?w=400&h=400&fit=crop&auto=format" alt="steak"/>
</div>
<div class="plate" id="p6">
  <img src="https://images.unsplash.com/photo-1551024506-0bccd828d307?w=400&h=400&fit=crop&auto=format" alt="dessert"/>
</div>

<!-- ══ Brand ══ -->
<div id="brand">
  <div class="eyebrow">Toronto &bull; Est. 2024</div>
  <h1>DineLocal</h1>
  <div class="orn">
    <div class="orn-line"></div><div class="orn-gem"></div>
    <span style="font-size:.48rem;letter-spacing:.18em;color:rgba(232,168,62,.35)">FARM TO TABLE</span>
    <div class="orn-gem"></div><div class="orn-line r"></div>
  </div>
  <div class="sub">A fine dining experience &middot; Queen Street West</div>
</div>

<!-- ══ Centre scene ══ -->
<div id="scene">
  <div id="plate-glow"></div>

  <!-- FORK — left of plate, GSAP will offset it -->
  <div id="fork-wrap">
    <svg id="fork-svg" viewBox="0 0 68 270" width="68" height="270"
         style="filter:drop-shadow(0 5px 16px rgba(0,0,0,.65))">
      <rect x="26" y="152" width="16" height="110" rx="8" fill="#C8A060"/>
      <rect x="26" y="152" width="7"  height="110" rx="3.5" fill="rgba(255,255,255,.1)"/>
      <path d="M26 152 Q23 126 26 108 L42 108 Q45 126 42 152Z" fill="#C8A060"/>
      <rect x="6"  y="20" width="9" height="90" rx="4.5" fill="#C8A060"/>
      <rect x="20" y="15" width="9" height="95" rx="4.5" fill="#C8A060"/>
      <rect x="34" y="15" width="9" height="95" rx="4.5" fill="#C8A060"/>
      <rect x="48" y="20" width="9" height="90" rx="4.5" fill="#C8A060"/>
      <path d="M6 108 Q34 119 62 108" fill="none" stroke="#C8A060" stroke-width="7" stroke-linecap="round"/>
      <line x1="10" y1="24" x2="10" y2="100" stroke="rgba(255,255,255,.16)" stroke-width="1.5"/>
    </svg>
  </div>

  <!-- MAIN PLATE -->
  <div id="main-plate">
    <svg id="plate-svg" viewBox="0 0 290 290" width="290" height="290"
         style="filter:drop-shadow(0 15px 48px rgba(0,0,0,.72)) drop-shadow(0 0 36px rgba(196,85,26,.2))">
      <ellipse cx="145" cy="282" rx="108" ry="9" fill="rgba(0,0,0,.32)"/>
      <circle cx="145" cy="143" r="136" fill="#F0E6D4" stroke="#D4B78C" stroke-width="2.5"/>
      <circle cx="145" cy="143" r="127" fill="none" stroke="#E5D3B8" stroke-width="1"/>
      <!-- Rim dots -->
      <circle cx="145" cy="9"   r="2.8" fill="rgba(196,85,26,.22)"/>
      <circle cx="222" cy="31"  r="2.8" fill="rgba(196,85,26,.22)"/>
      <circle cx="268" cy="108" r="2.8" fill="rgba(196,85,26,.22)"/>
      <circle cx="255" cy="198" r="2.8" fill="rgba(196,85,26,.22)"/>
      <circle cx="190" cy="262" r="2.8" fill="rgba(196,85,26,.22)"/>
      <circle cx="100" cy="262" r="2.8" fill="rgba(196,85,26,.22)"/>
      <circle cx="35"  cy="198" r="2.8" fill="rgba(196,85,26,.22)"/>
      <circle cx="22"  cy="108" r="2.8" fill="rgba(196,85,26,.22)"/>
      <circle cx="68"  cy="31"  r="2.8" fill="rgba(196,85,26,.22)"/>
      <!-- Inner well -->
      <circle cx="145" cy="143" r="108" fill="#FAF6EE"/>
      <!-- Steak -->
      <path d="M100 131 Q94 114 108 104 Q132 93 161 100 Q182 107 186 125 Q190 142 177 155 Q160 167 130 163 Q99 157 100 131Z" fill="#8B3A1A"/>
      <path d="M105 129 Q100 114 113 106 Q135 96 159 103 Q178 110 181 126 Q185 141 172 153 Q155 164 130 160 Q103 154 105 129Z" fill="#A04025"/>
      <path d="M110 116 Q135 109 162 116" fill="none" stroke="rgba(40,12,4,.44)" stroke-width="4" stroke-linecap="round"/>
      <path d="M110 126 Q135 119 162 126" fill="none" stroke="rgba(40,12,4,.40)" stroke-width="4" stroke-linecap="round"/>
      <path d="M112 136 Q135 129 159 136" fill="none" stroke="rgba(40,12,4,.36)" stroke-width="3" stroke-linecap="round"/>
      <!-- Asparagus -->
      <path d="M174 117 Q180 129 178 156" stroke="#4A8C30" stroke-width="5.5" fill="none" stroke-linecap="round"/>
      <path d="M182 121 Q188 133 186 158" stroke="#5A9C38" stroke-width="5" fill="none" stroke-linecap="round"/>
      <path d="M174 117 Q170 110 178 107 Q183 109 178 117Z" fill="#356820"/>
      <path d="M182 121 Q178 114 186 111 Q191 113 186 121Z" fill="#407828"/>
      <!-- Mash -->
      <path d="M99 149 Q96 161 108 166 Q122 172 136 165 Q144 162 142 150 Q126 146 99 149Z" fill="#F5E098"/>
      <ellipse cx="118" cy="157" rx="9" ry="4.5" fill="rgba(255,210,55,.48)"/>
      <!-- Sauce drizzle -->
      <path d="M97 129 Q95 140 99 149" fill="none" stroke="rgba(175,48,18,.42)" stroke-width="2.5" stroke-linecap="round"/>
      <!-- Microgreens -->
      <circle cx="160" cy="106" r="3.8" fill="rgba(80,160,50,.7)"/>
      <circle cx="168" cy="103" r="3"   fill="rgba(90,170,55,.65)"/>
      <circle cx="175" cy="105" r="2.3" fill="rgba(70,150,45,.72)"/>
      <!-- Monogram -->
      <circle cx="145" cy="143" r="43" fill="none" stroke="rgba(196,85,26,.1)" stroke-width="1.5"/>
      <text x="145" y="149" text-anchor="middle" font-family="'Cormorant Garamond',serif"
            font-size="18" font-weight="700" fill="rgba(196,85,26,.15)" letter-spacing="2">DL</text>
    </svg>
  </div>

  <!-- KNIFE — right of plate, GSAP will offset it -->
  <div id="knife-wrap">
    <svg id="knife-svg" viewBox="0 0 56 270" width="56" height="270"
         style="filter:drop-shadow(0 5px 16px rgba(0,0,0,.65))">
      <rect x="18" y="155" width="20" height="108" rx="10" fill="#C8A060"/>
      <rect x="26" y="155" width="7"  height="108" rx="3.5" fill="rgba(255,255,255,.09)"/>
      <circle cx="22" cy="170" r="2.5" fill="rgba(0,0,0,.13)"/>
      <circle cx="22" cy="184" r="2.5" fill="rgba(0,0,0,.13)"/>
      <circle cx="22" cy="198" r="2.5" fill="rgba(0,0,0,.13)"/>
      <rect x="14" y="148" width="28" height="10" rx="3" fill="#B09048"/>
      <path d="M28 18 Q50 65 46 148 L10 148 Q13 65 28 18Z" fill="#C8A060"/>
      <path d="M28 18 Q52 66 48 148" fill="none" stroke="rgba(255,255,255,.22)" stroke-width="1.8"/>
      <path d="M28 18 Q8 66 10 148"  fill="none" stroke="rgba(0,0,0,.07)"        stroke-width="1.2"/>
    </svg>
  </div>
</div><!-- /scene -->

<!-- ══ CTA ══ -->
<div id="cta-block">
  <h2>Ready to <em>Dine?</em></h2>
  <p>An unforgettable evening awaits</p>
  <button id="cta-btn" onclick="enterSite(event)">
    <span class="btn-dot"></span> Enter Restaurant
  </button>
</div>

<div id="hint">Click anywhere &nbsp;&bull;&nbsp; Tap to enter</div>

</div><!-- /stage -->
<div id="flash"></div>

<script>
// ── Particles ──
const cv = document.getElementById('fx'), cx = cv.getContext('2d');
function rsz(){ cv.width=innerWidth; cv.height=innerHeight; }
rsz(); window.addEventListener('resize', rsz);
const pts = Array.from({length:50},()=>({
  x:Math.random()*innerWidth,
  y:innerHeight*.35+Math.random()*innerHeight*.65,
  vx:(Math.random()-.5)*.32, vy:-Math.random()*.5-.08,
  r:Math.random()*1.7+.3, a:Math.random()*.25+.04, g:Math.random()>.42
}));
(function draw(){
  cx.clearRect(0,0,cv.width,cv.height);
  pts.forEach(p=>{
    p.x+=p.vx; p.y+=p.vy; p.a-=.0006;
    if(p.y<-8||p.a<=0){
      p.x=Math.random()*innerWidth;
      p.y=innerHeight*.5+Math.random()*innerHeight*.5;
      p.a=Math.random()*.25+.04;
    }
    cx.beginPath(); cx.arc(p.x,p.y,p.r,0,Math.PI*2);
    cx.fillStyle=p.g?'#E8A83E':'#FBF0DC';
    cx.globalAlpha=p.a; cx.fill();
  });
  cx.globalAlpha=1; requestAnimationFrame(draw);
})();

// ── Responsive values ──
const mob = window.innerWidth < 680;
const sm  = window.innerWidth < 400;

// Resize SVGs on mobile
if(mob){
  const ps = sm ? 175 : 210;
  document.getElementById('plate-svg').setAttribute('width', ps);
  document.getElementById('plate-svg').setAttribute('height', ps);
  const fw = sm ? 42 : 50, fh = sm ? 165 : 195;
  document.getElementById('fork-svg').setAttribute('width', fw);
  document.getElementById('fork-svg').setAttribute('height', fh);
  document.getElementById('fork-wrap').style.width = fw+'px';
  document.getElementById('fork-wrap').style.height = fh+'px';
  const kw = sm ? 35 : 42, kh = sm ? 165 : 195;
  document.getElementById('knife-svg').setAttribute('width', kw);
  document.getElementById('knife-svg').setAttribute('height', kh);
  document.getElementById('knife-wrap').style.width = kw+'px';
  document.getElementById('knife-wrap').style.height = kh+'px';
}

// Resting x offsets: fork left of plate, knife right of plate
const FORK_X  = mob ? (sm ? -120 : -155) : -230;
const KNIFE_X = mob ? (sm ? +105 : +135) : +205;

// ── GSAP initial states ──
gsap.set('#brand',     { y:-70, opacity:0 });
// All food plates start ABOVE viewport (carousel drop)
gsap.set(['#p1','#p2','#p3','#p4','#p5','#p6'], { y:-160, opacity:0, scale:.65 });
gsap.set('#plate-glow',{ opacity:0, scale:.35 });
// Plate drops from top with spin
gsap.set('#main-plate',{ y:-460, rotation:-360, opacity:0 });
// Fork off-screen left, knife off-screen right
gsap.set('#fork-wrap', { x: -600, opacity:0 });
gsap.set('#knife-wrap',{ x:  560, opacity:0 });
gsap.set('#cta-block', { y:55, opacity:0 });
gsap.set('#hint',      { opacity:0 });

// ── Intro timeline ──
const tl = gsap.timeline({ defaults:{ ease:'expo.out' } });

// 1. Brand
tl.to('#brand', { y:0, opacity:1, duration:1.0 })

// 2. Food plates cascade top-to-bottom like a carousel (pairs)
  .to('#p1', { y:0, opacity:1, scale:1, duration:.72, ease:'back.out(1.25)' }, '-=.25')
  .to('#p2', { y:0, opacity:1, scale:1, duration:.72, ease:'back.out(1.25)' }, '-=.64')
  .to('#p3', { y:0, opacity:1, scale:1, duration:.68, ease:'back.out(1.25)' }, '-=.55')
  .to('#p4', { y:0, opacity:1, scale:1, duration:.68, ease:'back.out(1.25)' }, '-=.60')
  .to('#p5', { y:0, opacity:1, scale:1, duration:.64, ease:'back.out(1.25)' }, '-=.52')
  .to('#p6', { y:0, opacity:1, scale:1, duration:.64, ease:'back.out(1.25)' }, '-=.56')

// 3. Plate glow warms up
  .to('#plate-glow', { opacity:1, scale:1, duration:.85, ease:'power2.out' }, '-=.4')

// 4. Plate ROLLS DOWN from top — 360° spin, bouncy land
  .to('#main-plate', { y:0, rotation:0, opacity:1, duration:1.3, ease:'power3.out' }, '-=.5')
  .to('#main-plate', { rotation: 10, duration:.2, ease:'power1.in' })
  .to('#main-plate', { rotation:  0, duration:.65, ease:'elastic.out(1,.38)' })

// 5. Fork slides in from LEFT → rests beside plate on left
  .to('#fork-wrap',  { x: FORK_X,  opacity:1, duration:1.0, ease:'back.out(1.6)' }, '-=.5')

// 6. Knife slides in from RIGHT → rests beside plate on right
  .to('#knife-wrap', { x: KNIFE_X, opacity:1, duration:1.0, ease:'back.out(1.6)' }, '-=.9')

// 7. CTA rises
  .to('#cta-block',  { y:0, opacity:1, duration:.85, ease:'power3.out' }, '-=.35')
  .to('#hint',       { opacity:1, duration:.6 }, '-=.15');


// ── Exit: fork + knife CROSS into X over plate ──
let leaving = false;
// Click anywhere
document.getElementById('stage').addEventListener('click', enterSite);

function enterSite(e){
  if(leaving) return;
  leaving = true;

  // How far each needs to travel to reach the plate centre (x=0 in GSAP space):
  // fork is at FORK_X (negative), needs to move by -FORK_X
  // knife is at KNIFE_X (positive), needs to move by -KNIFE_X
  const forkTarget  = 0;   // move to centre
  const knifeTarget = 0;   // move to centre

  gsap.timeline({
    onComplete(){
      gsap.to('#flash',{
        opacity:1, duration:.45,
        onComplete(){ window.location.href='index.php?enter=1'; }
      });
    }
  })
  // CTA + hint vanish
  .to(['#cta-block','#hint'], { opacity:0, y:-16, duration:.3 })

  // Food plates fly upward (reverse cascade — carousel effect)
  .to(['#p1','#p3','#p5'], { y:-180, opacity:0, scale:.55, duration:.42, stagger:.07, ease:'power2.in' }, '-=.05')
  .to(['#p2','#p4','#p6'], { y:-180, opacity:0, scale:.55, duration:.42, stagger:.07, ease:'power2.in' }, '-=.38')

  // Fork crosses to the right (+45°) — forms one arm of X
  .to('#fork-wrap',  { x: forkTarget,  rotation: 45,  duration:.6, ease:'power3.in' }, '-=.15')
  // Knife crosses to the left (−45°) — forms other arm of X
  .to('#knife-wrap', { x: knifeTarget, rotation:-45,  duration:.6, ease:'power3.in' }, '<')

  // Plate reacts on impact
  .to('#main-plate', { scale:1.1, duration:.14 }, '-=.1')
  .to('#main-plate', { scale:.88, duration:.1 })

  // Everything fades out
  .to(['#main-plate','#plate-glow','#fork-wrap','#knife-wrap','#brand'],
      { opacity:0, scale:.75, duration:.38, ease:'power2.in' })
}
</script>
</body>
</html>
