<style>
    .modal-title{color: #e2ae76}
</style>

{{-- resources/views/frontend/labor-cost/quick-help.blade.php --}}
<section id="lc-banner" class="mini-banner reveal" aria-label="Guida rapida costi lavoro">
    <div class="mini-banner-head d-flex align-items-center mb-2">
        <span class="banner-kicker">Nuovo</span>
        <h6 class="banner-title ms-2">Capire i Costi del Lavoro</h6>
        <button type="button" class="ms-auto banner-cta" data-bs-toggle="modal" data-bs-target="#lcAll">
            Vedi tutti <i class="bi bi-arrow-right-short"></i>
        </button>
    </div>

    <button class="scroll-arrow left" type="button" aria-label="Scorri a sinistra"><i class="bi bi-chevron-left"></i></button>
    <button class="scroll-arrow right" type="button" aria-label="Scorri a destra"><i class="bi bi-chevron-right"></i></button>

    <div class="scroller" tabindex="0">
       

        <button type="button" class="chip" data-bs-toggle="modal" data-bs-target="#lcBuckets">
            <span class="icon-badge"><i class="bi bi-diagram-3"></i></span>Voci: Condivise vs Reparto<span class="shine"></span>
        </button>

        <button type="button" class="chip" data-bs-toggle="modal" data-bs-target="#lcIncidenza">
            <span class="icon-badge"><i class="bi bi-percent"></i></span>Incidenza reparto (%)<span class="shine"></span>
        </button>

        <button type="button" class="chip chip--hot" data-bs-toggle="modal" data-bs-target="#lcBEP">
            <span class="icon-badge"><i class="bi bi-graph-up-arrow"></i></span>BEP: mensile e giornaliero<span class="ping"></span><span class="shine"></span>
        </button>

        <button type="button" class="chip" data-bs-toggle="modal" data-bs-target="#lcConsigli">
            <span class="icon-badge"><i class="bi bi-lightbulb"></i></span>Consigli pratici<span class="shine"></span>
        </button>

        <button type="button" class="chip" data-bs-toggle="modal" data-bs-target="#lcEsempio">
            <span class="icon-badge"><i class="bi bi-calculator"></i></span>Esempio passo-passo<span class="shine"></span>
        </button>
    </div>
</section>

{{-- ============ Styles (scoped for banner + modals) ============ --}}
<style>
/* palette */
:root { --primary:#041930; --accent:#e2ae76; }
.mini-banner{--br:18px; position:relative; margin-top:1.25rem; padding:14px 16px 12px; color:#fff; border-radius:var(--br);
  background: radial-gradient(120% 140% at 0% 0%, #0b2b53 0%, var(--primary) 42%, #0a264a 100%);
  border:1px solid rgba(226,174,118,.35); box-shadow:0 12px 32px rgba(4,25,48,.30), inset 0 1px 0 rgba(255,255,255,.04); overflow:hidden;}
.mini-banner::before{content:"";position:absolute;inset:-1px;padding:1.25px;border-radius:inherit;
  background:conic-gradient(from 0deg, rgba(226,174,118,.85) 0deg, transparent 90deg, rgba(226,174,118,.65) 180deg, transparent 270deg, rgba(226,174,118,.85) 360deg);
  -webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);-webkit-mask-composite:xor;mask-composite:exclude;animation:spin 10s linear infinite;pointer-events:none;}
.mini-banner::after{content:"";position:absolute;inset:0;background:
  radial-gradient(600px 200px at 90% -50%, rgba(226,174,118,.25), transparent 60%),
  radial-gradient(180px 180px at 10% 120%, rgba(255,255,255,.12), transparent 55%),
  radial-gradient(2px 2px at 40% 60%, rgba(255,255,255,.35), transparent 40%),
  radial-gradient(2px 2px at 70% 35%, rgba(255,255,255,.25), transparent 40%);mix-blend-mode:screen;animation:floatSparkles 8s ease-in-out infinite alternate;pointer-events:none;}
@keyframes spin{to{transform:rotate(360deg)}}
@keyframes floatSparkles{0%{transform:translateY(0)}100%{transform:translateY(-6px)}}
@keyframes shineSweep{0%{transform:translateX(-120%) skewX(-12deg)}100%{transform:translateX(120%) skewX(-12deg)}}
@keyframes ping{0%{transform:scale(.9);opacity:.9}70%{transform:scale(1.15);opacity:.15}100%{transform:scale(1.35);opacity:0}}
.banner-kicker{font-size:.70rem;letter-spacing:.14em;text-transform:uppercase;color:var(--accent);padding:2px 8px;border-radius:999px;border:1px solid rgba(226,174,118,.5);background:rgba(226,174,118,.08);}
.banner-title{margin:0;font-weight:800;letter-spacing:.2px;background:linear-gradient(90deg,#ffe0bf,var(--accent));-webkit-background-clip:text;background-clip:text;color:transparent;text-shadow:0 0 .0px transparent, 0 8px 28px rgba(226,174,118,.18);}
.banner-cta{display:inline-flex;align-items:center;gap:2px;font-weight:600;color:#fff;text-decoration:none;padding:6px 10px;border-radius:10px;background:rgba(255,255,255,.08);border:1px solid rgba(226,174,118,.35);transition:transform .15s ease, background .15s ease, border-color .15s ease;}
.banner-cta:hover{transform:translateY(-1px);background:rgba(255,255,255,.14);border-color:rgba(226,174,118,.6)}
.scroller{position:relative;z-index:2;display:flex;gap:.6rem;overflow:auto;scroll-snap-type:x mandatory;padding:6px 44px 8px 44px;}
.scroller:focus{outline:none;box-shadow:0 0 0 .25rem rgba(226,174,118,.25)}
.scroll-arrow{position:absolute;top:50%;transform:translateY(-50%);width:38px;height:38px;border-radius:12px;background:rgba(255,255,255,.12);color:#fff;border:1px solid rgba(226,174,118,.35);display:grid;place-items:center;z-index:3;cursor:pointer;backdrop-filter:blur(6px);transition:transform .15s ease, background .15s ease, border-color .15s ease, opacity .15s ease;}
.scroll-arrow:hover{transform:translateY(-50%) scale(1.06);background:rgba(255,255,255,.18);border-color:rgba(226,174,118,.6)}
.scroll-arrow.left{left:8px} .scroll-arrow.right{right:8px}
.chip{position:relative;scroll-snap-align:center;white-space:nowrap;display:inline-flex;align-items:center;gap:.55rem;padding:10px 14px;background:rgba(255,255,255,.10);border:1px solid rgba(226,174,118,.38);color:#fff;border-radius:999px;transition:transform .15s ease, background .15s ease, border-color .15s ease, box-shadow .15s ease;will-change:transform;box-shadow:0 6px 16px rgba(4,25,48,.18);text-decoration:none;}
.chip:hover{transform:translateY(-2px);background:rgba(255,255,255,.16);border-color:rgba(226,174,118,.7);box-shadow:0 10px 22px rgba(4,25,48,.22);}
.icon-badge{width:24px;height:24px;display:grid;place-items:center;border-radius:999px;background:rgba(226,174,118,.20);border:1px solid rgba(226,174,118,.45);color:var(--accent);flex:0 0 auto;}
.chip--cta{background:linear-gradient(90deg, rgba(226,174,118,.95), #ffd7ac);color:var(--primary);border-color:rgba(226,174,118,1);box-shadow:0 10px 24px rgba(226,174,118,.45);}
.chip--cta .icon-badge{background:rgba(255,255,255,.7);border-color:rgba(255,255,255,.9);color:var(--primary)}
.chip--hot .ping{position:absolute;inset:0;border-radius:inherit;pointer-events:none;border:2px solid rgba(226,174,118,.7);filter:drop-shadow(0 0 6px rgba(226,174,118,.35));animation:ping 2.2s cubic-bezier(0,0,0.2,1) infinite;}
.chip .shine{content:"";position:absolute;inset:auto 0 0 0;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,.45),transparent);mix-blend-mode:screen;opacity:.0;pointer-events:none;transform:translateX(-120%) skewX(-12deg);}
.chip:hover .shine{animation:shineSweep .9s ease forwards}
/* Modals */
.modal-glass{background:rgba(255,255,255,.9); backdrop-filter: blur(8px); border:1px solid rgba(226,174,118,.35);}
.modal-glass .modal-header{background:linear-gradient(135deg,#0b2b53,#041930); color:#ffe5c7; border:0;}
.modal-glass .btn-close{filter:invert(1) grayscale(1) brightness(200%); opacity:.7}
.formula{background:#fff7ef;border:1px dashed rgba(226,174,118,.6);border-radius:12px;padding:12px 14px;font-weight:600;}
.callout{background:#f8fafc;border-left:4px solid #e2ae76;border-radius:10px;padding:10px 12px;}
</style>

{{-- ============ Modals ============ --}}

{{-- Panoramica --}}
<div class="modal fade" id="lcAll" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content modal-glass rounded-4 shadow-lg">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Costi del lavoro — panoramica per tutti</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
      </div>
      <div class="modal-body">
        <p class="mb-2">Questa pagina serve a calcolare due tariffe: <strong>€/min interno</strong> (produzione in sede) e <strong>€/min esterno</strong> (fornitura/terzisti).</p>
        <ol class="mb-0">
          <li><strong>Compila le voci di reparto</strong> (es. pasticceri, ingredienti, imballo).</li>
          <li><strong>Compila le voci condivise</strong> (affitto, elettricità, tasse…).</li>
          <li>Imposta <strong>giorni/ore di apertura</strong> e (se serve) <strong>incidenza reparto %</strong>.</li>
          <li>Il sistema calcola i due <em>€/min</em> e il <strong>BEP</strong> (mensile/giornaliero).</li>
        </ol>
      </div>
      <div class="modal-footer">
        <button class="btn btn-accent" data-bs-dismiss="modal">Capito!</button>
      </div>
    </div>
  </div>
</div>

{{-- 1) €/min: come funziona --}}
<div class="modal fade" id="lcHowWorks" tabindex="-1" aria-hidden="true" aria-labelledby="lcHowWorksLabel">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content modal-glass rounded-4 shadow-lg">
      <div class="modal-header">
        <h5 id="lcHowWorksLabel" class="modal-title fw-bold">Come si calcola il costo minuto (€/min)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
      </div>
      <div class="modal-body">
        <div class="formula mb-3">
          Minuti totali/mese = <strong>Giorni apertura</strong> × <strong>Ore/gg</strong> × 60<br>
          <em>Divisione per</em> <strong>n. pasticceri</strong> per avere il costo per minuto per addetto.
        </div>

        <p class="mb-1"><strong>Interno (shop_cost_per_min)</strong> — esclude costi NON produttivi esterni:</p>
        <div class="formula mb-3">
          Base interno = (Totale voci <u>abilitate</u> − Ingredienti − Noleggio furgone − Stipendi fornitura esterna) ÷ (Minuti totali × Pasticceri)
        </div>

        <p class="mb-1"><strong>Esterno (external_cost_per_min)</strong> — esclude ciò che non pesa su fornitori:</p>
        <div class="formula mb-3">
          Base esterno = (Totale voci <u>abilitate</u> − Ingredienti − Addetti vendita<span class="text-muted">*</span>) ÷ (Minuti totali × Pasticceri)
        </div>

        <div class="callout mb-2">
          <strong>Fattore correttivo 4/3</strong>: per coprire tempi morti, sicurezza, ferie, imprevisti, l’app applica
          un fattore ≈ <code>× 4/3</code> alla “base”. È lo stesso che vedi nello script della pagina.
        </div>
        <small class="text-muted d-block">* Se stai lavorando su un reparto con <em>Incidenza%</em>, l’app usa la quota degli addetti vendita proporzionata a quell’incidenza.</small>
      </div>
      <div class="modal-footer">
        <button class="btn btn-accent" data-bs-dismiss="modal">Tutto chiaro</button>
      </div>
    </div>
  </div>
</div>

{{-- 2) Voci Condivise vs Reparto --}}
<div class="modal fade" id="lcBuckets" tabindex="-1" aria-hidden="true" aria-labelledby="lcBucketsLabel">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content modal-glass rounded-4 shadow-lg">
      <div class="modal-header">
        <h5 id="lcBucketsLabel" class="modal-title fw-bold">Voci: Condivise e di Reparto — cosa entra nel calcolo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
      </div>
      <div class="modal-body">
        <ul class="mb-3">
          <li><strong>Condivise</strong>: elettricità, affitto/mutuo, proprietario, tasse, noleggio furgone, addetti vendita.
              In modalità “reparto”, queste sono <em>bloccate</em> e applicate in quota (vedi Incidenza%).</li>
          <li><strong>Reparto</strong>: ingredienti, imballaggio, pasticceri, altri stipendi, driver fornitura esterna, altre categorie.
              Sono modificabili per singolo reparto.</li>
        </ul>
        <div class="callout mb-0">
          Il totale “abilitato” è la base da cui sottraiamo alcune voci (vedi scheda precedente) per ottenere le due tariffe €/min coerenti.
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-accent" data-bs-dismiss="modal">Ok, grazie</button>
      </div>
    </div>
  </div>
</div>

{{-- 3) Incidenza reparto (%) --}}
<div class="modal fade" id="lcIncidenza" tabindex="-1" aria-hidden="true" aria-labelledby="lcIncidenzaLabel">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content modal-glass rounded-4 shadow-lg">
      <div class="modal-header">
        <h5 id="lcIncidenzaLabel" class="modal-title fw-bold">Incidenza reparto (%) — come funziona la ripartizione</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
      </div>
      <div class="modal-body">
        <p>Quando selezioni un <strong>reparto</strong>, le voci condivise diventano di sola lettura. Viene applicata la quota:</p>
        <div class="formula mb-3">
          Quota condivise per reparto = Valore condiviso × (Incidenza% ÷ 100)
        </div>
        <p class="mb-0">Esempio: se il reparto “Pasticceria” ha Incidenza 40%, dell’affitto da 2.000€ vengono imputati <strong>800€</strong> (2.000 × 0,40).</p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-accent" data-bs-dismiss="modal">Ho capito</button>
      </div>
    </div>
  </div>
</div>

{{-- 4) BEP --}}
<div class="modal fade" id="lcBEP" tabindex="-1" aria-hidden="true" aria-labelledby="lcBEPLabel">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content modal-glass rounded-4 shadow-lg">
      <div class="modal-header">
        <h5 id="lcBEPLabel" class="modal-title fw-bold">Punto di Pareggio (BEP)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
      </div>
      <div class="modal-body">
        <p class="mb-2">Il BEP mostrato in pagina è un riferimento di <strong>fatturato minimo</strong> necessario a coprire i costi fissi/variabili indicati.</p>
        <div class="formula mb-3">
          BEP <em>mensile</em> ≈ Somma voci (condivise + reparto) del mese<br>
          BEP <em>giornaliero</em> = BEP mensile ÷ Giorni apertura
        </div>
        <div class="callout mb-0">
          Usa il BEP come bussola: se il fatturato medio giornaliero è sotto il BEP giornaliero, rivedi volumi, prezzi o costi.
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-accent" data-bs-dismiss="modal">Perfetto</button>
      </div>
    </div>
  </div>
</div>

{{-- 5) Consigli pratici --}}
<div class="modal fade" id="lcConsigli" tabindex="-1" aria-hidden="true" aria-labelledby="lcConsigliLabel">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content modal-glass rounded-4 shadow-lg">
      <div class="modal-header">
        <h5 id="lcConsigliLabel" class="modal-title fw-bold">Consigli pratici per tariffe sane</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
      </div>
      <div class="modal-body">
        <ul class="mb-0">
          <li><strong>Batching</strong>: aumentare la dose riduce €/pz perché diluisce i minuti.</li>
          <li><strong>Dati realistici</strong>: giorni/ore troppo ottimisti falsano il costo minuto.</li>
          <li><strong>Reparti separati</strong>: usa Incidenza% solo se davvero condividi risorse.</li>
          <li><strong>Controllo trimestrale</strong>: aggiorna prezzi energia, affitti, stipendi.</li>
        </ul>
      </div>
      <div class="modal-footer">
        <button class="btn btn-accent" data-bs-dismiss="modal">Benissimo</button>
      </div>
    </div>
  </div>
</div>

{{-- 6) Esempio pratico --}}
<div class="modal fade" id="lcEsempio" tabindex="-1" aria-hidden="true" aria-labelledby="lcEsempioLabel">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content modal-glass rounded-4 shadow-lg">
      <div class="modal-header">
        <h5 id="lcEsempioLabel" class="modal-title fw-bold">Esempio passo-passo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
      </div>
      <div class="modal-body">
        <ol class="mb-3">
          <li>Apertura: <strong>22 gg</strong>, <strong>8 ore/gg</strong> → 22×8×60 = <strong>10.560 min</strong>.</li>
          <li>Pasticceri: <strong>2</strong>.</li>
          <li>Totale voci abilitate (dopo incidenza): <strong>€ 18.000</strong>.</li>
        </ol>
        <div class="formula mb-2">
          Base interno = (€18.000 − ingredienti − furgone − driver esterno) ÷ (10.560 × 2)
        </div>
        <div class="formula mb-3">
          Base esterno = (€18.000 − ingredienti − quota addetti vendita) ÷ (10.560 × 2)
        </div>
        <div class="callout">Tariffa finale ≈ Base × <strong>4/3</strong>. Confronta il risultato con quello mostrato nei campi “€/min”.</div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-accent" data-bs-dismiss="modal">Chiaro</button>
      </div>
    </div>
  </div>
</div>

{{-- ============ Tiny script: arrows only for this banner ============ --}}
<script>
(function(){
  const wrap = document.getElementById('lc-banner'); if (!wrap) return;
  const scroller = wrap.querySelector('.scroller');
  const left = wrap.querySelector('.scroll-arrow.left');
  const right = wrap.querySelector('.scroll-arrow.right');
  function step(){ return Math.max(200, Math.round(scroller.clientWidth * 0.6)); }
  function update(){
    const max = scroller.scrollWidth - scroller.clientWidth - 2;
    left.disabled  = scroller.scrollLeft <= 2;
    right.disabled = scroller.scrollLeft >= max;
    left.style.opacity  = left.disabled  ? .5 : 1;
    right.style.opacity = right.disabled ? .5 : 1;
  }
  left?.addEventListener('click', ()=> scroller.scrollBy({left:-step(), behavior:'smooth'}));
  right?.addEventListener('click',()=> scroller.scrollBy({left: step(), behavior:'smooth'}));
  scroller?.addEventListener('scroll', update, {passive:true});
  window.addEventListener('resize', update);
  update();
})();
</script>
