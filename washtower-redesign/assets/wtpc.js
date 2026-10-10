(function(){
  var DEMO=[ /* shown ONLY if the WooCommerce Store API cannot be reached; clearly labelled "Demo" */
    {name:'Twin Collection A2',price:'£1,299'},{name:'Galley Wall L5',price:'£1,499'},{name:'Slimline S4',price:'£999'},{name:'Utility Cabinet U3',price:'£1,399'}
  ];
  var BASKET='<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 8h14l-1.2 11.2a2 2 0 0 1-2 1.8H8.2a2 2 0 0 1-2-1.8z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/></svg>';

  function esc(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return{'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}
  function money(p,v){if(v==null||v==='')return '';var m=+p.currency_minor_unit||0,n=(+v)/Math.pow(10,m);return (p.currency_prefix||'')+n.toLocaleString('en-GB',{minimumFractionDigits:m,maximumFractionDigits:m})+(p.currency_suffix||'');}
  function decode(s){var t=document.createElement('textarea');t.innerHTML=s;return t.value;}

  function init(root){
    if(!root||root.getAttribute('data-wtpc-ready'))return;root.setAttribute('data-wtpc-ready','1');
    var reduce=window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var base=(window.wc_add_to_cart_params&&wc_add_to_cart_params.wc_ajax_url)?null:location.origin;
    var view=root.querySelector('.wtpc__view'),track=root.querySelector('.wtpc__track'),dots=root.querySelector('.wtpc__dots'),msg=root.querySelector('.wtpc__msg');
    var prev=root.querySelector('.wtpc__nav--prev'),next=root.querySelector('.wtpc__nav--next');
    var shop=root.getAttribute('data-shop')||'/shop/',limit=parseInt(root.getAttribute('data-limit'),10)||8;
    root.querySelector('.wtpc__all').setAttribute('href',shop);
    var idx=0,per=4,cards=[];
    root.querySelectorAll('.wtpc__line').forEach(function(l){l.remove();});
    var line=document.createElement('div');line.className='wtpc__line';line.setAttribute('aria-hidden','true');line.appendChild(document.createElement('span'));dots.parentNode.insertBefore(line,dots);
    line.addEventListener('click',function(e){var r=line.getBoundingClientRect();idx=Math.round((e.clientX-r.left)/r.width*maxIdx());render();});

    /* ---------- carousel ---------- */
    function perView(){var w=window.innerWidth;return w<=767?1.15:(w<=1024?2:4);}
    function maxIdx(){return Math.max(0,Math.ceil(cards.length-per));}
    function stepPx(){if(!cards[0])return 0;var g=parseFloat(getComputedStyle(track).columnGap||getComputedStyle(track).gap)||16;return cards[0].getBoundingClientRect().width+g;}
    function render(anim){
      var m=maxIdx();idx=Math.max(0,Math.min(idx,m));
      var x=idx*stepPx();if(idx===m&&per%1){x=Math.max(0,track.scrollWidth-view.clientWidth);}
      if(anim===false){track.style.transition='none';}
      track.style.transform='translate3d('+(-x)+'px,0,0)';
      if(anim===false){track.offsetHeight;track.style.transition='';}
      prev.disabled=idx<=0;next.disabled=idx>=m;
      Array.prototype.forEach.call(dots.children,function(d,i){d.setAttribute('aria-selected',i===idx?'true':'false');d.tabIndex=i===idx?0:-1;});
      if(line){var m2=maxIdx(),f=cards.length?Math.min(1,per/cards.length):1,pos=m2?idx/m2:0;line.firstChild.style.width=(f*100)+'%';line.firstChild.style.transform='translateX('+(pos*(1/f-1)*100)+'%)';line.style.display=m2>0?'':'none';}
      cards.forEach(function(c,i){var vis=i>=idx&&i<idx+Math.ceil(per);c.setAttribute('aria-hidden',vis?'false':'true');c.querySelectorAll('a,button').forEach(function(el){el.tabIndex=vis?0:-1;});});
    }
    function buildDots(){
      dots.innerHTML='';var m=maxIdx();
      for(var i=0;i<=m;i++){(function(i){var b=document.createElement('button');b.type='button';b.setAttribute('role','tab');b.setAttribute('aria-label','Go to slide '+(i+1));b.addEventListener('click',function(){idx=i;render();});dots.appendChild(b);})(i);}
      dots.style.display=m>0?'':'none';
    }
    function layout(){per=perView();root.style.setProperty('--per',per);buildDots();render(false);
      var img=root.querySelector('.wtpc__img');if(img){root.style.setProperty('--navtop',(img.offsetTop+img.offsetHeight/2+6)+'px');}}
    prev.addEventListener('click',function(){idx--;render();});
    next.addEventListener('click',function(){idx++;render();});
    view.addEventListener('keydown',function(e){if(e.key==='ArrowRight'){idx++;render();e.preventDefault();}if(e.key==='ArrowLeft'){idx--;render();e.preventDefault();}});
    var rt;window.addEventListener('resize',function(){clearTimeout(rt);rt=setTimeout(layout,120);});

    /* drag + swipe (pointer events cover mouse and touch) */
    var sx=0,sy=0,dx=0,start=0,drag=false,lock=null,moved=false;
    view.addEventListener('pointerdown',function(e){if(e.button>0||!cards.length)return;sx=e.clientX;sy=e.clientY;dx=0;lock=null;moved=false;drag=true;var t=getComputedStyle(track).transform;start=t&&t!=='none'?parseFloat(t.split(',')[4]):0;});
    window.addEventListener('pointermove',function(e){if(!drag)return;var mx=e.clientX-sx,my=e.clientY-sy;if(lock===null&&(Math.abs(mx)>6||Math.abs(my)>6)){lock=Math.abs(mx)>Math.abs(my)?'x':'y';if(lock==='x'){root.classList.add('is-drag');try{view.setPointerCapture(e.pointerId);}catch(_){}}}
      if(lock!=='x')return;moved=true;dx=mx;var m=maxIdx(),edge=(idx<=0&&dx>0)||(idx>=m&&dx<0);track.style.transform='translate3d('+(start+(edge?dx*.35:dx))+'px,0,0)';});
    function end(){if(!drag)return;drag=false;root.classList.remove('is-drag');if(lock==='x'){var s=stepPx(),n=Math.round(Math.abs(dx)/s)||(Math.abs(dx)>40?1:0);idx+=dx<0?n:-n;}render();}
    window.addEventListener('pointerup',end);window.addEventListener('pointercancel',end);
    view.addEventListener('click',function(e){if(moved){e.preventDefault();e.stopPropagation();moved=false;}},true);
    view.addEventListener('dragstart',function(e){e.preventDefault();});

    /* ---------- products ---------- */
    function card(p,i){
      var img=(p.images&&p.images[0])||{},pr=p.prices||{},onSale=pr.sale_price&&pr.regular_price&&pr.sale_price!==pr.regular_price;
      var simple=p.type==='simple'&&p.is_purchasable&&p.is_in_stock!==false;
      var rating=parseFloat(p.average_rating)||0,count=parseInt(p.review_count,10)||0;
      var li=document.createElement('li');li.className='wtpc__card';li.style.setProperty('--i',i);
      li.innerHTML='<a class="wtpc__img" href="'+esc(p.permalink)+'" tabindex="-1" aria-hidden="true">'+(img.src?'<img src="'+esc(img.src)+'"'+(img.srcset?' srcset="'+esc(img.srcset)+'" sizes="(max-width:767px) 85vw,(max-width:1024px) 48vw,300px"':'')+' alt="'+esc(img.alt||decode(p.name))+'" loading="lazy" decoding="async">':'')+'</a>'+
        '<div class="wtpc__body"><h3 class="wtpc__name"><a href="'+esc(p.permalink)+'">'+esc(decode(p.name))+'</a></h3>'+
        '<div class="wtpc__rate">'+(count?'<span class="wtpc__stars" aria-label="Rated '+rating.toFixed(1)+' out of 5">&#9733;&#9733;&#9733;&#9733;&#9733;<span style="width:'+(rating/5*100)+'%">&#9733;&#9733;&#9733;&#9733;&#9733;</span></span><span>'+rating.toFixed(1)+' ('+count+')</span>':'')+'</div>'+
        '<div class="wtpc__price"><span class="wtpc__now">'+esc(money(pr,pr.price))+'</span>'+(onSale?'<span class="wtpc__was"><span class="screen-reader-text">Was </span>'+esc(money(pr,pr.regular_price))+'</span>':'')+'</div>'+
        (simple?'<button type="button" class="wtpc__btn" data-id="'+p.id+'" data-name="'+esc(decode(p.name))+'"><span>'+BASKET+'Add to basket</span></button>'
               :'<a class="wtpc__btn" href="'+esc(p.permalink)+'"><span>'+BASKET+'Choose options</span></a>')+'</div>';
      return li;
    }
    function demoCard(d,i){var li=document.createElement('li');li.className='wtpc__card';li.style.setProperty('--i',i);
      li.innerHTML='<div class="wtpc__img"></div><div class="wtpc__body"><h3 class="wtpc__name">'+esc(d.name)+'</h3><div class="wtpc__rate"></div><div class="wtpc__price"><span class="wtpc__now">'+esc(d.price)+'</span></div><button type="button" class="wtpc__btn" data-demo="1"><span>'+BASKET+'Add to basket</span></button></div>';return li;}
    function mount(list,isDemo){
      track.innerHTML='';list.forEach(function(p,i){track.appendChild(isDemo?demoCard(p,i):card(p,i));});
      cards=Array.prototype.slice.call(track.children);
      if(isDemo){root.querySelector('.wtpc__title').insertAdjacentHTML('beforeend','<span class="wtpc__demo">Demo data</span>');}
      layout();
    }
    if(root.getAttribute('data-ssr')){cards=Array.prototype.slice.call(track.children);cards.forEach(function(c,i){c.style.setProperty('--i',i);});layout();}
    else{
    var api=location.origin+'/wp-json/wc/store/v1/products';
    function get(q){return fetch(api+q,{credentials:'same-origin',headers:{Accept:'application/json'}}).then(function(r){if(!r.ok)throw new Error(r.status);return r.json();});}
    get('?featured=true&orderby=date&order=asc&per_page='+limit).then(function(f){
      if(f.length>=limit)return f;
      return get('?per_page='+(limit+f.length)+'&orderby=date&order=desc').then(function(all){var ids=f.map(function(p){return p.id;});return f.concat(all.filter(function(p){return ids.indexOf(p.id)<0;})).slice(0,limit);});
    }).then(function(list){if(!list.length)throw new Error('empty');mount(list,false);})
      .catch(function(){mount(DEMO,true);});

    }
    /* ---------- add to basket (WooCommerce AJAX) ---------- */
    function say(html,err){msg.innerHTML=html;msg.classList.toggle('is-err',!!err);clearTimeout(say.t);say.t=setTimeout(function(){msg.innerHTML='';},6000);}
    function applyFragments(fr){if(!fr)return;Object.keys(fr).forEach(function(sel){document.querySelectorAll(sel).forEach(function(el){var t=document.createElement('div');t.innerHTML=fr[sel];if(t.firstElementChild)el.replaceWith(t.firstElementChild);});});}
    track.addEventListener('click',function(e){
      var b=e.target.closest('button.wtpc__btn');if(!b||!track.contains(b))return;
      if(b.getAttribute('data-demo')){say('Demo mode: WooCommerce is not connected, nothing was added.',true);return;}
      var label=b.innerHTML;b.classList.add('is-loading');b.setAttribute('data-label','Adding…');b.setAttribute('aria-busy','true');
      var url=(window.wc_add_to_cart_params&&wc_add_to_cart_params.wc_ajax_url)?wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%','add_to_cart'):location.origin+'/?wc-ajax=add_to_cart';
      var fd=new FormData();fd.append('product_id',b.getAttribute('data-id'));fd.append('quantity','1');
      fetch(url,{method:'POST',body:fd,credentials:'same-origin'}).then(function(r){return r.json();}).then(function(res){
        if(!res||res.error||!res.fragments){throw res&&res.product_url?{url:res.product_url}:new Error('failed');}
        applyFragments(res.fragments);
        if(window.jQuery){jQuery(document.body).trigger('wc_fragment_refresh').trigger('added_to_cart',[res.fragments,res.cart_hash,jQuery()]);}
        root.querySelectorAll('.added_to_cart').forEach(function(a){a.remove();});
        document.querySelectorAll('.elementor-menu-cart__toggle_button,.wt-cart').forEach(function(c){c.classList.remove('wtpc-bump');void c.offsetWidth;c.classList.add('wtpc-bump');});
        b.classList.remove('is-loading');b.classList.add('is-done');b.innerHTML='<span><svg class="wtpc__tick" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M8 12.5l2.6 2.6L16 9.5"/></svg>Added to basket</span>';
        var cart=(window.wc_add_to_cart_params&&wc_add_to_cart_params.cart_url)||(location.origin+'/cart/');
        say(esc(b.getAttribute('data-name'))+' was added to your basket. <a href="'+esc(cart)+'">View basket</a>');
      }).catch(function(err){
        b.classList.remove('is-loading');b.classList.add('is-error');b.innerHTML='<span>Try again</span>';
        say(err&&err.url?'This product needs options. <a href="'+esc(err.url)+'">Choose options</a>':'Sorry, it could not be added to your basket. Please try again.',true);
      }).then(function(){setTimeout(function(){b.classList.remove('is-done','is-error');b.removeAttribute('aria-busy');b.innerHTML=label;},2600);});
    });

    /* ---------- scroll reveal (once) ---------- */
    var inEditor=document.body.classList.contains('elementor-editor-active')||/[?&]elementor-preview=/.test(location.search);
    if(!reduce&&!inEditor&&'IntersectionObserver' in window){
      root.classList.add('wtpc--anim');
      var io=new IntersectionObserver(function(en){en.forEach(function(x){if(x.isIntersecting){root.classList.add('wtpc--in');io.disconnect();}});},{threshold:.15});
      io.observe(root);
    }
  }
  window.wtpcInit=init;
  function all(){document.querySelectorAll('.wtpc').forEach(init);}
  if(document.readyState!=='loading')all();else document.addEventListener('DOMContentLoaded',all);
  window.addEventListener('elementor/frontend/init',function(){if(window.elementorFrontend&&elementorFrontend.hooks){elementorFrontend.hooks.addAction('frontend/element_ready/wt-product-carousel.default',function($el){var r=$el[0].querySelector('.wtpc');if(r)init(r);});}});
})();
