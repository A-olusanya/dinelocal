/* ==========================================
   DINELOCAL · animations.js
   GSAP ScrollTrigger — Bootstrap 5 version
   Showcase panels reveal on scroll
========================================== */
gsap.registerPlugin(ScrollTrigger);

window.addEventListener('DOMContentLoaded', () => {

  /* ── 1. HERO PARALLAX ── */
  gsap.to('#hero-img', { scale:1, yPercent:4, ease:'none', scrollTrigger:{ trigger:'#hero', start:'top top', end:'bottom top', scrub:1 }});
  gsap.to('.hero-body', { yPercent:-18, opacity:0, ease:'none', scrollTrigger:{ trigger:'#hero', start:'top top', end:'55% top', scrub:1 }});
  gsap.to('.scroll-cue', { opacity:0, ease:'none', scrollTrigger:{ trigger:'#hero', start:'top top', end:'18% top', scrub:1 }});

  /* ── 2. SHOWCASE PANELS — stagger reveal ── */
  ['#sp1','#sp2','#sp3'].forEach((id, i) => {
    const el = document.querySelector(id);
    if (!el) return;
    const photo   = el.querySelector('.sp-photo');
    const content = el.querySelector('.sp-inner');

    // Photo zooms out slightly from off-centre
    gsap.fromTo(photo,
      { scale: 1.08 },
      { scale: 1, ease:'none',
        scrollTrigger:{ trigger:el, start:'top bottom', end:'bottom top', scrub:1 }
      }
    );

    // Content slides in
    gsap.fromTo(content,
      { x: i % 2 === 0 ? 60 : -60, opacity: 0 },
      { x: 0, opacity: 1, duration: .85, ease:'power3.out',
        scrollTrigger:{ trigger:el, start:'top 72%', toggleActions:'play none none reverse' }
      }
    );

    // Number count-up effect
    const numEl = el.querySelector('.sp-num');
    if (numEl) {
      gsap.from(numEl, { opacity:0, y:30, duration:.6, ease:'power2.out',
        scrollTrigger:{ trigger:el, start:'top 72%', toggleActions:'play none none reverse' }
      });
    }
  });

  /* ── 3. STATS STRIP ── */
  gsap.fromTo('.stat-item',
    { y: 30, opacity: 0 },
    { y:0, opacity:1, duration:.65, stagger:.1, ease:'power2.out',
      scrollTrigger:{ trigger:'.stats-strip', start:'top 82%', toggleActions:'play none none reverse' }
    }
  );

  /* ── 4. KINETIC TEXT ── */
  gsap.timeline({ scrollTrigger:{ trigger:'#kinetic', start:'top 78%', toggleActions:'play none none reverse' }})
    .to('#k1',{x:0,opacity:1,duration:.7,ease:'power3.out'})
    .to('#k2',{x:0,opacity:1,duration:.7,ease:'power3.out'},'-=.46')
    .to('#k3',{x:0,opacity:1,duration:.7,ease:'power3.out'},'-=.46');

  /* ── 5. MENU CARDS ── */
  gsap.to('.mcard', { y:0, opacity:1, duration:.76, stagger:.13, ease:'power3.out',
    scrollTrigger:{ trigger:'#menu-sec', start:'top 78%', toggleActions:'play none none reverse' }
  });

  /* ── 6. RESERVATION ── */
  gsap.fromTo('.res-photo-col', {x:-60,opacity:0}, {x:0,opacity:1,duration:.95,ease:'power3.out', scrollTrigger:{trigger:'#res-sec',start:'top 82%',toggleActions:'play none none reverse'}});
  gsap.fromTo('#res-sec .col-lg-6:last-child', {x:60,opacity:0}, {x:0,opacity:1,duration:.95,ease:'power3.out', scrollTrigger:{trigger:'#res-sec',start:'top 82%',toggleActions:'play none none reverse'}});

  /* ── 7. FOOTER ── */
  gsap.fromTo('#footer .row > div', {y:35,opacity:0}, {y:0,opacity:1,duration:.65,stagger:.08,ease:'power2.out', scrollTrigger:{trigger:'footer',start:'top 88%'}});

  /* ── 8. NAV HIDE/SHOW ── */
  let lastY = 0;
  window.addEventListener('scroll', () => {
    const nav = document.getElementById('main-nav');
    if (!nav) return;
    const sy = window.scrollY;
    if (sy > 80 && sy > lastY) nav.classList.add('nav-hidden');
    else nav.classList.remove('nav-hidden');
    lastY = sy;
  }, {passive:true});

});